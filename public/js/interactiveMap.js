$.extend(APP.interactiveMap, 
{
	myData: {},
	
	showInformation: function(section, id)
	{
		var that = this;
		that.getMedia(section, id, function(sezione, identificativo){ that.openInfo(sezione, identificativo); });
	},
	
	openInfo: function(section, id)
	{
		var that = this;
		var myModal = $("body").find("#modal-"+section+"-info");
		if (myModal.length > 0)
			myModal.remove();
		
		myModal = $('<div id="modal-'+section+'-info" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="'+section+'" aria-hidden="true">\
						<div class="modal-dialog modal-lg">\
							<div class="modal-content">\
							  <div class="modal-header">\
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
								<h3 class="lead">'+that.myData[section][id].title+'</h3>\
							  </div>\
							  <div class="modal-body">\
								<div id="carousel-'+section+'-info" style="margin: -14px -15px 20px -15px;" class="carousel slide" data-ride="carousel">\
								  <!-- Indicators -->\
								  <ol class="carousel-indicators"></ol>\
								  <!-- Wrapper for slides -->\
								  <div class="carousel-inner"></div>\
								  <!-- Controls -->\
								  <a class="left carousel-control" href="#carousel-'+section+'-info" role="button" data-slide="prev">\
									<span class="glyphicon glyphicon-chevron-left"></span>\
								  </a>\
								  <a class="right carousel-control" href="#carousel-'+section+'-info" role="button" data-slide="next">\
									<span class="glyphicon glyphicon-chevron-right"></span>\
								  </a>\
								</div>\
								<div class="description"></div>\
							  </div>\
							  <div class="modal-footer">\
								<button type="button" data-dismiss="modal" class="btn btn-default">'+APP.i18n.translate('close')+'</button>\
							  </div>\
							</div>\
						</div>\
					</div>');
		
		$.each(that.myData[section][id].media.images, function(i,v)
		{
			var div = $('<div class="item">\
							<img src="'+v.image_url+'" alt="" class="img-responsive" style="width: 100%; height: 220px">\
							<div class="carousel-caption">\
								<h3>'+v.description+'</h3>\
							</div>\
						</div>');
			
			var indicatorLi = $('<li data-target="#carousel-'+section+'-info" data-slide-to="'+i+'"></li>');
			if (i === 0)
			{
				div.addClass("active");
				indicatorLi.addClass("active");
			}
			
			div.find("img").css({"width": "100%", "height": 220});
			
			myModal.find(".carousel-indicators").append(indicatorLi);
			myModal.find(".carousel-inner").append(div);
		});
		
		myModal.find(".modal-body .description").append(that.myData[section][id].description);
		
		$("body").append(myModal);
		
		myModal.modal();
	},
	
	zoomAt: function(section, id)
	{
		var that = this;		
		
		switch(section)
		{
			case "poi":
				var maxZoom = APP.map.globalData[APP.map.currentMapId].map.getMaxZoom();
				var latLng = [that.myData[section][id].geoJSON.coordinates[1], that.myData[section][id].geoJSON.coordinates[0]];
				APP.map.globalData[APP.map.currentMapId].map.setView(latLng, maxZoom, {animate: true});
				break;
			case "path": case "itinerary":
				APP.map.setExtent(that.myData[section][id].extent);
				break;
			default:
				break;
		}		
	},
	
	getMedia: function(section, id, callback)
	{
		var that = this;
		$.ajax({
			type: 'GET',
			url: '/jx/media/'+section+'/'+id,
			dataType: 'json',
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					if (APP.utils.isset(data.data) && APP.utils.isset(data.data.items) && data.data.items.length === 1)
						that.myData[section][id].media = data.data.items[0];
					if (APP.utils.isset(callback) && $.isFunction(callback))
						callback(section, id);
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
					var myModal = $("body").find("#modal-"+section);
					if (myModal.length > 0)
						myModal.remove();
					
					myModal = $('<div id="modal-'+section+'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="'+section+'" aria-hidden="true">\
									<div class="modal-dialog">\
										<div class="modal-content">\
										  <div class="modal-header">\
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
											<h3 class="lead">'+APP.i18n.translate(section+"_list")+'</h3>\
										  </div>\
										  <div class="modal-body">\
										  </div>\
										  <div class="modal-footer">\
											<button type="button" data-dismiss="modal" class="btn btn-default">'+APP.i18n.translate('close')+'</button>\
										  </div>\
										</div>\
									</div>\
								</div>');
					
					myModal.on('hidden.bs.modal', function (e) {
						APP.config.removeActiveClasses($('#bottomNavbarCollapse').find("ul"), "li");
					});
					
					$("body").append(myModal);
					
						
					switch(section)
					{
						case "itinerary":
							$("body").find('.modal-body').html('<div class="list-group list-group-wo-radius"></div>');
							$.each(data.data.items, function()
							{
								$.extend(that.myData[section][this.id], this);
								
								var media = $(	'<div class="media">\
												  <a class="pull-left" href="#">\
													<img class="media-object img-rounded" src="'+this.thumb_main_image+'" alt="'+APP.i18n.translate('no_image')+'" style="max-width: 60px; max-height: 60px">\
												  </a>\
												  <div class="media-body">\
													<h4 class="media-heading lead">'+this.name+'</h4>\
												  </div>\
												</div>');
								
								var a = $('<a href="#" class="list-group-item"></a>');
								a.append(media);
								myModal.find(".modal-body .list-group").append(a);
							});
							break;
						case "poi": case "path":
							if ($("body").find('#accordion-'+section).length > 0)
								$("body").find('#accordion-'+section).remove();
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
									var iconImage = $('<img src="'+this.icon+'" class="img-responsive" alt="" style="margin-top: -14px; max-height: 30px; max-width: 35px;">');
								
								panel.find(".panel-title .iconImage").html(iconImage);
								
								panel.find('.collapse').collapse({toggle: false});
								
								accordion.append(panel);
							});
							$.each(data.data.items, function(i, v)
							{
								$.extend(that.myData[section][v.id], v);
							
								var container = accordion.find("#collapse_"+section+"_"+this.typology_id+" .panel-body");
								if (container.find(".no_result").length>0)
									container.find(".no_result").remove();
								
								var media = $(	'<div class="media">\
												  <a class="pull-left" href="#" >\
													<img class="media-object img-responsive img-rounded" src="'+this.thumb_main_image+'" alt="'+APP.i18n.translate('no_image')+'" style="width: 60px; height: 60px">\
												  </a>\
												  <div class="media-body">\
													<h3 class="media-heading lead">'+this.title+'<span class="subtypologies pull-right row"></span></h3>\
												  </div>\
												</div>');
								
								var row = $('<a href="#" class="list-group-item"</a>');
								row.click(function(){
									myModal.modal("hide");
									//that.showInformation(section, v.id);
									that.zoomAt(section, v.id);
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
								if (!container.parent().hasClass("in"))
									container.parent().addClass("in");
								var counter = parseInt(container.parents(".panel:first").find(".badge").text());
								container.parents(".panel:first").find(".badge").text(counter+1)
							});
							$("body").find('.modal-body').html(accordion);
							break;
						default:
							break;						
					}
					
					myModal.modal();
					
					if (APP.utils.isset(callback) && $.isFunction(callback))
						callback(section);
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
						that.myData[section][v.id] = v;
						if (v.geoJSON.type === "Point")
						{
							var coords = [v.geoJSON.coordinates[1],v.geoJSON.coordinates[0]];
							
							var myIcon = null;
							var myIndex = APP.utils.getIndexFromField(APP.config.localConfig.typology, "id", v.typology_id);
							if (myIndex > -1)
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
							
							var myObj = {bounceOnAdd: true};
							if (myIcon)
								myObj.icon = myIcon;
							
							new L.Marker(coords,myObj)
								.on("click", function(){ that.showInformation(section, v.id); })
								.addTo(APP.map.globalData[APP.map.currentMapId].map);
						}
						else
						{
							new L.geoJson(v.geoJSON, {
								style: function (feature) {
									var oo = {};
									if (v.color)
										oo.color = v.color;
									oo.weight = APP.utils.isset(v.width)? v.width : 7;
										
									return oo;
								},
								onEachFeature: function (feature, layer) {
									layer.on("click", function(){ that.showInformation(section, v.id); });
								}
							}).addTo(APP.map.globalData[APP.map.currentMapId].map);
						}
					});
					
					if (APP.utils.isset(callback) && $.isFunction(callback))
						callback(section);
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
	
	start: function()
	{
		var that = this;
		
		$("html").css({"height":"100%","width":"100%"});
		$("body").css({"height":"100%","width":"100%","padding-top":"50px", "padding-bottom":"50px"});
		
		var bottomNavbar = $('<nav class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">\
								<div class="container-fluid">\
									<!-- Brand and toggle get grouped for better mobile display -->\
									<div class="navbar-header">\
										<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bottomNavbarCollapse">\
											<span class="sr-only">Toggle navigation</span>\
											<span class="icon-bar"></span>\
											<span class="icon-bar"></span>\
											<span class="icon-bar"></span>\
										</button>\
									</div>\
									<!-- Collect the nav links, forms, and other content for toggling -->\
									<div class="collapse navbar-collapse " id="bottomNavbarCollapse">\
										<ul class="nav navbar-nav text-center">\
											<li><a href="#" id="info"><strong>INFO PRINCIPALI</strong></a></li>\
											<li><a href="#" id="itinerari"><strong>ITINERARI</strong></a></li>\
											<li><a href="#" id="percorsi"><strong>PERCORSI</strong></a></li>\
											<li><a href="#" id="punti"><strong>PUNTI DI INTERESSE</strong></a></li>\
										</ul>\
									</div><!-- /.navbar-collapse -->\
								</div><!-- /.container-fluid -->\
							</nav>');
		
		bottomNavbar.find(".navbar-nav a").click(function()
		{
			//APP.map.removeAllLayers();
			bottomNavbar.find(".navbar-nav li").removeClass("active");
			$(this).parents("li").addClass("active");
			bottomNavbar.find('.navbar-collapse').collapse('hide');
			
			switch($(this).attr("id"))
			{
				case "itinerari":
					that.getData("itinerary");
					break;
				case "punti":
					that.getData("poi");
					break;
				case "percorsi":
					that.getData("path");
					break;
				default:
					break;
			}
		});
		
		$("body").append(bottomNavbar);
		$("#mainContent").css({"height":"100%","width":"100%","margin-bottom":"0px"});
		
		APP.map.setMap($("#mainContent"));
		$("#mainContent").height($("#mainContent").height());
		
		that.getGeo("poi");
		that.getGeo("path");
	}
});