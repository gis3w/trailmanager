$.extend(APP.adminMap, 
{
	thisSection: "home",
	body: null,
	map: null,
	featureGroup: null,
	reportings: null,
	reportingsUrl: "jx/admin/highlitingpoi",
	reportingsId: "id",
	reportingsTitle: "subject",
	myModal: null,
	
	layout: $(	'<div class="container-fluid" style="height: 100%">\
					<div class="row">\
						<div class="col-md-12"></div>\
					</div>\
					<div class="row"  style="height: 100%">\
						<div class="col-md-8 map" style="height: 100%"></div>\
						<div class="col-md-4 reportings list-group" style="height: 100%"></div>\
					</div>\
				</div>'),
				
	popup: $(	'<div class="container-fluid">\
					<div class="row">\
						<div class="col-md-4 image"></div>\
						<div class="col-md-8 description"></div>\
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
	
	initPopup: function(layer)
	{
		var that = this;
		
		var reporting = that.reportings.get(layer.options.reportingId);
		var popup = layer.getPopup();
		
		var puc = $(popup.getContent());
		puc.find(".image").html('<img src="'+reporting['image']+'" alt="" style="width:100%;height:100%">');
		puc.find(".description").html('<p></p>');
		
		popup.setContent(puc.html());
	},
	
	onLayerSelect: function(layer)
	{
		var that = this;
		
		var myId = layer.options.reportingId;
		//that.initPopup(layer);
		
		that.myModal.modal("show");
	},
	
	setMapBounds: function()
	{
		var that = this;
		
		var bounds = that.featureGroup.getBounds();
		that.map.fitBounds(bounds);
	},
	
	setReportings: function()
	{
		var that = this;		
		
		var rc = Backbone.Collection.extend({
			model: Backbone.Model.extend({
				idAttribute: that.reportingsId,
				layer: null,
			}),
			parse: function(response) {
			    return response.data.items;
			},
			url: that.reportingsUrl,
		});
		
		if (that.reportings)
			that.reportings.reset();
		else
			that.reportings = new rc();
	},
	
	getReportings: function()
	{
		var that = this;
		
		that.reportings.fetch({
			success: function(collection, response, options)
			{
				that.layout.find(".reportings").empty();
				$.each(response.data.items, function(i,v)
				{
					var anchor = $('<a href="#" class="list-group-item"></a>');
					anchor.append(v[that.reportingsTitle]);
					if (i == 0)
						anchor.addClass("active");
					that.layout.find(".reportings").append(anchor);
					//that.addMarker(v[that.reportingsTitle], L.GeoJSON.coordsToLatLng(v.the_geom.coordinates));
					L.geoJson(v.the_geom, {
						onEachFeature: function(feature, layer)
						{
							L.setOptions(layer, {
								"reportingId": v[that.reportingsId]
							});
							//layer.bindPopup(that.popup.clone());
							layer.on('click', function()
							{
								that.onLayerSelect(this);
							});
							that.addLayer(v[that.reportingsId], layer);
						}
					});
					
				});
				that.setMapBounds();
			}
		});
	},
	
	createModal: function()
	{
		var that = this;
		
		that.myModal = APP.utils.createModal({
			container: that.body,
			id: "adminMapModal",
			size: "lg",
			header: "Header",
			body: $('<div>BODY</div>'),
			shown: function(){
				
			},
			hidden: function(){ 
				
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
		mc.find("#"+that.thisSection+"Container").css(h100).html(that.layout);
		
		that.createModal();
		
		that.createMap();
		that.createFeatureGroup();
		that.setReportings();
		that.getReportings();
	},
});