$.extend(APP.adminMap, 
{
	thisSection: "home",
	mapControls: {
		defaultextent: null,
		draw: null,
		scale: null,
	},
	geometries: ['polyline','polygon','rectangle','circle','marker'], //leaflet.draw
	bCircleMarker: true,
	
	datastruct: {},
	info: {
		'reportings': {
			'resource': "highliting_poi",
			'idAttribute': "id",
			'titleAttribute': "subject",
			'tableId': "reportingsTable",
		},
		'trails': {
			'resource': "path",
			'idAttribute': "id",
			'titleAttribute': "title",
			'tableId': null,
		}
	},

	reportings: null,	// Backbone collection
	trails: null, // Backbone collection
	
	body: null,
	myModal: null,
	map: null,
	featureGroup: null,
	
	layout: $(	'<div class="container-fluid" style="height: 100%">\
					<div class="row">\
						<div class="col-md-12"></div>\
					</div>\
					<div class="row" style="height: 100%">\
						<div class="col-md-6 map" style="height: 100%"></div>\
						<div class="col-md-6 table-responsive" style="height: 100%; margin-bottom:0px; padding-top: 20px">\
						</div>\
					</div>\
				</div>'),
				
	popup: $(	'<div class="media">\
					<div class="media-left">\
					    <a href="#">\
					      <img class="media-object" src="" alt="">\
					    </a>\
				  	</div>\
					<div class="media-body">\
						<h4 class="media-heading"></h4>\
						<div class="text-center">\
							<button type="button" class="btn btn-default btn-sm popupDetailsBtn" style="margin-top: 10px">\
								<i class="icon icon-search"></i> '+APP.i18n.translate('Edit')+'\
							</button>\
						</div>\
					</div>\
				</div>'),
				
	createMap: function()
	{
		var that = this;
		
		if (that.map)
			that.map.remove();
		
		that.map = L.map(that.layout.find(".map")[0]);
		
		L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
		    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(that.map);
	},
	
	addMapControls: function()
	{
		var that = this;
		
		$.each(that.mapControls, function(i,v)
		{
			switch(i)
			{
				case "coordinates":
					that.mapControls[i] = L.control.coordinates({});
					break;
				case "defaultextent":
					that.mapControls[i] = L.control.defaultExtent({
						title: APP.i18n.translate('Zoom to default extent'),
					}).setCenter(that.map.getCenter()).setZoom(that.map.getZoom());
					break;
				case "draw":
					var drawOpts = {
						position: 'topleft',
						draw: {
							polyline: false,
							polygon: false,
							rectangle: false,
							circle: false,
							marker: false
						},
						edit: {
					        featureGroup: that.featureGroup,
					        edit: false,
					        remove: false
					    }
					};
					
					$.each(that.geometries, function(j,k)
					{
						if (APP.utils.isset(drawOpts.draw[k]))
							drawOpts.draw[k] = {};
					});
					
					that.mapControls[i] = new L.Control.Draw(drawOpts);
					
					that.map.on('draw:created', function (e) {
					    var type = e.layerType,
					        layer = e.layer;

					    if (type === 'marker' && that.bCircleMarker) {
					        layer = L.circleMarker(layer.getLatLng());
					    }
					    
					    that.featureGroup.addLayer(layer);
					    /*
					    that.openEditModal(null, layer, function(){
					    	that.start();
					    });
					    */
					    APP.config.backUrl = that.thisSection;
						APP.config.workSpace.navigate("new_highliting_poi", {trigger: true, replace: true});
					});
					
					that.map.on('draw:edited', function (e) {
					    
					});
					break;
				case "scale":
					that.mapControls[i] = L.control.scale();
					break;
			}
			that.mapControls[i].addTo(that.map);
		});
	},
	
	createFeatureGroup: function()
	{
		var that = this;
				
		that.featureGroup = L.featureGroup();
		that.featureGroup.addTo(that.map);
	},
	
	addLayer: function(obj)//layer,target,id,onClick,options
	{
		var that = this;
		
		var layerResult = null;
		var feature = obj.layer.feature.geometry;
		
		var setOtherOptions = function(l)
		{
			if (obj.onClick && $.isFunction(obj.onClick))
				l.on('click',obj.onClick);
			
			if (obj.label)
				l.bindLabel(obj.label);
			
			if (obj.options && $.isPlainObject(obj.options))
				L.setOptions(l, obj.options);
		};
		
		switch(feature.type)
		{
			case "MultiLineString":
				$.each(feature.coordinates, function(i,v){
					var l = L.polyline(L.GeoJSON.coordsToLatLngs(v));
					setOtherOptions(l);
					l.addTo(that.featureGroup);
				});
				break
			case "MultiPolygon": 
				$.each(feature.coordinates, function(i,v){
					var l = L.polygon(L.GeoJSON.coordsToLatLngs(v,1));
					setOtherOptions(l);
					l.addTo(that.featureGroup);
				});
				break;
			default:
				setOtherOptions(obj.layer);
				obj.layer.addTo(that.featureGroup);
		}
		
		obj.target.get(obj.id).set('layer',obj.layer);
	},
	
	setMapBounds: function(bounds)
	{
		var that = this;
		
		bounds = (bounds)? bounds : that.featureGroup.getBounds();
		that.map.fitBounds(bounds, {animate: false});
	},
	
	setDefaultExtent: function()
	{
		var that = this;
		
		if (that.mapControls.defaultextent)
		{
			var bounds = that.featureGroup.getBounds();
			var center = bounds.getCenter();
			var zoom = that.map.getBoundsZoom(bounds);
			that.mapControls.defaultextent.setCenter(center).setZoom(zoom);
		}
	},
	
	openPopup: function(id, target, targetInfo)
	{
		var that = this;
		
		if (!id)
			return false;
		
		var model = target.get(id);
		
		if (!model.get('popup'))
			model.set('popup', L.popup({}, model.get('layer')));
		
		if ($.isFunction(model.get('layer').getLatLng))	// marker
			model.get('popup').setLatLng(model.get('layer').getLatLng());
		else
		{
			if ($.isFunction(model.get('layer').getLatLngs))// vector layers
			{
				var ll = model.get('layer').getLatLngs()[0];
				var num = parseInt(ll.length/2);
				model.get('popup').setLatLng(ll[num]);
			}
			else
			{
				if ($.isFunction(model.get('layer').getBounds))// featureGroup
				{
					var ll = model.get('layer').getBounds().getCenter();
					model.get('popup').setLatLng(ll);
				}
			}
		}
		
		if (!model.get('popup').getContent())
		{
			var domPopup = that.popup.clone();
			//domPopup.find(".media-object").attr('src');
			domPopup.find(".media-heading").text(model.get(targetInfo.titleAttribute));
			domPopup.find(".popupDetailsBtn").click(function(){
				APP.config.backUrl = that.thisSection;
				APP.config.workSpace.navigate(targetInfo.resource+"/"+id, {trigger: true, replace: true});
				/*
				that.openEditModal(id, model.get('layer'), function(){
			    	that.start();
			    });
			    */
			});
			model.get('popup').setContent(domPopup[0]);
		}
		
		model.get('popup').openOn(that.map);
	},
	
	openEditModal: function(id, layer, onSave, onCancel)
	{
		var that = this;
		
		var target = that.reportings;
		var targetInfo = that.info['reportings'];
				
		var modalTitle = (id)? target.get(id).get(targetInfo.titleAttribute) : APP.utils.capitalize(APP.i18n.translate("New report"));
		
		var dataObj = null;
		if (layer && $.isFunction(layer.getLatLng))
			dataObj = {"the_geom": {"coordinates": [layer.getLatLng().lng, layer.getLatLng().lat]}};
		
		var modalBody = APP.anagrafica.createFormTemplate(id, dataObj, that.datastruct[targetInfo.resource], targetInfo.resource, []);
		
		var footerDiv = $(	'<div>\
								<button type="button" class="btn btn-success"><i class="icon icon-ok"></i> '+APP.i18n.translate('save')+'</button>\
								<button type="button" class="btn btn-default"><i class="icon icon-remove"></i> '+APP.i18n.translate('cancel')+'</button>\
							</div>');
		
		footerDiv.find(".btn-success").click(function()
		{
			var tg = modalBody.find("#APP-the_geom");
			if (tg.length === 0)
			{
				tg = $('<input id="APP-the_geom" name="the_geom" type="hidden">');
				modalBody.append(tg);
			}
			
			var template = {
				"type":"FeatureCollection",
				"features":[]
			};
			
			template.features.push(layer.toGeoJSON());
			tg.val(JSON.stringify(template));
			
			APP.anagrafica.formSubmit(id, targetInfo.resource, function(){
				that.setDefaultExtent();
				that.myModal.modal("hide");
				if (APP.utils.isset(onSave) && $.isFunction(onSave))
					onSave();
			}, true);
		});
		footerDiv.find(".btn-default").click(function(){
			that.myModal.modal("hide");
			
			if (APP.utils.isset(onCancel) && $.isFunction(onCancel))
				onCancel();
		});
						
		that.myModal = APP.modals.create({
			container: that.body,
			id: "adminMapModal",
			size: "lg",
			header: modalTitle,
			body: modalBody,
			footer: footerDiv,
			onShown: function(){
				var f = modalBody.find("form");
				APP.utils.setLookForm(f, null);
			},
			onHidden: function(){ 
				
			}
		});
		
		that.myModal.modal("show");
	},
	
	emptyDomItems: function(targetInfo)
	{
		var that = this;
		
		var table = that.layout.find("#"+targetInfo.tableId);
		if ($.fn.DataTable.fnIsDataTable( table[0] ))
		{
			table.dataTable().fnClearTable(true);
		}
		else
			table.find("tbody").empty();
	},
	
	setDomItems: function(target, targetInfo, obj)
	{
		var that = this;
		
		var anchor = $('<tr id="item_'+obj[targetInfo.idAttribute]+'"><td>'+obj[targetInfo.titleAttribute]+'</td></tr>');
		anchor.data(targetInfo.idAttribute, obj[targetInfo.idAttribute]);
		anchor.click(function(){
			that.onItemSelect($(this).data(targetInfo.idAttribute), target, targetInfo);
		});
		that.layout.find("#"+targetInfo.tableId).find("tbody").append(anchor);
	},
	
	setDomTable: function(targetInfo)
	{
		var that = this;
		
		var table = that.layout.find('#'+targetInfo.tableId);
		if (table.length>0)
		{
			if ($.fn.DataTable.fnIsDataTable( table[0] ))
			{
				table.dataTable().fnDestroy(true);
			}
		}
		
		if (targetInfo.tableId)
		{
			table = $(	'<table id="'+targetInfo.tableId+'" class="table table-hover table-striped" style="margin-bottom: 15px">\
							<caption>'+APP.i18n.translate(targetInfo.resource)+'</caption>\
							<thead><tr><th>'+APP.i18n.translate('name')+'</th></tr></thead>\
							<tbody></tbody>\
						</table>');
			that.layout.find(".table-responsive").append(table);
		}
	},
	
	datatablize: function(targetInfo)
	{
		var that = this;
		
		var table = that.layout.find("#"+targetInfo.tableId);
		if (table.length==0)
			return false;
		
		if ($.fn.DataTable.fnIsDataTable( table[0] ))
		{
			table.dataTable().fnDestroy(true);
		}
		
		table.dataTable({
			"sPaginationType": "full_numbers",
			"oLanguage": APP.utils.getDataTableLanguage(),
		});
	},
	
	initItems: function(target, targetInfo)
	{
		var that = this;
		
		that.setDomTable(targetInfo);
		
		var rc = Backbone.Collection.extend({
			model: Backbone.Model.extend({
				idAttribute: targetInfo.idAttribute,
				layer: null,
				popup: null,
			}),
			parse: function(response) {
			    return response.data.items;
			},
			url: APP.config.localConfig.urls[targetInfo.resource],
		});
		
		if (target)
			target.reset();
		else
			target = new rc();
		
		return target;
	},
	
	getItems: function(target, targetInfo, callback)
	{
		var that = this;
		
		target.fetch({
			success: function(collection, response, options)
			{
				that.emptyDomItems(targetInfo);
				
				$.each(response.data.items, function(i,v)
				{ 
					that.setDomItems(target, targetInfo, v);
					
					var gjo = {
						onEachFeature: function(feature, layer)
						{
							var opts = {};
							opts[targetInfo.idAttribute] = v[targetInfo.idAttribute];
							
							that.addLayer({
								'id': v[targetInfo.idAttribute],
								'layer': layer,
								'target': target,
								'options': opts,
								'label': v[targetInfo.titleAttribute],
								'onClick': function()
								{
									var y = this;
									that.onItemSelect(this.options[targetInfo.idAttribute], target, targetInfo);
								}
							});
						}
					};
					
					if (that.bCircleMarker)
					{
						gjo.pointToLayer = function(feature, latlng){
							return L.circleMarker(latlng);
						};
					}
					
					L.geoJson(v.the_geom, gjo);
				});
				if (callback && $.isFunction(callback))
					callback();
			}
		});
	},
	
	onItemSelect: function(id, target, targetInfo)
	{
		var that = this;
		
		var model = target.get(id);
		
		if (model.has('extent'))
		{
			var e = model.get('extent').split(",");
			var extent = L.latLngBounds([L.latLng(e[1], e[0]),L.latLng(e[3], e[2])]);
			that.setMapBounds(extent);
		}
		
		that.openPopup(id, target, targetInfo);
	},
	
	getItemsDatastruct: function(target, targetInfo)
	{
		var that = this;
		
		that.datastruct[targetInfo.resource] = APP.utils.setBaseStructure(targetInfo.resource, targetInfo.resource);
		
		$.ajax({
			type: 'GET',
			url: APP.config.localConfig.urls['dStruct']+"?tb="+targetInfo.resource,
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					APP.anagrafica.loadStructure(data, that.datastruct[targetInfo.resource]);
					that.datastruct[targetInfo.resource].values = target.toJSON();
				}
				else
					APP.utils.showErrMsg(data);
			},
			error: function(data)
			{
				APP.utils.showErrMsg(data);
			}
		});
	},
	
	start: function()
	{
		var that = this;
		
		var h100 = {"height":"100%","width":"100%"};
		
		that.body = $("body");
		that.body.parents("html").css(h100);
		that.body.css(h100);
		var mc = that.body.find("#mainContent");
		mc.css(h100).css({"padding":0,"margin":0});
		mc.find("#"+that.thisSection+"Container").css({
			"padding":0,
			"margin": 0,
			"height":"100%",
			"padding-top": that.body.find('#main_navbar_admin').parents('.navbar').height(),
			"padding-bottom": that.body.find('#main_navbar_admin').parents('.navbar').outerHeight()-that.body.find('#main_navbar_admin').height(),
		}).html(that.layout);
		
		that.createMap();
		that.createFeatureGroup();
		that.reportings = that.initItems(that.reportings, that.info['reportings']);
		that.getItems(that.reportings, that.info['reportings'], function(){
			that.datatablize(that.info['reportings']);			
			that.getItemsDatastruct(that.reportings, that.info['reportings']);
			that.setMapBounds();
			that.setDefaultExtent();
			that.addMapControls();
		});
		that.trails = that.initItems(that.trails, that.info['trails']);
		that.getItems(that.trails, that.info['trails'], function(){
			that.datatablize(that.info['trails']);
		});
	},
});