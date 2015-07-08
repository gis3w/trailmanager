$.extend(APP.anagrafica,
{
	mainDiv: null,
	currentBreadcrumb: null,
	sections: {},
	fkRequests: {},
	previousSection: null,
	currentSection: null,
	
	finish: function()
	{
		
	},
	
	callbackCall: function(cb)
	{
		if (APP.utils.isset(cb) && $.isFunction(cb))
			cb();
		else
			return false;
	},
	
	hideAllWindows: function(div)
	{
		var that = this;
		
		if (!APP.utils.isset(that.sections) || !APP.utils.isset(div))
			return false;
		
		$.each(that.sections, function(i, v)
		{
			$.each(that.sections[i].windows, function(j,k)
			{
				if ($.isFunction(k.hide))
					k.hide();
				if (div.find(k).length>0 && k.hasClass("active"))
					k.removeClass("active");
			});
		});
	},
	
	showWindow: function(target, windowId, callback)
	{
		var that = this;
		
		if (!APP.utils.isset(target) || !APP.utils.isset(target.windows) || !APP.utils.isset(windowId) || !APP.utils.isset(target.windows[windowId]))
			return false;
		
		var w = target.windows[windowId];
		w.addClass("active");
		
		return (APP.utils.isset(callback))? w.fadeIn({ complete: callback}) : w.show();
	},
	
	onRowSelected: function(target, itemId)
	{
		var that = this;
	},
	
	createTable: function(target, callback)
	{
		var that = this;
		
		if (!APP.utils.isset(target))
			return false;
		
		var table = null;
		var thead = null;
		var tbody = null;
		var tfoot = null;
		var cols = [];
		var dateboxFields = [];
		var datetimeboxFields = [];
		
		if (target.windows.table.find("table").length==0)
		{
			table = $('<table class="table table-hover table-bordered">\
							<thead><tr></tr></thead>\
							<tbody></tbody>\
							<tfoot></tfoot>\
						</table>');
			
			thead = table.find("thead tr");
			tbody = table.find("tbody");
			tfoot = table.find("tfoot");
			
			$.each(target.fields, function(i, v)
			{
				if (!v.table_show)
					return true;
				
				var th = $('<th>'+v.label+'</th>');
				if (APP.utils.isset(v.description))
				{
					th.tooltip({
						title: v.description,
						container: '#'+target.windows.table.attr("id"),
						placement: 'auto',
					});
				}
				thead.append(th);
				cols.push(i);
				
				if (v.form_input_type === "datebox")
					dateboxFields.push(cols.length-1);
				if (v.form_input_type === "datetimebox")
					datetimeboxFields.push(cols.length-1);
			});
			
			table.data({
				cols: cols,
				dateboxFields: dateboxFields,
				datetimeboxFields: datetimeboxFields
			});
		}
		else
		{
			table = target.windows.table.find("table:first");
			
			cols = table.data().cols;
			dateboxFields = table.data().dateboxFields,
			datetimeboxFields = table.data().datetimeboxFields;
			
			if ( $.fn.DataTable.isDataTable( table ) )
				table.DataTable().clear().draw().destroy();
			tbody = table.find("tbody");
		}
		
		$.each(target.values, function(i, v)
		{
			var tr = $('<tr id="'+target.resource+'_'+v.id+'"></tr>')
			.css("cursor", "pointer")
			.data(v)
			.click(function()
			{
				that.onRowSelected(target, $(this).data());
			});
			$.each(cols, function(j, k)
			{
				tr.append("<td>"+v[k]+"</td>")
			});
			tbody.append(tr);
		});
		
		var iDisplayLength = APP.utils.isset(target.items_per_page)? target.items_per_page : 10;
		var aaSorting = APP.utils.isset(target.sort_fields)? target.sort_fields : [[0, 'asc']];
		
		if (APP.utils.isset(target.windows.table) && target.windows.table.find("table").length==0)
			target.windows.table.html(table);
		
		table.DataTable({
			//"retrieve": true,
			"paging": true,
			"pagingType": "full_numbers",
			"pageLength": iDisplayLength,
			"order": aaSorting,
			"jQueryUI": false,
			"columnDefs": [
				{ "type": "date-eu", "targets": dateboxFields },
				{ "type": "date-euro", "targets": datetimeboxFields },
			],
			"language": APP.utils.getDataTableLanguage(),
			"fnDrawCallback": function( oSettings )
			{
				if (APP.utils.isset(target.filter))
					APP.filter.init(target.windows.table.find("#filterButton_"+that.currentSection), that.currentSection , callback);
			},
			"initComplete": function(oSettings, json)
			{
				target.windows.table.fadeIn(APP.config.fadeInDelay, function()
				{
					that.callbackCall(callback);
				});
			}
		});
		
		return table;
	},
	
	getData: function(target, filterString, callback)
	{
		var that = this;
		
		if (!APP.utils.isset(target))
			return false;
		
		if ($.inArray("list", target.capabilities) > -1)
		{
			filterString = (APP.utils.isset(filterString) && $.type(filterString) === "string")? filterString : "";
			$.ajax({
				type: 'GET',
				url: APP.config.localConfig.urls[target.resource]+filterString,
				dataType: 'json',
				success: function(result)
				{
					if (!APP.utils.checkError(result.error, null))
					{
						if (APP.utils.isset(result.data))
							target.values = result.data.items;
						that.callbackCall(callback);
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
		{
			APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("list_capability_denied")});
			return false;
		}
	},
	
	getStructure: function(target, section, callback)
	{
		var that = this;
		
		if (!APP.utils.isset(target) || !APP.utils.isset(section))
			return false;
		
		if (APP.utils.isset(target[section].fields) && !$.isEmptyObject(target[section].fields))
			that.callbackCall(callback);
		else			
			$.ajax({
				type: 'GET',
				url: APP.config.localConfig.urls['dStruct']+"?tb="+target[section].resource,
				dataType: 'json',
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, null))
					{
						$.extend(target[section], data.data);
						that.callbackCall(callback);
					}
					else
						APP.utils.showErrMsg(data);
				},
				error: function(data)
				{
					APP.utils.showErrMsg(data);
				}
			});
	},
	
	initSection: function(target, section)
	{
		var that = this;
		
		if (!APP.utils.isset(target) || !$.isPlainObject(target) || !APP.utils.isset(section))
			return false;
		
		if (target.hasOwnProperty(section))
			return target[section];
		
		return {
			resource: section,
			windows: {
				table: $('<div id="'+section+'_table" style="display:none" class="table-responsive"></div>').appendTo(that.mainDiv),
				item: $('<div id="'+section+'_item" style="display:none"></div>').appendTo(that.mainDiv),
			},
			form: null,
			groups: [],
			values: []
		};
	},
	
	getHtmlTable: function(target, fs, callback)
	{
		var that = this;
		
		$.ajax({
			type: 'GET',
			url: 'application/tables/_'+target.resource+'.php?filter='+fs,
			dataType: 'html',
			success: function(data, stato)
			{
				var t = target.windows.table.find("table");
				if (t.length > 0)
					if ( $.fn.DataTable.isDataTable( t ) )
						t.DataTable().destroy();
				
				target.windows.table.html(data);
				var table = target.windows.table.find("table");
				table.DataTable(
				{
					"paging": true,
					"pagingType": "full_numbers",
					"pageLength": 10,
					"order": eval(table.attr('data-order')),
					"jQueryUI": false,
					/*"columnDefs": [
						{ "type": "date-eu", "targets": dateboxFields },
						{ "type": "date-euro", "targets": datetimeboxFields },
					],*/
					"language": APP.utils.getDataTableLanguage(),
					"fnDrawCallback": function( oSettings )
					{
						if (APP.utils.isset(target.filter))
							APP.filter.init(target.windows.table.find("#filterButton_"+that.currentSection), that.currentSection , callback);
					},
					"initComplete": function(oSettings, json)
					{
						target.windows.table.fadeIn(APP.config.fadeInDelay, function()
						{
							that.callbackCall(callback);
						});
					}
				});
					
				target.windows.table.fadeIn(APP.config.fadeInDelay, function()
				{
					table.find("tbody tr").off("click").click(function()
					{
						var trId = $(this).attr("id");
						var index = trId.lastIndexOf('_');
						var resource = trId.substr(0, index);
						var itemId = trId.substr(index+1);
						that.onRowSelected(that.sections[resource], itemId);
					});
					that.callbackCall(callback);
				});
			},
			error: function(data)
			{
				APP.utils.showErrMsg(data);
			}
		});
	},
	
	start: function(div, section, filterString)
	{
		var that = this;
		
		if (!APP.utils.isset(div) || !APP.utils.isset(section) || $.type(section) != 'string')
			return false;
		
		that.mainDiv = $(div);
		
		that.previousSection = that.currentSection;
		that.currentSection = section;
				
		that.sections[that.currentSection] = that.initSection(that.sections, that.currentSection);
		
		that.hideAllWindows(div);
		
		var fs = (APP.utils.isset(filterString) && $.type(filterString) == "string")? filterString : "";
		
		that.getStructure(that.sections, that.currentSection, function()
		{
			that.getData(that.sections[that.currentSection], fs, function()
			{
				that.createTable(that.sections[that.currentSection]);
			});
		});
		
		//that.getHtmlTable(that.sections[that.currentSection], fs);
	}
});