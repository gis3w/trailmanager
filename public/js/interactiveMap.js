$.extend(APP.interactiveMap, 
{
	previousSection: null,
	currentSection: null,
	currentItinerary: null,
	myData: {},
	body: null,
	navbars: {
		top: null,
		bottom: null,
	},
	infoDelay: 1000,
	itemsOnSidebar: true,
	mySidebar: {
		control: null,
		div: null,
	},
	firstExtent: {},
	bouncingMarkers: false,
	searchUrl: '/jx/search?tofind=',
	
	getObjectTitle: function(section, id)
	{
		var that = this;
		return (section === "itinerary")? that.myData[section][id].data.name : that.myData[section][id].data.title;
	},
	
	getOverviewImage: function(section, id, thumbnail)
	{
		var that = this;
		if ($.isArray(that.myData[section][id].media.images) &&  that.myData[section][id].media.images.length > 0)
			return (thumbnail)? that.myData[section][id].media.images[0].image_thumb_url : that.myData[section][id].media.images[0].image_url;
		else
			return (thumbnail && APP.config.localConfig.default_overview_thumbnail)? APP.config.localConfig.default_overview_thumbnail : APP.config.localConfig.default_overview_image;
	},
	
	resize: function()
	{
		if (!APP.utils.isset(this.body))
			return;
		var x = this.body.find(".centerImage");
		x.css("width","100%");
		x.centerImage();
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
		if (section == "path" || section == "poi")
			if (APP.map.globalData.mainContent.addedLayers[section+"_"+id].layer)
				APP.map.highlightLayer(section+"_"+id);
	},
	
	resetHighlightLayer: function()
	{
		APP.map.highlightLayer(null);
	},
	
	onElementClick: function(element, section, id)
	{
		var that = this;
		if (that.currentSection != "itinerary")
			that.highlightLayer(section, id);
		that.zoomAt(section, id);
		if (that.itemsOnSidebar && L.control.sidebar && that.mySidebar.control)
		{
			that.mySidebar.control.hide();
		}
		
		/*
		that.showBottomBar(section, id, function()
		{
			that.hideBottomBar();
			that.resetHighlightLayer();
			APP.map.showAllLayers();
			APP.map.setExtent(that.firstExtent);
			if (that.itemsOnSidebar && L.control.sidebar)
			{
				that.mySidebar.div.find(".active").removeClass("active");
				that.mySidebar.control.show();
				
			}
			that.navbars.top.find("#"+that.currentSection+"Button").parents("li:first").addClass("active");
		});
		*/
		
		if (element)
		{
			if (!element.unbindPopup)
				element = APP.map.getLayer(section+"_"+id);
			if (element.unbindPopup && $.isFunction(element.unbindPopup))
				element.unbindPopup();
			if (element.bindPopup && $.isFunction(element.bindPopup))
			{
				po = {
					closeOnClick: true,
					maxWidth: 300,
					maxHeight: 300,
					minWidth: 300
				};
				if (section == "poi")
					po.offset = L.point(0, -25);
				
				var media = '<div class="media">\
								<a class="pull-left" href="#">\
									<img class="media-object thumbnail" style="width: 75px" src="'+that.getOverviewImage(section, id, true)+'" alt="">\
								</a>\
								<div class="media-body">\
									<h4 class="media-heading">'+that.getObjectTitle(section, id)+'</h4>\
									<button type="button" class="btn btn-link popupDetailsBtn">'+APP.i18n.translate('details')+'</button>\
								</div>\
							</div>';
							
				element.bindPopup(media, po);
				
				element.on('popupopen', function(a){
					var myBtn = $(a.popup._container).find(".popupDetailsBtn");
					myBtn.off("click").click(function(){
						//a.layer.closePopup();
						that.showInformation(section, id);
					});
				});
				
				element.openPopup();
			}
		}
	},
	
	showInformation: function(section, id, onCloseCallback)
	{
		var that = this;
		/*
		that.getData(section, function(){
			that.getMedia(section, id, function(){
				that.openInfo(section, id); 
			});
		});
		*/
		
		if (that.itemsOnSidebar && L.control.sidebar)
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
			that.body.find("#mainContent").height(that.body.find("#mainContent").height()+botNav.height());
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
												<span class="hidden-sm hidden-xs">'+APP.i18n.translate(section)+': </span> '+myTitle+'\
											</a>\
										</div>\
										<div id="bottomNavbarCollapse" class="navbar-collapse collapse">\
											<ul class="nav navbar-nav navbar-right">\
												<li><a href="#" class="detailsButton"><i class="icon icon-search"></i> '+APP.i18n.translate("details")+'</a></li>\
												<li><a href="#" class="closeButton"><i class="icon icon-remove"></i> '+APP.i18n.translate("close")+'</a></li>\
											</ul>\
										</div>\
									</div>\
								</nav>');
		
		/*<li><a href="#"><button type="button" class="btn btn-success detailsButton"><i class="icon icon-search"></i> '+APP.i18n.translate("details")+'</button></a></li>\
		<li><a href="#"><button type="button" class="btn btn-default closeButton"><i class="icon icon-remove"></i> '+APP.i18n.translate("close")+'</button></a></li>\*/
					
		that.navbars.bottom.find(".detailsButton").click(function(){
			var c = that.navbars.bottom.find('#bottomNavbarCollapse');
			c.collapse('hide');
			that.showInformation(section, id, function()
			{
				
			});
		});
		
		that.navbars.bottom.find(".closeButton").click(function(){
			that.currentItinerary = null;
			that.hideBottomBar();
			APP.map.showAllLayers();
			that.resetHighlightLayer();
			APP.map.setExtent(that.firstExtent);
			if (APP.utils.isset(onCloseCallback) && $.isFunction(onCloseCallback))
				onCloseCallback();
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
									<div class="col-md-4">\
										<div class="panel panel-default transportationTypes" style="display: none">\
											<div class="panel-heading">\
												<h3 class="panel-title">'+APP.i18n.translate('transportation_types')+'</h3>\
											</div>\
											<div class="panel-body">\
											</div>\
										</div>\
									</div>\
									<div class="col-md-4">\
										<div class="panel panel-default measures" style="display: none">\
											<div class="panel-heading">\
												<h3 class="panel-title">'+APP.i18n.translate('measures')+'</h3>\
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
				
		if (that.myData[section][id].media.images.length === 0)
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
			var div = $('<div class="'+voice+'"><h2>'+APP.i18n.translate(voice)+'</h2></div>');
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
													<img src="'+moreParams.values[ii][moreParams.icon]+'" class="img-responsive" style="margin-top: -3px" alt="'+moreParams.values[ii][moreParams.label]+'">\
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
						
						var span = $('<p><i class="icon icon-'+moreParams.icon+'"></i> <b>'+APP.i18n.translate(voice)+'</b>: '+that.myData[section][id].data[voice]+'m </p>');
						overviewToAppend[moreParams.voiceResult].push(span);
					}
					return;
				case "paths":
					if (that.myData[section][id].data[voice].length === 0)
						bInsert = false;
					else
					{
						$.each(that.myData[section][id].data[voice], function(i,v)
						{
							var btn = $('<button type="button" class="btn btn-link">'+that.myData.path[v].data.title+'</button>');
							btn.data("id", v).click(function(){
								var myId = $(this).data("id");
								that.highlightLayer('path', myId);
								that.zoomAt('path', myId);
								myModal.fadeOut(1500, function(){
									myModal.modal("hide");
									that.showInformation('path', myId);
								});
							});
							div.append($('<p></p>').append(btn));
						});
					}
					break;
				case "pois":
					if (that.myData[section][id].data[voice].length === 0)
						bInsert = false;
					else
					{
						$.each(that.myData[section][id].data[voice], function(i,v)
						{
							var btn = $('<button type="button" class="btn btn-link">'+that.myData.poi[v].data.title+'</button>');
							btn.data("id", v).click(function(){
								var myId = $(this).data("id");
								that.highlightLayer('poi', myId);
								that.zoomAt('poi', myId);
								myModal.fadeOut(1500, function(){
									myModal.modal("hide");
									that.showInformation('poi', myId);
								});
							});
							div.append($('<p></p>').append(btn));
						});
					}
					break;					
				case "video":
					if (APP.utils.isset(that.myData[section][id].media.videos) && (that.myData[section][id].media.videos.length > 0))
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
				checkVoice('information_url', 'text');
				checkVoice('video_poi', 'video');
				break;
			case "path":
				checkVoice('typology_id', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('typologies', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('description', 'text');
				checkVoice('length', 'ov-icon', {icon: "resize-horizontal", voiceResult: "measures"});
				checkVoice('altitude_gap', 'ov-icon', {icon: "resize-vertical", voiceResult: "measures"});
				checkVoice('path_modes', 'ov-img', {values: APP.config.localConfig.path_modes, label: 'name', icon: "icon", voiceResult: "transportationTypes"});
				checkVoice('reason', 'text');
				checkVoice('period_schedule', 'text');
				checkVoice('accessibility', 'text');
				checkVoice('information_url', 'text');
				checkVoice('video_path', 'video');
				break;
			case "itinerary":
				checkVoice('typology_id', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('typologies', 'ov-img', {values: APP.config.localConfig.typology, label: 'name', icon: "icon", voiceResult: "categories"});
				checkVoice('description', 'text');
				checkVoice('pois', 'pois');
				checkVoice('paths', 'paths');
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
			//that.body.find('.blueimp-gallery').remove();
			//that.resetHighlightLayer();
			if (APP.utils.isset(onCloseCallback) && $.isFunction(onCloseCallback))
				onCloseCallback(); 
		});
		
		that.body.append(myModal);
		myModal.modal();
	},
	
	zoomAt: function(section, id)
	{
		var that = this;		
		APP.map.globalData[APP.map.currentMapId].globalExtent = null;
		
		switch(section)
		{
			case "poi":
				var maxZoom = APP.map.globalData[APP.map.currentMapId].map.getMaxZoom();
				//var currentZoom = APP.map.globalData[APP.map.currentMapId].map.getZoom();
				var latLng = [that.myData[section][id].geo.geoJSON.coordinates[1], that.myData[section][id].geo.geoJSON.coordinates[0]];
				//APP.map.setGlobalExtent(that.myData[section][id].geo.extent);
				//APP.map.setExtent(APP.map.globalData[APP.map.currentMapId].globalExtent);
				//APP.map.globalData[APP.map.currentMapId].map.setView(latLng, maxZoom, {animate: true});
				APP.map.globalData[APP.map.currentMapId].map.panTo(latLng);
				return;
			case "path":
				APP.map.setGlobalExtent(that.myData[section][id].geo.extent);
				break;
			case "itinerary":
				$.each(that.myData[section][id].data.paths, function(i,v){
					APP.map.setGlobalExtent(that.myData["path"][v].geo.extent);
				});
				$.each(that.myData[section][id].data.pois, function(i,v){
					APP.map.setGlobalExtent(that.myData["poi"][v].geo.extent);
				});
			default:
				break;
		}
		APP.map.setExtent(APP.map.globalData[APP.map.currentMapId].globalExtent);
	},
	
	showItems: function(section, callback)
	{
		var that = this;
		if (that.previousSection === that.currentSection)
		{
			if (that.itemsOnSidebar && L.control.sidebar)
				that.mySidebar.control.toggle();
			else
				that.body.find("#modal-"+section).modal("toggle");
		}
		else
		{
			if (that.itemsOnSidebar  && L.control.sidebar)
				that.showItemsOnSidebar(section, callback);
			else
				that.showItemsOnModal(section, callback);
		}
	},
	
	showItemsOnSidebar: function(section, callback)
	{
		var that = this;
		
		that.mySidebar.div = APP.map.sidebar.div;
		that.mySidebar.div.empty();	
		
		that.mySidebar.control = APP.map.sidebar.control;
		that.mySidebar.control.on('shown', function (e) {
			that.navbars.top.find("#"+that.currentSection+"Button").parent().addClass("active");
		});
		that.mySidebar.control.show();
		
		that.mySidebar.control.on('hidden', function (e) {
			APP.config.removeActiveClasses(that.navbars.top, "li");
		});
			
		switch(section)
		{
			case "itinerary":
				var listGroup = $('<div class="list-group list-group-wo-radius" style="margin: 0px -23px 0px -23.5px; padding: -10px"></div>');
				$.each(that.myData[section], function(i,v)
				{					
					var media = $(	'<div class="media">\
									  <a class="pull-left" href="#">\
										<img class="media-object img-rounded" src="'+this.data.thumb_main_image+'" alt="'+APP.i18n.translate('no_image')+'" style="max-width: 60px; max-height: 60px">\
									  </a>\
									  <div class="media-body">\
										<h4 class="media-heading">'+this.data.name+'</h4>\
									  </div>\
									</div>');
					
					var a = $('<a id="item_'+section+'_'+v.data.id+'" href="#" class="list-group-item '+((that.currentItinerary === v.data.id)? "active" : "")+'"></a>');
					a.data(v).append(media);
					a.click(function(){
						var params = $(this).data();
						that.currentItinerary = params.data.id;
						APP.config.removeActiveClasses($(this).parents(".list-group:first"), "a");
						$(this).addClass("active");
						that.mySidebar.control.hide();
						APP.map.hideAllLayers();
						
						$.each(params.data.paths,function(j,k){
							//APP.map.addLayer(that.myData.path[v.data.id].geo.geoJSON);
							that.sendGeojsonLayerToMap(that.myData.path[k].geo, "path");
						});
						$.each(params.data.pois,function(j,k){
							//APP.map.addLayer(that.myData.poi[v.data.id].geo.geoJSON);
							that.sendGeojsonLayerToMap(that.myData.poi[k].geo, "poi");
						});
						
						that.showBottomBar(section, params.data.id, function(){
							that.mySidebar.div.find(".active").removeClass("active");
							that.mySidebar.control.hide();
							that.showItems(that.currentSection)
							/*that.navbars.top.find("li").removeClass("active");
							that.navbars.top.find("#"+that.currentSection+"Button").parent().addClass("active");*/
						});
						
						that.zoomAt(section, params.data.id);
					});
					listGroup.append(a);
				});
				that.mySidebar.div.html(listGroup);
				break;
			case "poi": case "path":
				var accordion = $('<div id="accordion-'+section+'" style="margin: 0px -23px 0px -23.5px; padding: -10px"></div>');
				
				$.each(APP.config.localConfig.typology, function()
				{
					var header = $('<h4 style="vertical-align: middle; border-radius:0px">\
										<span class="pull-left iconImage" style="margin-right: 5px"></span>\
										'+this.name+'\
										<span class="badge pull-right">0</span>\
									</h4>');
									
					var content = $('<div id="collapse_'+section+"_"+this.id+'" class="list-group list-group-wo-radius" style="padding: 0px; margin-bottom: 0px; border-radius:0px">\
										<a href="#" class="list-group-item disabled no_result">'+APP.i18n.translate("no_result")+'</a>\
									</div>');
					
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
					if (container.find(".no_result").length>0)
						container.find(".no_result").remove();
					
					var media = $(	'<div class="media">\
									  <a class="pull-left" href="#" >\
										<img class="media-object img-responsive img-rounded" src="'+(APP.utils.isset(v.data.thumb_main_image)? v.data.thumb_main_image : APP.config.localConfig.default_overview_image)+'" alt="'+APP.i18n.translate('no_image')+'" style="width: 60px; height: 60px">\
									  </a>\
									  <div class="media-body">\
										<h4 class="media-heading">\
											'+v.data.title+'\
											<span class="subtypologies pull-right"></span>\
										</h4>\
									  </div>\
									</div>');					
					
					var row = $('<a id="item_'+section+'_'+v.data.id+'" href="#" class="list-group-item"></a>');
					row.data(v).click(function(){
						that.onElementClick($(this), section, v.data.id);
					});
					
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
					
					row.append(media);
					container.append(row);
					var counter = parseInt(container.prev().find(".badge").text());
					container.prev().find(".badge").text(counter+1)
				});
				that.mySidebar.div.html(accordion);
				break;
			default:
				break;						
		}
		
		that.mySidebar.div.prepend('<h3>'+APP.i18n.translate(section)+'</h3>');
		
		that.mySidebar.div.find("#accordion-"+section).accordion({
			heightStyle: "content",
			collapsible: true,
			active: false,
		});
		
		if (APP.utils.isset(callback) && $.isFunction(callback))
			callback();
	},
	
	showItemsOnModal: function(section, callback)
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
										<img class="media-object img-rounded" src="'+this.data.thumb_main_image+'" alt="'+APP.i18n.translate('no_image')+'" style="max-width: 60px; max-height: 60px">\
									  </a>\
									  <div class="media-body">\
										<h4 class="media-heading">'+this.data.name+'</h4>\
									  </div>\
									</div>');
					
					var a = $('<a item_'+section+'_'+v.data.id+' href="#" class="list-group-item '+((that.currentItinerary === v.data.id)? "active" : "")+'"></a>');
					a.data(v).append(media);
					a.click(function(){
						var params = $(this).data();
						APP.config.removeActiveClasses($(this).parents(".list-group:first"), "a");
						$(this).addClass("active");
						that.body.find("#modal-"+section).modal("hide");
						APP.map.hideAllLayers();
						
						$.each(params.data.paths,function(j,k){
							//APP.map.addLayer(that.myData.path[v.data.id].geo.geoJSON);
							that.sendGeojsonLayerToMap(that.myData.path[k].geo, "path");
						});
						$.each(params.data.pois,function(j,k){
							//APP.map.addLayer(that.myData.poi[v.data.id].geo.geoJSON);
							that.sendGeojsonLayerToMap(that.myData.poi[k].geo, "poi");
						});
						
						that.showBottomBar(section, params.data.id, function(){
							that.body.find("#modal-"+section).find(".active").removeClass("active");
							that.body.find("#modal-"+section).modal('hide');
							that.showItems(that.currentSection);
						});
						
						that.zoomAt(section, params.data.id);
					});
					listGroup.append(a);
				});
				myModal.find(".modal-body").html(listGroup);
				break;
			case "poi": case "path":
				if (that.body.find('#accordion-'+section).length > 0)
					that.body.find('#accordion-'+section).remove();
				var accordion = $('<div class="panel-group" id="accordion-'+section+'"></div>');
				
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
											<div class="panel-body list-group list-group-wo-radius" style="padding: 0px; margin-bottom: 0px">\
												<a href="#" class="list-group-item disabled no_result">'+APP.i18n.translate("no_result")+'</a>\
											</div>\
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
					var container = accordion.find("#collapse_"+section+"_"+v.data.typology_id+" .panel-body");
					if (container.find(".no_result").length>0)
						container.find(".no_result").remove();
					
					var media = $(	'<div class="media">\
									  <a class="pull-left" href="#" >\
										<img class="media-object img-responsive img-rounded" src="'+(APP.utils.isset(v.data.thumb_main_image)? v.data.thumb_main_image : APP.config.localConfig.default_overview_image)+'" alt="'+APP.i18n.translate('no_image')+'" style="width: 60px; height: 60px">\
									  </a>\
									  <div class="media-body">\
										<h3 class="media-heading lead">\
											'+v.data.title+'\
											<span class="subtypologies pull-right row"></span>\
										</h3>\
									  </div>\
									</div>');
										
					var row = $('<a id="item_'+section+'_'+v.data.id+'" href="#" class="list-group-item"</a>');
					row.data(v).click(function(){
						that.onElementClick($(this), section, v.data.id);
					});
					
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
					
					row.append(media);
					container.append(row);
					var counter = parseInt(container.parents(".panel:first").find(".badge").text());
					container.parents(".panel:first").find(".badge").text(counter+1)
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
	
	getMedia: function(section, id, callback)
	{
		var that = this;
		
		if (!APP.utils.isset(that.myData[section]))
			that.myData[section] = {};
		
		var myUrl = (APP.utils.isset(id))? '/jx/media/'+section+'/'+id : '/jx/media/'+section;
		$.ajax({
			type: 'GET',
			url: myUrl,
			dataType: 'json',
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					if (APP.utils.isset(data.data) && APP.utils.isset(data.data.items))
					{
						$.each(data.data.items, function(i, v)
						{
							if (!APP.utils.isset(that.myData[section][v.id]))
								that.myData[section][v.id] = {};
							that.myData[section][v.id].media = v;
						});
					}
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
	
	getData: function(section, callback)
	{
		var that = this;
		
		if (!APP.utils.isset(that.myData[section]))
			that.myData[section] = {};
		
		$.ajax({
			type: 'GET',
			url: '/jx/data/'+section+'/',
			dataType: 'json',
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					if (data.data && data.data.items)
					{
						$.each(data.data.items, function(i, v)
						{
							if (!APP.utils.isset(that.myData[section][v.id]))
								that.myData[section][v.id] = {};
							that.myData[section][v.id].data = v;
						});
					}
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
	
	getGeo: function(section, callback)
	{
		var that = this;
		$.ajax({
			type: 'GET',
			url: '/jx/geo/'+section+'/',
			dataType: 'json',
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					that.myData[section] = {};
					
					$.each(data.data.items, function(i,v)
					{
						that.myData[section][v.id] = {};
						that.myData[section][v.id].geo = v;
						
						that.sendGeojsonLayerToMap(v, section);
						
						APP.map.setGlobalExtent(that.myData[section][v.id].geo.extent);
						APP.map.setExtent(APP.map.globalData[APP.map.currentMapId].globalExtent);
						that.firstExtent = APP.map.globalData[APP.map.currentMapId].globalExtent;
					});
					
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
		
		var map = APP.map.getMap();
		
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
			layer.on("click", function(){
				that.onElementClick(layer, section, v.id);
			});
			if (!APP.map.globalData[APP.map.currentMapId].map.hasLayer(layer))
				APP.map.addLayer(layer, section+"_"+v.id);
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
					layer.on("click", function()
					{
						that.onElementClick(layer, section, v.id);
					});
				}
			});
			if (!APP.map.globalData[APP.map.currentMapId].map.hasLayer(layer))
				APP.map.addLayer(layer, section+"_"+v.id);
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
	
	start: function()
	{
		var that = this;		
		var arr = [
			{
				obj: APP.config.localConfig,
				label: 'config',
			},
			{
				obj: APP.config.localConfig.default_overview_image,
				label: 'default_overview_image',
			},
			{
				obj: APP.config.localConfig.default_extent,
				label: 'default_extent',
			},
			{
				obj: APP.config.localConfig.background_layer,
				label: 'background_layer',
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
			
		$("html").css({"height":"100%","width":"100%"});
		
		that.body = $("body");
		that.body.css({"height":"100%","width":"100%","padding-top":"50px"});
		that.navbars.top = that.body.find(".navbar-nav:first"); // navbar-nav
		that.navbars.top.find("a").click(function()
		{
			var c = that.navbars.top.parents(".navbar-collapse:first");
			c.collapse('hide');
			
			if ($(this).parents("li:first").hasClass("disabled"))
				return false;
				
			that.navbars.top.find("li").removeClass("active");
			$(this).parents("li:first").addClass("active");
			
			var section = $(this).attr("id");
			section = section.split("Button")[0];
			that.previousSection = that.currentSection;
			that.currentSection = section;
			that.showItems(section);
		});
		var bt = that.body.find('#topNavbarSearch button');		
		bt.click(function(){
			var inp = that.body.find('#topNavbarSearch input');
			$.ajax({
				type: 'GET',
				url: that.searchUrl+inp.val(),
				dataType: 'json',
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, null))
					{
						
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
		
		$("#mainContent").css({"height":"100%","width":"100%","margin-bottom":"0px"});		
		
		APP.map.sidebar.div = $('<div id="leafletSidebar" style="margin-top: -60px"></div>');
		that.body.append(APP.map.sidebar.div);
		APP.map.setMap($("#mainContent"));
		
		$("#mainContent").height($("#mainContent").height());
				
		that.getData("itinerary", function(){
			that.navbars.top.find('#itineraryButton').parents("li:first").removeClass("disabled");
			that.getMedia("itinerary"); 
		});
		that.getGeo("path", function(){
			that.getData("path", function(){
				that.navbars.top.find('#pathButton').parents("li:first").removeClass("disabled");
			});
			that.getMedia("path");
		});
		that.getGeo("poi", function(){
			that.getData("poi", function(){
				that.navbars.top.find('#poiButton').parents("li:first").removeClass("disabled");
			}); 
			that.getMedia("poi");
		});
	}
});