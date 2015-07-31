$.extend(APP.interactiveMap, 
{
	leafletHash: null,
	bCurrentLayers: false, //visualizza la lista degli elementi in base ai layers visibili sulla mappa
	bQrCode: false,
	bEverytypeGeometries: true,
	arrEverytypeGeometries: ["poi","path","area"],
	frontPrefix: "front_",
	previousSection: null,
	currentSection: null,
	currentItinerary: null,
	selectedElement: { identifier: null, section: null},
	myData: {},
	body: null,
	navbars: {
		top: null,
		bottom: null,
	},
	infoDelay: 1000,
	searchModal: null,
	registrationModal: null,
	loginMsgModal: null,
	loginModal: null,
	itemsOnSidebar: true,
	mySidebar: {
		control: null,
		div: null,
	},
	icons: {
		'altitude_gap': '/public/img/dislivello.png',
		'length': '/public/img/lunghezza.png',
		'time': '/public/img/durata.png',
	},
	bouncingMarkers: false,
	searchUrl: '/jx/search?tofind=',
	pages: {},
	eventObj: $(document),
	leafletSaveMapPluginDir: '/public/modules/leaflet-save-map/',
	
	insertRowAlphabetically: function(container, row, selector, offset)
	{
		var that = this;
		offset = (offset)? offset : 0;
		var cs = container.children();
		if (offset === cs.length)
		{
			container.append(row);
			return false;
		}
		var node = $(cs[offset]);
		var nodeTitle = node.find(selector).text();
		var rowTitle = row.find(selector).text();
		if (nodeTitle > rowTitle)
			row.insertBefore(node);
		else
			that.insertRowAlphabetically(container, row, selector, offset+1);
	},
	
	insertMediaImage: function(src, media)
	{
		var that = this;
		if (!APP.utils.isset(src) && !that.bDefaultImage)
			return;
		var img = $('<img class="media-object img-responsive img-rounded" src="'+(APP.utils.isset(src)? src : APP.config.localConfig.default_overview_image)+'" alt="" style="max-width: 60px; max-height: 60px">')
		media.find("a:first").append(img);
	},
	
	getSectionFromLayerType: function(type)
	{
		switch(type)
		{
			case "marker":
				return "highlitingpoi";
			case "polyline":
				return "highlitingpath";
			case "polygon":
				return "highlitingarea";
			default:
				return null;
		}
	},
	
	getObjectTitle: function(section, id)
	{
		var that = this;
		return (section === "itinerary")? that.myData[section][id].data.name : that.myData[section][id].data.title;
	},
	
	getOverviewImage: function(section, id, thumbnail)
	{
		var that = this;
		if (that.myData[section][id].media && that.myData[section][id].media.images && $.isArray(that.myData[section][id].media.images) && that.myData[section][id].media.images.length > 0)
			return (thumbnail)? that.myData[section][id].media.images[0].image_thumb_url : that.myData[section][id].media.images[0].image_url;
		else
			return (thumbnail && APP.config.localConfig.default_overview_thumbnail)? APP.config.localConfig.default_overview_thumbnail : APP.config.localConfig.default_overview_image;
	},
	
	getTypology: function(typologyId)
	{
		var index = APP.utils.getIndexFromField(APP.config.localConfig.typology, "id", typologyId);
		if (index > -1 && APP.utils.isset(APP.config.localConfig.typology[index]))
			return APP.config.localConfig.typology[index];
		return false;
	},
	
	resize: function()
	{
		if (!APP.utils.isset(this.body))
			return;
		var x = this.body.find(".centerImage");
		x.css("width","100%");
		x.centerImage();
	},
	
	openEditModal: function(section, id, layer, onSave, onCancel)
	{
		var that = this;
		
		var dataObj = null;
		if (layer && $.isFunction(layer.toGeoJSON))
			dataObj = {"the_geom": layer.toGeoJSON()};
		
		var body = APP.anagrafica.createFormTemplate(id, dataObj, APP.anagrafica.sections[that.frontPrefix+section], that.frontPrefix+section, []);
		
		var footerDiv = $(	'<div>\
								<button type="button" class="btn btn-success"><i class="icon icon-ok"></i> '+APP.i18n.translate('save')+'</button>\
								<button type="button" class="btn btn-default"><i class="icon icon-remove"></i> '+APP.i18n.translate('cancel')+'</button>\
							</div>');
		
		footerDiv.find(".btn-success").click(function()
		{
			var tg = body.find("#APP-the_geom");
			if (tg.length === 0)
			{
				tg = $('<input id="APP-the_geom" name="the_geom" type="hidden">');
				body.append(tg);
			}
			
			var template = {
				"type":"FeatureCollection",
				"features":[]
			};
			
			template.features.push(layer.toGeoJSON());
			tg.val(JSON.stringify(template));
			
			APP.anagrafica.formSubmit(id, that.frontPrefix+section, function(){
				myModal.modal("hide");
				if (APP.utils.isset(onSave) && $.isFunction(onSave))
					onSave();
			}, true);
		});
		footerDiv.find(".btn-default").click(function(){
			myModal.modal("hide");
			if (myModal.find(".mapboxDiv").length > 0)
				APP.map.reverseMap();
			var map = APP.map.getCurrentMap();
			map.removeLayer(layer);
			
			if (APP.utils.isset(onCancel) && $.isFunction(onCancel))
				onCancel();
		});
		
		var modalId = "newGeometry";
		
		var myModal = APP.modals.create({
			container: that.body,
			id: modalId,
			size: "lg",
			keyboard: 'false',
			backdrop: "static",
			bTopCloseButton: false,
			header: APP.utils.capitalize(APP.i18n.translate("New report")),
			body: body,
			footer: footerDiv,
			onShown: function(){
				var f = that.body.find("#"+modalId+" form");
				APP.utils.setLookForm(f, null);
			},
			onHidden: function(){ 
				
			}
		});
		
		if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
			that.mySidebar.control.hide();
		
		myModal.modal("show");
	},
	
	addMarker: function(latlng)
	{
		var that = this;
		var map = APP.map.getCurrentMap();
		var marker = L.marker(latlng);
		marker.addTo(map);
		
		setTimeout(function(){
			that.openEditModal("highlitingpoi", null, marker);
		},250);
	},
	
	toggleDrawEditor: function(bDraw, geometries)
	{
		var that = this;
		
		if (!APP.utils.isset(APP.map.globalData[APP.map.currentMapId].drawnItems))
			APP.map.globalData[APP.map.currentMapId].drawnItems = new L.FeatureGroup();
		if (!APP.map.globalData[APP.map.currentMapId].map.hasLayer(APP.map.globalData[APP.map.currentMapId].drawnItems))
			APP.map.globalData[APP.map.currentMapId].map.addLayer(APP.map.globalData[APP.map.currentMapId].drawnItems);
		var options = {
			position: 'topleft',
			draw: {
				polyline: false,
				polygon: false,
				rectangle: false,
				circle: false,
				marker: false
			},
			edit: {
				featureGroup: APP.map.globalData[APP.map.currentMapId].drawnItems,
				edit: false,
				remove: false
			}
		};
		if (!APP.utils.isset(geometries) || !$.isArray(geometries) || geometries.length===0)
		{
			geometries = ['marker'];
			/*
			if (APP.config.checkLoggedUser())
			{
				geometries.push('polyline');
				geometries.push('polygon');
			}
			*/
		}				
		$.each(geometries, function(i, v){
			delete options.draw[v];
		});				
		APP.map.toggleDrawEditor(APP.map.currentMapId, bDraw, options);
		if (bDraw)
		{
			APP.utils.showNoty({title: APP.i18n.translate("Information"), type: "information", content: APP.i18n.translate("use_left_buttons_to_draw_geometries"), timeout: 2000});
			APP.map.globalData[APP.map.currentMapId].map.on('draw:drawstart', function (e)
			{
				//alert(e.layerType);
			});
			
			APP.map.globalData[APP.map.currentMapId].map.on('draw:created', function (e)
			{
				APP.map.globalData[APP.map.currentMapId].map.addLayer(e.layer);
				
				var key = that.getSectionFromLayerType(e.layerType);
				that.openEditModal(key, null, e.layer);
			});
			
			/*
			APP.map.globalData[APP.map.currentMapId].map.on('draw:edited', function (e) {
				var layers = e.layers;
				layers.eachLayer(function (layer) {
					//do whatever you want, most likely save back to db
				});
			});
			*/
		}
		else
		{
			APP.map.globalData[APP.map.currentMapId].map.off('draw:drawstart');
			APP.map.globalData[APP.map.currentMapId].map.off('draw:created');
			//APP.map.globalData[APP.map.currentMapId].map.off('draw:edited');
		}
	},
	
	toggleGeometry: function(b, bAskLogin)
	{
		var that = this;
		
		if (b && bAskLogin)
		{
			if (!APP.utils.isset(APP.config.localConfig.authuser))
			{
				if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
					that.mySidebar.control.hide();
				that.loginMsgModal.modal("show");
				return false;
			}			
		}
		
		that.bAddGeometry = APP.utils.isset(b)? b : !that.bAddGeometry;
		
		if (APP.config.isMobileVersion())
		{
			if (that.currentNoty)
				that.currentNoty.close();
			if (that.bAddGeometry)
				that.currentNoty = APP.utils.showNoty({content: '<p>'+APP.i18n.translate("Click to map to place marker")+'.</p>', title: APP.i18n.translate("Information"), type: "information", timeout: false});
			else
				that.currentNoty = null;
		}
		else
		{
			that.toggleDrawEditor(that.bAddGeometry);
		}		
		if (that.bAddGeometry)
		{
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
				that.mySidebar.control.hide();
			that.navbars.top.find("#addGeometriesButton").parent().addClass("active");
		}
		else
			that.navbars.top.find("#addGeometriesButton").parent().removeClass("active");
	},
	
	checkVideoUrl: function(url)
	{
		var that = this;
		
		var arr = url.split("/");
		var id = arr[arr.length-1];
		id = id.split('?')[0];
		
		if (url.indexOf("vimeo") >= 0)
			return {
				href: 'https://vimeo.com/'+id,
				type: 'text/html',
				vimeo: id,
				//poster: 'https://secure-b.vimeocdn.com/ts/'+id+'.jpg'
			};
		if (url.indexOf("youtube") >= 0)
			return {
				href: 'https://www.youtube.com/watch?v='+id,
				type: 'text/html',
				youtube: id,
				poster: 'https://img.youtube.com/vi/'+id+'/maxresdefault.jpg',
			};
		return false;
	},
	
	highlightLayer: function(section, id)
	{
		if (section !== "itinerary")
		{
			if (APP.map.globalData.mainContent.addedLayers[section+"_"+id].layer)
				APP.map.highlightLayer(section+"_"+id);
			this.selectedElement.identifier = id;
			this.selectedElement.section = section;
		}
	},
	
	resetHighlightLayer: function()
	{
		this.unselectItem();
		APP.map.highlightLayer(null, null);
	},
	
	onItineraryClick: function(o)
	{
		var that = this;
		
		var element = o.element;
		var section = o.section;
		var id = o.id;
		
		if (!that.checkIfMyDataExists(section, id))
			return;
		
		if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.div)
		{
			var it = that.mySidebar.div.find("#item_"+section+"_"+id);
			if (it.length>0)
				it.addClass("active");
		}
		
		that.currentItinerary = id;
		APP.map.hideAllLayers();
		
		$.each(that.myData[section][id].data.areas,function(j,k){
			//APP.map.addLayer(that.myData.path[v.data.id].geo.geoJSON);
			that.sendGeojsonLayerToMap(that.myData.area[k].geo, "area");
		});
		$.each(that.myData[section][id].data.paths,function(j,k){
			//APP.map.addLayer(that.myData.path[v.data.id].geo.geoJSON);
			that.sendGeojsonLayerToMap(that.myData.path[k].geo, "path");
		});
		$.each(that.myData[section][id].data.pois,function(j,k){
			//APP.map.addLayer(that.myData.poi[v.data.id].geo.geoJSON);
			that.sendGeojsonLayerToMap(that.myData.poi[k].geo, "poi");
		});
		
		that.showBottomBar(section, id, function()
		{
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
			{
				that.mySidebar.div.find(".active").removeClass("active");
				that.mySidebar.control.hide();
			}
			else
			{
				that.body.find("#modal-"+section).find(".active").removeClass("active");
				that.body.find("#modal-"+section).modal('hide');
			}
			
			that.showItems(that.currentSection, ['poi', 'path', 'area']);
		});
		
		APP.utils.showNoty({content: '<p>'+APP.i18n.translate("You are currently viewing the elements of the following itinerary")+': <strong class="text-danger">'+that.getObjectTitle(section, id)+'</strong>.<br>'+APP.i18n.translate('To view all the elements again, exit from itinerary')+'.</p>', title: APP.i18n.translate("Information"), type: "alert", timeout: 6000});
		that.zoomAt(section, id);
	},
	
	closeItinerary: function(onCloseCallback)
	{
		var that = this;
		that.currentItinerary = null;
		that.selectedElement = {identifier: null, section: null};
		that.hideBottomBar();
		APP.map.showAllLayers();
		that.resetHighlightLayer();
		APP.map.setExtent(APP.map.globalData[APP.map.currentMapId].globalExtent);
		if (APP.utils.isset(onCloseCallback) && $.isFunction(onCloseCallback))
			onCloseCallback();
	},
	
	bindPopup: function(section, id, latlng)
	{
		var that = this;
		
		var element = APP.map.getLayer(section+"_"+id);
		if (element.unbindPopup && $.isFunction(element.unbindPopup))
			element.unbindPopup();
			
		var po = {
			//closeButton: false,
			closeOnClick: true,
			maxWidth: (that.body.width() > 320)? 300 : that.body.width()-(that.body.width()*0.3),
			//maxHeight: that.body.height()-((that.body.height()*20/)100),
			minWidth: (that.body.width() > 320)? 280 : (that.body.width()-(that.body.width()*0.4)),
			autoPan: false,
		};
		if (section == "poi")
			po.offset = L.point(0, -25);
		
		var media = '<div class="media">\
						<a class="pull-left" href="#">\
							<img class="media-object thumbnail" style="width: 75px" src="'+that.getOverviewImage(section, id, true)+'" alt="">\
						</a>\
						<div class="media-body">\
							<h4 class="media-heading hidden-xs hidden-sm">'+that.getObjectTitle(section, id)+'</h4>\
							<h5 class="media-heading hidden-md hidden-lg">'+that.getObjectTitle(section, id)+'</h5>\
							<div>\
								<button type="button" class="btn btn-default btn-sm popupDetailsBtn" style="margin-top: 10px"><i class="icon icon-search"></i> '+APP.i18n.translate('View data sheet')+'</button>\
							</div>\
						</div>\
					</div>';
					
		element.bindPopup(media, po);
		
		element.off('popupopen').on('popupopen', function(a){
			var myBtn = $(a.popup._container).find(".popupDetailsBtn");
			var closeBtn = $(a.popup._container).find(".leaflet-popup-close-button");
			closeBtn.off("click").click(function(){
				that.resetHighlightLayer();
			});			
			myBtn.off("click").click(function(){
				that.showInformation(section, id);
			});
		});
		
		element.off('popupclose').on('popupclose', function(a){
			if (a.layer)
				a.layer.unbindPopup();
			else
				if (a.target)
					a.target.unbindPopup();
		});
		
		if (section === 'area' && !APP.utils.isset(latlng))
			latlng = L.latLng(that.myData[section][id].geo.centroids[0].coordinates[1], that.myData[section][id].geo.centroids[0].coordinates[0]);
		element.openPopup(latlng);
	},
	
	unselectItem: function(element)
	{
		var that = this;
		var lg = null;
		if (element)
			lg = element.parents(".accordion-list:first");
		else
		{
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
			{
				that.mySidebar.div.find(".list-group-item.active").removeClass("active");
			}
			else
			{
				//that.mySidebar.div.
			}
		}
		if (lg)
			lg.find("a.active").removeClass("active");
		
		if (that.selectedElement.section == "poi")
		{
			var obj = that.myData.poi[that.selectedElement.identifier];
			var id = that.selectedElement.section+"_"+that.selectedElement.identifier;
			var scale = that.getScale(APP.map.globalData[APP.map.currentMapId].map);
			if (APP.utils.isset(obj.geo.max_scale) && scale > obj.geo.max_scale)
				APP.map.hideLayer(id);
		}
			
		that.selectedElement = { identifier: null, section: null};
	},
	
	checkIfMyDataExists: function(section, id)
	{
		var that = this;
		var r = (APP.utils.isset(that.myData[section]) && APP.utils.isset(that.myData[section][id]));
		
		if (r)
			return true;
		else
		{
			APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("not_found")+": "+section+"/"+id});
			return false;
		}
	},
	
	onElementClick: function(o)
	{
		var that = this;
		
		var element = o.element;
		var section = o.section;
		var id = o.id;
		var latlng = o.latlng;
		
		if (!that.checkIfMyDataExists(section, id))
			return;
		
		if (element && element.hasClass && element.hasClass("list-group-item"))
		{
			that.unselectItem(element);
			element.addClass("active");
		}
		
		if (!APP.utils.isset(that.currentItinerary))
		{
			that.highlightLayer(section, id);
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.div)
			{
				that.mySidebar.div.find(".list-group-item.active").removeClass("active");
				var it = that.mySidebar.div.find("#item_"+section+"_"+id);
				if (it.length>0)
					it.addClass("active");
			}
		}
		
		var afterHidden = function()
		{
			APP.map.showLayer(section+"_"+id);
			that.zoomAt(section, id);
			if (section === "itinerary")
				return false;
			
			that.bindPopup(section, id, latlng);
		};
		
		if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control && that.navbars.top.parents(".navbar").find(".navbar-toggle").is(":visible"))
		{
			that.mySidebar.control.hide();
			setTimeout(function(){ afterHidden(); }, 600);
		}
		else
			afterHidden();
	},
	
	showInformation: function(section, id, onCloseCallback)
	{
		var that = this;
		
		if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
			APP.map.sidebar.control.hide();
			
		that.openInfo(section, id, onCloseCallback);
	},
	
	showBottomBar: function(section, id, onCloseCallback)
	{
		var that = this;
		
		var myTitle = that.getObjectTitle(section, id);
		
		that.navbars.bottom = that.body.find(".navbar-fixed-bottom");
		if (that.navbars.bottom.length > 0)
		{
			that.body.find("#mainContent").height(that.body.find("#mainContent").height()+that.navbars.bottom.height());
			that.navbars.bottom.remove();
		}
		
		that.navbars.bottom = $('<nav class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">\
									<div class="container-fluid">\
										<div class="navbar-header">\
											<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bottomNavbarCollapse">\
												<span class="sr-only">Toggle navigation</span>\
												<span class="icon-bar"></span>\
												<span class="icon-bar"></span>\
												<span class="icon-bar"></span>\
											</button>\
											<a class="navbar-brand" href="#" style="display:none; color:white">\
												<span class="hidden-sm hidden-xs">'+APP.i18n.translate(APP.utils.capitalize(section))+': </span> '+myTitle+'\
											</a>\
										</div>\
										<div id="bottomNavbarCollapse" class="navbar-collapse collapse">\
											<ul class="nav navbar-nav navbar-right">\
												<li><a href="#" class="detailsButton"><i class="icon icon-search"></i> '+APP.i18n.translate("Data sheet")+'</a></li>\
												<li><a href="#" class="closeButton"><i class="icon icon-remove"></i> '+APP.i18n.translate("Exit from itinerary")+'</a></li>\
											</ul>\
										</div>\
									</div>\
								</nav>');
		
		/*<li><a href="#"><button type="button" class="btn btn-success detailsButton"><i class="icon icon-search"></i> '+APP.i18n.translate("Data sheet")+'</button></a></li>\
		<li><a href="#"><button type="button" class="btn btn-default closeButton"><i class="icon icon-remove"></i> '+APP.i18n.translate("close")+'</button></a></li>\*/
					
		that.navbars.bottom.find(".detailsButton").click(function(){
			if (that.navbars.bottom.find(".navbar-collapse").hasClass("in"))
				that.navbars.bottom.find('.navbar-collapse').collapse('hide');
			that.showInformation(section, id, function()
			{
				
			});
		});
		
		that.navbars.bottom.find(".closeButton").click(function(){
			that.closeItinerary(onCloseCallback);
		});
		
		that.body.append(that.navbars.bottom);
		var h = that.body.find("#mainContent").height();
		that.body.find("#mainContent").height(h-that.navbars.bottom.height());
		
		that.navbars.bottom.find(".navbar-brand").click(function(){
			that.body.find("#bottomNavbarCollapse").collapse("toggle");
		}).fadeIn(1500);
	},
	
	hideBottomBar: function()
	{
		var that = this;
		
		var botNav = that.body.find(".navbar-fixed-bottom");
		if (botNav.length > 0)
		{
			var h = that.body.find("#mainContent").height();
			that.body.find("#mainContent").height(h+botNav.height());
			botNav.remove();
		}
		that.navbars.bottom = null;
	},
	
	openInfo: function(section, id, onCloseCallback)
	{
		var that = this;
		var myModal = that.body.find("#modal-"+section+"-info");
		if (myModal.length > 0)
			myModal.remove();
		
		var myTitle = that.getObjectTitle(section, id);
		
		myModal = $('<div id="modal-'+section+'-info" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="'+section+'" aria-hidden="true">\
						<div class="modal-dialog modal-lg">\
							<div class="modal-content">\
							  <div class="modal-header">\
								<button type="button" class="btn-lg close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>\
								<h3 class="lead">'+myTitle+'</h3>\
							  </div>\
							  <div class="modal-body">\
								<div class="gallery" style="margin: -15px -15px 0px -15px">\
									<div class="overviewImage" style="width: 100%; height: 300px"></div>\
									<div class="row thumbnailsRow" style="padding: 20px; vertical-align: middle"></div>\
								</div>\
								<div class="row">\
									<div class="col-md-4">\
										<div class="panel panel-default categories" style="display: none">\
											<div class="panel-heading">\
												<h3 class="panel-title">'+APP.i18n.translate('categories')+'</h3>\
											</div>\
											<div class="panel-body">\
											</div>\
										</div>\
									</div>\
									<div class="col-md-8">\
										<div class="panel panel-default features" style="display: none">\
											<div class="panel-heading">\
												<h3 class="panel-title">'+APP.i18n.translate('features')+'</h3>\
											</div>\
											<div class="panel-body">\
											</div>\
										</div>\
									</div>\
								</div>\
								<div class="paragraphes text-justify"></div>\
							  </div>\
							  <div class="modal-footer">\
								<button type="button" data-dismiss="modal" class="btn btn-primary">'+APP.i18n.translate('close')+'</button>\
							  </div>\
							</div>\
						</div>\
					</div>');
				
		if (!APP.utils.isset(that.myData[section][id].media) || !APP.utils.isset(that.myData[section][id].media.images) || !$.isArray(that.myData[section][id].media.images) || that.myData[section][id].media.images.length === 0)
		{
			var img = $('<img alt="" class="img-responsive centerImage" style="width: 100%; height:100%;">');
			img.attr('src', APP.config.localConfig.default_overview_image);			
			myModal.find(".overviewImage").append(img);			
			/*var thumbnail = $('<div class="col-xs-4 col-md-2">\
								<a href="#" class="thumbnail">\
								  <img src="'+APP.config.localConfig.default_overview_image+'" alt="">\
								</a>\
							  </div>');
			myModal.find(".thumbnailsRow").append(thumbnail)*/
		}
		else
		{
			var imageGalleryId = 'big_'+section+'_'+id;
			
			$.each(that.myData[section][id].media.images, function(i,v)
			{
				if (i === 0)
				{
					var img = $('<img alt="" data-toggle="tooltip" class="img-responsive centerImage" style="width: 100%; height:100%">');
					img.attr('src', v.image_url);
					img.tooltip({container: '#modal-'+section+'-info', placement: 'top', title: $(v.description).text()});
					myModal.find(".overviewImage").append(img);
				}
				var thumbnail = $('<div class="col-xs-4 col-md-2">\
									<a href="'+v.image_url+'" title="'+$(v.description).text()+'" class="" data-gallery="#'+imageGalleryId+'">\
									  <img src="'+v.image_thumb_url+'" alt="'+v.description+'" class="img-thumbnail">\
									</a>\
								  </div>');
				
				thumbnail.find('img').tooltip({container: '#modal-'+section+'-info', placement: 'auto', title: $(v.description).text()});
				
				myModal.find(".thumbnailsRow").append(thumbnail)
			});
			
			that.setBlueimpGalleryDiv({
				container: that.body,
				id: imageGalleryId,
				classes: 'blueimp-gallery blueimp-gallery-controls',
				closeBtn: true,
			});
		}
		
		var videosContainer = null;
		var videos = [];
		var parToAppend = [];
		var overviewToAppend = {};
		var checkVoice = function(voice, type, moreParams)
		{
			var div = $('<div class="'+voice+'"><h3>'+APP.i18n.translate(APP.utils.capitalize(voice))+'</h3></div>');
			var bInsert = true;
			switch(type)
			{
				case "text":
					if (APP.utils.isset(that.myData[section][id].data[voice]) && !APP.utils.isEmptyString(that.myData[section][id].data[voice]))
						div.append(that.myData[section][id].data[voice]);
					else
						bInsert = false;
					break;
				case "ov-img":
					if (APP.utils.isset(that.myData[section][id].data[voice]) && APP.utils.isset(moreParams) && $.isArray(moreParams.values))
					{
						if (!APP.utils.isset(overviewToAppend[moreParams.voiceResult]))
							overviewToAppend[moreParams.voiceResult] = [];
						var arr = [];
						if (!$.isArray(that.myData[section][id].data[voice]))
							arr[0] = that.myData[section][id].data[voice];
						else
							arr = that.myData[section][id].data[voice];
							
						$.each(arr, function(i, v)
						{
							var ii = APP.utils.getIndexFromField(moreParams.values, "id", v);
							if (ii > -1)
							{
								var img  = $('<div class="media">\
												<a class="pull-left" href="#">\
													<img src="'+moreParams.values[ii][moreParams.icon]+'" class="img-responsive" style="margin-top: -3px; max-width: 32px" alt="'+moreParams.values[ii][moreParams.label]+'">\
												</a>\
												<div class="media-body">\
													<h5 class="media-heading"><span class="label label-'+((voice === "typology_id")? 'danger' : 'default')+'">'+moreParams.values[ii][moreParams.label]+'</span></h5>\
												</div>\
											</div>');
								overviewToAppend[moreParams.voiceResult].push(img);
							}
						});
					}
					return;
				case "ov-icon":
					if (APP.utils.isset(that.myData[section][id].data[voice]) && !APP.utils.isEmptyString(that.myData[section][id].data[voice]))
					{
						if (!APP.utils.isset(overviewToAppend[moreParams.voiceResult]))
							overviewToAppend[moreParams.voiceResult] = [];
						
						var span = $('<p><i class="icon icon-'+moreParams.icon+'"></i> <b>'+APP.i18n.translate(voice)+'</b>: '+that.myData[section][id].data[voice]+'</p>');
						overviewToAppend[moreParams.voiceResult].push(span);
					}
					return;
				case "ov-icage": //mixed
					if (APP.utils.isset(that.myData[section][id].data[voice]) && !APP.utils.isEmptyString(that.myData[section][id].data[voice]))
					{
						if (!APP.utils.isset(overviewToAppend[moreParams.voiceResult]))
							overviewToAppend[moreParams.voiceResult] = [];
						
						var span = $('<p><b>'+APP.i18n.translate(voice)+'</b>: '+that.myData[section][id].data[voice]+'</p>');
						if (moreParams.image && moreParams.image !== "")
							span.prepend('<img class="pull-left" src="'+moreParams.image+'" style="margin-right: 5px">');
						overviewToAppend[moreParams.voiceResult].push(span);
					}
					return;
				case "ov-descriptionWithInlineImages":
					if (APP.utils.isset(that.myData[section][id].data[voice]) && APP.utils.isset(moreParams) && $.isArray(moreParams.values))
					{
						if (!APP.utils.isset(overviewToAppend[moreParams.voiceResult]))
							overviewToAppend[moreParams.voiceResult] = [];
						var arr = [];
						if (!$.isArray(that.myData[section][id].data[voice]))
							arr[0] = that.myData[section][id].data[voice];
						else
							arr = that.myData[section][id].data[voice];
						
						var span = $('<p class="pull-left"><b>'+APP.i18n.translate(moreParams.description)+'</b>: </p>');
							
						$.each(arr, function(i, v)
						{
							var ii = APP.utils.getIndexFromField(moreParams.values, "id", v);
							if (ii > -1)
							{
								var img  = $('<img src="'+moreParams.values[ii][moreParams.icon]+'" class="img-responsive pull-right" style="margin-right: 3px; max-width: 30px" >');
								img.tooltip({title: moreParams.values[ii][moreParams.label]});
								span.append(img);
							}
						});
						overviewToAppend[moreParams.voiceResult].push(span);
					}
					return;
				case "paths": case "pois": case "areas":
					if (that.myData[section][id].data[voice].length === 0)
						bInsert = false;
					else
					{
						var s = null;
						switch(type)
						{
							case "paths": 
								s = "path";
								break;
							case "pois":
								s = "poi";
								break;
							case "areas":
								s = "area";
								break;
						}
						
						var ul = $('<ul class="media-list"></ul>');
						
						$.each(that.myData[section][id].data[voice], function(i,v)
						{
							var btn = $('<a href="#" class="btn-link">'+that.getObjectTitle(s, v)+'</a>');
							btn.data("id", v).click(function(){
								var myId = $(this).data("id");
								myModal.modal("hide");
								that.onElementClick({ element: $(this), section: s, id: myId, latlng: null});
							});
													
							var typology = that.getTypology(that.myData[s][v].data.typology_id);
							
							var li = $(	'<li class="media">\
											<a class="pull-left" href="#">\
											</a>\
											<div class="media-body">\
												<h5 class="media-heading"></h5>\
											</div>\
										</li>');
							
							li.find(".media-heading").append(btn);
							
							if (typology)
							{
								var iimmgg = $('<img class="media-object" src="'+typology.icon+'" alt="'+typology.name+'">');
								iimmgg.tooltip({title: APP.i18n.translate(typology.name)});
								li.find("a:first").append(iimmgg);
							}
								
							ul.append(li);
						});
						
						div.append(ul);
					}
					break;
				case "c3chart":
					var c3c = $('<div id="APP-'+voice+'" name="'+voice+'" class="c3chart" data-chartType="'+moreParams.chartType+'"></div>');
					div.append(c3c);
					
					$.ajax({
						type: 'GET',
						url: APP.config.localConfig.urls[voice]+id,
						dataType: 'json',
						success: function(data)
						{
							var result = data.data;
							
							var ax = {};
							ax['data-'+id] = 'y';
														
							var chart = c3.generate({
								bindto: "#"+c3c.attr("id"),
								size: {
								  width: 858,
								  height: 320
								},
							    data: {
							    	x: 'x',
							        columns: result,
							        type: "area",
							        axes: ax
							    },
							    zoom: {
							        enabled: true
							    },
							    legend: {
							        show: false
							    },
							    axis: {
							    	x: {
							            label: {
							                text: 'Distanza (m)',
							                position: 'outer-center'
							            },
							            tick: {
							                format: function (x) { return Number((x).toFixed(1)); },
							                count: 9,
							            }
							        },
							        y: {
							            label: {
							                text: 'Altitudine (m)',
							                position: 'outer-middle'
							            }
							        },
							    },
							    point: {
							        show: false
							    }
							});
						}
					});
					
					break;						
				case "url":
					if (APP.utils.isset(that.myData[section][id].data[voice]) && !APP.utils.isEmptyString(that.myData[section][id].data[voice]))
					{
						var dl = $('<ul></ul>');
						$.each(that.myData[section][id].data[voice], function(ii,vv)
						{
							var li = $("<li></li>");
							var myUrl = (vv.url.indexOf('http://') === -1)? "http://"+vv.url : vv.url;
							li.append('<a class="btn-link" href="'+myUrl+'" target="_blank">'+vv.alias+'</a>');
							li.append('<span style="margin-left: 10px"><small><i>'+vv.description_url+'</i></small></span>');
							dl.append(li);							
						});
						div.append(dl);
					}
					else
						bInsert = false;
					break;
				case "video":
					if (APP.utils.isset(that.myData[section][id].media) && APP.utils.isset(that.myData[section][id].media.videos) && $.isArray(that.myData[section][id].media.videos) && (that.myData[section][id].media.videos.length > 0))
					{
						var galleryId = 'bvg_'+section+'_'+id;
						
						that.setBlueimpGalleryDiv({
							container: div,
							id: galleryId,
							classes: 'blueimp-gallery blueimp-gallery-controls blueimp-gallery-carousel',
							closeBtn: false,
						});
						
						videosContainer = div.find('#'+galleryId);
						
						$.each(that.myData[section][id].media.videos, function(i, v)
						{
							var vo = that.checkVideoUrl($(v.video_embed).attr("src"));
							if (vo)
							{
								vo.title = v.title;
								videos.push(vo);
							}
						});						
					}
					else
						bInsert = false;
					break;
				default:
					bInsert = false;
					break;
			}
			if (bInsert)
				parToAppend.push(div);
		};
		
		switch(section)
		{
			case "poi":
				checkVoice('typology_id', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('typologies', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('description', 'text');
				checkVoice('reason', 'text');
				checkVoice('period_schedule', 'text');
				checkVoice('accessibility', 'text');
				checkVoice('urls', 'url');
				checkVoice('video_poi', 'video');
				break;
			case "path":
				checkVoice('typology_id', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('typologies', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('description', 'text');
				checkVoice('length', 'ov-icage', {image: that.icons['length'], voiceResult: "features"});
				checkVoice('altitude_gap', 'ov-icage', {image: that.icons.altitude_gap, voiceResult: "features"});
				checkVoice('heightsprofilepath', 'c3chart', {chartType: 'line'});
				checkVoice('modes', 'ov-descriptionWithInlineImages', {values: APP.config.localConfig.path_mode, label: 'mode', icon: "icon", voiceResult: "features", description: APP.i18n.translate('transportation_types')});
				checkVoice('reason', 'text');
				checkVoice('period_schedule', 'text');
				checkVoice('accessibility', 'text');
				checkVoice('urls', 'url');
				checkVoice('video_path', 'video');
				break;
			case "area":
				checkVoice('typology_id', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('typologies', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('description', 'text');
				checkVoice('plus_information', 'text');
				checkVoice('inquiry', 'text');
				checkVoice('urls', 'url');
				checkVoice('video_area', 'video');
				break;
			case "itinerary":
				checkVoice('typology_id', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('typologies', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('description', 'text');
				checkVoice('areas', 'areas');
				checkVoice('paths', 'paths');
				checkVoice('pois', 'pois');
				break;
			default:
				break;
		}
		if (parToAppend.length>0)
		{
			$.each(parToAppend, function(){
				myModal.find(".modal-body .paragraphes").append(this);
			});
		}
		
		$.each(overviewToAppend, function(key, value){
			var l = value.length;
			if (l === 0)
				return true;
			var row = $('<div></div>');
			$.each(value, function(k1,v1){
				row.append(v1);
			});
			myModal.find(".modal-body ."+key+" .panel-body").append(row);
			if (!myModal.find(".modal-body ."+key+" .panel-body").is(':empty'))
				myModal.find(".modal-body ."+key).show();
		});
		
		myModal.on('shown.bs.modal', function(){
			myModal.find(".centerImage").centerImage();
			
			blueimp.Gallery(videos, {
				container: videosContainer,
				carousel: false,
			});			
		});
		
		myModal.on('hidden.bs.modal', function()
		{ 
			if (APP.utils.isset(onCloseCallback) && $.isFunction(onCloseCallback))
				onCloseCallback(); 
		});
		
		that.body.append(myModal);
		myModal.modal();
	},
	
	zoomAt: function(section, id)
	{
		var that = this;		
		APP.map.globalData[APP.map.currentMapId].globalExtent = {};
		
		switch(section)
		{
			case "poi":
				var maxZoom = APP.map.globalData[APP.map.currentMapId].map.getMaxZoom();
				var currentZoom = APP.map.globalData[APP.map.currentMapId].map.getZoom();
				var latLng = L.latLng(that.myData[section][id].geo.geoJSON.coordinates[1], that.myData[section][id].geo.geoJSON.coordinates[0]);
				nextZoom = (currentZoom >= maxZoom-3)? currentZoom : maxZoom-3;
				APP.map.globalData[APP.map.currentMapId].map.setView(latLng, nextZoom, {animate: true});
				/*APP.map.globalData[APP.map.currentMapId].map.panTo(latLng, {animate: true});
				APP.map.globalData[APP.map.currentMapId].map.setZoom(nextZoom, {animate: true});*/
				return;
			case "path": case "area":
				APP.map.setGlobalExtent(that.myData[section][id].geo.extent);
				break;
			case "itinerary":
				$.each(that.myData[section][id].data.paths, function(i,v){
					APP.map.setGlobalExtent(that.myData["path"][v].geo.extent);
				});
				$.each(that.myData[section][id].data.pois, function(i,v){
					APP.map.setGlobalExtent(that.myData["poi"][v].geo.extent);
				});
				break;
			default:
				break;
		}
		APP.map.setExtent(APP.map.globalData[APP.map.currentMapId].globalExtent);
	},
	
	closeItems: function(section, callback)
	{
		var that = this;
		if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
			that.mySidebar.control.hide();
		else
			that.body.find("#modal-"+section).modal("hide");
	},
		
	showItems: function(section, elements, callback)
	{
		var that = this;
		if (that.previousSection === that.currentSection)
		{
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
				that.mySidebar.control.toggle();
			else
				that.body.find("#modal-"+section).modal("toggle");
		}
		else
		{
			if (that.itemsOnSidebar && L.control.sidebar)
				that.showItemsOnSidebar(section, elements, callback);
			else
				that.showItemsOnModal(section, elements, callback);
		}
	},
	
	showItemsOnSidebar: function(section, elements, callback)
	{
		var that = this;
		
		that.mySidebar.div.empty();
		
		that.mySidebar.control.on('shown', function (e) {
			that.navbars.top.find("#"+that.currentSection+"Button").parent().addClass("active");
		});
		that.mySidebar.control.show();
		
		that.mySidebar.control.on('hidden', function (e) {
			APP.config.removeActiveClasses(that.navbars.top, "li");
		});
		
		switch(section)
		{
			case "info":
				that.getPage(section, true);
				if (APP.utils.isset(callback) && $.isFunction(callback))
					callback();
				return;
			case "itinerary":
				var listGroup = $('<div class="list-group list-group-wo-radius" style="margin: 0px -23px 0px -23.5px; padding: -10px"></div>');
				
				$.each(that.myData[section], function(i,v)
				{					
					var media = $(	'<div class="media">\
									  <a class="pull-left" href="#">\
										<img class="media-object img-rounded" src="'+that.getOverviewImage(section, v.data.id, true)+'" alt="'+APP.i18n.translate('no_image')+'" style="max-width: 60px; max-height: 60px">\
									  </a>\
									  <div class="media-body">\
										<h4 class="media-heading">'+this.data.name+'</h4>\
									  </div>\
									</div>');
					
					var a = $('<a id="item_'+section+'_'+v.data.id+'" href="#" class="list-group-item '+((that.currentItinerary === v.data.id)? "active" : "")+'"></a>');
					a.data(v).append(media);
					a.click(function(){
						var lg = $(this).parents(".list-group:first");
						lg.find("a.active").removeClass("active");
						$(this).addClass("active");
						that.mySidebar.control.hide();
						that.onItineraryClick({element: $(this), section: section, id: $(this).data().data.id});
					});
					that.insertRowAlphabetically(listGroup, a, ".media-heading")
				});
				that.mySidebar.div.html(listGroup);
				break;
			default: //case "everytype": case "highlightingsdata": case "poi": case "path": case "area":
				var accordion = $('<div id="accordion-'+section+'" class="accordion-list" style="margin: 0px -23px 0px -23.5px; padding: -10px"></div>');
				
				$.each(APP.config.localConfig.typology, function()
				{
					var header = $('<h3 style="vertical-align: middle; border-radius:0px">\
										<span class="pull-left iconImage" style="margin-right: 5px"></span>\
										'+this.name+'\
										<!--<span class="checkboxSpan pull-right" style="margin-left: 5px"><i class="icon-check"></i></span>-->\
										<span class="badge pull-right">0</span>\
									</h3>');
					
					header.find(".checkboxSpan").click(function(e){
						var checkIcon = $(this).find("i");
						if (checkIcon.hasClass('icon-check'))
						{
							checkIcon.removeClass().addClass('icon-check-empty');
							var items = header.next().find("a.list-group-item");
							$.each(items, function(j,k){
								k = $(k);
								var kId = k.attr("id");
								var kIdSplitted = kId.split("item_")[1];
								APP.map.hideLayer(kIdSplitted);
							});
						}
						else
						{
							checkIcon.removeClass().addClass('icon-check');
							var items = header.next().find("a.list-group-item");
							$.each(items, function(j,k){
								k = $(k);
								var kId = k.attr("id");
								var kIdSplitted = kId.split("item_")[1];
								APP.map.showLayer(kIdSplitted);
							});
						}
						return false;
					});
									
					var content = $('<div id="collapse_'+section+"_"+this.id+'" class="list-group list-group-wo-radius" style="padding: 0px; margin-bottom: 0px; border-radius:0px"></div>');
					
					var iconImage = $('<span class="glyphicon glyphicon-chevron-right"></span>');
					if (APP.utils.isset(this.icon) && this.icon !== "")
						var iconImage = $('<img src="'+this.icon+'" class="img-responsive" alt="" style="margin-top: -5px; max-height: 30px; max-width: 35px;">');
					
					header.find(".iconImage").html(iconImage);
					
					//panel.find('.collapse').collapse({toggle: false});
					
					accordion.append(header).append(content);
				});
				$.each(elements, function(j,k)
				{
					$.each(that.myData[k], function(i, v)
					{
						if (that.currentItinerary && $.inArray(that.currentItinerary, that.myData[k][i].data.itineraries) === -1)
							return true;
						var container = accordion.find("#collapse_"+section+"_"+v.data.typology_id);
						
						var media = $(	'<div class="media">\
										  <a class="pull-left" href="#" >\
											<img class="media-object img-responsive img-rounded" src="'+(APP.utils.isset(v.data.thumb_main_image)? v.data.thumb_main_image : APP.config.localConfig.default_overview_image)+'" alt="'+APP.i18n.translate('no_image')+'" style="width: 60px; height: 60px">\
										  </a>\
										  <div class="media-body">\
											<h6 class="media-heading">'+that.getObjectTitle(k, v.data.id)+'</h6>\
										  </div>\
										</div>');					
						
						var row = $('<a id="item_'+k+'_'+v.data.id+'" href="#" class="list-group-item '+((that.selectedElement.section === section && that.selectedElement.identifier === v.data.id)? "active" : "")+'"></a>');
						row.data(v).click(function(){
							that.onElementClick({ element: $(this), section: k, id: v.data.id, latlng: null});
						});
						row.append(media);
						that.insertRowAlphabetically(container, row, ".media-heading");
						var counter = parseInt(container.prev().find(".badge").text());
						container.prev().find(".badge").text(counter+1);
					});
				});
				
				that.mySidebar.div.html(accordion);
				break;
			/*
			case "poi": case "path": case "area":
				
				var accordion = $('<div id="accordion-'+section+'" class="accordion-list" style="margin: 0px -23px 0px -23.5px; padding: -10px"></div>');
				
				$.each(APP.config.localConfig.typology, function()
				{
					var header = $('<h4 style="vertical-align: middle; border-radius:0px">\
										<span class="pull-left iconImage" style="margin-right: 5px"></span>\
										'+this.name+'\
										<span class="badge pull-right">0</span>\
									</h4>');
									
					var content = $('<div id="collapse_'+section+"_"+this.id+'" class="list-group list-group-wo-radius" style="padding: 0px; margin-bottom: 0px; border-radius:0px"></div>');
					
					var iconImage = $('<span class="glyphicon glyphicon-chevron-right"></span>');
					if (APP.utils.isset(this.icon) && this.icon !== "")
						var iconImage = $('<img src="'+this.icon+'" class="img-responsive" alt="" style="margin-top: -5px; max-height: 30px; max-width: 35px;">');
					
					header.find(".iconImage").html(iconImage);
					
					//panel.find('.collapse').collapse({toggle: false});
					
					accordion.append(header).append(content);
				});				
				$.each(that.myData[section], function(i, v)
				{
					if (that.currentItinerary &&  $.inArray(that.currentItinerary, that.myData[section][i].data.itineraries) === -1)
						return true;
					var container = accordion.find("#collapse_"+section+"_"+v.data.typology_id);
					
					var media = $(	'<div class="media">\
									  <a class="pull-left" href="#" >\
										<img class="media-object img-responsive img-rounded" src="'+(APP.utils.isset(v.data.thumb_main_image)? v.data.thumb_main_image : APP.config.localConfig.default_overview_image)+'" alt="'+APP.i18n.translate('no_image')+'" style="width: 60px; height: 60px">\
									  </a>\
									  <div class="media-body">\
										<h5 class="media-heading">'+v.data.title+'</h5>\
									  </div>\
									</div>');					
					
					var row = $('<a id="item_'+section+'_'+v.data.id+'" href="#" class="list-group-item '+((that.selectedElement.section === section && that.selectedElement.identifier === v.data.id)? "active" : "")+'"></a>');
					row.data(v).click(function(){
						that.onElementClick({ element: $(this), section: section, id: v.data.id, latlng: null});
					});
					row.append(media);
					that.insertRowAlphabetically(container, row, ".media-heading");
					var counter = parseInt(container.prev().find(".badge").text());
					container.prev().find(".badge").text(counter+1);
				});
				that.mySidebar.div.html(accordion);
				
				break;
			*/				
		}
		
		that.mySidebar.div.prepend('<h3>'+APP.i18n.translate(APP.utils.capitalize(section)+" section")+'</h3>');
		
		$.each(that.mySidebar.div.find("#accordion-"+section+" .list-group"), function(){
			if ($(this).children().length === 0)
			{
				$(this).prev().remove();
				$(this).remove();
			}				
		});
		
		that.mySidebar.div.find("#accordion-"+section).accordion({
			heightStyle: "content",
			collapsible: true,
			active: false,
			header: "h3"
		});
		
		if (APP.utils.isset(callback) && $.isFunction(callback))
			callback();
	},
	
	showItemsOnModal: function(section, elements, callback)
	{
		var that = this;
		var myModal = that.body.find("#modal-"+section);
		if (myModal.length > 0)
			myModal.remove();
		
		myModal = $('<div id="modal-'+section+'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="'+section+'" aria-hidden="true">\
						<div class="modal-dialog">\
							<div class="modal-content">\
							  <div class="modal-header">\
								<button type="button" class="btn-lg close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>\
								<h3 class="lead">'+APP.i18n.translate(section+"_list")+'</h3>\
							  </div>\
							  <div class="modal-body">\
							  </div>\
							  <div class="modal-footer">\
								<button type="button" data-dismiss="modal" class="btn btn-primary">'+APP.i18n.translate('close')+'</button>\
							  </div>\
							</div>\
						</div>\
					</div>');
		
		myModal.on('hidden.bs.modal', function (e) {
			//APP.config.removeActiveClasses(that.navbars.top, "li");
		});
		
		that.body.append(myModal);					
			
		switch(section)
		{
			case "itinerary":					
				var listGroup = $('<div class="list-group list-group-wo-radius" style="margin: -15px"></div>');
				
				$.each(that.myData[section], function(i,v)
				{					
					var media = $(	'<div class="media">\
									  <a class="pull-left" href="#">\
										<img class="media-object img-rounded" src="'+that.getOverviewImage(section, v.data.id, true)+'" alt="'+APP.i18n.translate('no_image')+'" style="max-width: 60px; max-height: 60px">\
									  </a>\
									  <div class="media-body">\
										<h4 class="media-heading">'+this.data.name+'</h4>\
									  </div>\
									</div>');
					
					var a = $('<a item_'+section+'_'+v.data.id+' href="#" class="list-group-item '+((that.currentItinerary === v.data.id)? "active" : "")+'"></a>');
					a.data(v).append(media);
					a.click(function(){
						APP.config.removeActiveClasses($(this).parents(".list-group:first"), "a");
						$(this).addClass("active");
						that.body.find("#modal-"+section).modal("hide");
						that.onItineraryClick({element: $(this), section: section, id: $(this).data().data.id});
					});
					that.insertRowAlphabetically(listGroup, a, ".media-heading");
				});
				myModal.find(".modal-body").html(listGroup);
				break;
			case "poi": case "path": case "area":
				if (that.body.find('#accordion-'+section).length > 0)
					that.body.find('#accordion-'+section).remove();
				var accordion = $('<div class="panel-group accordion-list" id="accordion-'+section+'"></div>');
				
				$.each(APP.config.localConfig.typology, function()
				{
					var panel = $(	'<div class="panel panel-default">\
										<div class="panel-heading">\
											<h4 class="panel-title" style="vertical-align: middle">\
												<span class="pull-left iconImage" style="margin-right: 5px"></span>\
												<a data-toggle="collapse" data-parent="#accordion-'+section+'" href="#collapse_'+section+"_"+this.id+'">\
													'+this.name+'\
												</a>\
												<span class="badge pull-right" style="">0</span>\
											</h4>\
										</div>\
										<div id="collapse_'+section+"_"+this.id+'" class="panel-collapse collapse">\
											<div class="panel-body list-group list-group-wo-radius" style="padding: 0px; margin-bottom: 0px"></div>\
										</div>\
									</div>');
					
					var iconImage = $('<span class="glyphicon glyphicon-chevron-right"></span>');
					if (APP.utils.isset(this.icon) && this.icon !== "")
						var iconImage = $('<img src="'+this.icon+'" class="img-responsive" alt="" style="margin-top: -5px; max-height: 30px; max-width: 35px;">');
					
					panel.find(".panel-title .iconImage").html(iconImage);
					
					panel.find('.collapse').collapse({toggle: false});
					
					accordion.append(panel);
				});
				$.each(that.myData[section], function(i, v)
				{
					if (that.currentItinerary &&  $.inArray(that.currentItinerary, that.myData[section][i].data.itineraries) === -1)
						return true;
					
					var container = accordion.find("#collapse_"+section+"_"+v.data.typology_id+" .panel-body");
					
					var media = $(	'<div class="media">\
									  <a class="pull-left" href="#" >\
										<img class="media-object img-responsive img-rounded" src="'+(APP.utils.isset(v.data.thumb_main_image)? v.data.thumb_main_image : APP.config.localConfig.default_overview_image)+'" alt="'+APP.i18n.translate('no_image')+'" style="width: 60px; height: 60px">\
									  </a>\
									  <div class="media-body">\
										<h5 class="media-heading">\
											'+v.data.title+'\
										</h5>\
									  </div>\
									</div>');
										
					var row = $('<a id="item_'+section+'_'+v.data.id+'" href="#" class="list-group-item '+((that.selectedElement.section === section && that.selectedElement.identifier === v.data.id)? "active" : "")+'"</a>');
					row.data(v).click(function(){
						that.onElementClick({ element: $(this), section: section, id: v.data.id, latlng: null});
						myModal.modal("hide");
					});
					
					/*
					if (v.typologies)
					{
						$.each(v.typologies, function(ii,vv)
						{
							var index = APP.utils.getIndexFromField(APP.config.localConfig.typology, "id", vv);
							if (index > -1 && APP.utils.isset(APP.config.localConfig.typology[index].icon))
							{
								var thumb =	$('<span class="col-md-1"><img src="'+APP.config.localConfig.typology[index].icon+'" alt="" class="img-responsive img-thumbnail" style="max-width: 25px; max-height: 20px; padding:0px"></span>');
								thumb.find("img").tooltip({
									title: APP.config.localConfig.typology[index].name,
									container: "body",
								});
								media.find(".subtypologies").append(thumb);
							}
						});
					}
					*/
					
					row.append(media);
					that.insertRowAlphabetically(container, row, ".media-heading");
					var counter = parseInt(container.parents(".panel:first").find(".badge").text());
					container.parents(".panel:first").find(".badge").text(counter+1)
				});
				
				$.each(accordion.find(".panel-body"), function(){
					if ($(this).children().length === 0)
					{
						$(this).parents(".panel:first").remove();
					}				
				});
				
				that.body.find('.modal-body').html(accordion);
				break;
			default:
				break;						
		}
		
		myModal.modal();
		
		if (APP.utils.isset(callback) && $.isFunction(callback))
			callback();
	},
	
	secs: {
		"poi": false,
		"path": false,
		"area": false,
		//"itinerary": false
	},
	
	checkIfsectionCompleted: function(section)
	{
		var that = this;
		var b = false;
		var howManyReqs = 3; // 3 sta per data, media e geo
		
		if (APP.utils.isset(section) && APP.utils.isset(that.myData[section]))
		{
			if ($.isEmptyObject(that.myData[section]))
			{
				if ($.type(that.secs[section]) === "boolean")
					that.secs[section]=0;
				that.secs[section]++;
				if ((section == "itinerary" && that.secs[section] < howManyReqs-1) || (section != "itinerary" && that.secs[section] < howManyReqs))
					b = false;
				else
				{
					b = true;
					that.secs[section] = true;
				}
			}
			else
			{
				$.each(that.myData[section], function(i,v)
				{
					if ($.isPlainObject(v.data) && $.isPlainObject(v.media))
					{
						if (section == "itinerary" || $.isPlainObject(v.geo))
							b = true;
					}
					return false;
				});
			}
		}
		if (b)
		{
			that.eventObj.trigger(section+'_completed');
			that.navbars.top.find('#'+section+'Button').parents("li:first").removeClass("disabled");
			that.secs[section] = true;
			var sectionsCompleted = 0;
			$.each(that.secs, function(i,v)
			{
				if ($.type(v) == "boolean" && v === true)
					sectionsCompleted++;
			});
			if (sectionsCompleted === Object.keys(that.secs).length)
			{
				that.navbars.top.find('#everytypeButton').parents("li:first").removeClass("disabled");
				that.eventObj.trigger('all_sections_completed');
				$.each(that.secs, function(i,v){
					that.secs[i] = false;
				});
			}
		}
	},
	
	getMedia: function(o)
	{
		var that = this;
		
		var section = (APP.utils.isset(o.section))? o.section : null;
		var id = (APP.utils.isset(o.id))? o.id : null;
		var destination = (APP.utils.isset(o.destination))? o.destination : that.myData;
		var url = (APP.utils.isset(o.url))? o.url : (APP.utils.isset(section))? '/jx/media/'+section+'/' : '/jx/media/everytype/';
		var callback = (APP.utils.isset(o.callback))? o.callback : null;
		var bAsync = (APP.utils.isset(o.bAsync))? o.bAsync : true;
		var bSectionCompleted = true;
		
		$.ajax({
			type: 'GET',
			url: url+(APP.utils.isset(id)? "/"+id : ""),
			dataType: 'json',
			async: bAsync,
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					if (APP.utils.isset(data.data) && APP.utils.isset(data.data.items))
					{
						$.each(data.data.items, function(i, v)
						{
							var cs = (APP.utils.isset(section))? section : i.toLowerCase();
							if (!APP.utils.isset(destination[cs]))
								destination[cs] = {};
							if (!$.isArray(v) && $.isPlainObject(v))
							{
								if (!APP.utils.isset(destination[cs][v.id]))
									destination[cs][v.id] = {};
								destination[cs][v.id].media = v;
							}
							else
							{
								$.each(v, function(j,k)
								{
									if (!APP.utils.isset(destination[cs][k.id]))
										destination[cs][k.id] = {};
									destination[cs][k.id].media = k;
								});
								that.checkIfsectionCompleted(cs);
								bSectionCompleted = false;
							}
						});
					}
					if (bSectionCompleted)
						that.checkIfsectionCompleted(section);
					if (APP.utils.isset(callback) && $.isFunction(callback))
						callback();
				}
				else
					APP.utils.showErrMsg(data);
			},
			error: function(result)
			{
				APP.utils.showErrMsg(result);
			}
		});
	},
	
	getData: function(o)
	{
		var that = this;
		
		var section = (APP.utils.isset(o.section))? o.section : null;
		var destination = (APP.utils.isset(o.destination))? o.destination : that.myData;
		var url = (APP.utils.isset(o.url))? o.url : (APP.utils.isset(section))? '/jx/data/'+section+'/' : '/jx/data/everytype/';
		var callback = (APP.utils.isset(o.callback))? o.callback : null;
		var bAsync = (APP.utils.isset(o.bAsync))? o.bAsync : true;
		var bSectionCompleted = true;
		
		$.ajax({
			type: 'GET',
			url: url,
			dataType: 'json',
			async: bAsync,
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					if (data.data && data.data.items)
					{
						$.each(data.data.items, function(i, v)
						{
							var cs = (APP.utils.isset(section))? section : i.toLowerCase();
							if (!APP.utils.isset(destination[cs]))
								destination[cs] = {};
							if (!$.isArray(v) && $.isPlainObject(v))
							{
								if (!APP.utils.isset(destination[cs][v.id]))
									destination[cs][v.id] = {};
								destination[cs][v.id].data = v;
							}
							else
							{
								$.each(v, function(j,k)
								{
									if (!APP.utils.isset(destination[cs][k.id]))
										destination[cs][k.id] = {};
									destination[cs][k.id].data = k;
								});
								that.checkIfsectionCompleted(cs);
								bSectionCompleted = false;
							}
						});
					}
					if (bSectionCompleted)
						that.checkIfsectionCompleted(section);
					if (APP.utils.isset(callback) && $.isFunction(callback))
						callback();
				}
				else
					APP.utils.showErrMsg(data);
			},
			error: function(result)
			{
				APP.utils.showErrMsg(result);
			}
		});
	},
	
	getGeo: function(o)
	{
		var that = this;
		var section = (APP.utils.isset(o.section))? o.section : null;
		var destination = (APP.utils.isset(o.destination))? o.destination : that.myData;
		var url = (APP.utils.isset(o.url))? o.url : APP.utils.isset(section)? '/jx/geo/'+section+'/' : '/jx/geo/everytype/';
		var callback = (APP.utils.isset(o.callback))? o.callback : null;
		var bAsync = (APP.utils.isset(o.bAsync))? o.bAsync : true;
		var bSectionCompleted = true;
		
		$.ajax({
			type: 'GET',
			url: url,
			dataType: 'json',
			async: bAsync,
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					if (data.data && data.data.items)
					{
						var ddi = {
							'Area': null,
							'Path': null,
							'Poi': null
						};
						$.each(data.data.items, function(i,v)
						{
							if (i.toLowerCase() == 'area')
								ddi['Area'] = data.data.items['Area'];
							if (i.toLowerCase() == 'path')
								ddi['Path'] = data.data.items['Path'];
							if (i.toLowerCase() == 'poi')
								ddi['Poi'] = data.data.items['Poi'];
						});
						$.each(ddi, function(i,v)
						{
							var cs = (APP.utils.isset(section))? section : i.toLowerCase();
							if (!APP.utils.isset(destination[cs]))
								destination[cs] = {};
							if (!$.isArray(v) && $.isPlainObject(v))
							{
								if (!APP.utils.isset(destination[cs][v.id]))
									destination[cs][v.id] = {};
								destination[cs][v.id].geo = v;
								that.sendGeojsonLayerToMap(v, cs);
								if (APP.config.localConfig.use_default_extent === "0")
									APP.map.setGlobalExtent(destination[cs][v.id].geo.extent);
							}
							else
							{
								$.each(v, function(j,k)
								{
									if (!APP.utils.isset(destination[cs][k.id]))
										destination[cs][k.id] = {};
									destination[cs][k.id].geo = k;
									that.sendGeojsonLayerToMap(k, cs);
									if (APP.config.localConfig.use_default_extent === "0")
										APP.map.setGlobalExtent(destination[cs][k.id].geo.extent);
								});
								that.checkIfsectionCompleted(cs);
								bSectionCompleted = false;
							}
						});
					}
					if (bSectionCompleted)
						that.checkIfsectionCompleted(section);
					if (!APP.utils.isset(that.leafletHash) && !that.bQrCode)
						APP.map.setExtent(APP.map.globalData[APP.map.currentMapId].globalExtent);
					
					if (APP.utils.isset(callback) && $.isFunction(callback))
						callback();
				}
				else
					APP.utils.showErrMsg(data);
			},
			error: function(result)
			{
				APP.utils.showErrMsg(result);
			}
		});
	},
	
	sendGeojsonLayerToMap: function(v, section)
	{
		var that = this;
		
		var map = APP.map.getCurrentMap();
		
		if (v.geoJSON.type === "Point")
		{
			var coords = [v.geoJSON.coordinates[1],v.geoJSON.coordinates[0]];
			
			var myIcon = null;
			var myIndex = APP.utils.getIndexFromField(APP.config.localConfig.typology, "id", v.typology_id);
			if (myIndex > -1 && APP.utils.isset(APP.config.localConfig.typology[myIndex].marker) && APP.utils.isset(APP.config.localConfig.typology[myIndex].icon))
			{
				myIcon = L.icon({
					iconUrl: APP.config.localConfig.typology[myIndex].marker,
					//iconRetinaUrl: 'my-icon@2x.png',
					//iconSize: [38, 95],
					iconAnchor: [16, 37],
					//popupAnchor: [-3, -76],
					//shadowUrl: 'my-icon-shadow.png',
					//shadowRetinaUrl: 'my-icon-shadow@2x.png',
					//shadowSize: [68, 95],
					//shadowAnchor: [22, 94]
				});
			}
			else
			{
				APP.utils.showNoty({title: APP.i18n.translate("error"), content: APP.i18n.translate("typology_icon_marker_requested"), type: "error"});
				return false;
			}
			
			var myObj = {bounceOnAdd: that.bouncingMarkers};
			if (myIcon)
				myObj.icon = myIcon;
			
			var layer = new L.Marker(coords,myObj);
			layer.on("click", function(args){
				that.onElementClick({ element: layer, section: section, id: v.id, latlng: null});
			});
			if (!APP.map.globalData[APP.map.currentMapId].map.hasLayer(layer))
				APP.map.addLayer({layer: layer, id: section+"_"+v.id, max_scale: v.max_scale});
		}
		else
		{
			var layer = new L.geoJson(v.geoJSON, {
				style: function (feature) {
					var oo = {};
					if (v.color)
						oo.color = v.color;
					oo.weight = APP.utils.isset(v.width)? v.width : 7;
						
					return oo;
				},
				onEachFeature: function (feature, layer) {
					layer.on("click", function(args)
					{
						that.onElementClick({ element: args.layer, section: section, id: v.id, latlng: args.latlng});
					});
				}
			});
			if (!APP.map.globalData[APP.map.currentMapId].map.hasLayer(layer))
				APP.map.addLayer({layer: layer, id: section+"_"+v.id, max_scale: v.max_scale});
		}
	},
	
	isAvailableObject: function(myObj)
	{
		if (!APP.utils.isset(myObj.obj))
		{
			APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: myObj.label+" "+APP.i18n.translate("not_configured")});
			return false;
		}
		return true;
	},
	
	setBlueimpGalleryDiv: function(params)
	{
		var that = this;
		
		if (params.container.find("#"+params.id).length > 0)
			params.container.find("#"+params.id).remove();
		
		var div = $('<div id="'+params.id+'" class="'+params.classes+'">\
						<div class="slides"></div>\
						<h3 class="title"></h3>\
						<a class="prev"><i class="icon icon-chevron-sign-left"></i></a>\
						<a class="next"><i class="icon icon-chevron-sign-right"></i></a>\
						<a class="close"><i class="icon icon-remove"></i></a>\
						<a class="play-pause"></a>\
						<ol class="indicator"></ol>\
					</div>');
		
		if (!params.closeBtn)
			div.find(".close").remove();
			
		params.container.append(div);
	},
	
	setSearchModal: function()
	{
		var that = this;
		var modalId = "searchModal";
		
		that.searchModal = $('<div class="modal fade" id="'+modalId+'" tabindex="-1" role="dialog" aria-labelledby="'+modalId+'Label" aria-hidden="true">\
								<div class="modal-dialog modal-lg">\
									<div class="modal-content">\
										<div class="modal-header">\
											<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'+APP.i18n.translate("Close")+'</span></button>\
											<h4 class="modal-title" id="'+modalId+'Label">'+APP.i18n.translate("Search Panel")+'</h4>\
										</div>\
										<div class="modal-body">\
											<form role="form">\
												<div class="form-group">\
													<label for="searchInput">'+APP.i18n.translate("Search")+'</label>\
													<div class="input-group">\
														<input type="text" class="form-control" id="searchInput" placeholder="'+APP.i18n.translate("What are you looking for?")+'">\
														<span class="input-group-btn">\
															<button class="btn btn-default submitBtn" type="button"><i class="icon icon-search"></i> '+APP.i18n.translate('OK')+'</button>\
														</span>\
													</div>\
												</div>\
											</form>\
											<div class="table-responsive" style="">\
												<table class="table table-striped table-hover">\
													<thead>\
														<tr><th>'+APP.i18n.translate("Type")+'</th><th>'+APP.i18n.translate("Title")+'</th><th>'+APP.i18n.translate("Category")+'</th><th class="hidden-xs hidden-sm">'+APP.i18n.translate("Teaser")+'</th></tr>\
													</thead>\
													<tbody>\
													</tbody>\
												</table>\
											</div>\
										</div>\
										<div class="modal-footer">\
											<button type="button" class="btn btn-primary" data-dismiss="modal">'+APP.i18n.translate("Close")+'</button>\
										</div>\
									</div>\
								</div>\
							</div>');
		
		that.searchModal.find(".submitBtn").click(function()
		{
			var inp = that.searchModal.find('#searchInput');
			$.ajax({
				type: 'GET',
				url: that.searchUrl+inp.val(),
				dataType: 'json',
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, null))
					{
						var table = that.searchModal.find("table");
						var tbody = that.searchModal.find("table tbody");
						
						if ($.fn.DataTable.fnIsDataTable( table[0] ))
						{
							table.dataTable().fnDestroy();
							tbody.empty();
						}
						
						var createDataTable = function()
						{
							that.searchModal.find("table").dataTable({
								"bSort": true,
								"bRetrieve": true,
								"bPaginate": true,
								"sPaginationType": "full_numbers",
								"aLengthMenu": [10, 20, 30, 50, 100],
								"iDisplayLength" : (data.items_per_page)? data.items_per_page : 10,
								"aaSorting": [[0,'asc'],[1,'asc']],
								"bJQueryUI": false,
								"oLanguage": APP.utils.getDataTableLanguage(),
								"fnDrawCallback": function( oSettings ) {
									
								},
								"fnInitComplete": function(oSettings, json) {
								
								}
							});
						};
						
						if (!data.data || !data.data.items || !$.isArray(data.data.items) || data.data.items.length === 0)
						{
							createDataTable();
							return false;
						}
						
						$.each(data.data.items, function(i,v)
						{
							var sectionIcon = that.body.find("#"+v.type.toLowerCase()+"Button .icon").clone().addClass("icon-2x").tooltip({title: APP.i18n.translate(v.type.toLowerCase())});
							var row = $('<tr style="cursor: pointer"><td class="sectionIcon"><span style="display:none">'+v.type.toLowerCase()+'</span></td><td>'+v.title+'</td><td class="categoryTd"></td><td class="hidden-xs hidden-sm">'+v.teaser+'</td></tr>');
							row.find(".sectionIcon").append(sectionIcon);
							var typology = that.getTypology(that.myData[v.type.toLowerCase()][v.id].data.typology_id);
							if (typology)
							{
								var span = $('<span style="display:none">'+typology.name+'</span>');
								var img = $('<img class="img-responsive" src="'+typology.icon+'"  alt="'+typology.name+'" style="max-width: 32px">');
								img.tooltip({
									title: APP.i18n.translate(typology.name),
								});
								row.find(".categoryTd").append(span).append(img);
							}
							
							row.data({section: v.type.toLowerCase(), id: v.id});
							row.click(function()
							{
								var myData = $(this).data();
								that.searchModal.modal("hide");
								
								var openItem = function()
								{
									if (myData.section === "itinerary")
										that.onItineraryClick({ element: $(this), section: myData.section, id: myData.id});
									else
										that.onElementClick({ element: $(this), section: myData.section, id: myData.id, latlng: null});
								};
								
								if (that.currentItinerary)
								{
									that.closeItinerary(function(){
										openItem();
									});
								}
								else
									openItem();
							});
							tbody.append(row);
						});
						
						createDataTable();
					}
					else
					{
						APP.utils.showErrMsg(data);
					}
				},
				error: function(result)
				{
					APP.utils.showErrMsg(result);
				}
			});
		});		
		that.searchModal.find("#searchInput").on('keypress', function(e){
			var code = e.keyCode || e.which;
			if (code == 13)
			{
				that.searchModal.find(".submitBtn").click();
				e.preventDefault();
			}
		});
		that.searchModal.on('show.bs.modal', function(){
			var n = that.navbars.top.parents(".navbar:first");
			if (n.find(".navbar-collapse").hasClass("in"))
				n.find('.navbar-collapse').collapse('hide');
			that.body.find('#searchButton').parent().addClass("active");
		});
		that.searchModal.on('shown.bs.modal', function(){
			that.searchModal.find('#searchInput').focus();
		});		
		that.searchModal.on('hide.bs.modal', function(){
			that.body.find('#searchButton').parent().removeClass("active");
		});
		that.body.append(that.searchModal);
		
		var bt = that.body.find('#searchButton');
		bt.attr("data-target","#"+modalId).click(function(){
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
				that.mySidebar.control.hide();
		});
	},
	
	getScale: function(map)
	{
		var that = this;
		var maxWidth = 100;
		var bounds = map.getBounds(),
		    centerLat = bounds.getCenter().lat,
		    halfWorldMeters = 6378137 * Math.PI * Math.cos(centerLat * Math.PI / 180),
		    dist = halfWorldMeters * (bounds.getNorthEast().lng - bounds.getSouthWest().lng) / 180,

		    size = map.getSize(),
		    options = this.options,
		    maxMeters = 0;

		if (size.x > 0) {
			maxMeters = dist * (maxWidth / size.x);
		}
		
		var getRoundNum = function(num)
		{
			var pow10 = Math.pow(10, (Math.floor(num) + '').length - 1),
		    d = num / pow10;

			d = d >= 10 ? 10 : d >= 5 ? 5 : d >= 3 ? 3 : d >= 2 ? 2 : 1;

			return pow10 * d;
		};

		if (maxMeters)
			return getRoundNum(maxMeters);
	},
	
	showPage: function(section)
	{
		var that = this;
		var htmlPage = that.pages[section].content;
		if (that.pages[section].type === "sidebar" && that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
		{
			that.mySidebar.div.append(htmlPage);
			that.mySidebar.control.show();
		}
		else
		{
			var modalId = "modal-"+section;
			var myModal = that.body.find("#"+modalId);
			if (myModal.length === 0)
			{
				myModal = APP.utils.createModal({
					container: that.body,
					id: modalId,
					size: "lg",
					header: APP.utils.capitalize(APP.i18n.translate(section)),
					body: htmlPage,
					shown: function(){
						
					},
					hidden: function(){ 
						
					}
				});
			}
			var n = that.navbars.top.parents(".navbar:first");
			if (n.find(".navbar-collapse").hasClass("in"))
				n.find('.navbar-collapse').collapse('hide');
			myModal.modal('show');
		}
	},
	
	getPage: function(section, bShow)
	{
		var that = this;
		
		if (!APP.utils.isset(that.pages[section]))
		{
			APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("page_not_found")});
			return false;
		}
		
		if (!APP.utils.isset(that.pages[section].content))
		{
			$.ajax({
				type: 'GET',
				url: that.pages[section].url,
				dataType: 'json',
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, null))
					{
						that.pages[section].content = data.data.items[0].body;
						that.body.find("#"+section+"Button").parent().removeClass("disabled");
						if (bShow)
							that.showPage(section);
					}
					else
					{
						APP.utils.showErrMsg(data);
					}
				},
				error: function(result)
				{
					APP.utils.showErrMsg(result);
				}
			});
		}
		else
			if (bShow)
				that.showPage(section);
	},
	
	setPages: function()
	{
		var that = this;
		
		$.each(APP.config.localConfig.page_urls, function(i,v)
		{
			var t = (i === "help")? "modal" : "sidebar";
			that.pages[i] = {url: v, content: null, type: t};
		});
	},
	
	checkUrlCoords: function()
	{
		var that = this;
		var uc = window.location.href;
		uc = uc.split("#");
		if (uc.length === 1)
			return;
		uc = uc[1].split("/");
		if (uc.length === 3)
		{
			that.leafletHash = {
				zoom: uc[0],
				lat: uc[1],
				lng: uc[2]
			};
		}
		if (uc.length === 2)
		{
			var section = uc[0];
			var id = parseInt(uc[1]);
			var callback = function(){
				that.onElementClick({element: null, section: section, id: id});
			};
			if (section == "itinerary")
			{
				callback = function(){
					that.onItineraryClick({element: null, section: section, id: id});
				};
			}
			if (!isNaN(id))
			{
				that.bQrCode = true;
				that.eventObj.on(section+"_completed", function(){
					callback();
				});
			}
		}
	},
	
	setLoginMsg: function()
	{
		var that = this;
		var callback = function(){ 
			that.toggleGeometry(true, false);
		};
		
		var body = $('<p>'+APP.i18n.translate('you_are_not_logged_in')+'</p>');
		var footer = $('<div>\
							<button type="button" class="btn btn-success" id="loginMsg_login">'+APP.i18n.translate('Login')+'</button>\
							<button type="button" class="btn btn-primary" id="loginMsg_register">'+APP.i18n.translate('Register')+'</button>\
							<button type="button" class="btn btn-default pull-right" id="loginMsg_close">'+APP.i18n.translate('Skip')+'</button>\
						</div>');
		
		footer.find("#loginMsg_login").click(function(){
			that.bWouldAddGeometry = true;
			//that.loginMsgModal.modal("hide");
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
				that.mySidebar.control.hide();
			that.loginModal.modal("show");
			that.changeLoginModalPage("login");
		});
		footer.find("#loginMsg_register").click(function(){
			//that.loginMsgModal.modal("hide");
			/*
			that.registrationModal.on('hidden.bs.modal', function(){
				that.registrationModal.off('hidden.bs.modal');
				if (APP.utils.isset(callback) && $.isFunction(callback))
					callback();
			});
			*/
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
				that.mySidebar.control.hide();
			that.registrationModal.modal("show");
			that.navbars.top.find("li").removeClass("active");
		});
		footer.find("#loginMsg_close").click(function(){
			that.loginMsgModal.modal("hide");
			if (APP.utils.isset(callback) && $.isFunction(callback))
				callback();
		});
		
		that.loginMsgModal = APP.modals.create({
			container: that.body,
			id: "loginMsgModal",
			size: "sm",
			keyboard: 'false',
			backdrop: "static",
			bTopCloseButton: true,
			header: APP.i18n.translate("Notice"),
			body: body,
			footer: footer,
			/*
			onShown: function(){
				
			},
			onHidden: function(){ 
				
			}
			*/
		});
	},
	
	changeLoginModalPage: function(page)
	{
		var that = this;
		switch(page)
		{
			case "forgot_password":
				that.loginModal.find(".fg_login").hide();
				that.loginModal.find(".fg_forgot_password").show();
				break;
			case "login":
				that.loginModal.find(".fg_forgot_password").hide();
				that.loginModal.find(".fg_login").show();
				break;
			default:
				console.log("aggiungere page: "+page);
				break;
		}
		
	},
	
	setLoginModal: function()
	{
		var that = this;
		
		var htmlPage = $(	'<form role="form">\
								<div class="form-group fg_login">\
									<label class="control-label" for="APP-username">'+APP.i18n.translate("Username")+'</label>\
									<input type="text" class="form-control" id="APP-username" name="username" placeholder="'+APP.i18n.translate("Enter username")+'">\
								</div>\
								<div class="form-group fg_forgot_password" style="display:none">\
									<label class="control-label" for="APP-email">'+APP.i18n.translate("Email")+'</label>\
									<input type="email" class="form-control" id="APP-email" name="email" placeholder="'+APP.i18n.translate("Enter email")+'">\
								</div>\
								<div class="form-group fg_login">\
									<label class="control-label" for="APP-password">'+APP.i18n.translate("Password")+'</label>\
									<input type="password" class="form-control" id="APP-password" name="password" placeholder="'+APP.i18n.translate("Enter password")+'">\
								</div>\
								<div class="form-group fg_login">\
									<button type="button" class="btn btn-link">'+APP.i18n.translate("Forgot password")+'?</button>\
								</div>\
							</form>');
		
		htmlPage.find(".btn-link").click(function(){
			that.changeLoginModalPage("forgot_password");
		});
		
		var footerPage = $('<div>\
								<button id="backToLoginBtn" type="button" class="btn btn-default pull-left fg_forgot_password" style="display:none"><i class="icon icon-undo"></i> '+APP.i18n.translate("Back")+'</button>\
								<button id="form_login" type="button" class="btn btn-default fg_login">'+APP.i18n.translate("Login")+'</button>\
								<button id="form_register" type="button" class="btn btn-primary fg_login">'+APP.i18n.translate("Register")+'</button>\
								<button id="form_resetpassword" type="button" class="btn btn-success fg_forgot_password" style="display:none"><i class="icon icon-ok"></i> '+APP.i18n.translate("Send email")+'</button>\
							</div>');
		
		footerPage.find("#backToLoginBtn").click(function(){
			that.changeLoginModalPage("login");
		});
		footerPage.find("#form_login").click(function(){
			if (!APP.utils.isset(APP.config.localConfig.urls.login))
				return;
			htmlPage.find(".help-block").remove();
			htmlPage.find(".has-error").removeClass("has-error");
			$.ajax({
				type: 'POST',
				url: APP.config.localConfig.urls.login,
				dataType: 'json',
				data: htmlPage.find(".fg_login :input").serializeArray(),
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, that.loginModal.find("form")))
					{
						if (APP.utils.isset(data.data.authuser))
						{
							that.loginModal.modal("hide");
							APP.config.localConfig.authuser = data.data.authuser;
							$.each(["poi","path","area"], function(i, v)
							{
								that.getDstruct(v, function(){
									if (that.body.find("#loginButton").parent().is(":visible"))
										that.body.find("#loginButton").parent().hide();
									if (that.body.find("#logoutButton").parent().is(":hidden"))
										that.body.find("#logoutButton").parent().show();
									if (that.body.find("#mydatadropdownButton").parent().is(":hidden"))
										that.body.find("#mydatadropdownButton").parent().show();
									if (that.body.find("#addPathButton").parent().hasClass("disabled"))
										that.body.find("#addPathButton").parent().removeClass("disabled");
									if (that.body.find("#addAreaButton").parent().hasClass("disabled"))
										that.body.find("#addAreaButton").parent().removeClass("disabled");
								});
							});
							if (that.bAddGeometry || that.bWouldAddGeometry)
							{
								that.toggleGeometry(false,false);
								that.toggleGeometry(true,false);
							}
						}
						else
							APP.utils.showErrMsg(data);
					}
					else
						APP.utils.showErrMsg(data);
				},
				error: function(result)
				{
					APP.utils.showErrMsg(result);
				}
			});
		});
		footerPage.find("#form_register").click(function(){
			//that.loginModal.modal("hide");
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
				that.mySidebar.control.hide();
			that.registrationModal.modal("show");
			//setTimeout(function(){that.registrationModal.modal("show");},600);
		});
		footerPage.find("#form_resetpassword").click(function(){
			if (!APP.utils.isset(APP.config.localConfig.urls.reset_password))
				return;
			htmlPage.find(".help-block").remove();
			htmlPage.find(".has-error").removeClass("has-error");
			$.ajax({
				type: 'POST',
				url: APP.config.localConfig.urls.reset_password,
				dataType: 'json',
				data: htmlPage.find(".fg_forgot_password :input").serializeArray(),
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, that.loginModal.find("form")))
					{
						that.loginModal.modal("hide");
						APP.utils.showNoty({title: APP.i18n.translate("success"), type: "success", content: APP.i18n.translate("email_sent")});
					}
					else
						APP.utils.showErrMsg(data);
				},
				error: function(result)
				{
					APP.utils.showErrMsg(result);
				}
			});
		});
		
		var header = $('<span><span class="fg_login">'+APP.utils.capitalize(APP.i18n.translate("login"))+'</span><span class="fg_forgot_password" style="display:none">'+APP.utils.capitalize(APP.i18n.translate("reset_password"))+'</span></span>');
		
		that.loginModal = APP.modals.create({
			container: that.body,
			id: "loginModal",
			size: "sm",
			bTopCloseButton: true,
			header: header,
			body: htmlPage,
			footer: footerPage,
			/*
			onShown: function(){
				
			},*/
			onHidden: function(){
				that.bWouldAddGeometry = false;
			}			
		});
		
		that.body.find("#loginButton").click(function(){
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
				that.mySidebar.control.hide();
			that.loginModal.modal("show");
			that.changeLoginModalPage("login");
		}).parent().removeClass("disabled");
		
		that.body.find("#logoutButton").click(function(){
			$.ajax({
				type: 'POST',
				url: APP.config.localConfig.urls.logout,
				dataType: 'json',
				data: APP.config.localConfig.authuser,
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, null))
					{
						delete APP.config.localConfig.authuser;
						$.each(["poi","path","area"], function(i, v)
						{
							that.getDstruct(v, function(){
								if (that.body.find("#loginButton").parent().is(":hidden"))
									that.body.find("#loginButton").parent().show();
								if (that.body.find("#logoutButton").parent().is(":visible"))
									that.body.find("#logoutButton").parent().hide();
								if (that.body.find("#mydatadropdownButton").parent().is(":visible"))
									that.body.find("#mydatadropdownButton").parent().hide();
							});
						});
						if (!APP.config.isMobileVersion() && that.bAddGeometry)
						{
							that.toggleGeometry(false,false);
							that.toggleGeometry(true,false);
						}
					}
					else
						APP.utils.showErrMsg(data);
					
				},
				error: function(result)
				{
					APP.utils.showErrMsg(result);
				}
			});
		}).parent().removeClass("disabled");
		
		if (APP.utils.isset(APP.config.localConfig.authuser))
		{
			that.body.find("#loginButton").parent().hide();
			that.body.find("#mydatadropdownButton").parent().show();
			that.body.find("#logoutButton").parent().show();
		}
		else
		{
			that.body.find("#loginButton").parent().show();
			that.body.find("#mydatadropdownButton").parent().hide();
			that.body.find("#logoutButton").parent().hide();
		}
	},
	
	openRegistrationModal: function(data)
	{
		var that = this;
		if (!APP.utils.isset(that.registrationModal))
			that.setRegistrationModal();
		if (data && data.items && data.items.length > 0)
		{
			$.each(data.items[0], function(i,v){
				that.registrationModal.find("#APP-"+i).val(v);
			});
		}
		if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
			that.mySidebar.control.hide();
		that.registrationModal.modal('show');
	},
	
	setRegistrationModal: function()
	{
		var that = this;
		var body = $('<div></div>');
		
		var footer = $('<div>\
							<button type="button" class="btn btn-success"><i class="icon icon-ok"></i> '+APP.i18n.translate('send')+'</button>\
							<button type="button" class="btn btn-default"><i class="icon icon-remove"></i> '+APP.i18n.translate('cancel')+'</button>\
						</div>');
		
		footer.find(".btn-success").click(function()
		{
			var s = (APP.config.checkLoggedUser())? that.frontPrefix+"userdata" : that.frontPrefix+"registration";
		
			if (!APP.utils.isset(APP.config.localConfig.urls[s]))
				return;
			var id = (APP.config.checkLoggedUser())? APP.config.localConfig.authuser.id : null;
			that.registrationModal.find("form").attr('id','fm_'+s);
			
			var tk = APP.config.getToken(s);
			if (tk)
				that.registrationModal.find("form .tokenInput").val(tk);
			APP.anagrafica.formSubmit(id, s, function(){
				that.registrationModal.modal("hide");
			}, false);
		});
		footer.find(".btn-default").click(function()
		{
			that.registrationModal.modal("hide");
		});
		
		that.getDstruct("registration", function()
		{
			var registrationForm = APP.anagrafica.createFormTemplate(null, null, APP.anagrafica.sections[that.frontPrefix+"registration"], that.frontPrefix+"registration", []);
			body.append(registrationForm);
			APP.utils.setLookForm(registrationForm, null);
		});
		
		that.registrationModal = APP.modals.create({
			container: that.body,
			id: "registrationModal",
			size: "lg",
			keyboard: 'false',
			backdrop: "static",
			bTopCloseButton: false,
			header: APP.i18n.translate("Register"),
			body: body,
			footer: footer,
			onShown: function(){
				var title = (APP.config.checkLoggedUser())? APP.i18n.translate("User's data") : APP.i18n.translate("Register");
				that.registrationModal.find(".modal-header .lead").text(title);
			},
			onHidden: function(){
				var f = that.registrationModal.find("form");
				APP.utils.resetFormErrors(f);
				f[0].reset();
			}
		});
	},
	
	manipulateCanvasFunction: function(savedMap)
	{
		var that = this;
		dataURL = savedMap.toDataURL("image/png");
		dataURL = dataURL.replace(/^data:image\/(png|jpg);base64,/, "");
		$.post(that.leafletSaveMapPluginDir+"saveMap.php", { savedMap: dataURL }, function(data) {
			console.log('Image Saved to : ' + data);
		});
	},
	
	printPage: function()
	{
		var that = this;
		
		that.body.append('<div class="mapMap"></div>');
		that.body.append('<div class="svgMap"></div>');
		
		
		html2canvas($('#mainContent'),{
			//allowTaint: true,
			background: undefined,
			//height: null,
			//letterRendering: true,
			//logging: true,
			proxy: that.leafletSaveMapPluginDir+"proxy.php",
			taintTest: false,
			//timeout: 0,
			//width: null,
			//useCORS: true,
		}).then(function(canvas){
			that.body.find(".svgMap").append(canvas);
		});
		
		html2canvas($('#mainContent'),{
			allowTaint: true,
			background: undefined,
			//height: null,
			//letterRendering: true,
			//logging: true,
			proxy: that.leafletSaveMapPluginDir+"proxy.php",
			//taintTest: false,
			//timeout: 0,
			//width: null,
			//useCORS: true,
		}).then(function(canvas){
			that.body.find(".mapMap").append(canvas);
		});
	},
	
	getDstruct: function(section, callback)
	{
		var that = this;
		
		if (!APP.utils.isset(section))
			return false;
		
		var s = that.frontPrefix+section;
		if (!APP.anagrafica.hasOwnProperty("sections"))
			APP.anagrafica.sections = {};
		//if (!APP.anagrafica.sections.hasOwnProperty(s)) // forzo il reset perch� in questo progetto i datastruct possono cambiare dinamicamente
			APP.anagrafica.sections[s] = APP.utils.setBaseStructure(s, s);
		APP.anagrafica.getStructure(APP.config.localConfig.urls.dStruct+"?tb="+s, s, null, false, callback);
	},
	
	updateMyData: function(end, myCallback)
	{
		var that = this;
		APP.map.removeAllLayers();
		that.myData = {};
		
		that.eventObj.off('all_sections_completed').on('all_sections_completed', function()
		{
			if (APP.utils.isset(myCallback) && $.isFunction(myCallback))
				myCallback();
		});
		
		that.getGeo({url: BASE_URL+"jx/geo/"+end});
		that.getData({url: BASE_URL+"jx/data/"+end});
		that.getMedia({url: BASE_URL+"jx/media/"+end});
	},
	
	closeHighlightingsdata: function(callback)
	{
		var that = this;
		that.bMyReportView = false;
		if (APP.utils.isset(that.currentNoty))
		{
			that.currentNoty.close();
			that.currentNoty = null;
		}
		
		if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
			that.mySidebar.control.hide();
		
		that.updateMyData("", callback);
	},
	
	safeExecution: function(callback)
	{
		var that = this;
		
		if (that.bMyReportView)
		{
			if (that.currentSection == "highlightingsdata")
				that.updateMyData("my", callback);
			else
				that.closeHighlightingsdata(callback);
		}
		else
		{
			if (APP.utils.isset(callback) && $.isFunction(callback))
				callback();
		}
	},
	
	start: function()
	{
		var that = this;
		
		var arr = [
			{
				obj: APP.config.localConfig,
				label: 'config',
			},
			{
				obj: APP.config.localConfig.background_layer,
				label: 'background_layer',
			},
			{
				obj: APP.config.localConfig.default_extent,
				label: 'default_extent',
			},
			{
				obj: APP.config.localConfig.default_overview_image,
				label: 'default_overview_image',
			},
			{
				obj: APP.config.localConfig.i18n,
				label: 'i18n',
			},
			{
				obj: APP.config.localConfig.page_urls,
				label: 'page_urls',
			},
			{
				obj: APP.config.localConfig.path_mode,
				label: 'path_mode',
			},
			{
				obj: APP.config.localConfig.typology,
				label: 'typology',
			},
			{
				obj: APP.config.localConfig.urls,
				label: 'urls',
			},
		];		
		
		var errorObj = false;
		$.each(arr, function(){
			if (!that.isAvailableObject(this))
			{
				errorObj = true;
				return false;
			}
		});
		
		if (errorObj)
			return;
		
		var errorIM = false;
		$.each(APP.config.localConfig.typology, function(){
			if (!APP.utils.isset(this.icon) || !APP.utils.isset(this.marker))
			{
				errorIM = true;
				return false;
			}
		});
		if (errorIM)
		{
			APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("typology_icon_marker_requested")});
			return;
		}
		
		that.checkUrlCoords();
		that.setPages();
		$("html").css({"height":"100%","width":"100%"});
		
		that.body = $("body");
		that.body.css({"height":"100%","width":"100%","padding-top":"50px"});
		that.body.find(".navbar-nav:first li").addClass("disabled");
		that.navbars.top = that.body.find(".navbar-nav:first"); // navbar-nav
		that.navbars.top.find("a").click(function()
		{
			var btn = $(this);
			var n = that.navbars.top.parents(".navbar:first");
			if (n.find(".navbar-collapse").hasClass("in") && btn.parents(".dropdown").length == 0)
				n.find('.navbar-collapse').collapse('hide');
			
			if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control && btn.parent().hasClass("dropdown-toggle"))
				that.mySidebar.control.hide();
			
			if (btn.parents("li:first").hasClass("disabled"))
				return false;
				
			that.navbars.top.find("li").removeClass("active");
			btn.parents("li:first").addClass("active");
			
			var section = btn.attr("id");
			section = section.split("Button")[0];
			that.previousSection = that.currentSection;
			that.currentSection = section;
			switch(section)
			{
				case "addGeometries":
					var callbackFun = function(){
						that.navbars.top.find("li").removeClass("active");
						that.toggleGeometry(!that.bAddGeometry, true);
					};
					
					that.safeExecution(callbackFun);
					break;
				case "to_default_extent":
					btn.parents("li:first").removeClass("active");
					if (!APP.utils.isset(APP.config.localConfig.default_extent))
						APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("Default extent not found")});
					APP.map.setGlobalExtent(APP.config.localConfig.default_extent);
					APP.map.setExtent(APP.config.localConfig.default_extent);
					break;
				default:
					var arr = (that.bEverytypeGeometries && section == "everytype")? that.arrEverytypeGeometries : [section];
					that.showItems(section, arr);
			}
		});
		that.navbars.top.find("#to_default_extentButton").parent().removeClass("disabled");
		that.setRegistrationModal();
		that.setLoginMsg();
		that.setLoginModal();
		that.setSearchModal();
		
		$("#mainContent").css({"height":"100%","width":"100%","margin-bottom":"0px"});		
		
		APP.map.sidebar.div = $('<div id="leafletSidebar" style="margin-top: -60px"></div>');
		that.body.append(APP.map.sidebar.div);
		var params = {container: $("#mainContent")};
		if (that.leafletHash)
		{
			params.center = new L.LatLng(that.leafletHash.lat, that.leafletHash.lng);
			params.zoom = that.leafletHash.zoom;
		}
		APP.map.setMap(params);
		that.mySidebar.div = APP.map.sidebar.div;
		that.mySidebar.control = APP.map.sidebar.control;
		that.getPage("info", false);
		APP.map.getCurrentMap().on('click',function(){			
			that.resetHighlightLayer();
		});
		APP.map.getCurrentMap().on('move', function()
		{
			if (that.bCurrentLayers)
				APP.map.getCurrentViewLayers(APP.map.getCurrentMap());
		});
		APP.map.getCurrentMap().on('zoomend', function()
		{			
			var map = APP.map.getCurrentMap();
			var scale = that.getScale(APP.map.globalData[APP.map.currentMapId].map);
			$.each(APP.map.globalData[APP.map.currentMapId].addedLayers, function(i,v)
			{
				if (i.indexOf("poi") === -1 || that.currentItinerary)
					return true;
				
				if (!APP.utils.isset(v.max_scale) || scale <= v.max_scale || (that.selectedElement.section === "poi" && that.selectedElement.identifier ===  parseInt(i.split("_")[1])))
					APP.map.showLayer(i);
				else
					APP.map.hideLayer(i);
			});
		});
		
		$("#mainContent").height($("#mainContent").height());		
		
		if (that.bEverytypeGeometries)
		{
			that.getGeo({callback: function(){
				var map = APP.map.getCurrentMap();
				map.fire("zoomend");
			}});
			that.getData({});
			that.getMedia({});
			
			//that.getDstruct("highlitingarea");
			//that.getDstruct("path");
			that.getDstruct("highlitingpoi");
			$.each(that.arrEverytypeGeometries, function(i,v)
			{
				var btn = that.body.find("#"+v+"Button");
				if (btn.length>0)
					btn.parent().hide();
			});
		}
		else
		{
			that.body.find("#everytypeButton").parent().hide();
			that.getGeo({section: "poi", callback: function(){
				var map = APP.map.getCurrentMap();
				map.fire("zoomend");
			}});
			that.getData({section: "poi"});
			that.getMedia({section: "poi"});
			that.getGeo({section: "path"});
			that.getData({section: "path"});
			that.getMedia({section: "path"});
			that.getGeo({section: "area"});
			that.getData({section: "area"});
			that.getMedia({section: "area"});
		}
		
		that.getData({section: "itinerary"});
		that.getMedia({section: "itinerary"});
		
		that.body.find("#addGeometriesButton").parent().removeClass("disabled");
		that.body.find("#userdataButton").click(function(){
			if (!APP.utils.isset(APP.config.localConfig.authuser))
				return false;
			$.ajax({
				type: 'GET',
				url: APP.config.localConfig.urls[that.frontPrefix+that.currentSection]+"/"+APP.config.localConfig.authuser.id,
				dataType: 'json',
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, null))
					{
						that.openRegistrationModal(data.data);
					}
					else
						APP.utils.showErrMsg(data);
				},
				error: function(result)
				{
					APP.utils.showErrMsg(result);
				}
			});
		}).parent().removeClass("disabled").show();
		that.body.find("#highlightingsdataButton").click(function(){
			//APP.map.hideAllLayers();
		}).parent().removeClass("disabled").show();
		
		that.body.find("#helpButton").click(function(){
			that.closeItems();
			that.getPage('help', true);
		});
		
		that.body.find("#printButton").click(function(){
			that.printPage();
		})
		
		if (!that.leafletHash && !that.navbars.top.parents(".navbar").find(".navbar-toggle").is(":visible"))
		{
			setTimeout(function(){
				that.navbars.top.find("#infoButton").click();
			},2000);
		}
	}
});