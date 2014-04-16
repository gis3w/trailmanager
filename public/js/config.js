$.extend(APP.config,{
	localConfig: {},
	currentConfigSection: null,
	default_iDisplayLength: 10,
	breadCrumb: [],
	fadeInDelay: 400,
	fadeOutDelay: 100,
	periodicRequestsIds: [],
	serverSide: false,
	
	bc_getLastSrcElement: function()
	{
		return (this.breadCrumb.length-1 >= 0)? this.breadCrumb[this.breadCrumb.length-1].srcElement : null;
	},
	
	xhrObj: 
	{
		queue: [],
		init: function()
		{
			$.ajaxSetup({
				cache:  false,/*
				ajaxStart: function(jqXHR)
				{
					APP.config.xhrObj.queue.push(jqXHR);
				},
				ajaxSend: function()
				{
					APP.utils.toggleLoadingImage(true);
				},
				ajaxComplete: function(jqXHR)
				{
					
				},
				ajaxStop: function(jqXHR)
				{
					APP.utils.toggleLoadingImage(false);
					var index = APP.config.xhrObj.queue.indexOf(jqXHR);
					if (index > -1)
						APP.config.xhrObj.queue.splice(index, 1);
				}
				*/
			});
			
			$(document)
			.bind("ajaxStart", function(jqXHR)
			{
				APP.config.xhrObj.queue.push(jqXHR);
			})
			.bind("ajaxSend", function()
			{
				APP.utils.toggleLoadingImage(true);
			})
			.bind("ajaxComplete", function(jqXHR)
			{
				
			})
			.bind("ajaxStop", function(jqXHR)
			{
				APP.utils.toggleLoadingImage(false);
				var index = APP.config.xhrObj.queue.indexOf(jqXHR);
				if (index > -1)
					APP.config.xhrObj.queue.splice(index, 1);
			});
		},
		abortAll: function()
		{
			var that = this;
			$.each(APP.config.xhrObj.queue, function(idx, jqXHR)
			{
				
				if (APP.utils.isset(jqXHR) && $.isFunction(jqXHR.abort))
					jqXHR.abort();
			});
			APP.config.xhrObj.queue = [];
		}
	},
	
	getToken: function(ds)
	{
		var that = this;
		ds = (APP.utils.isset(ds) && ds !== "")? "?datastruct="+ds : "";
		var token = "";
		$.ajax({
			type: 'GET',
			url: APP.config.localConfig.urls["token"]+ds,
			dataType: 'json',
			async: false,
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					token = data.data.token;
					//console.log("token ricevuto: "+that.csrf_token);
				}
				else
					APP.utils.showErrMsg(data);
			},
			error: function(result)
			{
				APP.utils.showErrMsg(result);
			}
		});
		return token;
	},
	
	getValue: function(obj, stringTemplate, params)
	{
		$.each(params, function(i, v)
		{
			stringTemplate = obj.hasOwnProperty(v)? stringTemplate.replace(i,obj[v]) : stringTemplate.replace(i,"");
		});
		
		return stringTemplate;
	},
	
	createTabs: function(div, menu)
	{
		var that = this;
		var tabs = menu.items;
		if (!APP.utils.isset(div) || !APP.utils.isset(tabs))
			return;
		var ul = $('<ul class="nav nav-tabs"></ul>');
		var counter = 0;
		$.each(tabs, function(i, v)
		{
			var active = (counter === 0)? 'class="active"' : "";
			var li = $('<li id="'+v.id+'" '+active+'><a href="#"><i class="icon icon-'+v.icon+'"></i> '+v.name+'</a></li>');
			li.data({"data": v, "label": i});
			li.click(function()
			{
				var item = $(this);
				that.removeActiveClasses($(".nav-tabs"), "li");
				item.addClass("active");
				var dataItem = item.data();
				div.find("#tabsContent").empty();
				if (!APP.utils.isset(that.localConfig.urls[dataItem.label]))
					that.localConfig.urls[dataItem.label] = dataItem.data.url;
				that.checkMenuType("tabsContent", dataItem.label, dataItem.data.name, item, dataItem.data.menu);
				//APP.anagrafica.start(item, dataItem.data.name, dataItem.label, div.find("#tabsContent"));
			});
			ul.append(li);
			counter++;
		});
		div.html(ul);
		div.append("<div id='tabsContent' style='padding-top: 20px'></div>");
		
		div.find("ul.nav.nav-tabs").find("li.active").click();
	},
	
	/*
	createAffix: function(div, menu)
	{
		var that = this;
		var items = menu.items;
		if (!APP.utils.isset(div) || !APP.utils.isset(items))
			return;
		var ul = $('<ul class="list-group"></ul>');
		var counter = 0;
		$.each(items, function(i, v)
		{
			var active = (counter === 0)? 'class="list-group-item active"' : 'class="list-group-item"';
			var li = $('<li id="'+v.id+'" '+active+'><i class="icon icon-'+v.icon+' pull-left"></i><i class="icon icon-chevron-right pull-right"></i>&nbsp;&nbsp;'+v.name+'</li>');
			li.data({"data": v, "label": i});
			li.click(function()
			{
				var item = $(this);
				that.removeActiveClasses(item.parents(".list-group"), "li");
				item.addClass("active");
				var dataItem = item.data();
				div.find("#affixContent").empty();					
				if (!APP.utils.isset(that.localConfig.urls[dataItem.label]))
					that.localConfig.urls[dataItem.label] = dataItem.data.url;
				that.checkMenuType("affixContent", dataItem.label, dataItem.data.name, item, dataItem.data.menu);
				//APP.anagrafica.start(item, dataItem.data.name, dataItem.label, div.find("#affixContent"));
			});
			ul.append(li);
			counter++;
		});
		
		var row = $('<div class="row">\
						<div class="col-md-3"></div>\
						<div class="col-md-9" id="affixContent"></div>\
					</div>');
		
		
		row.find(".col-md-3").html(ul);
		div.html(row);
		
		ul.find("li.active").click();
	},
	*/
	
	createAffix: function(div, menu)
	{
		var that = this;
		var items = menu.items;
		if (!APP.utils.isset(div) || !APP.utils.isset(items))
			return;
		var ul = $('<div class="list-group"></div>');
		var counter = 0;
		$.each(items, function(i, v)
		{
			var active = (counter === 0)? 'class="list-group-item active"' : 'class="list-group-item"';
			var li = $('<a id="'+v.id+'" '+active+' href="#"><i class="icon icon-'+v.icon+' pull-left"></i><i class="icon icon-chevron-right pull-right"></i>&nbsp;&nbsp;'+v.name+'</a>');
			li.data({"data": v, "label": i});
			li.click(function()
			{
				var item = $(this);
				that.removeActiveClasses(item.parents(".list-group"), "a");
				item.addClass("active");
				var dataItem = item.data();
				div.find("#affixContent").empty();					
				if (!APP.utils.isset(that.localConfig.urls[dataItem.label]))
					that.localConfig.urls[dataItem.label] = dataItem.data.url;
				that.checkMenuType("affixContent", dataItem.label, dataItem.data.name, item, dataItem.data.menu);
				//APP.anagrafica.start(item, dataItem.data.name, dataItem.label, div.find("#affixContent"));
			});
			ul.append(li);
			counter++;
		});
		
		var row = $('<div class="row">\
						<div class="col-md-3"></div>\
						<div class="col-md-9" id="affixContent"></div>\
					</div>');
		
		
		row.find(".col-md-3").html(ul);
		div.html(row);
		
		ul.find(".active").click();
	},
	
	createCollapse: function(div, menu)
	{
		var that = this;
		var items = menu;
		if (!APP.utils.isset(div) || !APP.utils.isset(items))
			return;
			
		var accordionDiv = $('<div class="accordion" id="accordion_'+that.currentConfigSection+'"></div>');
		
		$.each(items, function(i, v)
		{
			var ag = $(	'<div class="accordion-group">\
							<div class="accordion-heading">\
								<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_'+that.currentConfigSection+'" href="#collapse_'+v.id+'">\
									<i class="icon icon-'+v.icon+'"></i> '+v.name+'\
								</a>\
							</div>\
							<div id="collapse_'+v.id+'" class="accordion-body collapse">\
								<div class="accordion-inner">\
								</div>\
							</div>\
						</div>'
						);
			
			ag.find(".accordion-toggle").data({"data": v, "label": i}).click(function()
			{
				var item = $(this);
				var dataItem = item.data();
				var destDiv = item.parents(".accordion-group").first().find(".accordion-inner").first();
				if (!APP.utils.isset(that.localConfig.urls[dataItem.label]))
					that.localConfig.urls[dataItem.label] = dataItem.data.url;
				destDiv.empty();
				that.checkMenuType('accordion_'+that.currentConfigSection, dataItem.label, dataItem.data.name, item, dataItem.data.menu);
				//APP.anagrafica.start(item, dataItem.data.name, dataItem.label, destDiv);
			});
			accordionDiv.append(ag);
		});
		
		div.html(accordionDiv);
		
		accordionDiv.find("div.accordion-group").first().find(".accordion-toggle").click();
	},
	
	checkMenuType: function(divId, section, label, button, menuObj)
	{
		if (APP.utils.isset(menuObj) && !$.isEmptyObject(menuObj))
		{
			switch(menuObj.type)
			{
				case "tabs":
					this.createTabs($("#mainContent").find("#"+divId), menuObj);
					break;
				case "affix":
					this.createAffix($("#mainContent").find("#"+divId), menuObj);
					break;
				case "collapse":
					this.createCollapse($("#mainContent").find("#"+divId), menuObj);
					break;
				default:
					console.log("Francesco, aggiungi il seguente menuObj.type: "+menuObj.type);
			}
		}
		else
		{
			if (APP.config.serverSide)
				APP.anagrafica_ss.start(button, label, section, $("#"+divId));
			else
				APP.anagrafica.start(button, label, section, $("#"+divId));
		}
	},
	
	insertContent: function(button, section)
	{
		var that = this;
		this.removeActiveClasses($(".navbar"), "li");
		button.closest("li").addClass("active");
		
		var prevDiv = $("#mainContent").find("#"+this.currentConfigSection+"Container");
		if (prevDiv.length > 0)
			prevDiv.remove();
		
		switch(this.currentConfigSection)
		{
			default:
				if (APP.config.serverSide)
					APP.anagrafica_ss.finish();
				else
					APP.anagrafica.finish();
				break;
		}
		this.currentConfigSection = section;		
		APP.utils.updateBreadcrumb("empty");
		var divId = this.currentConfigSection+"Container";
		$("#mainContent").append("<div id='"+divId+"' style='padding-top: 20px'></div>");
		
		this.checkMenuType(divId, section, button.text(), button, this.localConfig.menu[this.currentConfigSection].menu);
	},
	
	removeActiveClasses: function(group, elemType)
	{
		this.xhrObj.abortAll();
		$.each(group.find(elemType), function(i, v)
		{
			v = $(v);
			if (v.hasClass('active'))
			{
				v.removeClass('active');				
				return false;
			}
		});
	},
	
	setPeriodicRequests: function()
	{
		if (APP.utils.isset(APP.config.localConfig.periodic_requests))
		{
			$.each(APP.config.localConfig.periodic_requests, function(i,v){
				periodicRequestsIds.push(setInterval(function(){
					$.ajax({
						type: 'GET',
						url: v.url,
						dataType: 'json',
						success: function(result)
						{
							if (!APP.utils.checkError(result.error, null))
							{
								$.each(result.data.messages, function(j, obj)
								{
									APP.utils.showNoty({title: obj.title, type: obj.type, content: obj.content});
								});
								
								$.each(result.data.badges, function(j, obj)
								{
									$.each($("#APP-"+j).attr("class").toString(), function(k, className)
									{
										if (className !== "notification")
											$("#APP-"+j).removeClass(className);
									});
									$("#APP-"+j).addClass(obj.type);
									$("#APP-"+j).html(obj.content);
								});
								
								$.each(result.data.labels, function(j, obj)
								{
									$.each($("#APP-"+j).attr("class").toString(), function(k, className)
									{
										if (className !== "label")
											$("#APP-"+j).removeClass(className);
									});
									$("#APP-"+j).addClass("label-"+obj.type);
									$("#APP-"+j).html(obj.content);
								});
							}	
							else
								APP.utils.showErrMsg(result);
						},
						error: function(result)
						{
							APP.utils.showErrMsg(result);
						}
					});
				},v.frequency));
			});
		}
	},
	
	setConfig: function(data)
	{
		if (!APP.utils.isset(data.data.config))
		{
			APP.utils.showErrMsg(data);
			return;
		}
		APP.config.localConfig = data.data.config;		
		APP.i18n.loadLocale(APP.config.localConfig.i18n);
		$.datepicker.setDefaults($.datepicker.regional[APP.config.localConfig.i18n.split("-")[0]]);
		APP.config.setPeriodicRequests();
	},
	
	loadConfig: function()
	{
		$.ajax({
			type: 'GET',
			async: false,
			url: BOOTSTRAP_URL,//'/jx/config',
			success: APP.config.setConfig,
			error: APP.utils.showErrMsg
		});
	},
	
	setMsgDialog: function()
	{
		$("body").append(	'<div id="defaultMessageDialog" class="modal fade" role="dialog">\
								<div class="modal-dialog">\
									<div class="modal-content">\
										<div class="modal-header">\
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
											<h4 class="modal-title">Bootstrap Modal</h4>\
										</div>\
										<div class="modal-body"></div>\
										<div class="modal-footer">\
											<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">'+APP.i18n.translate("close")+'</button>\
										</div>\
									</div>\
								</div>\
							</div>'
						);
		
		$('#defaultMessageDialog').modal({
			show: false
		});
	},
	
	setLoadingImage: function()
	{
		$("body").append(	'<div id="loadingImageDiv" class="panel panel-success" style="display: none; position: fixed; z-index: 10000;">\
								<div class="panel-heading"><i class="icon-spinner icon-spin icon-large"></i>&nbsp;&nbsp;<b>'+APP.i18n.translate("loading")+'</b>...</div>\
								<div class="panel-body" style="display: none"></div>\
							</div>');
	},
	
	setMenu: function()
	{
		var that = this;
		if (!that.localConfig.menu)
		{
			APP.interactiveMap.start();
			return;
		}
		$.each(that.localConfig.menu, function(i, v){
			var button = $("#"+v.id+"Button");
			var callBack = (i === "logout")? function(){ location.href = v.url; } : function(){ that.insertContent($(this), v.id); }
			button.click(callBack);
		});
	},
		
	setMainContent: function()
	{
		$("body").append("<div id='mainContent' style='padding: 60px 15px 60px 15px'></div>");
	},
	
	setFilterDialogsDiv: function()
	{
		$("body").append("<div id='filterDialogsDiv'></div>");
	},
	
	setEuroDate: function()
	{
		$.extend( jQuery.fn.dataTableExt.oSort, {
			"date-euro-pre": function ( a ) {
				if ($.trim(a) != '') {
					var frDatea = $.trim(a).split(' ');
					if ($.isArray(frDatea) && frDatea.length < 2)
						return 10000000000000;
					var frTimea = frDatea[1].split(':');
					var frDatea2 = frDatea[0].split('/');
					var x = (frDatea2[2] + frDatea2[1] + frDatea2[0] + frTimea[0] + frTimea[1] + frTimea[2]) * 1;
				} else {
					var x = 10000000000000; // = l'an 1000 ...
				}
				 
				return x;
			},
		 
			"date-euro-asc": function ( a, b ) {
				return a - b;
			},
		 
			"date-euro-desc": function ( a, b ) {
				return b - a;
			}
		} );
	},
	
	setDateEu: function()
	{
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
			"date-eu-pre": function ( date ) {
				var date = date.replace(" ", "");
				  
				if (date.indexOf('.') > 0) {
					/*date a, format dd.mn.(yyyy) ; (year is optional)*/
					var eu_date = date.split('.');
				} else {
					/*date a, format dd/mn/(yyyy) ; (year is optional)*/
					var eu_date = date.split('/');
				}
				
				if ($.isArray(eu_date) && eu_date.length < 2)
					return 10000000000000;
				  
				/*year (optional)*/
				if (eu_date[2]) {
					var year = eu_date[2];
				} else {
					var year = 0;
				}
				  
				/*month*/
				var month = eu_date[1];
				if (month.length == 1) {
					month = 0+month;
				}
				  
				/*day*/
				var day = eu_date[0];
				if (day.length == 1) {
					day = 0+day;
				}
				  
				return (year + month + day) * 1;
			},
		 
			"date-eu-asc": function ( a, b ) {
				return ((a < b) ? -1 : ((a > b) ? 1 : 0));
			},
		 
			"date-eu-desc": function ( a, b ) {
				return ((a < b) ? 1 : ((a > b) ? -1 : 0));
			}
		} );
	},
	
	setResize: function()
	{
		$(window).on("resize",function(){
			APP.map.resizeMap();
		});
	},
		
	init: function()
	{	
		if ($("#login").length === 1)
			return;
			
		/*$(document).on('load_start', function(){ APP.utils.toggleLoadingImage(true); });
		$(document).on('load_end', function(){ APP.utils.toggleLoadingImage(false); });*/
		
		this.xhrObj.init();	
		this.setResize();
		this.setLoadingImage();
		this.setMsgDialog();
		this.setDateEu(); // gg/mm/aaaa
		this.setEuroDate(); // gg/mm/aaaa hh:mm:ss
		this.loadConfig(); // synchronous
		this.setMainContent();
		this.setFilterDialogsDiv();
		this.setMenu();
		
		$(".navbar-nav:first").find("a:first").click();
		return;
	}
});