$.extend(APP.adminMap, 
{
	map: null,
	featureGroup: null,
	
	reportings: null,	// Backbone collection
	
	reportingsUrl: "jx/admin/highlitingpoi",
	reportingsId: "id",
	reportingsTitle: "subject",
	
	thisSection: "home",
	body: null,
	myModal: null,
	
	layout: $(	'<div class="container-fluid" style="height: 100%">\
					<div class="row">\
						<div class="col-md-12"></div>\
					</div>\
					<div class="row" style="height: 100%">\
						<div class="col-md-8 map" style="height: 100%"></div>\
						<div class="col-md-4 reportings list-group" style="height: 100%"></div>\
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
						<div>\
							<button type="button" class="btn btn-default btn-sm popupDetailsBtn" style="margin-top: 10px">\
								<i class="icon icon-search"></i> Vedi scheda\
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
		that.map.fitBounds(bounds);
	},
	
	initPopup: function(id)
	{
		var that = this;
		
		var model = that.reportings.get(id);
		
		model.set('popup', L.popup({}, model.get('layer')));
	},
	
	emptyDomReportings: function()
	{
		var that = this;
		
		that.layout.find(".reportings").empty();
	},
	
	setDomReportings: function(obj)
	{
		var that = this;
		
		var anchor = $('<a href="#" id="reporting_'+obj[that.reportingsId]+'" class="list-group-item"></a>');
		anchor.data(that.reportingsId, obj[that.reportingsId]);
		anchor.click(function(){
			that.onReportingSelect($(this).data(that.reportingsId));
		});
		anchor.append(obj[that.reportingsTitle]);
		that.layout.find(".reportings").append(anchor);
	},
	
	selectDomReporting: function(id)
	{
		var that = this;
		
		var list = that.layout.find(".reportings");
		list.find(".active").removeClass("active");
		list.find("#reporting_"+id).addClass("active");
	},

	setReportings: function()
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
			url: that.reportingsUrl,
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
					
					L.geoJson(v.the_geom, {
						pointToLayer: function(feature, latlng)
						{
							return L.circleMarker(latlng);
						},
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
					});
				});
				if (callback && $.isFunction(callback))
					callback();
			}
		});
	},
	
	openReportingDetail: function(id)
	{
		var that = this;
		
		var model = that.reportings.get(id);
		
		that.myModal.find(".modal-header").find(".lead").text(model.get(that.reportingsTitle));
		
		that.myModal.modal("show");
	},
	
	onReportingSelect: function(id)
	{
		var that = this;
		
		var model = that.reportings.get(id);
		var center = null;
		
		if (model.has('extent'))
		{
			var e = model.get('extent').split(",");
			var extent = L.latLngBounds([L.latLng(e[1], e[0]),L.latLng(e[3], e[2])]);
			that.setMapBounds(extent);
			center = extent.getCenter();
		}
		
		if (!model.get('popup'))
			that.initPopup(id);
		if (center)
			model.get('popup').setLatLng(center);
		if (!model.get('popup').getContent())
		{
			var domPopup = that.popup.clone();
			//domPopup.find(".media-object").attr('src');
			domPopup.find(".media-heading").text(model.get(that.reportingsTitle));
			domPopup.find(".popupDetailsBtn").click(function(){
				that.openReportingDetail(id);
			});
			model.get('popup').setContent(domPopup[0]);
		}
		
		model.get('popup').openOn(that.map);
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
		that.getReportings(function(){
			that.setMapBounds();
		});
	},
});