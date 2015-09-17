$.extend(APP.adminMap, 
{
	thisSection: "home",
	mapControls: {
		coordinates: null,
		defaultextent: null,
		draw: null,
		layers: null,
		scale: null,
	},
	geometries: ['polyline','marker'], //leaflet.draw
	bCircleMarker: false,
	
	datastruct: {},
	info: {
		'reportings': {
			'resource': "highliting_poi",
			'idAttribute': "id",
			'titleAttribute': "subject",
			'tableId': "reportingsTable",
			'table': null,
		},
		'pathreportings': {
			'resource': "highliting_path",
			'idAttribute': "id",
			'titleAttribute': "subject",
			'tableId': "trailsTable",
			'table': null,
		},
		'trails': {
			'resource': "path",
			'idAttribute': "id",
			'titleAttribute': "title",
			'tableId': null,
			'table': null,
		}
	},
	
	fkValori: {},
	
	markerIconBaseUrl: '/download/mappin/index/',
	
	body: null,
	myModal: null,
	map: null,
	featureGroup: null,
	
	layout: $(	'<div class="container-fluid" style="height: 100%">\
					<div class="row" style="height: 100%">\
						<div class="col-md-5" style="height: 100%; padding-top: 15px">\
							<div class="row">\
								<div class="col-md-12 report">\
								</div>\
							</div>\
							<div class="row" style="height: 50%;">\
								<div class="col-md-12 map" style="height: 100%">\
								</div>\
							</div>\
						</div>\
						<div class="col-md-7 table-responsive" style="height: 100%; overflow-y: scroll; margin-bottom:0px; padding-top: 20px">\
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
					that.mapControls[i] = L.control.coordinates({
						position:"bottomleft", //optional default "bootomright"
					    //decimals:2, //optional default 4
					    //decimalSeperator:".", //optional default "."
					    //labelTemplateLat:"Latitude: {y}", //optional default "Lat: {y}"
					    //labelTemplateLng:"Longitude: {x}", //optional default "Lng: {x}"
					    //enableUserInput:true, //optional default true
					    //useDMS:false, //optional default false
					    //useLatLngOrder: true, //ordering of labels, default false-> lng-lat
					    //markerType: L.marker, //optional default L.marker
					    //markerProps: {} //optional default {}
					});
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
					    					    
					    var gj = layer.toGeoJSON();
					    APP.config.parameters['the_geom'] = gj;
					    var resource = null;
					    switch (gj.geometry.type)
					    {
						    case "Point":
						    	resource = "new_highliting_poi";
						    	break;
						    case "Polygon":
						    	resource = "new_highliting_area";
						    	break;
						    case "LineString":
						    	resource = "new_highliting_path";
						    	break;
					    }
					    if (resource)
					    	APP.config.workSpace.navigate(resource, {trigger: true, replace: true});
					});
					
					that.map.on('draw:edited', function (e) {
					    
					});
					break;
				case "layers":
					var bgl = APP.config.getControlLayers();
					that.mapControls[i] = L.control.layers(bgl.baselayers, bgl.overlays);
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
			
			if (l.setStyle && obj.style && $.isPlainObject(obj.style))
				l.setStyle(obj.style);
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
		{
			var popupParams = {};
			if ($.isFunction(model.get('layer').getLatLng) && !that.bCircleMarker)
				popupParams.offset = L.point(0, -33);
			model.set('popup', L.popup(popupParams, model.get('layer')));
		}
		
		if ($.isFunction(model.get('layer').getLatLng))	// marker
		{
			var latlng = model.get('layer').getLatLng();
			model.get('popup').setLatLng(latlng);
		}
		else
		{
			if ($.isFunction(model.get('layer').getLatLngs))// vector layers
			{
				var ll = model.get('layer').getLatLngs()[0];
				var num = Math.floor(ll.length/2);
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
				APP.config.bBack = true;
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
	/*
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
	*/
	
	setReport: function()
	{
		var that = this;
		
		$.ajax({
			type: 'GET',
			url: APP.config.localConfig.urls['highliting_summary'],
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					var myData = data.data;
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
	
	setTable: function(resource)
	{
		var that = this;
		
		if (!that.info[resource].tableId)
			return false;
		
		if (that.info[resource].table)
		{
			if ($.fn.DataTable.fnIsDataTable( that.info[resource].table[0] ))
			{
				that.info[resource].table.dataTable().fnClearTable(true);
				that.info[resource].table.dataTable().fnDestroy(true);
			}
			that.info[resource].table.remove();
		}
		
		that.info[resource].table = $(	'<table id="'+that.info[resource].tableId+'" class="table table-bordered table-hover table-striped table-condensed">\
											<caption><h3>'+APP.i18n.translate(that.info[resource].resource)+'</h3></caption>\
											<thead><tr></tr></thead>\
											<tbody></tbody>\
										</table>');
				
		$.each(that.datastruct[that.info[resource].resource].columns, function(i, v)
		{
			if (!v.table_show)
				return true;
			
			that.info[resource].table.find("thead tr").append('<th class="table-th">'+v.label+'</th>');
			
			if (!v.hasOwnProperty("foreign_key") && v.form_input_type == "combobox" && !APP.utils.isset(v.slave_of) && !APP.utils.isset(that.fkValori[v.name]))
			{
				that.fkValori[v.name] = APP.utils.getForeignValue(v, null);
			}
		});
				
		$.each(that[resource].models, function(modK, model)
		{
			var modelId = model.get(that.info[resource].idAttribute);
			var tr = $('<tr style="cursor: pointer" id="item_'+modelId+'"></tr>');
			
			tr = APP.utils.setTableRow({
				row: tr,
				model: model.toJSON(),
				datastruct: that.datastruct[that.info[resource].resource],
				valori: that.fkValori,
			});
			
			tr.data(that.info[resource].idAttribute, model.get(that.info[resource].idAttribute));
			tr.click(function(){
				that.onItemSelect($(this).data(that.info[resource].idAttribute), that[resource], that.info[resource]);
			});
			that.info[resource].table.find("tbody").append(tr);
		});
	},
	
	showTables: function()
	{
		var that = this;
		
		$.each(that.info, function(key, targetInfo)
		{
			if (targetInfo.table)
			{
				that.layout.find(".table-responsive").append(targetInfo.table);
				targetInfo.table.dataTable({
					"sPaginationType": "full_numbers",
					"oLanguage": APP.utils.getDataTableLanguage(),
				});
			}
		});
		
		that.layout.find(".table-responsive").fadeIn();
	},
	
	initItems: function(target, targetInfo)
	{
		var that = this;
		
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
				$.each(response.data.items, function(i,v)
				{ 
					var gjo = {
						pointToLayer: function(feature, latlng)
						{
							if (that.bCircleMarker)
							{
								return L.circleMarker(latlng);
							}
							
							var markerParams = {};
							if (v.highliting_typology_id && v.highliting_state_id)
							{
								markerParams.icon = L.icon({
									iconUrl: that.markerIconBaseUrl+'hs'+v.highliting_state_id+'tp'+v.highliting_typology_id+'.png',
									//iconRetinaUrl: 'my-icon@2x.png',
									iconSize: [APP.config.localConfig.icon_data.width, APP.config.localConfig.icon_data.height],
									iconAnchor: [APP.config.localConfig.icon_data.width / 2, APP.config.localConfig.icon_data.height],
									popupAnchor: [0, -APP.config.localConfig.icon_data.height],
									//shadowUrl: '/public/img/pin_shadow.png',
									//shadowRetinaUrl: 'my-icon-shadow@2x.png',
									shadowSize: [55, 51],
									shadowAnchor: [28, 48]
								});
							}
							
							return L.marker(latlng, markerParams);
						},
						onEachFeature: function(feature, layer)
						{
							var opts = {};
							opts[targetInfo.idAttribute] = v[targetInfo.idAttribute];
							
							var params = {
								'id': v[targetInfo.idAttribute],
								'layer': layer,
								'target': target,
								'options': opts,
								'style': {},
								'label': v[targetInfo.titleAttribute],
								'onClick': function()
								{
									var y = this;
									that.onItemSelect(this.options[targetInfo.idAttribute], target, targetInfo);
								}
							};
							
							if (v['color'])
								params.style.color = v['color'];
							if (v['width'])
								params.style.weight = v['width'];
							
							that.addLayer(params);
						}
					};
					
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
	
	getItemsDatastruct: function(target, targetInfo, callback)
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
					if ($.isFunction(callback))
						callback();
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
		that.layout.find(".table-responsive").hide();
		mc.find("#"+that.thisSection+"Container").css({
			"padding":0,
			"margin": 0,
			"height":"100%",
			"padding-top": that.body.find('#main_navbar_admin').parents('.navbar').height(),
			"padding-bottom": that.body.find('#main_navbar_admin').parents('.navbar').outerHeight()-that.body.find('#main_navbar_admin').height(),
		}).html(that.layout);
		
		that.createMap();
		that.createFeatureGroup();
		
		APP.utils.setHomeReport({
			panelPerRow: 2,
			container: that.layout.find(".report").empty(),
			section: 'highliting_summary',
		});
		
		var counter = 0;
		$.each(that.info, function(resource, objR)
		{
			that[resource] = that.initItems(that[resource], that.info[resource]);
			
			that.getItems(that[resource], that.info[resource], function()
			{
				that.getItemsDatastruct(that[resource], that.info[resource], function(){
					counter++;
					//that.datastruct[that.info[resource]].values = that[resource].toJSON();
					that.setTable(resource);
					if (counter === Object.keys(that.info).length)
					{
						that.setMapBounds();
						that.setDefaultExtent();
						that.addMapControls();
						that.showTables();
					}
				});
			});
		});
	},
});