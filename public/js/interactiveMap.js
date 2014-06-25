$.extend(APP.interactiveMap, 
{
	getPoints: function()
	{
		
	},
	
	getItinerary: function()
	{
		
	},
	
	start: function()
	{
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
		
		bottomNavbar.find(".navbar-nav a").click(function(){
			//APP.map.removeAllLayers();
			bottomNavbar.find(".navbar-nav li").removeClass("active");
			$(this).parents("li").addClass("active");
			switch($(this).attr("id"))
			{
				case "itinerari":
					var section = "itinerary";
					$.ajax({
						type: 'GET',
						url: '/jx/data/'+section+'/',
						dataType: 'json',
						success: function(data)
						{
							if (!APP.utils.checkError(data.error, null))
							{
								var myModal = $("#mainContent").find("#modal-"+section);
								if (myModal.length === 0)
								{
									myModal = $('<div id="modal-'+section+'" class="modal fade" tabindex="-1">\
														  <div class="modal-header">\
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
															<h3>'+APP.i18n.translate(section+"_list")+'</h3>\
														  </div>\
														  <div class="modal-body">\
															<div class="list-group"></div>\
														  </div>\
														  <div class="modal-footer">\
															<button type="button" data-dismiss="modal" class="btn btn-default">'+APP.i18n.translate('close')+'</button>\
														  </div>\
														</div>');
									
									$("#mainContent").append(myModal);
								}
								else
									myModal.find(".modal-body .list-group").empty();
								
								$.each(data.data.items, function(){
								var media = $(	'<div class="media">\
												  <a class="pull-left" href="#">\
													<img class="media-object img-rounded" src="'+this.thumb_main_image+'" alt="" style="width: 60px; height: 60px">\
												  </a>\
												  <div class="media-body">\
													<h4 class="media-heading">'+this.name+'</h4>\
												  </div>\
												</div>');
								
									var a = $('<a href="#" class="list-group-item"></a>');
									a.append(media);
									myModal.find(".modal-body .list-group").append(a);
								});
								
								myModal.modal("show");
								
								/*
								bootbox.dialog({
								  message: "Qui ci sar&agrave; la Lista degli itinerari",
								  title: "Qui ci sar&agrave; il titolo",
								  className: "container",
								  onEscape: true,
								  buttons: {
									'Close': {
									  className: "btn-default",
									  callback: function() {}
									},
								  },
								  closeButton: true,
								});
								*/
							}
							else
								APP.utils.showErrMsg(data);
						},
						error: function(result)
						{
							APP.utils.showErrMsg(result);
						}
					});
					break;
				case "punti":
					var section = "poi";
					$.ajax({
						type: 'GET',
						url: '/jx/data/'+section+'/',
						dataType: 'json',
						success: function(data)
						{
							if (!APP.utils.checkError(data.error, null))
							{
								var myModal = $("#mainContent").find("#modal-"+section);
								if (myModal.length === 0)
								{
									myModal = $('<div id="modal-'+section+'" class="modal fade" tabindex="-1">\
														  <div class="modal-header">\
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
															<h3>'+APP.i18n.translate(section+"_list")+'</h3>\
														  </div>\
														  <div class="modal-body">\
															<div class="list-group"></div>\
														  </div>\
														  <div class="modal-footer">\
															<button type="button" data-dismiss="modal" class="btn btn-default">'+APP.i18n.translate('close')+'</button>\
														  </div>\
														</div>');
									
									$("#mainContent").append(myModal);
								}
								else
									myModal.find(".modal-body .list-group").empty();
								
								$.each(data.data.items, function(){
								var media = $(	'<div class="media">\
												  <a class="pull-left" href="#">\
													<img class="media-object img-rounded" src="'+this.thumb_main_image+'" alt="" style="width: 60px; height: 60px">\
												  </a>\
												  <div class="media-body">\
													<h4 class="media-heading">'+this.title+'</h4>\
												  </div>\
												</div>');
								
									var a = $('<a href="#" class="list-group-item"></a>');
									a.append(media);
									myModal.find(".modal-body .list-group").append(a);
								});
								
								myModal.modal("show");
								
								/*
								bootbox.dialog({
								  message: "Qui ci sar&agrave; la Lista degli itinerari",
								  title: "Qui ci sar&agrave; il titolo",
								  className: "container",
								  onEscape: true,
								  buttons: {
									'Close': {
									  className: "btn-default",
									  callback: function() {}
									},
								  },
								  closeButton: true,
								});
								*/
							}
							else
								APP.utils.showErrMsg(data);
						},
						error: function(result)
						{
							APP.utils.showErrMsg(result);
						}
					});
					/*
					$.ajax({
						type: 'GET',
						url: '/jx/data/poi/',
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
										new L.geoJson(v.geoJSON).addTo(APP.map.globalData[APP.map.currentMapId].map);
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
					*/
					break;
				case "percorsi":
					var section = "path";
					$.ajax({
						type: 'GET',
						url: '/jx/data/'+section+'/',
						dataType: 'json',
						success: function(data)
						{
							if (!APP.utils.checkError(data.error, null))
							{
								var myModal = $("#mainContent").find("#modal-"+section);
								if (myModal.length === 0)
								{
									myModal = $('<div id="modal-'+section+'" class="modal fade" tabindex="-1">\
														  <div class="modal-header">\
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
															<h3>'+APP.i18n.translate(section+"_list")+'</h3>\
														  </div>\
														  <div class="modal-body">\
															<div class="list-group"></div>\
														  </div>\
														  <div class="modal-footer">\
															<button type="button" data-dismiss="modal" class="btn btn-default">'+APP.i18n.translate('close')+'</button>\
														  </div>\
														</div>');
									
									$("#mainContent").append(myModal);
								}
								else
									myModal.find(".modal-body .list-group").empty();
								
								$.each(data.data.items, function(){
								var media = $(	'<div class="media">\
												  <a class="pull-left" href="#">\
													<img class="media-object img-rounded" src="'+this.thumb_main_image+'" alt="" style="width: 60px; height: 60px">\
												  </a>\
												  <div class="media-body">\
													<h4 class="media-heading">'+this.title+'</h4>\
												  </div>\
												</div>');
								
									var a = $('<a href="#" class="list-group-item"></a>');
									a.append(media);
									myModal.find(".modal-body .list-group").append(a);
								});
								
								myModal.modal("show");
								
								/*
								bootbox.dialog({
								  message: "Qui ci sar&agrave; la Lista degli itinerari",
								  title: "Qui ci sar&agrave; il titolo",
								  className: "container",
								  onEscape: true,
								  buttons: {
									'Close': {
									  className: "btn-default",
									  callback: function() {}
									},
								  },
								  closeButton: true,
								});
								*/
							}
							else
								APP.utils.showErrMsg(data);
						},
						error: function(result)
						{
							APP.utils.showErrMsg(result);
						}
					});
				
					/*
					$.ajax({
						type: 'GET',
						url: '/jx/data/path/',
						dataType: 'json',
						success: function(data)
						{
							if (!APP.utils.checkError(data.error, null))
							{
								
								bootbox.dialog({
								  message: "Qui ci sar&agrave; la Lista dei percorsi",
								  title: "Qui ci sar&agrave; il titolo",
								  onEscape: true,
								  buttons: {
									'Close': {
									  className: "btn-default",
									  callback: function() {}
									},
								  },
								  closeButton: true,
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
					*/
					break;
				default:
					break;
			}
		});
		
		$("body").append(bottomNavbar);
		$("#mainContent").css({"height":"100%","width":"100%","margin-bottom":"0px"});
		
		
		/*$("#mainContent").append('<div>\
									<div id="ac_background" class="ac_background">\
										<img class="ac_bgimage" src="/public/modules/AnimatedContentMenu/images/Padule.jpg" alt="Background"/>\
										<div class="ac_overlay"></div>\
										<div class="ac_loading"></div>\
									</div>\
									<div id="ac_content" class="ac_content">\
										<div class="ac_menu">\
											<ul>\
												<li>\
													<a href="/public/modules/AnimatedContentMenu/images/Itinerari.jpg">Itinerari</a>\
													<div class="ac_subitem">\
														<span class="ac_close"></span>\
														<h2>Itinerari</h2>\
														<ul>\
															<li>Seleziona un itinerario</li>\
															<li>La cicogna rosa</li>\
															<li>Alla ricerca der tarpone bianco!</li>\
														</ul>\
													</div>\
												</li>\
											</ul>\
										</div>\
									</div>\
								</div>');*/
								
		APP.map.setMap($("#mainContent"));
		$("#mainContent").height($("#mainContent").height());
	}
});