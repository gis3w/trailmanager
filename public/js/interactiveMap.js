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
								<h3>'+that.myData[section][id].title+'</h3>\
							  </div>\
							  <div class="modal-body">\
								<div id="carousel-example-generic" style="margin: -14px -15px 20px -15px;" class="carousel slide" data-ride="carousel">\
								  <!-- Indicators -->\
								  <ol class="carousel-indicators"></ol>\
								  <!-- Wrapper for slides -->\
								  <div class="carousel-inner"></div>\
								  <!-- Controls -->\
								  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">\
									<span class="glyphicon glyphicon-chevron-left"></span>\
								  </a>\
								  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">\
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
							<img src="'+v.image_url+'" alt="" style="width: 100%; height: 220px">\
							<div class="carousel-caption">\
								<h3>'+v.description+'</h3>\
							</div>\
						</div>');
			
			var indicatorLi = $('<li data-target="#carousel-example-generic" data-slide-to="'+i+'"></li>');
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
							new L.Marker(coords,{bounceOnAdd: true})
								.on("click", function(){ that.showInformation(section, v.id); })
								.addTo(APP.map.globalData[APP.map.currentMapId].map);
						}
						else
						{
							new L.geoJson(v.geoJSON, {
								style: function (feature) {
									return {color: v.color};
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
											<h3>'+APP.i18n.translate(section+"_list")+'</h3>\
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
							$("body").find('.modal-body').html('<div class="list-group"></div>');
							$.each(data.data.items, function()
							{
								$.extend(that.myData[section][this.id], this);
								
								var media = $(	'<div class="media">\
												  <a class="pull-left" href="#">\
													<img class="media-object img-rounded" src="'+this.thumb_main_image+'" alt="'+APP.i18n.translate('no_image')+'" style="width: 60px; height: 60px">\
												  </a>\
												  <div class="media-body">\
													<h4 class="media-heading">'+this.name+'</h4>\
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
														<span class="glyphicon glyphicon-chevron-right pull-left" style="margin-right: 5px"></span>\
														<span class="badge pull-right" style="">0</span>\
														<h4 class="panel-title">\
															<a data-toggle="collapse" data-parent="#accordion-'+section+'" href="#collapse_'+section+"_"+this.id+'">\
																'+this.name+'\
															</a>\
														</h4>\
													</div>\
													<div id="collapse_'+section+"_"+this.id+'" class="panel-collapse collapse">\
														<div class="panel-body" style="padding: 0px">\
															<div class="row no_result" style="margin-left: 15px">'+APP.i18n.translate("no_result")+'</div>\
														</div>\
													</div>\
												</div>');
								
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
												  <a class="pull-left" href="#">\
													<img class="media-object img-rounded" src="'+this.thumb_main_image+'" alt="'+APP.i18n.translate('no_image')+'" style="width: 60px; height: 60px">\
												  </a>\
												  <div class="media-body">\
													<h4 class="media-heading">'+this.title+'</h4>\
													<div class="subtypologies"></div>\
												  </div>\
												</div>');
								
								var row = $('<div class="row" style="margin: 5px; cursor: pointer"></div>');
								row.click(function(){
									$(this).css("background-color","#428BCA");
									myModal.modal("hide");
									//that.showInformation(section, v.id);
									that.zoomAt(section, v.id);
								});
								
								if (v.typologies)
								{
									$.each(v.typologies, function(ii,vv){
										var index = APP.utils.getIndexFromField(APP.config.localConfig.typology, "id", vv);
										if (index > -1 && APP.utils.isset(APP.config.localConfig.typology[index].icon))
											media.find(".subtypologies").append('<span class="fa fa-'+APP.config.localConfig.typology[index].icon+'"></span>');
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