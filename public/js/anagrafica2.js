$.extend(APP.anagrafica,
{
	sections: {},
	
	finish: function()
	{
		tinymce.remove(".textEditor");
		$("body").off("table_shown");
		APP.map.finish();
		APP.subforms.finish();
	},
	
	start: function(el, titolo, section, div, filterString, loadCallback)
	{
		var that = this;
		this.windows = [];
		//this.sections = {};
		this.previousSection = null;
		this.currentSection = section;
		this.selectedItem = null;
		this.tmpSelectedItem = el;
		this.mainDiv = APP.utils.isset(div)? div : $("#"+section+"Container");
		$("#mainContent").css("padding", "60px 15px 60px 15px");
		
		$("body").one("table_shown", function(){
			
		});
		
		if (this.previousSection == null)
			this.previousSection = this.currentSection;

		this.createWindow();
		
		if (section === "home")
		{
			/*
			this.panelPerRow = 1;
			$.ajax({
				type: 'GET',
				url: APP.config.localConfig.urls[section],
				dataType: 'json',
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, null))
						that.displayHomeItems(data.data);
					else
						APP.utils.showErrMsg(data);
				},
				error: function(result)
				{
					APP.utils.showErrMsg(result);
				}
			});
			*/
			//APP.interactiveMap.start();
		}
		else
		{
			this.processSectionsProperty({
				'title': titolo,
				'section': this.currentSection,
				'filterString': (APP.utils.isset(filterString))? filterString : APP.filter.getActiveFilterString(section),
				'loadCallback': ($.isFunction(loadCallback))? loadCallback : function(res){
						var obj = {
							'section': that.currentSection,
							'items_per_page': res.data.items_per_page,
							'tableContainer': that.windows[0].hide(),
							'window': that.windows[0],
							'oldWindow': that.windows[0],
							'sort_fields': res.data.sort_fields,
							'onSelectRow': function(t){
								that.onSelectTableRow(t, that.currentSection, that.windows[0]);
							}
						};
						that.showTable(obj);
				}
			});
		}
	},
	
	/*
	displayHomeItems: function(data)
	{
		if (data.length === 0)
			return;
		
		var that = this;
		var numPanels = data.length;
		var numRows = Math.ceil(numPanels/this.panelPerRow);	

		var row = null;
		$.each(data, function(j,k)
		{
			if (j === 0 || j%that.panelPerRow === 0)
			{
				row = $('<div class="row"></div>');
				that.windows[that.windows.length-1].append(row);
			}
			row.append(that.createPanel(k));
		});
	},
	
	createPanel: function(obj)
	{
		var that = this;
		var spanNumber = parseInt(12/that.panelPerRow);
		var well = $('<div class="panel panel-primary table-responsive"></div>');
		well.append('<div class="panel-heading">\
						<h3 class="panel-title"><strong>'+obj.title+'</strong><span class="badge badge-important" style="margin-left: 10px">'+obj.items.length+'</span></h3>\
					</div>');
		var table = $('<table class="table table-striped table-hover"><thead><tr></tr></thead><tbody></tbody></table>');
		var trHeader = table.find("thead tr");
		$.each(obj.items, function(i,v)
		{
			var row = $('<tr></tr>');
			$.each(v, function(j,k)
			{
				if (i === 0)
					trHeader.append('<th>'+j+'</th>');
				if (APP.utils.isset(obj.colorField) && j == obj.colorField)
				{
					if (k)
						row.addClass("success");
				}
				row.append('<td>'+k+'</td>');
			});
			row.css('cursor','pointer').data(v).click(function(){
				that.onSelectedItem($(this), obj.title);
			});
			table.find('tbody').append(row);
		});
		well.append(table);
		var spanx = $('<div class="col-md-'+spanNumber+'"></div>');
		spanx.append(well);
		return spanx;
	},
	
	onSelectedItem: function(row, title)
	{
		var that = this;
		var obj = row.data();
		
		var opDiv = this.mainDiv.css('opacity');
		this.mainDiv.css({ opacity: 0.1 });
		
		this.processSectionsProperty({
			'title': title,
			'section': obj.datastruct,
			'filterString': "/"+obj.id,
			'loadCallback': function(res){
				var serialLoad = function(history, y)
				{
					that.processSectionsProperty({
						'title': history[y].datastruct,
						'section': history[y].datastruct,
						'filterString': "/"+history[y].id,
						'loadCallback': function(res1){
							that.onSelectTableRow(res1.data.items[0], history[y].datastruct, that.windows[that.windows.length-1]);
							var label = APP.config.getValue(res1.data.items[0], that.sections[history[y].datastruct].title.title_toshow, that.sections[history[y].datastruct].title.title_toshow_params);
							if (y < history.length-1)
								serialLoad(history, y+1);
							else
							{
								that.onSelectTableRow(obj, obj.datastruct, that.windows[that.windows.length-1]);
								that.mainDiv.css({ opacity: opDiv });
							}
						}
					});
				};
				
				if (res.data.items[0].history.length > 0)
					serialLoad(res.data.items[0].history, 0);
			}
		})
	},
	*/
	
	createWindow: function()
	{
		var l = this.windows.length;
		var div = $("<div id='window_"+l+"'></div>");
		this.mainDiv.append(div);
		this.windows.push(div);
		if (l > 0)
			this.toggleWindow(this.windows[this.windows.length-1], this.windows[l-1]);
	},
	
	destroyWindow: function()
	{
		tinymce.remove(".textEditor");
		var l = this.windows.length;
		
		if (l > 1)
			this.toggleWindow(this.windows[l-2], this.windows[l-1]);
		if (l > 0)
		{
			this.windows.pop();
			this.mainDiv.find("#window_"+this.windows.length).remove();
			APP.utils.updateBreadcrumb("remove");
		}
		this.finish();
	},
	
	toggleWindow: function(newDiv, oldDiv)
	{
		oldDiv.hide();
		newDiv.show();
	},
	
	processSectionsProperty: function(params) // title, section, filterString, loadCallback
	{						
		var that = this;
		var section = params.section;
		var filterString = params.filterString;
		
		if (!this.sections.hasOwnProperty(section))
		{
			this.sections[section] = APP.utils.setBaseStructure(params.title, section);
			this.getStructure(APP.config.localConfig.urls['dStruct']+"?tb="+this.sections[section].resource, section, filterString, true, params.loadCallback);
		}
		else
			this.loadData(APP.config.localConfig.urls[section]+filterString, that.sections[section], params.loadCallback);
	},
	
	callbackCall: function(cb)
	{
		if (APP.utils.isset(cb) && $.isFunction(cb))
			cb();
		else
			return false;
	},
	
	getStructure: function(u, section, filterString, bLoad, callback)
	{
		var that = this;
		
		$.ajax({
			type: 'GET',
			url: u,
			dataType: 'json',
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					that.loadStructure(data, that.sections[section]);
					if (!bLoad)
					{
						if (APP.utils.isset(callback) && $.isFunction(callback))
							callback();
						return;
					}
					if (!APP.utils.isset(APP.config.localConfig.urls[section]))
					{
						alert("Inserire nel CONFIG l'url per la sezione "+section);
						return;
					}
					else
						that.loadData(APP.config.localConfig.urls[section]+filterString, that.sections[section], callback);
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
	},
	
	getStructure_old: function(u, section, filterString, callback)
	{
		var that = this;
		
		if (APP.utils.isset(that.sections[section]) && !$.isEmptyObject(that.sections[section].columns))
		{
			that.loadData(APP.config.localConfig.urls[section]+filterString, that.sections[section], callback);
			return;
		}
		
		$.ajax({
			type: 'GET',
			url: u,
			dataType: 'json',
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					that.loadStructure(data, that.sections[section]);
					if (!APP.utils.isset(APP.config.localConfig.urls[section]))
					{
						alert("Inserire nel CONFIG l'url per la sezione "+section);
						return;
					}
					else
						that.loadData(APP.config.localConfig.urls[section]+filterString, that.sections[section], callback);
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
	},
	
	loadStructure: function(data, subSection)
	{
		var that = this;
		var obj = APP.utils.isset(subSection)? subSection : that.sections[this.currentSection];
		$.each(data.data.fields, function(ii, vv)
		{
			var o = {
				name: ii,
				sortable: true,
			};
			$.extend(o, vv);
			obj.columns.push(o);
		});
		$.extend(obj, data.data);
		delete obj.fields;
		//ACHTUNG
		//obj.capabilities = ["list"];
	},
	
	loadData: function(u, target, callback)
	{
		var that = this;
		
		if ($.inArray("list", target.capabilities) > -1)
		{
			$.ajax({
				type: 'GET',
				url: u,
				success: function(result)
				{
					if (result.status)
					{	
						if (APP.utils.isset(result.data) && APP.utils.isset(target))
							target.values = result.data.items;
						if (APP.utils.isset(callback) && $.isFunction(callback))
							callback(result);
					}						
					else
						APP.utils.showErrMsg(result);
				},
				error: function(result)
				{
					APP.utils.showErrMsg(result);
				}
			});
		}
		else
			APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("list_capability_denied")});		
	},
	
	onSelectTableRow: function(t, subSection, contentDiv, backUrl)
	{
		var that = this;
		that.selectedItem = that.tmpSelectedItem;
		
		APP.config.prevUrl = APP.config.currentUrl;
		if (APP.config.queue.length==0)
		{
			APP.config.queue.push(subSection);
			APP.config.prevUrl = subSection;
		}
		
		APP.config.currentUrl = subSection+'/'+t[that.sections[subSection].primary_key];
		if (!APP.config.bBack)
			APP.config.queue.push(APP.config.currentUrl);
		console.log(APP.config.queue);
		
		if (APP.utils.isset(that.sections[subSection].menu))
		{
			that.createWindow();
			that.showItem(t, that.windows[that.windows.length-1], that.windows[that.windows.length-2], subSection);
		}
		else
		{
			if (contentDiv.find("#subMainTableDiv").length > 0)
			{
				var arr = [
					{
						htmlString: '<a id="button_list" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("list")+'" class="btn btn-default btn-lg tooltipElement" href="#"><i class="icon-list"></i></a>',
						onClick: function(){
							that.selectedItem.click();
							APP.config.currentUrl = APP.config.prevUrl;
						}
					}					
				];
				
				if ($.inArray("update", that.sections[subSection].capabilities) > -1)
				{
					arr.push(
					{
						htmlString: '<a id="button_save" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("save")+'" class="btn btn-lg btn-success tooltipElement" href="#"><i class="icon-ok icon-white"></i></a>',
						onClick: function(){ 
							that.formSubmit(t[that.sections[subSection].primary_key], subSection, function(){ 
								that.finish();
								that.selectedItem.click();
							});  }
					});
				}
					
				if ($.inArray("delete", that.sections[subSection].capabilities) > -1)
				{
					arr.push({
						htmlString: '<a id="button_remove" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("remove")+'" class="btn btn-lg btn-danger tooltipElement" href="#"><i class="icon-remove icon-trash"></i></a>',
						onClick: function(){ that.removeItem(t[that.sections[subSection].primary_key], {'section': subSection, 'contentDiv': contentDiv, 'callback': function(){
							that.selectedItem.click();
						}});  }
					});
				}
				
				that.editItem(t[that.sections[subSection].primary_key], t, contentDiv.find("#subMainTableDiv"), subSection, arr);
			}
			else
			{
				that.createWindow();
				
				var arr = [];
				
				arr.push({
					htmlString: '<a id="button_cancel" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("cancel")+'" class="btn btn-default btn-lg tooltipElement" href="#"><i class="icon-arrow-left"></i></a>',
					onClick: function(){
						if (APP.config.backUrl)
						{
							APP.config.workSpace.navigate(APP.config.backUrl, {trigger: true, replace: true});
							APP.config.backUrl = null;
							return false;
						}
						APP.config.bBack = true;
						APP.config.queue.pop();
						if (APP.config.bDirectUrl && APP.config.queue.length>0)
						{
							var ssss = APP.config.queue[APP.config.queue.length-1];
							var ios = ssss.indexOf("path_segment");
							
							if (ios > -1)
							{
								var psa = ssss.split("/");
								ssss = "pathsegment";
								
								if (psa.length==2)
									ssss =  "pathsegment"+"/"+psa[1];
							}
							
							APP.config.workSpace.navigate(ssss, {trigger: true, replace: true});
						}
						else
						{
							that.tmpSelectedItem = that.selectedItem;
							that.destroyWindow();
							that.selectedItem = APP.config.bc_getLastSrcElement();
							APP.config.currentUrl = APP.config.prevUrl;
						}
					}
				});
				
				if ($.inArray("update", that.sections[subSection].capabilities) > -1)
				{
					arr.push({
						htmlString: '<a id="button_save" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("save")+'" class="btn btn-lg btn-success tooltipElement" href="#"><i class="icon-ok icon-white"></i></a>',
						onClick: function(){ 
							that.formSubmit(t[that.sections[subSection].primary_key], subSection, function(){
								//$("body").find(".navbar").first().find(".navbar-inner").first().find("ul").first().find("li.active").find("a").first().click();
								that.finish();
								that.selectedItem.click();
							});  
						}
					});
				}
				
				if (subSection == "administration_roles")
				{
					arr.push({
						htmlString: '<a id="button_capabilities" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("grid_mode")+'" class="btn btn-lg btn-info tooltipElement" href="#"><i class="icon-th"></i></a>',
						onClick: function(){ 
							that.showCapabilitiesGrid(t, [], that.windows[that.windows.length-1]);
						}
					});
				}
				
				if ($.inArray("delete", that.sections[subSection].capabilities) > -1)
				{
					arr.push({
						htmlString: '<a id="button_remove" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("remove")+'" class="btn btn-lg btn-danger tooltipElement" href="#"><i class="icon-remove icon-trash"></i></a>',
						onClick: function(){ that.removeItem(t[that.sections[subSection].primary_key], {'section': subSection, 'contentDiv': contentDiv, 'callback': function(){
							that.selectedItem.click();
						}});  }
					});
				}
				
				that.editItem(t[that.sections[subSection].primary_key], t, that.windows[that.windows.length-1], subSection, arr);
			}
		}
		//console.log(APP.config.currentUrl);
	},
	
	showCapabilitiesGrid: function(obj, permissions, parentDiv)
	{
		var that = this;
		that.createWindow();
		
		var arr = [];
		
		arr.push({
			htmlString: '<a id="button_cancel" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("cancel")+'" class="btn btn-default btn-lg tooltipElement" href="#"><i class="icon-arrow-left"></i></a>',
			onClick: function(){ 
				that.destroyWindow();
			}
		});
		
		arr.push({
			htmlString: '<a id="button_save" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("save")+'" class="btn btn-lg btn-success tooltipElement" href="#"><i class="icon-ok icon-white"></i></a>',
			onClick: function(){
				that.finish();
				var activeButtons = that.windows[that.windows.length-1].find("button.active");
				var o = {};
				$.each(activeButtons, function(i,v)
				{
					o[$(v).text()] = true;
				});				
				
				$.ajax({
					type: 'POST',
					url: "/jx/administration/aclroles/"+obj.id,
					data: o,
					dataType: 'json',
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
			}
		});
		
		var div = that.windows[that.windows.length-1];
		
		var mainButtonsRow = $('<div class="row" style="margin-bottom: 20px">\
									<div class="row col-md-6">\
										<div class="row">\
											<div class="col-md-4"></div>\
											<div class="col-md-8"></div>\
										</div>\
									</div>\
								</div>');
		
		$.each(arr, function(i,v){
			var str = $(v.htmlString);
			str.tooltip();
			str.css("margin-right", "5px");
			str.click(function(){ v.onClick($(this)); });
			mainButtonsRow.find(".col-md-8").append(str)
		});
		div.append(mainButtonsRow);
		
		$.ajax({
			type: 'GET',
			url: "/jx/administration/aclroles/"+obj.id,
			dataType: 'json',
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					var row = null;
					var colonne = 4;
					$.each(data.data.items[0], function(i,v)
					{
						if (i === 0 || i % colonne === 0)
							row = $('<div class="row" style="height: 70px"></div>');
						
						$.each(v, function(j,k)
						{
							var btnClass = (k)? "btn-success" : "btn-danger";
							var active = (k)? "active" : "";
							var button = $('<button type="button" class="btn btn-sm '+btnClass+' '+active+'">'+j+'</button>');
							button.click(function(){
								var btn = $(this);
								btn.toggleClass("active");
								btn.toggleClass("btn-success");
								btn.toggleClass("btn-danger");
							});
							
							var sp2 = $('<div class="col-md-'+parseInt(12/colonne)+'"></div>');
							sp2.append(button);
							row.append(sp2);
							
							if (i%colonne === 0 || i === data.length-1)
								div.append(row);
						});
						
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
	
	showTable: function(params) // tableContainer, section, items_per_page, window, oldWindow, onSelectRow
	{
		var that = this;
		var tableDiv = params.tableContainer;
		var section = APP.utils.isset(params.section)? params.section : this.currentSection;
		if (!APP.utils.isset(this.sections[section].values))
			return;
		var iDisplayLength = (params.items_per_page === this.sections[section].values.length)? APP.config.default_iDisplayLength : params.items_per_page;
		var contentDiv = APP.utils.isset(params.window)? params.window : tableDiv;
		var oldContentDiv = APP.utils.isset(params.oldWindow)? params.oldWindow : contentDiv;
		var onSelectRowCallback = (APP.utils.isset(params.onSelectRow) && $.isFunction(params.onSelectRow))? params.onSelectRow : function(){};
		var aaSorting = APP.utils.isset(params.sort_fields)? params.sort_fields : [[0, 'desc']];
		
		var table = $('<table class="table table-bordered table-striped table-hover datatable">\
							<thead>\
								<tr></tr>\
							</thead>\
							<tbody></tbody>\
						</table>');
		var thead = table.find("thead tr");
		var tbody = table.find("tbody");
		
		var cols = [];
		var dateboxFields = [];
		var datetimeboxFields = [];
		var valori = {};
		
		$.each(this.sections[section].columns, function(i, v)
		{
			if (!v.table_show)
				return true;
			if (APP.utils.isset(v.description))
			{
				var tthh = $('<th class="table-th" title="'+v.description+'">'+v.label+'</th>');
				tthh.tooltip({
					container: '#'+contentDiv.attr("id"),
					placement: 'auto',
				});
				thead.append(tthh);
			}
			else
				thead.append('<th class="table-th">'+v.label+'</th>');
			
			if (!v.hasOwnProperty("foreign_key") && v.form_input_type == "combobox" && !APP.utils.isset(v.slave_of))
				valori[v.name] = APP.utils.getForeignValue(v, null);
			cols.push(v);
			
			if (v.form_input_type === "datebox") // eurodates di default
			{
				dateboxFields.push(cols.length-1);
			}
			if (v.form_input_type === "datetimebox") // eurodates di default
			{
				datetimeboxFields.push(cols.length-1);
			}
			
		});
		
		$.each(this.sections[section].values, function(i, v)
		{
			var tr =  $("<tr></tr>");
			tr.css("cursor", "pointer");
			tr.data(v);
			tr.click(function(){ 
				APP.utils.toggleLoadingImage(true);
				var t = $(this).data(); 
				onSelectRowCallback(t); 
			});
			$.each(cols, function(j, k)
			{
				var classesAndStyles = APP.utils.getStyleByCssParams(k.name, v.css_params);
				
				if (APP.utils.isset(k.foreign_key))
				{
					var fValues = APP.config.localConfig[k.foreign_key];
					var data = (APP.utils.isset(v[k.name]))? v[k.name] : [];
					var str = "";
					
					if (!$.isArray(data))
						data = [data];
					
					$.each(data, function(x, y)
					{
						var primary_key = (APP.utils.isset(k.foreign_value_field))? k.foreign_value_field : "id";
						
						y = (!$.isPlainObject(y))? y : y[primary_key];
						var oi = APP.utils.getIndexFromField(fValues, primary_key, y);
						if (oi > -1)
							str += APP.config.getValue(fValues[oi], k.foreign_toshow, k.foreign_toshow_params)+", ";//str += APP.config.getValue(k.name, fValues[oi])+", "; 
					});
					str = str.substr(0, str.length-2);
					
					tr.append("<td class='table-td "+classesAndStyles[0]+"' style='"+classesAndStyles[1]+"'>"+APP.utils.displayData(str, k)+"</td>");
					return true;
				}
				
				if (k.form_input_type == "combobox")
				{
					var fValues = valori[k.name];
					var data = (APP.utils.isset(v[k.name]))? v[k.name] : [];
					var str = "";
					
					if (!$.isArray(data))
						data = [data];
					
					$.each(data, function(x, y)
					{
						var primary_key = (APP.utils.isset(k.foreign_value_field))? k.foreign_value_field : "id";
						y = (!$.isPlainObject(y))? y : y[primary_key];
						var oi = APP.utils.getIndexFromField(fValues, primary_key, y);
						if (oi > -1)
							str += APP.config.getValue(fValues[oi], k.foreign_toshow, k.foreign_toshow_params)+", ";//str += APP.config.getValue(k.name, fValues[oi])+", "; 
					});
					str = str.substr(0, str.length-2);
					
					tr.append("<td class='table-td "+classesAndStyles[0]+"' style='"+classesAndStyles[1]+"'>"+APP.utils.displayData(str, k)+"</td>");
					return true;
				}
				
				/*
				if ($.isArray(v[k.name]))
				{
					var str = "";
					$.each(v[k.name], function(x, y)
					{
						//str += APP.config.getValue(k.name, y)+", ";
						str += APP.config.getValue(y, k.foreign_toshow, k.foreign_toshow_params)+", ";
					});
					str = str.substr(0, str.length-2);
					tr.append("<td class='table-td'>"+str+"</td>");
					return true;
				}
				if ($.isPlainObject(v[k.name]))
				{
					//var str = APP.config.getValue(k.name, v[k.name])+", ";
					var str = APP.config.getValue(v[k.name], k.foreign_toshow, k.foreign_toshow_params)+", "; 
					str = str.substr(0, str.length-2);
					tr.append("<td class='table-td'>"+str+"</td>");
					return true;
				}*/
				if (!APP.utils.isset(v[k.name]))
				{
					var str = APP.utils.getSecondaryValue(k); 
					
					tr.append("<td class='table-td "+classesAndStyles[0]+"' style='"+classesAndStyles[1]+"'>"+APP.utils.displayData(str, k)+"</td>");
					return true;
				}
				tr.append("<td class='table-td "+classesAndStyles[0]+"' style='"+classesAndStyles[1]+"'>"+APP.utils.displayData(v[k.name], k)+"</td>");
			});
			tbody.append(tr);
		});
	
		var s = $('<div></div>');
		
		if ($.inArray("insert", this.sections[section].capabilities) > -1)
			s.append('<button type="button" id="addItemButton_'+section+'" class="btn btn-primary"><i class="icon-plus"></i> '+APP.i18n.translate("add")+'</button>');
				
		if (this.sections[section].hasOwnProperty('filter') && this.sections[section].filter)
		{
			var btnClass = (APP.filter.hasActiveFilter(section))? "btn-warning" : "btn-default";
			s.append('<button type="button" id="filterButton_'+section+'" class="btn '+btnClass+'" style="margin-left: 5px"><i class="icon-search"></i> '+APP.i18n.translate("search")+'</button>');
		}
		
		if (s.children().length > 0)
		{
			s.css("margin-bottom", 20);
		}
				
		/*if (tableDiv.attr("id") === "subMainTableDiv")
			s.find("div").prepend('<button type="button" id="viewList_'+section+'" class="btn btn-default"><i class="icon-list"></i> '+APP.i18n.translate("list")+'</button>');*/
		
		tableDiv.empty();
		tableDiv.html(s);
		var tabResp = $('<div class="table-responsive"></div>');
		tabResp.append(table);
		tableDiv.append(tabResp);
		/*tableDiv.find(".icon-eye-open").tooltip({title: APP.i18n.translate("show")});
		tableDiv.find(".icon-pencil").tooltip({title: APP.i18n.translate("edit")});
		tableDiv.find(".icon-remove").tooltip({title: APP.i18n.translate("remove")});
		tableDiv.find(".icon-lock").tooltip({title: APP.i18n.translate("off")});
		tableDiv.find(".icon-off").tooltip({title: APP.i18n.translate("on")});*/
		
		var cb = function(result)
		{
			var ow = (that.windows.length-2>=0)? that.windows[that.windows.length-2] : that.windows[that.windows.length-1];
			var obj = {//tableContainer, section, items_per_page, window, oldWindow, onSelectRow
				'section': section,
				'tableContainer': ow.hide(),//tableDiv.hide(),
				'window': that.windows[that.windows.length-1],
				'oldWindow': ow,
				'items_per_page': result.data.items_per_page,// APP.config.default_iDisplayLength,
				'sort_fields': result.data.sort_fields,
				'onSelectRow': function(t){
					//that.selectedItem = $(this);
					that.onSelectTableRow(t, section, that.windows[that.windows.length-1]);
				}
			};
			that.sections[section].values = result.data.items;
			that.showTable(obj);
		};
			
		tableDiv.find("table.datatable").dataTable({
			"bProcessing": true,
			"bRetrieve": true,
			"bPaginate": true,
			"sPaginationType": "full_numbers",
			"iDisplayLength" : iDisplayLength,
			"aaSorting": aaSorting,
			"bJQueryUI": false,
			"aoColumnDefs": [
				{ "sType": "date-eu", "aTargets": dateboxFields },
				{ "sType": "date-euro", "aTargets": datetimeboxFields },
			],
			"oLanguage": APP.utils.getDataTableLanguage(),
			"fnDrawCallback": function( oSettings ) {
				tableDiv.fadeIn(APP.config.fadeInDelay);
				if (!!that.sections[section].filter)
					APP.filter.init(tableDiv.find("#filterButton_"+section).first(), section, cb);
			},
			"fnInitComplete": function(oSettings, json) {
				$("body").trigger("table_shown");
			}
		});
		
		tableDiv.find("#viewList_"+section).click(function(){
			that.showItem(t, tableDiv, that.windows[that.windows.length-1], section); 
		});
		tableDiv.find("#addItemButton_"+section).click(function()
		{
			var t = null;
			var ot = contentDiv;
			that.selectedItem = that.tmpSelectedItem;
			if (tableDiv.attr("id") === "subMainTableDiv")
			{
				t = tableDiv;
			}
			else
			{
				that.createWindow();
				t = that.windows[that.windows.length-1];
				ot = contentDiv;
			}
			that.addItem(null, t, ot, section);
		});
	},
	
	addItem: function(dataObject, contentDiv, oldContentDiv, section)
	{
		var that = this;
		var arr = [];
		if (contentDiv.attr("id") === "subMainTableDiv")
		{
			arr.push({
				htmlString: '<a id="button_list" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("list")+'" class="btn btn-default btn-lg tooltipElement" href="#"><i class="icon-th-list"></i></a>',
				onClick: function(){ 
					that.selectedItem.click();
				}
			});
			
			if ($.inArray("insert", that.sections[section].capabilities) > -1)
			{
				arr.push({
					htmlString: '<a id="button_save" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("save")+'" class="btn btn-lg btn-success tooltipElement" href="#"><i class="icon-ok icon-white"></i></a>',
					onClick: function(){
						that.formSubmit(null, section, function(){
							if (APP.config.backUrl)
							{
								APP.config.workSpace.navigate(APP.config.backUrl, {trigger: true, replace: true});
								APP.config.backUrl = null;
								return false;
							}
							that.finish();
							if (that.selectedItem && $.isFunction(that.selectedItem.click))
								that.selectedItem.click();
						});  
					}
				});
			}
		}
		else
		{
			arr.push({
				htmlString: '<a id="button_cancel" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("cancel")+'" class="btn btn-default btn-lg tooltipElement" href="#"><i class="icon-arrow-left"></i></a>',
				onClick: function(){ 
					that.tmpSelectedItem = that.selectedItem;
					that.destroyWindow();
					that.selectedItem = APP.config.bc_getLastSrcElement();
					if (APP.config.backUrl)
					{
						APP.config.bMustNullBackUrl = true;
						APP.config.workSpace.navigate(APP.config.backUrl, {trigger: true, replace: true});
					}
				}
			});
			
			if ($.inArray("insert", that.sections[section].capabilities) > -1)
			{
				arr.push({
					htmlString: '<a id="button_save" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("save")+'" class="btn btn-lg btn-success tooltipElement" href="#"><i class="icon-ok icon-white"></i></a>',
					onClick: function(){ 
						that.formSubmit(null, section, function(){
							if (APP.config.backUrl)
							{
								APP.config.workSpace.navigate(APP.config.backUrl, {trigger: true, replace: true});
								APP.config.backUrl = null;
								return false;
							}
							that.finish();
							if (that.selectedItem && $.isFunction(that.selectedItem.click))
								that.selectedItem.click();
						});  
					}
				});
			}
		}
		
		this.editItem(null, dataObject, contentDiv, section, arr);
	},
	
	showItem: function(data, contentDiv, oldContentDiv, section)
	{
		var that = this;
		
		/*contentDiv = APP.utils.isset(contentDiv)? contentDiv : that.windows[that.windows.length-1];
		oldContentDiv = APP.utils.isset(oldContentDiv)? oldContentDiv : that.windows[that.windows.length-2];
		this.toggleWindow(contentDiv, oldContentDiv);*/
		
		var title = "-";
		if (APP.utils.isset(this.sections[section].title) && APP.utils.isset(this.sections[section].title.title_toshow)) 
		{
			title = this.sections[section].title.title_toshow;
			$.each(this.sections[section].title.title_toshow_params, function(ii, vv)
			{
				title = APP.utils.replaceAll(ii, data[vv], title);
			});
		}
		
		var div = $('<div class="row" style="margin-bottom: 20px">\
						<div class="col-md-9 col-md-offset-3">\
							<ul class="breadcrumb"></ul>\
						</div>\
					</div>\
					<div class="row">\
						<div class="col-md-3">\
							<div class="row" style="margin-bottom: 20px">\
								<div class="col-xs-3">\
									<a id="button_cancel" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("cancel")+'" class="btn btn-default btn-lg tooltipElement" href="#"><i class="icon-arrow-left"></i></a>\
								</div>\
								<div class="col-xs-9">\
									<h1 class="text-danger" style="padding: 0px; margin: 0px"><strong>'+title+'</strong></h1>\
								</div>\
							</div>\
							<div class="row">\
								<div class="col-md-12">\
									<ul id="itemsRecipient" class="list-group">\
									</ul>\
								</div>\
							</div>\
						</div>\
						<div class="col-md-9">\
							<div id="subMainTableDiv"></div>\
						</div>\
					</div>');
		
		var items = APP.utils.isset(that.sections[section].menu)? that.sections[section].menu : [];
		
		var ul = div.find("ul.list-group");
		$.each(items, function(i, v)
		{
			//var activeClass = (i === 0)? "active" : "";
			var leftIcon = (APP.utils.isset(v.icon))? '<i class="icon icon-'+v.icon+' pull-left" style="margin-top: 1px; margin-right: 10px"></i>' : '';
			var li = $(	'<li class="list-group-item">\
							'+leftIcon+'<i class="icon icon-chevron-right pull-right"></i> '+APP.i18n.translate(v.label)+'\
						</li>');
			li.data(v);
			li.click(function()
			{
				var actualLi = $(this);
				var dataLi = actualLi.data();
				
				var smtd = that.windows[that.windows.length-1].find("#subMainTableDiv").first();
				smtd.hide();
				
				that.tmpSelectedItem = $(this);
				APP.config.removeActiveClasses(div.find(".list-group"), "li");
				actualLi.addClass("active");				
				
				var arr = [];
				
				if ($.inArray("update", that.sections[section].capabilities) > -1)
				{
					arr.push({
						htmlString: '<a id="button_save" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("save")+'" class="btn btn-lg btn-success tooltipElement" href="#"><i class="icon-ok icon-white"></i></a>',
						onClick: function(){ that.formSubmit(data[that.sections[section].primary_key], section, function(){
							that.selectedItem = APP.config.bc_getLastSrcElement();
							that.destroyWindow();
							if (that.windows.length === 1)
								oldContentDiv.empty();
							that.selectedItem.click();
						});
						}
					});
				}
				if ($.inArray("delete", that.sections[section].capabilities) > -1)
				{
					arr.push({
						htmlString: '<a id="button_remove" data-toggle="tooltip" data-placement="bottom" title="'+APP.i18n.translate("remove")+'" class="btn btn-lg btn-danger tooltipElement" href="#"><i class="icon-remove icon-trash"></i></a>',
						onClick: function(){ that.removeItem(data[that.sections[section].primary_key], {'section': section, 'contentDiv': contentDiv, 'callback': function(){
							that.selectedItem = APP.config.bc_getLastSrcElement();
							that.destroyWindow();
							if (that.windows.length === 1)
								oldContentDiv.empty();
							that.selectedItem.click();
						}});  }
					});
				}
				
				//APP.utils.updateBreadcrumb("replaceLast", dataLi.label);
				
				switch (dataLi.url)
				{
					case null:
						that.editItem(data[that.sections[section].primary_key], data, contentDiv.find("#subMainTableDiv"), section, arr);
						break;
					default:
						var subSection = null;
						var uuu = dataLi.url.split("?")[0];
						$.each(APP.config.localConfig.urls, function(i, v)
						{
							if (uuu === v)
								subSection = i;
						});
						if (!APP.utils.isset(subSection))
						{
							alert("Aggiungi alla voce urls della richiesta config: "+dataLi.url);
							return;
						}
						
						var uri = dataLi.url;
						$.each(dataLi.url_params, function(ii, vv)
						{
							uri = APP.utils.replaceAll(ii, data[vv], uri);
						});
						
						that.processSectionsProperty({
							'title': APP.i18n.translate(dataLi.label),
							'section': subSection,
							'filterString': "?"+uri.split("?")[1],
							'loadCallback': function(res){
								var obj = {
									'section': subSection,
									'items_per_page': res.data.items_per_page,
									'sort_fields': res.data.sort_fields,
									'tableContainer': contentDiv.find("#subMainTableDiv").hide(),
									'window': contentDiv,
									'oldWindow': oldContentDiv,
									'onSelectRow': function(t){ 
										that.onSelectTableRow(t, subSection, contentDiv);
									}
								};
								that.showTable(obj);
							}
						});
				}
			});
			ul.append(li);
		});
		
		contentDiv.html(div);
		APP.utils.updateBreadcrumb("add", {'icon': that.sections[section].icon, 'label': title, 'data': data, 'level': section, 'srcElement': that.selectedItem});
		ul.find("li").first().click();
		contentDiv.find("#button_cancel").tooltip().click(function(){
			that.tmpSelectedItem = that.selectedItem;
			that.destroyWindow();
			that.selectedItem = APP.config.bc_getLastSrcElement();
		});
	},
	
	editItem: function(id, dataObject, contentDiv, section, buttons)
	{
		var that = this;
		contentDiv = APP.utils.isset(contentDiv)? contentDiv : that.windows[that.windows.length-1];
		contentDiv.html(this.createFormTemplate(id, dataObject, this.sections[section], section, buttons));
		contentDiv.show();
		var form = contentDiv.find("#fm_"+section);
		
		if (form.find("div.mapbox").length > 0)
		{
			var ddd = form.find("div.mapbox");
			APP.map.setMap({container: ddd});
			
			if (APP.utils.isset(APP.map.globalData[APP.map.currentMapId].drawnItems))
			{
				APP.map.globalData[APP.map.currentMapId].map.removeLayer(APP.map.globalData[APP.map.currentMapId].drawnItems);
				APP.map.globalData[APP.map.currentMapId].drawnItems = null;
			}
			if (APP.utils.isset(APP.map.globalData[APP.map.currentMapId].drawControl))
			{
				APP.map.globalData[APP.map.currentMapId].map.removeControl(APP.map.globalData[APP.map.currentMapId].drawControl);
				APP.map.globalData[APP.map.currentMapId].drawControl = null;
			}
			
			var index = APP.utils.getIndexFromField(this.sections[this.currentSection].columns, "form_input_type", "mapbox");
			if (index > -1)
			{
				var obj = this.sections[this.currentSection].columns[index];
				
				if (obj.map_box_editing)
				{
					var opt = {
						draw: {
							polyline: false,
							polygon: false,
							circle: false,
							marker: false,
							rectangle: false
						},
						edit: {
							edit: {
								selectedPathOptions: {}
							},
							remove: {}
						}
					};
					if (!obj.editable)
						opt.edit.edit = false;
					/*if (!obj.removable)
						opt.edit.remove = false;*/
						
					$.each(obj.map_box_editing_geotype, function(i,v)
					{
						opt.draw[v] = {title: APP.i18n.translate(v)};
					});
					APP.map.toggleDrawEditor(APP.map.currentMapId, true, opt);
					
					APP.map.globalData[APP.map.currentMapId].map.on('draw:created', function (e)
					{
						var type = e.layerType;
						var	layer = e.layer;
						
						APP.map.globalData[APP.map.currentMapId].drawnItems.addLayer(layer);
					});
					APP.map.globalData[APP.map.currentMapId].map.on('draw:edited', function (e)
					{
						console.log("imposta l'evento draw:edited");
					});
					APP.map.globalData[APP.map.currentMapId].map.on('draw:deleted', function (o)
					{
						$.each(o.layers.getLayers(), function(ij,vj){
							APP.map.globalData[APP.map.currentMapId].drawnItems.removeLayer(vj);
						});
						//APP.map.globalData[APP.map.currentMapId].drawnItems.clearLayers(layers);
					});
				}
				
				var inp = form.find("input.mapbox");
				if (inp.length > 0 && inp.val() != "")
				{
					var value = inp.val();
					
					var gj = L.geoJson($.parseJSON(value), {
						/*
						coordsToLatLng: function(coords)
						{
							return [coords[1],coords[0]];
						},
						*/
						onEachFeature: function(feature, layer)
						{
							switch( feature.type)
							{
								
								case "MultiLineString": 
									$.each(feature.coordinates, function(i,v){
										L.polyline(L.GeoJSON.coordsToLatLngs(v)).addTo(APP.map.globalData[APP.map.currentMapId].drawnItems);
									});
								
									/*var cs = feature.coordinates;
									$.each(cs, function(i,v){
										$.each(v,function(j,k){
											v[j] = [k[1],k[0]];
										});
										L.polyline(v).addTo(APP.map.globalData[APP.map.currentMapId].drawnItems);
									});
									*/	
									break
								case "LineString":
									L.polyline(feature.coordinates).addTo(APP.map.globalData[APP.map.currentMapId].drawnItems);
									break;
								case "MultiPolygon": 
									$.each(feature.coordinates, function(i,v){
										L.polygon(L.GeoJSON.coordsToLatLngs(v,1)).addTo(APP.map.globalData[APP.map.currentMapId].drawnItems);
									});
									break;
								case "Polygon":
									L.polygon(feature.coordinates).addTo(APP.map.globalData[APP.map.currentMapId].drawnItems);
									/*
									var cs = feature.coordinates;
									$.each(cs, function(i,v){
										$.each(v,function(w,z){
											$.each(z,function(j,k){
												z[j] = [k[1],k[0]];
											});
											L.polygon(z).addTo(APP.map.globalData[APP.map.currentMapId].drawnItems);
										});
									});
									*/
									break;
								default:
									layer.addTo(APP.map.globalData[APP.map.currentMapId].drawnItems);

							}
						},
						/*
						pointToLayer: function (data, latlng) {
							return L.circleMarker(latlng);
						},
						*/
					});
					
					APP.map.globalData[APP.map.currentMapId].map.fitBounds(APP.map.globalData[APP.map.currentMapId].drawnItems.getBounds());
				}
				
				if (obj.map_box_fileloading)
				{
					var options_eraseall = {
						layer :  APP.map.globalData[APP.map.currentMapId].drawnItems
					};
					APP.map.globalData[APP.map.currentMapId].eraseAllControl = new L.Control.EraseALL(options_eraseall); 
					APP.map.globalData[APP.map.currentMapId].map.addControl(APP.map.globalData[APP.map.currentMapId].eraseAllControl);	
					
					//var style = {color:'red', opacity: 1.0, fillOpacity: 1.0, weight: 2, clickable: false};
					L.Control.FileLayerLoad.LABEL = '<i class="icon-folder-open"></i>';
					var control = L.Control.fileLayerLoad({
						fitBounds: true,
						layerOptions: {
							pointToLayer: function (data, latlng) {
								return L.circleMarker(latlng);
							},
						},
					}).addTo(APP.map.globalData[APP.map.currentMapId].map);
					
					
					control.loader.on('data:loaded', function (e) {
						var ttt = arguments;
						APP.map.globalData[APP.map.currentMapId].drawnItems.addLayer(e.layer);
						//APP.map.globalData[APP.map.currentMapId].map.removeLayer(e.layer);
					});					
					
				}
			}
		}
		
		APP.utils.setLookForm(form, id);
	},
	
	deactiveItem: function(id, section, contentDiv)
	{
		var that = this;
		
		var deactiveConfirmMsg = (APP.utils.isset(that.sections[section].messages) && APP.utils.isset(that.sections[section].messages['deactive_confirm']))? that.sections[section].messages['deactive_confirm'] : APP.i18n.translate("off_confirm");
		APP.utils.showMsg({
			title: APP.i18n.translate("warning"),
			content: deactiveConfirmMsg,
			buttons: [
				{
					text: APP.i18n.translate("yes"),
					click: function() {		
						$.ajax({
							type: "POST",
							url: APP.config.localConfig.urls[section]+"/"+id,
							data: {"attivato": false},
							success: function(data)
							{
								that.loadData(APP.config.localConfig.urls[section], that.sections[section]);
							},
							error: function(result){ APP.utils.showErrMsg(result); },
						});
						
						$(this).parents(".modal").first().modal('hide');
					}
				},					
				{
					text: APP.i18n.translate("no"),
					click: function() {
						$(this).parents(".modal").first().modal('hide');
					}
				}
			]
		});
	},
	
	activeItem: function(id, section, contentDiv)
	{
		var that = this;
		
		var activeConfirmMsg = (APP.utils.isset(that.sections[section].messages) && APP.utils.isset(that.sections[section].messages['active_confirm']))? that.sections[section].messages['active_confirm'] : APP.i18n.translate("on_confirm");
		APP.utils.showMsg({
			title: APP.i18n.translate("warning"),
			content: activeConfirmMsg,
			buttons: [
				{
					text: APP.i18n.translate("yes"),
					click: function() {		
						$.ajax({
							type: "POST",
							url: APP.config.localConfig.urls[section]+"/"+id,
							data: {"attivato": true},
							success: function(data)
							{
								that.loadData(APP.config.localConfig.urls[section], that.sections[section]);
							},
							error: function(result){ APP.utils.showErrMsg(result); },
						});
						
						$(this).parents(".modal").first().modal('hide');
					}
				},					
				{
					text: APP.i18n.translate("no"),
					click: function() {
						$(this).parents(".modal").first().modal('hide');
					}
				}
			]
		});
	},
	
	createFormTemplate: function(identifier, dataObject, sectionTarget, sectionLabel, buttons)
	{
		var that = this;
		var obj = {};
		var index = null;
		if (APP.utils.isset(identifier))
		{
			/*
			if (sectionLabel == "fleetgroup")
				obj = APP.orgchart.getNodeFromId(identifier);
			else
			*/
			{
				index = APP.utils.getIndexFromField(sectionTarget.values, sectionTarget.primary_key, identifier);
				if (index > -1)
					obj = sectionTarget.values[index];
			}
		}
		else
		{
			//if (sectionLabel == "fleetgroup")
				obj = APP.utils.isset(dataObject)? dataObject : obj;
		}
		
		var enctype = APP.utils.isset(sectionTarget.enctype)? 'enctype="'+sectionTarget.enctype+'"' : '';
		
		var form = null;
		var v = null;
		var position = null;
		
		//sectionTarget.wizard_mode = true;
		
		var displayInputs = function(k, parNode)
		{
			$.each(k.fields, function(j1, k1)
			{
				var index = APP.utils.getIndexFromField(sectionTarget.columns, "name", k1);
				if (index == -1)
					return true;
					
				var v = sectionTarget.columns[index];
				var valore = (APP.utils.isset(obj[v.name]))? obj[v.name] : "";
				if (!APP.utils.isset(valore) || valore === "")
					valore = APP.utils.getSecondaryValue(v);
									
				if (($.type(v.form_show) === "boolean" && !v.form_show) || ($.isPlainObject(v.form_show) && ((!v.form_show.insert && !APP.utils.isset(identifier)) || (!v.form_show.update && APP.utils.isset(identifier))))) // se deve essere visualizzato nel form
					return true;
				
				var required = APP.utils.isset(v.required)? " required " : "";
				var displayOnOff = (v.form_input_type == "hidden")? "style='display: none'" : "";
				var inp = null;
				
				if (APP.utils.isset(v.scrollto_button))
				{
					var str = $('<a data-toggle="tooltip" data-placement="bottom" title="'+v.label+'" class="btn btn-lg btn-default tooltipElement" href="#"><i class="icon '+v.scrollto_button.icon+'"></i></a>');
					//str.css("margin-right", "5px");
					str.click(function(){ 
						$('html, body').animate({
							scrollTop: form.find("#APP-"+v.name).first().offset().top-60
						}, 1000);
					});
					fb.append(str);
				}
				
				inp = APP.utils.getInputFormat(identifier, obj, required, sectionLabel, sectionTarget, that, v, valore, form);
				
				if (inp === null)
					return true;
				
				var spanValues = null;
				if (APP.utils.isset(sectionTarget.cols_relation))
					spanValues = sectionTarget.cols_relation;
				else
					spanValues = (APP.utils.isset(position) && position === "blockColForm")? [2, 9, 1] : [2, 9, 1];
				
				var myFieldLabel = (APP.utils.isset(v.label))? v.label+":" : null;
				if (!myFieldLabel)
				{
					spanValues[1] = spanValues[0]+spanValues[1];
					spanValues[0] = 0;
				}
				if (!APP.utils.isset(v.description))
				{
					spanValues[1] = spanValues[1]+spanValues[2];
					spanValues[2] = 0;
				}

				var ctrlGrp = $("<div class='form-group'>\
									<div class='controls col-md-"+spanValues[1]+"' "+displayOnOff+"></div>\
								</div>");
				
				if (myFieldLabel)
					ctrlGrp.prepend("<label class='control-label col-md-"+spanValues[0]+"' for='APP-"+v.name+"' "+displayOnOff+">"+((v.required)? APP.utils.getRequiredSymbol() : '')+myFieldLabel+"</label>");
				
				var cssClass = (APP.utils.isset(v.css_class))? v.css_class : "";
				ctrlGrp.addClass(cssClass);
								
				ctrlGrp.find(".controls").append(inp);
				
				if (APP.utils.isset(v.description))
				{
					var descrInput = $("<div class='descrInput col-md-"+spanValues[2]+"'></div>");
					descrInput.append($('<span id="description_'+v.name+'" data-toggle="tooltip" title="'+v.description+'" data-placement="auto" data-container="body" class="tooltipElement text-muted" style="padding-left: 5px"><i class="icon icon-info-sign"></i></span>'));
					ctrlGrp.append(descrInput);
				}
								
				parNode.append(ctrlGrp);							
				
				if (($.type(v.editable) === "boolean" && !v.editable) || ($.isPlainObject(v.editable) && ((!v.editable.insert && !APP.utils.isset(identifier)) || (!v.editable.update && APP.utils.isset(identifier)))) || (APP.utils.isset(identifier) && $.inArray("update", sectionTarget.capabilities) === -1) || (!APP.utils.isset(identifier) && $.inArray("insert", sectionTarget.capabilities) === -1))//if (!v.editable || $.inArray("update", sectionTarget.capabilities) === -1)
				{
					if (parNode.find("#APP-"+v.name).is(":input"))
						parNode.find("#APP-"+v.name).attr("disabled", true);
					else
						parNode.find("#APP-"+v.name).find(":input").attr("disabled", true);
				}
					
				if (APP.utils.isset(v.send_with_file) && v.send_with_file)
					parNode.find("#APP-"+v.name).addClass("sendWithFile");
			});
		};
		
		if (!APP.utils.isset(sectionTarget.groups) || sectionTarget.groups.length === 0)
		{
			sectionTarget.groups = [{
				"name": sectionLabel,
				"position": "left",
				"fields": []
			}];
			$.each(sectionTarget.columns, function(i, v)
			{
				sectionTarget.groups[0].fields.push(v.name);
			});
		}
		
		if (APP.utils.isset(sectionTarget.wizard_mode) && sectionTarget.wizard_mode === true && sectionTarget.groups.length > 1)
		{
			form = $('<form id="fm_'+sectionLabel+'" class="form-horizontal wizard" '+enctype+' role="form"></form>');
			
			form.prepend('<p id="formButtons" class="row" style="margin-bottom: 30px; padding-left: 15px; display: none"></p>');
			
			$.each(buttons, function(i, v)
			{
				var str = $(v.htmlString);
				if (str.attr("id") === "button_save")
				{
					form.on("wizardfinish", function(){ v.onClick($(this)); });
					return true;
				}
				str.css("margin-right", 7);
				str.click(function(){ v.onClick($(this)); });
				form.find("#formButtons").append(str);
			});
			
			$.each(sectionTarget.groups, function(j, k)
			{
				var step = $('<fieldset>\
								<legend>'+APP.i18n.translate(k.name)+'</legend>\
							</fieldset>');
				
				if (k['class'])
					step.addClass(k['class']);
				
				form.append(step);
				displayInputs(k, step);
				form.data({
					jWizardSettings: 
					{
						buttons: {
							cancel: false,
							next:
							{
								"class": "btn btn-primary",
								text: APP.i18n.translate("next"),
								type: "button",
							},
							prev:
							{
								"class": "btn btn-default",
								text: APP.i18n.translate("previous"),
								type: "button",
							},
							finish:
							{
								"class": "btn btn-success",
								text: APP.i18n.translate("done"),
								type: "button",
							}
						},
						allButtons: buttons,
						effects: {
							steps: {
								hide: {
									effect:    "blind",
									direction: "right",
									duration:  250
								},
								show: {
									effect:    "fade",
									direction: "right",
									duration:  250
								}
							}
						},
						wizardfinish: function()
						{
							
						},
					}
				});
			});
			form.find("fieldset").first().append('<input type="hidden" name="csrf_token" class="tokenInput" value="'+APP.config.getToken(sectionTarget.resource)+'">');
			
			return form;
		}
		
		var form = $('<form id="fm_'+sectionLabel+'" class="form-horizontal" '+enctype+' role="form">\
						<input type="hidden" name="csrf_token" class="tokenInput" value="'+APP.config.getToken(sectionTarget.resource)+'">\
						<div class="row">\
							<div class="col-md-12">\
								<div id="formButtons" class="well well-sm" style="text-align: center; display:none"></div>\
							</div>\
						</div>\
					</form>');
			
		var fb = form.find("#formButtons");
		if (buttons.length>0)
		{
			fb.show();
			$.each(buttons, function(i, v)
			{
				var str = $(v.htmlString);
				//str.tooltip();
				str.css("margin", "3px");
				//fb.append("&nbsp;");
				str.click(function(){ v.onClick($(this)); });
				fb.append(str);
			});
		}
		
		if (APP.utils.isset(APP.config.localConfig.crud_menu))
		{
			var hDistance = 4;
			switch(APP.config.localConfig.crud_menu.position)
			{
				case "TL":
					fb.css({
						'position': "fixed",
						'z-index': 50,
						'top': 55,
						'left': hDistance
					});
					break;
				case "TC":
					fb.css({
						'position': "fixed",
						'z-index': 50,
						'top': 55,
						'left': "50%",
						'right': "50%",
					});
					break;
				case "TR":
					fb.css({
						'position': "fixed",
						'z-index': 50,
						'top': 55,
						'right': hDistance
						//'right': "2%"
					});
					break;
				case "ML":
					fb.css({
						'position': "fixed",
						'z-index': 50,
						'top': "50%",
						'left': hDistance,
					});
					break;
				case "MR":
					fb.css({
						'position': "fixed",
						'z-index': 50,
						'top': "50%",
						'right': hDistance,
					});
					break;
				case "BL":
					fb.css({
						'position': "fixed",
						'z-index': 50,
						'bottom': 0,
						'left': hDistance,
					});
					break;
				case "BC":
					fb.css({
						'position': "fixed",
						'z-index': 50,
						'bottom': 0,
						'left': "50%",
						'right': "50%",
					});
					break;
				case "BR":
					fb.css({
						'position': "fixed",
						'z-index': 50,
						'bottom': 0,
						'right': hDistance,
					});
					break;
			}
			switch(APP.config.localConfig.crud_menu.orentation)
			{
				case "H":
					break;
				case "V":
					//alert(fb.find(".btn:first").width());
					fb.css("width","5%");
					break;
			}
		}
		
		if (APP.utils.isset(sectionTarget.tabs))
		{
			var ul = $('<ul class="nav nav-tabs"></ul>');
			var tabsContent = $('<div class="itemContentTabs row"></div>');
			$.each(sectionTarget.tabs, function(i, v)
			{
				var classe = (i===0)? "active" : "";
				var tab = $('<li id="'+v.name+'" role="presentation" class="'+classe+'"><a href="#">'+APP.i18n.translate(v.name)+'</a></li>');
				if (v.icon)
					tab.find("a").prepend('<i class="icon-'+v.icon+'"></i> ');
				tab.click(function()
				{
					var tabId = tab.attr("id");
					ul.find("li.active").removeClass("active");
					tab.addClass("active");
					form.find(".itemContentTabs").find('.active').removeClass("active").hide();
					form.find(".itemContentTabs").find("#"+tabId).addClass("active").show();
					if (v.groups)
						APP.utils.setLookForm(form,form.attr("id"));
					else
					{
						var urlV = v.url_values;
						if (v.url_params)
						{
							$.each(v.url_params, function(j,k){
								urlV = APP.utils.replaceAll(j, obj[k], urlV);
							});
						}

						var getFilteredItems = function()
						{
							$.ajax({
								type: 'GET',
								url: urlV,
								success: function(result)
								{
									if (result.data.items.length>0)
									{
										var idField = that.sections[v.datastruct].primary_key;
										$.each(result.data.items, function(j,k){
											var index = APP.utils.getIndexFromField(that.sections[v.datastruct].values, idField, k[idField]);
											if (index>-1)
												that.sections[v.datastruct].values[index] = k;
											else
												that.sections[v.datastruct].values.push(k);
										});
										
										that.showTable({
											tableContainer: form.find(".itemContentTabs").find("#"+tabId), 
											section: v.datastruct,
											sort_fields: {},
											items_per_page: result.data.items_per_page, 
											window: that.windows[that.windows.length-1], 
											oldWindow: (that.windows.length-2>=0)? that.windows[that.windows.length-2] : that.windows[that.windows.length-1], 
											onSelectRow: function(t){
												APP.config.workSpace.navigate(v.datastruct+"/"+t[idField], {trigger: true, replace: true});
											}
										});
									}
								},
								error: function(result)
								{
									APP.utils.showErrMsg(result);
								}
							});
						};
						
						if (!that.sections.hasOwnProperty(v.datastruct))
						{
							that.sections[v.datastruct] = APP.utils.setBaseStructure(v.datastruct, v.datastruct);
							that.getStructure(APP.config.localConfig.urls['dStruct']+"?tb="+v.datastruct, v.datastruct, "",true, function(){ getFilteredItems()});
						}
						else
							getFilteredItems();
					}
					var navTabs = form.find(".itemContentTabs").find(".tabContent");
					if (navTabs.length>0)
					{
						$.each(navTabs, function(i,v){
							var v = $(v);
							if (v.hasClass("active"))
							{
								v.css("opacity",1);
								v.show();
							}
							else
								v.hide();
						});
					}
				});

				ul.append(tab);
				var tabc = $('<div id="'+v.name+'" class="col-md-12 tabContent '+classe+'">\
								<div class="row">\
									<div class="leftColForm col-md-6"></div>\
									<div class="rightColForm col-md-6"></div>\
								</div>\
								<div class="row">\
									<div class="blockColForm col-md-12"></div>\
								</div>\
							</div>');
				tabc.css("opacity",0);
				tabsContent.append(tabc);
			});
			form.append('<div class="row"><div class="itemTabsDiv col-md-12"></div></div>');
			form.find('.itemTabsDiv').append(ul);
			form.append(tabsContent);
			
		}
		else
		{
			form.append('<div class="row">\
				<div class="leftColForm col-md-6"></div>\
				<div class="rightColForm col-md-6"></div>\
			</div>');
			form.append('<div class="row">\
				<div class="blockColForm col-md-12"></div>\
			</div>');
		}
		
		$.each(sectionTarget.groups, function(j, k)
		{
			var group = $(	'<fieldset style="padding-bottom: 22px">\
								<legend class="text-info">'+APP.i18n.translate(k.name)+'</legend>\
							</fieldset>');
			
			if (k['class'])
				group.addClass(k['class']);
			
			position = "leftColForm";
			if (APP.utils.isset(k.position))
			{
				switch (k.position)
				{
					case "left":
						position = "leftColForm";
						break;
					case "right":
						position = "rightColForm";
						break;
					case "block":
						position = "blockColForm";
						break;
					default:
						console.log("aggiungi questa posizione: "+k.position);
				}
			};
			if (sectionTarget.tabs)
			{
				$.each(sectionTarget.tabs, function(i, v)
				{
					var tabsIndex = $.inArray(k.name, v.groups);
					if (tabsIndex>=0)
					{
						var tabName = sectionTarget.tabs[i].name;
						form.find("#"+tabName).find("."+position).append(group);
					}
					//form.find("."+position).append(group);
				});
			}
			else
				form.find("."+position).append(group);
			
			displayInputs(k, group);
		});
		/*
		var scrollToTopButton = $('<div class="text-center"><span class="btn btn-default"><i class="icon icon-circle-arrow-up"></i> '+APP.i18n.translate("scroll_to_top")+'</span></div>');
		scrollToTopButton.click(function(){
			$('html, body').animate({
				scrollTop: 0,
			}, 1000);
		});
		form.append(scrollToTopButton);
		*/
		
		return form;
	},
	
	formSubmit: function(id, section, endCallBack)
	{
		var form = $("#fm_"+section);
		APP.utils.resetFormErrors(form);
		var that = this;
		
		var mtype = APP.utils.isset(id)? 'POST' : 'PUT';
		var queue = APP.utils.isset(id)? "/"+id : "";
		
		if (form.find(".textEditor").length > 0)
			tinyMCE.triggerSave();
		
		var d = form.serializeArray();
		
		/*
		form.find(".datepicker").each(function()
		{
			var x = $(this);
			var index = APP.utils.getIndexFromField(d, "name", x.attr("name"));
			if (index > -1)
			{
				if (!APP.utils.isset(x.val()) || APP.utils.isEmptyString(x.val()))
					return true;
				var dateArr = x.val().split("/");
				if ($.type(dateArr) === "array" && dateArr.length === 3)
				{
					d[index].value = dateArr[2]+"-"+dateArr[1]+"-"+dateArr[0];
				}
			}
		});
		*/
		form.find(':input:disabled ').each( function() {
			if ($(this).hasClass("fileupload") || $(this).hasClass("subform") || $(this).hasClass("multifield")  || !APP.utils.isset($(this).attr('name')) || $(this).hasClass("mapbox"))
				return true;
			var o = {};
			o.name = $(this).attr('name');
			o.value = $(this).val();
			d.push(o);
        });
		
		//if (form.find(".jquery_fileupload"))
			//form.attr("enctype","multipart/form-data");
		
		if (form.find(".fileupload").length > 0)
		{
			form.attr("enctype","multipart/form-data");
			$.each(form.find(".fileupload"), function()
			{
				var n = $(this).attr("name");
				if (APP.utils.isset(n))
					d.push(APP.fileuploader.preserialize(n));
			});
		}
		if (form.find(".subformTable").length>0)
		{
			$.each(form.find(".subformTable"), function()
			{
				var sfn = $(this).attr("name");
				d.push(APP.subforms.preserialize(sfn));
			});
		}
		if (form.find(".multifieldTable").length>0)
		{
			$.each(form.find(".multifieldTable"), function()
			{
				var sfn = $(this).attr("name");
				d.push(APP.multifields.preserialize(sfn));
				
				var items = d[d.length-1].value.split(';');
				var uguaglianze = items[0].split('&');
				var namesArray = [];
				$.each(uguaglianze, function(i, v){
					var voce = v.split('=')[0];
					namesArray.push(voce);
				});
				
				var elemsToDelete = [];
				
				$.each(d, function(i,v){
					if ($.inArray(v.name, namesArray) > -1)
						elemsToDelete.push(parseInt(i));
				});
				
				elemsToDelete.sort(function(a, b){return b-a});
				$.each(elemsToDelete, function(i,number){
					d.splice(number,1);
				});
			});
		}
		if (form.find(":input.mapbox").length>0)
		{
			$.each(form.find(":input.mapbox"), function(i,v)
			{
				var name = $(v).attr("name");
				var index = APP.utils.getIndexFromField(d, "name", name);
				if (index === -1)
					return true;

				d[index] = APP.map.preserialize(name);
			});
		}
		
		var sendNow = function()
		{
			$.ajax({
				type: mtype,
				url: APP.config.localConfig.urls[section]+queue,
				data: d,
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, form))
					{
						APP.utils.showNoty({title: APP.i18n.translate("success"), type: "success", content: APP.i18n.translate("operation_success")});
						
						if (APP.utils.isset(data.data.actions))
						{
							$.each(data.data.actions, function(j,k)
							{
								switch(j)
								{
									case "reinsert":
										var labels = {
											'yes': APP.i18n.translate("Yes"),
											'no': '<i class="icon icon-remove"></i> '+APP.i18n.translate("No")
										};
										var callbacks = {
											'yes': function()
											{												
												var newDiv = null;
												var oldDiv = null;
												that.destroyWindow();
												if (that.windows[that.windows.length-1].find("#subMainTableDiv").length>0)
												{
													newDiv = that.windows[that.windows.length-1].find("#subMainTableDiv").first();
													oldDiv = that.windows[that.windows.length-1];
												}
												else
												{
													that.createWindow();
													newDiv = that.windows[that.windows.length-1];
													oldDiv = (that.windows.length-2 < 0)? 0 : that.windows.length-2;
												}
												
												that.addItem(k, newDiv, oldDiv, section);
											},
											'no': function(){
												defaultCallback();
											},
										};
										APP.utils.confirmMsg(APP.i18n.translate("reinsert_question"), labels, callbacks);
										break;
									default:
										break;
								}
							});
						}
						else
						{
							if (APP.utils.isset(endCallBack) && $.isFunction(endCallBack))
								endCallBack();
							else
							{
								that.selectedItem.click();
							}
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
		};
		var labels = {
			'yes': APP.i18n.translate("save"),
			'no': APP.i18n.translate("cancel")
		};
		var callbacks = {
			'yes': function(){ sendNow(); },
			'no': null,
		};
		var saveConfirmMsg = (APP.utils.isset(that.sections[section]) && APP.utils.isset(that.sections[section].messages) && APP.utils.isset(that.sections[section].messages['save_confirm']))? that.sections[section].messages['save_confirm'] : APP.i18n.translate("save_confirm");
		APP.utils.confirmMsg(saveConfirmMsg, labels, callbacks);
	},
	
	removeItem: function(id, params)
	{
		var that = this;
		
		var btns = $('<button class="btn btn-primary" type="button">'+APP.i18n.translate("yes")+'</button><button class="btn btn-default" data-dismiss="modal" aria-hidden="true">'+APP.i18n.translate("no")+'</button>');
		var yesBtn = $(btns[0]);
		yesBtn.click(function()
		{
			$.ajax({
				type: "DELETE",
				url: APP.config.localConfig.urls[params.section]+"/"+id,
				data: params.contentDiv.find("#fm_"+params.section).serializeArray(),
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, null))
					{
						params.callback();
						//that.loadData(APP.config.localConfig.urls[params.section], that.sections[params.section]);
					}
					else
						APP.utils.showErrMsg(data);
				},
				error: function(result){ APP.utils.showErrMsg(result); },
			});
			
			$(this).parents(".modal").first().modal('hide');
		});		
		
		var deleteConfirmMsg = (APP.utils.isset(that.sections[params.section].messages) && APP.utils.isset(that.sections[params.section].messages['delete_confirm']))? that.sections[params.section].messages['delete_confirm'] : APP.i18n.translate("remove_confirm");
		APP.utils.showMsg({
			title: APP.i18n.translate("warning"),
			content: deleteConfirmMsg,
			buttons: btns
		});
	},
});