$.extend(APP.interactiveMap, 
{
	myData: {},
	
	showInformation: function(section, id)
	{
		var that = this;
		/*
		that.getData(section, function(){
			that.getMedia(section, id, function(){
				that.openInfo(section, id); 
			});
		});
		*/
		that.openInfo(section, id); 
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
								<button type="button" class="btn-lg close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>\
								<h3 class="lead">'+that.myData[section][id].data.title+'</h3>\
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
								<div class="">\
									<div class="well pull-right overview" style="margin-left: 20px"></div>\
									<div class="paragraphes text-justify"></div>\
								</div>\
							  </div>\
							  <div class="modal-footer">\
								<button type="button" data-dismiss="modal" class="btn btn-primary">'+APP.i18n.translate('close')+'</button>\
							  </div>\
							</div>\
						</div>\
					</div>');
		
		$.each(that.myData[section][id].media.images, function(i,v)
		{
			var div = $('<div class="item">\
							<img src="'+v.image_url+'" alt="" class="img-responsive" style="width: 100%;">\
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
			
			//div.find("img").css({"width": "100%"});
			
			myModal.find(".carousel-indicators").append(indicatorLi);
			myModal.find(".carousel-inner").append(div);
		});
		
		var parToAppend = [];
		var overviewToAppend = {};
		var checkVoice = function(voice, type, moreParams)
		{
			var div = $('<div class="'+voice+'"><h2 class="text-capitalize">'+APP.i18n.translate(voice)+'</h2></div>');
			switch(type)
			{
				case "text":
					if (APP.utils.isset(that.myData[section][id].data[voice]) && !APP.utils.isEmptyString(that.myData[section][id].data[voice]))
						div.append(that.myData[section][id].data[voice]);
					else
						div.append('<p><em>'+APP.i18n.translate("no_content")+'</em></p>');
					break;
				case "fk":
					if (APP.utils.isset(that.myData[section][id].data[voice]) && APP.utils.isset(moreParams) && $.isArray(moreParams.values))
					{
						if (!APP.utils.isset(overviewToAppend[voice]))
							overviewToAppend[voice] = [];
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
								var img  = $('<img src="'+moreParams.values[ii][moreParams.icon]+'" class="img-responsive" style="margin-top: -14px" alt="'+moreParams.values[ii][moreParams.label]+'">');
								img.tooltip({container: 'body', placement: 'auto', title: moreParams.values[ii][moreParams.label]});
								overviewToAppend[voice].push(img);
							}
						});
						if (arr.length === 0)
							div.append('<p><em>'+APP.i18n.translate("no_content")+'</em></p>');
					}
					else
						div.append('<p><em>'+APP.i18n.translate("no_content")+'</em></p>');
					return;
				case "video":
					if (APP.utils.isset(that.myData[section][id].data[voice]) && !APP.utils.isEmptyString(that.myData[section][id].data[voice]))
						div.append(that.myData[section][id].data[voice]);
					else
						div.append('<p><em>'+APP.i18n.translate("no_content")+'</em></p>');
					break;
				default:
					break;
			}
			parToAppend.push(div);
		};
		
		switch(section)
		{
			case "poi":
				checkVoice('typology_id', 'fk', {values: APP.config.localConfig.typology, label: 'name', icon: "icon"});
				checkVoice('typologies', 'fk', {values: APP.config.localConfig.typology, label: 'name', icon: "icon"});
				checkVoice('description', 'text');
				checkVoice('reason', 'text');
				checkVoice('period_schedule', 'text');
				checkVoice('accessibility', 'text');
				checkVoice('information_url', 'text');
				break;
			case "path":
				checkVoice('typology_id', 'fk', {values: APP.config.localConfig.typology, label: 'name', icon: "icon"});
				checkVoice('typologies', 'fk', {values: APP.config.localConfig.typology, label: 'name', icon: "icon"});
				checkVoice('description', 'text');
				checkVoice('length', 'text');
				checkVoice('altitude_gap', 'text');
				checkVoice('reason', 'text');
				checkVoice('period_schedule', 'text');
				checkVoice('accessibility', 'text');
				checkVoice('information_url', 'text');
				checkVoice('video_path', 'video');
				break;
			default:
				break;
		}
		$.each(parToAppend, function(){
			myModal.find(".modal-body .paragraphes").append(this);
		});
		$.each(overviewToAppend, function(key, value){
			var l = value.length;
			if (l === 0)
				return true;
			var size = parseInt(12/l);
			myModal.find(".modal-body .overview").append('<p style="margin-top: 5px"><b>'+APP.i18n.translate(key)+':</b></p>');
			var row = $('<div class="row"></div>');
			$.each(value, function(k1,v1){
				var col = $('<div class="col-md-'+size+'"></div>');
				col.append(v1);
				row.append(col);
			});
			myModal.find(".modal-body .overview").append(row);
		});
		//myModal.find(".modal-body .overview").append(overview);
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
				var latLng = [that.myData[section][id].geo.geoJSON.coordinates[1], that.myData[section][id].geo.geoJSON.coordinates[0]];
				APP.map.globalData[APP.map.currentMapId].map.setView(latLng, maxZoom, {animate: true});
				break;
			case "path": case "itinerary":
				APP.map.setExtent(that.myData[section][id].geo.extent);
				break;
			default:
				break;
		}		
	},
	
	showItems: function(section, callback)
	{
		var that = this;
		var myModal = $("body").find("#modal-"+section);
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
			APP.config.removeActiveClasses($('#bottomNavbarCollapse').find("ul"), "li");
		});
		
		$("body").append(myModal);					
			
		switch(section)
		{
			case "itinerary":
				$("body").find('.modal-body').html('<div class="list-group list-group-wo-radius"></div>');
				$.each(that.myData[section], function()
				{					
					var media = $(	'<div class="media">\
									  <a class="pull-left" href="#">\
										<img class="media-object img-rounded" src="'+this.data.thumb_main_image+'" alt="'+APP.i18n.translate('no_image')+'" style="max-width: 60px; max-height: 60px">\
									  </a>\
									  <div class="media-body">\
										<h4 class="media-heading lead">'+this.data.name+'</h4>\
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
				$.each(that.myData[section], function(i, v)
				{				
					var container = accordion.find("#collapse_"+section+"_"+v.data.typology_id+" .panel-body");
					if (container.find(".no_result").length>0)
						container.find(".no_result").remove();
					
					var media = $(	'<div class="media">\
									  <a class="pull-left" href="#" >\
										<img class="media-object img-responsive img-rounded" src="'+v.data.thumb_main_image+'" alt="'+APP.i18n.translate('no_image')+'" style="width: 60px; height: 60px">\
									  </a>\
									  <div class="media-body">\
										<h3 class="media-heading lead">'+v.data.title+'<span class="subtypologies pull-right row"></span></h3>\
									  </div>\
									</div>');
					
					var row = $('<a href="#" class="list-group-item"</a>');
					row.click(function(){
						myModal.modal("hide");
						that.showInformation(section, v.data.id);
						that.zoomAt(section, v.data.id);
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
				$("body").find('.modal-body').html(accordion);
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
	
	start: function()
	{
		var that = this;		
		
		if (!APP.utils.isset(APP.config.localConfig) || 
			!APP.utils.isset(APP.config.localConfig.default_extent) || 
			!APP.utils.isset(APP.config.localConfig.background_layer) || 
			!APP.utils.isset(APP.config.localConfig.typology) ||
			!APP.utils.isset(APP.config.localConfig.urls)
		)
		{
			APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("config_not_configured")});
			return;
		}
		
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
											<li class="disabled"><a href="#" id="itinerari"><strong>ITINERARI</strong></a></li>\
											<li class="disabled"><a href="#" id="percorsi"><strong>PERCORSI</strong></a></li>\
											<li class="disabled"><a href="#" id="punti"><strong>PUNTI DI INTERESSE</strong></a></li>\
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
					that.showItems("itinerary");
					break;
				case "punti":
					that.showItems("poi");
					break;
				case "percorsi":
					that.showItems("path");
					break;
				default:
					break;
			}
		});
		
		$("body").append(bottomNavbar);
		$("#mainContent").css({"height":"100%","width":"100%","margin-bottom":"0px"});
		
		APP.map.setMap($("#mainContent"));
		$("#mainContent").height($("#mainContent").height());
				
		that.getData("itinerary", function(){
			$("body").find('#bottomNavbarCollapse #itinerari').parents("li:first").removeClass("disabled");
			that.getMedia("itinerary"); 
		}); 
		that.getGeo("poi", function(){
			that.getData("poi", function(){
				$("body").find('#bottomNavbarCollapse #punti').parents("li:first").removeClass("disabled");
			}); 
			that.getMedia("poi");
		});
		that.getGeo("path", function(){
			that.getData("path", function(){
				$("body").find('#bottomNavbarCollapse #percorsi').parents("li:first").removeClass("disabled");
			});
			that.getMedia("path");
		});
	}
});