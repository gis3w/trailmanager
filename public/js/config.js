$.extend(APP.config,{
	localConfig: {},
	currentConfigSection: null,
	currentUrl: null,
	prevUrl: null,
	default_iDisplayLength: 10,
	breadCrumb: [],
	bBack: false,
	bDirectUrl: false,
	queue: [],
	fadeInDelay: 400,
	fadeOutDelay: 100,
	maxStringsLength: 128,
	periodicRequests: {},
	serverSide: false,
	workSpace: null,
	backboneRouter: null,
	parameters: {},
	
	setBackboneRouting: function()
	{
		var that = this;
		
		var workNow = function(sec, secTitle, query, bAdd)
		{	
			that.bDirectUrl = true;
			
			that.removeActiveClasses($(".navbar"), "li");
			var button = $("#"+sec+"Button");
			button.closest("li").addClass("active");
			APP.anagrafica.finish();
			that.currentConfigSection = sec;		
			APP.utils.updateBreadcrumb("empty");
			var w = $('<div id="'+sec+'Container"></div>');
			$("#mainContent").html(w);
			
			var ff = (query)? "/"+query : "";
			if (sec == "home")
			{
				APP.adminMap.start();
				return false;
			}
			APP.anagrafica.start(button, secTitle, sec, w, ff, function()
			{
				if (!query)
				{
					var tw = APP.anagrafica.windows[APP.anagrafica.windows.length-1];
					var prevw = (APP.anagrafica.windows.length>2)? APP.anagrafica.windows[APP.anagrafica.windows.length-2] : tw;
					
					var obj = {//tableContainer, section, items_per_page, window, oldWindow, onSelectRow
							'section': sec,
							'tableContainer': tw,//tableDiv.hide(),
							'window': tw,
							'oldWindow': prevw,
							'items_per_page': APP.config.default_iDisplayLength,
							'sort_fields': {},
							'onSelectRow': function(t){
								//that.selectedItem = $(this);
								APP.anagrafica.onSelectTableRow(t, sec, tw);
							}
						};
					if (bAdd)
						APP.anagrafica.addItem(APP.config.parameters, prevw, tw, sec);
					else
						APP.anagrafica.showTable(obj);
					//APP.config.parameters = {};
				}
				else
				{
					var iw = APP.anagrafica.windows[APP.anagrafica.windows.length-1];				
					
					var index = APP.utils.getIndexFromField(APP.anagrafica.sections[sec].values, APP.anagrafica.sections[sec].primary_key, parseInt(query));
					if (index>-1)
						APP.anagrafica.onSelectTableRow(APP.anagrafica.sections[sec].values[index],sec,iw);
				}
				if (APP.config.queue.length==0)
					APP.config.bDirectUrl = false;
				APP.config.bBack = false;
				
			});
			
			/*
			var button = $("#"+sec+"Button");
			
			var viewItem = function(index, w)
			{
				APP.anagrafica.onSelectTableRow(APP.anagrafica.sections[sec].values[index],sec,w);
			};
			
			var loadData = function()
			{
				if (APP.anagrafica.windows.length==0)
					APP.anagrafica.createWindow();
				var w = APP.anagrafica.windows[APP.anagrafica.windows.length-1];
				if (!APP.anagrafica.sections[sec].values)
					APP.anagrafica.sections[sec].values = [];
				var index = APP.utils.getIndexFromField(APP.anagrafica.sections[sec].values, APP.anagrafica.sections[sec].primary_key, parseInt(query));
				if (index>-1)
					viewItem(index, w);
				else
				{
					$.ajax({
						type: 'GET',
						url: APP.config.localConfig.urls[sec]+"/"+query,
						success: function(result)
						{
							if (result.data && result.data.items && result.data.items.length>0)
							{
								var obj = result.data.items[0];
								APP.anagrafica.sections[sec].values.push(obj);
								viewItem(APP.anagrafica.sections[sec].values.length-1, w);
							}
						},
					});
				}
			};
			
			if (!APP.config.localConfig)
				that.loadConfig();
			
			if (!APP.anagrafica.sections)
			{
				APP.anagrafica.windows = [];
				APP.anagrafica.sections = {};
				APP.anagrafica.previousSection = null;
				APP.anagrafica.currentSection = sec;
				APP.anagrafica.selectedItem = null;
				APP.anagrafica.tmpSelectedItem = button;
				if ($("#"+sec+"Container").length==0)
					$("#mainContent").html($('<div id="'+sec+'Container"></div>'))
				APP.anagrafica.mainDiv = $("#"+sec+"Container");
			}
			
			if (!APP.anagrafica.sections.hasOwnProperty(sec))
			{
				APP.anagrafica.sections[sec] = APP.utils.setBaseStructure(secTitle, sec);
				
				$.ajax({
					type: 'GET',
					url: APP.config.localConfig.urls['dStruct']+"?tb="+sec,
					dataType: 'json',
					success: function(data)
					{
						if (!APP.utils.checkError(data.error, null))
						{
							APP.anagrafica.loadStructure(data, APP.anagrafica.sections[sec]);
							loadData();
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
				loadData();
			
			*/
		};
		
		that.backboneRouter = Backbone.Router.extend({

			  routes: {
				"home": "home",
				
				"highlitingpoi/:query": "highliting_poi",
				"highlitingpoi": "highliting_poi",
				"new_highlitingpoi": "new_highliting_poi",
				"highliting_poi/:query": "highliting_poi",
				"highliting_poi": "highliting_poi",
				"new_highliting_poi": "new_highliting_poi",
				
				"highlitingpath/:query": "highliting_path",
				"highlitingpath": "highliting_path",
				"new_highlitingpath": "new_highliting_path",
				"highliting_path/:query": "highliting_path",
				"highliting_path": "highliting_path",
				"new_highliting_path": "new_highliting_path",
				
				"path/:query": "path",
				"path": "path",
				"new_path": "new_path",
				
				"area/:query": "area",
				"area": "area",
				"new_area": "new_area",
			    
				"poi/:query": "poi",
			    "poi": "poi",
			    "new_poi": "new_poi",
			    "itinerary/:query": "itinerary",
			    "itinerary": "itinerary",
			    "pathsegment/:query": "pathsegment",
			    "pathsegment": "pathsegment",
			    "search/:query/p:page": "search"   // #search/kiwis/p7
			  },
			  
			  home: function(){
				  workNow("home", "Segnalazioni su Mappa", null);
			  },
			  
			  new_highliting_poi: function(query) {
				  workNow("highliting_poi", "Nuova segnalazione", null, true);
			  },
			  
			  highliting_poi: function(query) {
			  	workNow("highliting_poi", "Segnalazioni", query);
			  },
			  
			  new_highliting_path: function(query) {
				  workNow("highliting_path", "Nuova segnalazione", null, true);
			  },
			  
			  highliting_path: function(query) {
			  	workNow("highliting_path", "Segnalazioni", query);
			  },
			  
			  new_path: function(query) {
				  workNow("path", "Nuovo sentiero", null, true);
			  },

			  path: function(query) {
				  workNow("path", "Sentieri", query);
			  },
			  
			  new_poi: function(query) {
				  workNow("poi", "Nuovo punto di interesse", null, true);
			  },
			  
			  poi: function(query) {
				  workNow("poi", "Punti di interesse", query);
			  },
			  
			  itinerary: function(query) {
				  workNow("itinerary", "Itinerari", query);
			  },
			  
			  pathsegment: function(query) {
				  workNow("path_segment", "Tratte", query);
			  },
			  
			  new_area: function(query) {
				  workNow("area", "Nuova area", null, true);
			  },
			  
			  area: function(query) {
				  workNow("area", "Aree", query);
			  },

			  search: function(query, page) {
			    
			  }

		});
		that.workSpace = new that.backboneRouter;
		Backbone.history.start();
	},
	
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
		if (!APP.utils.isset(APP.config.localConfig.urls["token"]))
		{
			alert("Attenzione: inserire url del token nel CONFIG");
			return false;
		}
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
	
	getControlLayers: function(params)
	{
		var that = this;
		
		var baseLayers = {};
		var overlays = (APP.utils.isset(params) && APP.utils.isset(params.overlays))? params.overlays : {};
		var layers = [];
		var defLayName = null;
		var defLayUrl = null;
		var defaultLayer = null;
		var defaultOverlays = [];
		if (APP.utils.isset(params) && APP.utils.isset(params.initialOverlays))
		{
			$.each(params.initialOverlays, function(i,v){
				if (APP.utils.isset(overlays[v]))
					defaultOverlays.push(overlays[v]);
			});
		}
		
		if (APP.utils.isset(that.localConfig))
		{
			$.each(that.localConfig.background_layer, function(i,v)
			{
				var l = null;
				switch(v.source)
				{
					case "GOOGLE":
						l = new L.Google();
						break;
					case "BING":
						l = new L.BingLayer(v.url,{type: v.layers});
						break;					
					default:
						switch(v.layer_type)
						{
							case "tilelayer":
								l = new L.tileLayer(v.url, {/*minZoom: 5, maxZoom: 19,*/ attribution: v.description, tileLayerId: v.id});
								break;
							case "tilelayer.wms":
								l = L.tileLayer.betterWms(v.url, {
									tileLayerId: v.id,
									layers: v.layers,
									version: v.version,
									styles: v.styles,
									format: v.format,
									transparent:  v.transparent,
									attribution: v.description,
									tileSize:1024
								});
								break;
						}
				}
				layers.push(l);
				if (v.def)
				{
					if (!v.transparent)
					{
						defLayUrl = v.url;
						defaultLayer = l;
						defLayName = v.name;
						baseLayers[v.name] = l;
					}
					else
					{
						var nlg = new L.LayerGroup();
						l.addTo(nlg);
						defaultOverlays.push(nlg);
						overlays[v.name] = l;
					}
				}
				else
				{
					if (v.transparent)
						overlays[v.name] = l;
					else
						baseLayers[v.name] = l;
				}
			});
		}
		else
		{
			defLayUrl = "http://{s}.tile.osm.org/{z}/{x}/{y}.png";
			defaultLayer = new L.tileLayer(defLayUrl, {/*minZoom: 3, maxZoom: 18,*/ attribution: "Mappa stradale"});
			defLayName = "Openstreetmap";
		}
		
		var initialLayers = [defaultLayer];		
		$.each(defaultOverlays, function(i,v){
			initialLayers.push(v);
		});
		
		return {
			baselayers: baseLayers,
			overlays: overlays,
			initialLayers: initialLayers
		};
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
			switch(section)
			{
				case "home":
					APP.adminMap.start();
					break;
				case "administration_uploadpathzip":
					APP.ziploader.start(button, label, section, $("#"+divId));
					break;
				case 'administration_createroutingtopology':
					var myDiv = $('<div class="createroutingtopology container"><div class="row rt_butt"></div><div class="row rt_results"></div></div>');
					var butt = $('<button type="button" class="btn btn-default">'+APP.i18n.translate('Create')+'</button>');
					butt.click(function() {
						$.ajax({
							method: 'GET',
							url: APP.config.localConfig.menu.administration.menu.items[section].url,
							success: function(response) {
								if (response.error.errcode) {
									APP.utils.showErrMsg(response);
									return false;
								}
								myDiv.find('.rt_results').html(response.data);
							},
							error: function(response) {
								if (response.error.errcode) {
									APP.utils.showErrMsg(response);
									return false;
								}
							}
						});
					});
					myDiv.find('.rt_butt').html(butt);
					$("#"+divId).append(myDiv);
					break;
				default:
					if (APP.config.serverSide)
						APP.anagrafica_ss.start(button, label, section, $("#"+divId));
					else
						APP.anagrafica.start(button, label, section, $("#"+divId));
			}
			
		}
	},
	
	insertContent: function(button, section)
	{
		var that = this;
		this.removeActiveClasses($(".navbar"), "li");
		button.closest("li").addClass("active");
		
		//$("#mainContent").css("padding", "60px 15px 60px 15px");
		
		var prevDiv = $("#mainContent").find("#"+this.currentConfigSection+"Container");
		if (prevDiv.length > 0)
		{
			prevDiv.remove();
			this.prevUrl = this.currentUrl;
		}
		
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
		this.currentUrl = section;
		APP.config.queue = [];
		APP.config.bDirectUrl = false;
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
		var self = this;
		$.each(self.periodicRequests, function(i,v){
			clearInterval(v);
		});
		self.periodicRequests = {};
		
		if (!APP.utils.isset(APP.config.localConfig.periodic_requests))
			return false;
		
		$.each(APP.config.localConfig.periodic_requests, function(i,v)
		{			
			self.periodicRequests[i] = setInterval(function()
			{
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
			},v.frequency);
		});
	},
	
	setCSSSelector: function(obj)
	{
		var that = this;
		var btnId = "themeButton";
		var sel = $("body").find("#"+btnId);
		/*
		if (sel)
			sel.empty();
		$.each(obj.items, function(i,v){
			var selected = (obj.selected_skin === v)? " selected " : " ";
			sel.append('<option '+selected+' value="'+v+'">'+i+'</option>');
		});
		*/
		if (sel.length == 0)
			return;
		sel.change(function()
		{
			var link = $("head").find("#bootstrapCSS");
			if (link)
				link.attr("href",$(this).val());
			
			if (!APP.utils.isset(APP.config.localConfig.urls["theme"]))
				return;
			$.ajax({
				type: 'POST',
				url: APP.config.localConfig.urls["theme"],
				dataType: 'json',
				data: {theme: $(this).find("option:selected").text()},
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, null))
					{
						
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
		APP.config.setCSSSelector(null);
		APP.config.setPeriodicRequests();
	},
	
	loadConfig: function(callback)
	{
		$.ajax({
			type: 'GET',
			async: false,
			url: BOOTSTRAP_URL,//'/jx/config',
			success: function(result)
			{
				APP.config.setConfig(result);
				if ($.isFunction(callback))
					callback();
			},
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
			var callBack = null;
			switch(i)
			{
				case "logout":
					callBack = function(){ location.href = v.url; };
					break;
				default:
					callBack = function(){
						tinymce.remove(".textEditor");
						that.insertContent($(this), v.id);
					};
			}
			button.click(callBack);
		});
		
		setTimeout(function(){
			var cf = Backbone.history.getFragment();
			if (APP.utils.isEmptyString(cf))
				$(".navbar-nav:first").find("a:first").click();
		},250);
		
		
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
			APP.interactiveMap.resize();
		});
	},
	
	amIAdmin: function()
	{
		var that = this;
		var mri = parseInt(that.localConfig.authuser.main_role_id);
		var index = APP.utils.getIndexFromField(that.localConfig.role, "id", mri);
		return (APP.i18n.translate(that.localConfig.role[index]).name === APP.i18n.translate("admin_role"))? true : false;
	},
	
	isMobileVersion: function()
	{
		var that = this;
		return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))
	},
	
	hasLittleWidth: function()
	{
		var navbarToggle = $("body").find(".navbar:first").find('.navbar-toggle');
		return (navbarToggle.length>0 && navbarToggle.is(":visible"));
	},
	
	checkLoggedUser: function()
	{
		return (this.localConfig.authuser)? true : false;
	},
	
	setCreditsButton: function()
	{
		var that = this;
		
		var cb = $("#creditsButton");
		if (cb.length==0)
			return false;
		
		cb.css({
			padding: 0,
			margin: 0
		});
		
		that.creditsModal = APP.utils.createModal({
			id: 'creditsModal',
			container: $("body"),
			size: "sm",
			header: 'Credits',
			body: 	'<div>\
						<p class="text-center">Progettato e realizzato da</p>\
						<p class="text-center"><img src="public/img/logo_gis3w_h60.png" alt="GIS3W Sas"></p>\
						<p style="margin-top: 30px">\
							<address>\
							  <strong>GIS3W</strong><br>\
								<em>di Lorenzetti Walter e C. S.a.S.</em><br>\
								Viale Verdi, 24<br>\
								51016 - Montecatini Terme (PT)<br>\
								Fax 0572 901639<br>\
								P. IVA 01782000473<br>\
							  	Tel. +39 347-6597931<br>\
								Email info@gis3w.it<br>\
								Web <a href="http://www.gis3w.it" target="_blank" class="btn btn-link">www.gis3w.it</a>\
							</address>\
						</p>\
						<div>Icons made by <a href="http://www.flaticon.com/authors/scott-de-jonge" title="Scott de Jonge">Scott de Jonge</a>, <a href="http://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a>             is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a></div>\
					</div>'
		});
		
		var img = $('<img src="public/img/logo_gis3w_h60.png" alt="Credits" data-toggle="modal" data-toggle="tooltip" data-target="#creditsModal" data-placement="auto" title="Credits" style="max-height: 50px">');
		img.tooltip();
		
		cb.html(img);
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
		this.setCreditsButton();
		this.setBackboneRouting();
		this.setMenu();

		return;
	}
});