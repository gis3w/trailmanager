$.extend(APP.adminMap, 
{
	thisSection: "home",
	mapControls: {
		layers: null,
		coordinates: null,
		defaultextent: null,
		draw: null,
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
			'layers': [],
		},
		'pathreportings': {
			'resource': "highliting_path",
			'idAttribute': "id",
			'titleAttribute': "subject",
			'tableId': "trailsTable",
			'table': null,
			'layers': [],
		},
	},
	
	overlays: {
		'Sentieri': {
			'resource': "path",
			'idAttribute': "id",
			'titleAttribute': "title",
			'bLabel': true,
			'bClick': true,
			'layers': [],
			'showDefault': true,
		},
		'Tratte': {
			'resource': "path_segment",
			'idAttribute': "id",
			'titleAttribute': "id",
			'bLabel': true,
			'bClick': true,
			'layers': [],
			'showDefault': false,
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
						<div class="col-md-5 map" style="height: 100%">\
						</div>\
						<div class="col-md-7" style="height: 100%; margin-bottom:0px; padding-top: 15px; overflow-y: auto">\
							<ul class="nav nav-tabs" role="tablist">\
								<li role="presentation" class="active"><a data-tabname="Report" href="#report" aria-controls="report" role="tab" data-toggle="tab"></a></li>\
								<li role="presentation"><a data-tabname="Highlitings" href="#highliting" aria-controls="highliting" role="tab" data-toggle="tab"></a></li>\
							</ul>\
							<div class="tab-content" style="padding-top: 15px; ">\
								<div role="tabpanel" class="tab-pane report active" id="report"></div>\
								<div role="tabpanel" class="tab-pane" id="highliting">\
									<div class="tableContainer"></div>\
								</div>\
							</div>\
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
								<i class="icon icon-search"></i> <span></span>\
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
	
	setOverlays: function()
	{
		var that = this;
		
		$.each(that.overlays, function(i,v)
		{
			v.layerGroup = L.layerGroup();
			that.mapControls.layers.addOverlay(v.layerGroup, i);
			
			$.ajax({
				method: "GET",
				url: APP.config.localConfig.urls[v.resource],
				success: function(result, status, jqXHR)
				{
					if (!result || !result.data || !$.isArray(result.data.items))
						return false;					
					
					$.each(result.data.items, function(ii,vv)
					{
						var gjl = L.geoJson({
							"type": "Feature",
						    "geometry": vv.the_geom,
						}, vv);
						if (v.bClick)
							gjl.on('click',function(){
								that.navigateToItem(v.resource, vv[v.idAttribute]);
							});
						if (v.bLabel)
							gjl.bindLabel(""+vv[v.titleAttribute]);
						v.layers.push(gjl);
						v.layerGroup.addLayer(gjl);
					});
					
					if (v.showDefault)
						v.layerGroup.addTo(that.map);
					
				}
			});
		});
	},
	
	createFeatureGroup: function()
	{
		var that = this;
				
		that.featureGroup = L.featureGroup();
		that.featureGroup.addTo(that.map);
	},
	
	addLayer: function(obj)//layer,target,targetInfo,id,onClick,options
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
				$.each(feature.coordinates, function(i,v)
				{
					var l = L.polyline(L.GeoJSON.coordsToLatLngs(v));
					setOtherOptions(l);
					obj.targetInfo.layers.push(l);
					l.addTo(that.featureGroup);
				});
				break
			case "MultiPolygon": 
				$.each(feature.coordinates, function(i,v){
					var l = L.polygon(L.GeoJSON.coordsToLatLngs(v,1));
					setOtherOptions(l);
					obj.targetInfo.layers.push(l);
					l.addTo(that.featureGroup);
				});
				break;
			default:
				setOtherOptions(obj.layer);
				obj.targetInfo.layers.push(obj.layer);
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
				that.navigateToItem(targetInfo.resource, id);
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
	
	navigateToItem: function(resource, id)
	{
		APP.config.bBack = true;
		APP.config.backUrl = this.thisSection;
		APP.config.workSpace.navigate(resource+"/"+id, {trigger: true, replace: true});
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
	
	setTables: function()
	{
		var that = this;
		
		$.each(that.info, function(resource, targetInfo)
		{
			if (!targetInfo.tableId)
				return true;
			
			if (targetInfo.table)
			{
				if ($.fn.DataTable.fnIsDataTable( targetInfo.table[0] ))
				{
					targetInfo.table.dataTable().fnClearTable(true);
					targetInfo.table.dataTable().fnDestroy(true);
				}
				targetInfo.table.remove();
				targetInfo.table = null;
			}
			
			targetInfo.table = $(	'<table id="'+that.info[resource].tableId+'" class="table table-bordered table-hover table-striped table-condensed">\
										<thead><tr></tr></thead>\
										<tbody></tbody>\
									</table>');
			
			$.each(that.datastruct[targetInfo.resource].columns, function(i, v)
			{
				if (!v.table_show)
					return true;
				
				targetInfo.table.find("thead tr").append('<th class="table-th">'+v.label+'</th>');
				
				if (!v.hasOwnProperty("foreign_key") && v.form_input_type == "combobox" && !APP.utils.isset(v.slave_of) && !APP.utils.isset(that.fkValori[v.name]))
				{
					that.fkValori[v.name] = APP.utils.getForeignValue(v, null);
				}
			});
					
			$.each(that[resource].models, function(modK, model)
			{
				var modelId = model.get(targetInfo.idAttribute);
				var tr = $('<tr style="cursor: pointer" id="item_'+modelId+'"></tr>');
				
				tr = APP.utils.setTableRow({
					row: tr,
					model: model.toJSON(),
					datastruct: that.datastruct[targetInfo.resource],
					valori: that.fkValori,
				});
				
				tr.data(targetInfo.idAttribute, model.get(targetInfo.idAttribute));
				tr.click(function(){
					that.onItemSelect($(this).data(targetInfo.idAttribute), that[resource], targetInfo);
				});
				targetInfo.table.find("tbody").append(tr);
			});
		});
	},
	
	showTables: function()
	{
		var that = this;
		
		that.layout.find(".tableContainer").empty();
		
		$.each(that.info, function(key, targetInfo)
		{
			if (targetInfo.table)
			{
				var div = $('<div></div>');
				div.append('<h3>'+APP.i18n.translate(APP.utils.capitalize(targetInfo.resource))+'</h3>');
				
				var tableResponsive = $('<div class="table-responsive"></div>');
				tableResponsive.append(targetInfo.table);
				div.append(tableResponsive);
				
				targetInfo.table.dataTable({
					"sPaginationType": "full_numbers",
					"oLanguage": APP.utils.getDataTableLanguage(),
				});
				
				that.layout.find(".tableContainer").append(div);
			}
		});
		
		that.layout.find(".tableContainer").fadeIn();
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
								'targetInfo': targetInfo,
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
		
		var as = that.layout.find(".nav-tabs a");
		$.each(as, function(i,v){
			v = $(v);
			v.html(APP.i18n.translate(v.attr("data-tabname")));
		});
		
		that.popup.find(".popupDetailsBtn span").html(APP.i18n.translate("Edit"));
		
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
		
		APP.utils.setHomeReport({
			panelPerRow: 2,
			container: that.layout.find(".report").empty(),
			section: 'highliting_summary',
			callback: function(){}
		});
		
		that.createMap();
		that.createFeatureGroup();
		
		var counter = 0;
		$.each(that.info, function(resource, objR)
		{
			that[resource] = that.initItems(that[resource], that.info[resource]);
			
			that.getItems(that[resource], that.info[resource], function()
			{
				that.getItemsDatastruct(that[resource], that.info[resource], function()
				{
					counter++;
					//that.datastruct[that.info[resource]].values = that[resource].toJSON();
					if (counter === Object.keys(that.info).length)
					{
						that.setMapBounds();
						that.setDefaultExtent();
						that.addMapControls();
						that.setOverlays();
						that.setTables();
						that.showTables();
					}
				});
			});
		});
	},
});