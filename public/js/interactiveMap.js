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
									<div class="collapse navbar-collapse" id="bottomNavbarCollapse">\
										<ul class="nav navbar-nav pull-right">\
											<li><a href="#" id="info"><strong>INFO PRINCIPALI</strong></a></li>\
											<li><a href="#" id="itinerari"><strong>ITINERARI</strong></a></li>\
											<li><a href="#" id="percorsi"><strong>PERCORSI</strong></a></li>\
											<li><a href="#" id="punti"><strong>PUNTI DI INTERESSE</strong></a></li>\
										</ul>\
									</div><!-- /.navbar-collapse -->\
								</div><!-- /.container-fluid -->\
							</nav>');
		
		bottomNavbar.find(".navbar-nav a").click(function(){
			bottomNavbar.find(".navbar-nav li").removeClass("active");
			$(this).parents("li").addClass("active");
			switch($(this).attr("id"))
			{
				case "itinerari":
					
					break;
				case "punti":
					$.ajax({
						type: 'GET',
						url: '/jx/geo/poi/',
						dataType: 'json',
						success: function(data)
						{
							if (!APP.utils.checkError(data.error, null))
							{
								new L.Marker([44.160534,11.04126], {bounceOnAdd: true}).addTo(APP.map.globalData[APP.map.currentMapId].map);
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