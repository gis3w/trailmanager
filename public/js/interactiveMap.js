$.extend(APP.interactiveMap, 
{
	zoomAt: function(section, id)
	{
		
	},
	
	getMedia: function()
	{
		
	},
	
	getGeo: function(section)
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
					$.each(data.data.items, function(i,v)
					{
						if (v.geoJSON.type === "Point")
						{
							var coords = [v.geoJSON.coordinates[1],v.geoJSON.coordinates[0]];
							new L.Marker(coords,{bounceOnAdd: true}).addTo(APP.map.globalData[APP.map.currentMapId].map);
						}
						else
						{
							new L.geoJson(v.geoJSON, {
								style: function (feature) {
									return {color: v.color};
								},
								onEachFeature: function (feature, layer) {
									//layer.bindPopup(feature.properties.description);
								}
							}).addTo(APP.map.globalData[APP.map.currentMapId].map);
						}
					});
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
	
	getData: function(section)
	{
		var that = this;
		$.ajax({
			type: 'GET',
			url: '/jx/data/'+section+'/',
			dataType: 'json',
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					var myModal = $("body").find("#modal-"+section);
					if (myModal.length === 0)
					{
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
						
						$("body").append(myModal);
					}
					else
						myModal.find(".modal-body").empty();
						
					switch(section)
					{
						case "itinerary":
							$("body").find('.modal-body').html('<div class="list-group"></div>');
							$.each(data.data.items, function()
							{
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
							var accordion = $('<div class="panel-group" id="accordion-'+section+'"></div>');
							
							$.each(APP.config.localConfig.typology, function()
							{
								var panel = $(	'<div class="panel panel-default">\
													<div class="panel-heading">\
														<span class="glyphicon glyphicon-chevron-right pull-left" style="margin-right: 5px"></span>\
														<h4 class="panel-title">\
															<a data-toggle="collapse" data-parent="#accordion-'+section+'" href="#collapse_'+section+"_"+this.id+'">\
																'+this.name+'\
															</a>\
														</h4>\
													</div>\
													<div id="collapse_'+section+"_"+this.id+'" class="panel-collapse collapse">\
														<div class="panel-body container-fluid" style="padding: 0px">\
														</div>\
													</div>\
												</div>');
								
								panel.find('.collapse').collapse({toggle: false});
								
								accordion.append(panel);
							});
							$.each(data.data.items, function(i, v)
							{
								var container = accordion.find("#collapse_"+section+"_"+this.typology_id+" .container-fluid");
								
								var media = $(	'<div class="media">\
												  <a class="pull-left" href="#">\
													<img class="media-object img-rounded" src="'+this.thumb_main_image+'" alt="'+APP.i18n.translate('no_image')+'" style="width: 60px; height: 60px">\
												  </a>\
												  <div class="media-body">\
													<h4 class="media-heading">'+this.title+'</h4>\
												  </div>\
												</div>');
								
								var row = $('<div class="row" style="margin: 5px"></div>');
								row.click(function(){
									that.zoomAt(section, v.id);
								});
								row.append(media);
								container.append(row);
								if (!container.parent().hasClass("in"))
									container.parent().addClass("in");
							});
							$("body").find('.modal-body').html(accordion);
							break;
						default:
							break;						
					}
					
					myModal.modal();
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