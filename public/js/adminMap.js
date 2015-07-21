$.extend(APP.adminMap, 
{
	thisSection: "home",
	mapControls: {
		defaultextent: null,
		draw: null,
		scale: null,
	},
	geometries: ['polyline','polygon','rectangle','circle','marker'],
	bCircleMarker: true,
	reportingsResource: "highliting_poi",
	reportingsId: "id",
	reportingsTitle: "subject",
	
	reportingsDatastruct: null,
	reportings: null,	// Backbone collection	
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
							<table id="reportingsTable" class="table table-hover table-striped">\
								<thead><tr><th>'+APP.i18n.translate('name')+'</th></tr></thead>\
								<tbody class="reportings"></tbody>\
							</table>\
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
					    that.openEditModal(null, layer, function(){
					    	that.start();
					    });
					});
					
					that.map.on('draw:edited', function (e) {
					    var layers = e.layers;
					    layers.eachLayer(function (layer) {
					    	that.openEditModal(layer.options[that.reportingsId], layer, function(){
						    	that.start();
						    });
					    });
					    
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
	
	addLayer: function(id, layer)
	{
		var that = this;

		that.featureGroup.addLayer(layer);
		that.reportings.get(id).set('layer',layer);
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
	
	openPopup: function(id)
	{
		var that = this;
		
		if (!id)
			return false;
		
		var model = that.reportings.get(id);
		
		if (!model.get('popup'))
			model.set('popup', L.popup({}, model.get('layer')));
		
		if ($.isFunction(model.get('layer').getLatLng))	// marker
			model.get('popup').setLatLng(model.get('layer').getLatLng());
		else
		{
			if ($.isFunction(model.get('layer').getBounds))	// vector layers
				model.get('popup').setLatLng(model.get('layer').getBounds().getCenter());
		}
		
		if (!model.get('popup').getContent())
		{
			var domPopup = that.popup.clone();
			//domPopup.find(".media-object").attr('src');
			domPopup.find(".media-heading").text(model.get(that.reportingsTitle));
			domPopup.find(".popupDetailsBtn").click(function(){
				//APP.config.bBack = true;
				APP.config.backUrl = "home";
				APP.config.workSpace.navigate("highliting_poi/"+id, {trigger: true, replace: true});
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
	
	emptyDomReportings: function()
	{
		var that = this;
		
		var table = that.layout.find("#reportingsTable");
		if ($.fn.DataTable.fnIsDataTable( table[0] ))
		{
			table.dataTable().fnDestroy();
		}
		that.layout.find(".reportings").empty();
	},
	
	setDomReportings: function(obj)
	{
		var that = this;
		
		var anchor = $('<tr id="reporting_'+obj[that.reportingsId]+'"><td>'+obj[that.reportingsTitle]+'</td></tr>');
		anchor.data(that.reportingsId, obj[that.reportingsId]);
		anchor.click(function(){
			that.onReportingSelect($(this).data(that.reportingsId));
		});
		that.layout.find(".reportings").append(anchor);
	},
	
	selectDomReporting: function(id)
	{
		var that = this;
		
		var list = that.layout.find(".reportings");
		list.find(".active").removeClass("active");
		list.find("#reporting_"+id).addClass("active");
	},

	initReportings: function()
	{
		var that = this;		
		
		var rc = Backbone.Collection.extend({
			model: Backbone.Model.extend({
				idAttribute: that.reportingsId,
				layer: null,
				popup: null,
			}),
			parse: function(response) {
			    return response.data.items;
			},
			url: APP.config.localConfig.urls[that.reportingsResource],
		});
		
		if (that.reportings)
			that.reportings.reset();
		else
			that.reportings = new rc();
	},
	
	getReportings: function(callback)
	{
		var that = this;
		
		that.reportings.fetch({
			success: function(collection, response, options)
			{
				that.emptyDomReportings();
				
				$.each(response.data.items, function(i,v)
				{
					that.setDomReportings(v);
					
					var gjo = {
						onEachFeature: function(feature, layer)
						{
							var opts = {};
							opts[that.reportingsId] = v[that.reportingsId];
							L.setOptions(layer, opts);
							layer.on('click', function()
							{
								that.onReportingSelect(layer.options[that.reportingsId]);
							});
							that.addLayer(v[that.reportingsId], layer);
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

	openEditModal: function(id, layer, onSave, onCancel)
	{
		var that = this;
				
		var modalTitle = (id)? that.reportings.get(id).get(that.reportingsTitle) : APP.utils.capitalize(APP.i18n.translate("New report"));
		var modalBody = APP.anagrafica.createFormTemplate(id, null, that.reportingsDatastruct, that.reportingsResource, []);
		
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
			
			APP.anagrafica.formSubmit(id, that.reportingsResource, function(){
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
	
	onReportingSelect: function(id)
	{
		var that = this;
		
		var model = that.reportings.get(id);
		
		if (model.has('extent'))
		{
			var e = model.get('extent').split(",");
			var extent = L.latLngBounds([L.latLng(e[1], e[0]),L.latLng(e[3], e[2])]);
			that.setMapBounds(extent);
		}
		
		that.openPopup(id);
	},
	
	getReportingsDatastruct: function()
	{
		var that = this;
		
		that.reportingsDatastruct = APP.utils.setBaseStructure(that.reportingsResource, that.reportingsResource);
		
		$.ajax({
			type: 'GET',
			url: APP.config.localConfig.urls['dStruct']+"?tb="+that.reportingsResource,
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					APP.anagrafica.loadStructure(data, that.reportingsDatastruct);
					that.reportingsDatastruct.values = that.reportings.toJSON();
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
		mc.css(h100);
		mc.css({
			"padding": 0,
			"margin": 0,
			"padding-top": that.body.find('#main_navbar_admin').height(),
		});
		mc.find("#"+that.thisSection+"Container").css({
			"padding":0,
			"margin": 0,
			"height":"100%",
		}).html(that.layout);
		
		that.createMap();
		that.createFeatureGroup();
		that.initReportings();
		that.getReportings(function(){
			that.layout.find("#reportingsTable").dataTable({
				"sPaginationType": "full_numbers",
				"oLanguage": APP.utils.getDataTableLanguage(),
			});			
			
			that.getReportingsDatastruct();
			that.setMapBounds();
			that.setDefaultExtent();
			that.addMapControls();
		});
	},
});