$.extend(APP.subforms,
{
	sectionTarget: null,
	subformRows: {},
	
	finish: function()
	{
		this.subformRows = {};
	},
	
	setTooltips: function(div)
	{
		div.find(".icon-eye-open").parents("button").first().tooltip({title: APP.i18n.translate("show")});
		div.find(".icon-pencil").parents("button").first().tooltip({title: APP.i18n.translate("edit")});
		div.find(".icon-trash").parents("button").first().tooltip({title: APP.i18n.translate("remove")});
		div.find(".icon-lock").parents("button").first().tooltip({title: APP.i18n.translate("off")});
		div.find(".icon-off").parents("button").first().tooltip({title: APP.i18n.translate("on")});
	},
	
	generateActionButtonsString: function(i, subformName)
	{
		var that = this;
		var str = $('<div class="actionButtonsString"></div>');
		str.css('white-space','nowrap');
		if ($.inArray("update", that.sectionTarget.subforms[subformName].capabilities) > -1)
			str.append("<button type='button' id='edit_"+i+"' data-subformname='"+subformName+"' name='edit' class='btn btn-default' ><i class='icon-pencil'></i></button>");
		else
			str.append("<button type='button' id='show_"+i+"' data-subformname='"+subformName+"' name='show' class='btn btn-default' ><i class='icon-eye-open'></i></button>");
		if ($.inArray("delete", that.sectionTarget.subforms[subformName].capabilities) > -1)
			str.append("<button type='button' id='remove_"+i+"' data-subformname='"+subformName+"' name='remove' class='btn btn-danger' ><i class='icon-trash'></i></button>");
		//str.find("button").data({subformName: subformName});
		return str.html();
	},
	
	preserialize: function(subformName)
	{
		var that = this;
		var str = "";
		$.each(that.subformRows[subformName], function()
		{
			str += $.param(this)+";";
		});
		str = str.substr(0, str.length-1);
		return {'name': subformName, 'value': str};
	},
	
	setActionButtonsClick: function(recipient, params)
	{
		var that = this;
		var buttons = recipient.find(".btn");
		$.each(buttons, function(i, v){
			$(v).data(params);
			$(v).click(function()
			{ 
				that.onActionButtonClick($(this), recipient);
			});
		});
		that.setTooltips(recipient);
	},
	
	addSubformItem: function(form, tableDiv, subformName)
	{
		var that = this;
		var table = tableDiv.find(".datatable").dataTable();
		var serializedData = form.serializeArray();
		
		var data = {};
		var ths = table.find("th");
		$.each(ths, function(i,v)
		{
			data[$(v).attr("name")] = "";
		});
		
		var obj = {};
		$.each(form.find(":input").not(':button'), function(i,v)
		{
			switch(v.tagName)
			{
				case "SELECT":
					var t = $(v.selectedOptions);
					var value = "";
					var text = "";
					$.each(t, function(ii, vv)
					{
						value += $(vv).val()+",";
						text += $(vv).text()+"<br><br>";
					});
					value = value.substr(0, value.length-1);
					text = text.substr(0, text.length-8);
					var name = $(v).attr("name");
					data[name] = text;
					break;
				case "INPUT": case "TEXTAREA":
					var name = $(v).attr("name");
					if (!APP.utils.isset(name) || name == "")
						break;
					if ($(v).hasClass("fileupload"))
					{
						var value = "";
						$.each(APP.fileuploader.myFiles, function(ii, vv)
						{
							if (vv.stato === "D")
								return true;
							if (vv.type.split("/")[0] === "image")
								value += '<img src="'+vv.thumbnail_url+'" alt="">|';
							else
								value += '<i class="icon icon-file-alt"></i>' + vv[APP.fileuploader.inputName]+"|";
						});
						//APP.fileuploader.myFiles = {};
						value = value.substr(0, value.length-1);
						data[APP.fileuploader.inputName] = APP.utils.replaceAll('|', '<br>', value);
						obj[APP.fileuploader.inputName] = APP.fileuploader.myFiles[APP.fileuploader.myFiles.length-1][APP.fileuploader.inputName];
						break;
					}
					var value = $(v).val();
					data[name] = value;
					break;
				default:
					console.log("Aggiungi questo tagName: "+v.tagName);
			}
		});
		data.actions = that.generateActionButtonsString(that.sectionTarget.subforms[subformName].token, subformName);
		var dataArr = [];
		$.each(data, function(i,v)
		{
			dataArr.push(v);
		});
		var newRowIndex = table.fnAddData(dataArr);
		$(table.find("tbody").find("tr")[newRowIndex[0]]).find("td").addClass("table-td");
		$.each(serializedData, function(i, v)
		{
			if (v.name.substr(v.name.length-2) == "[]")
			{
				var name = v.name.substr(0, v.name.length-2);
				if (!$.isArray(obj[name]))
					obj[name] = [];
				obj[name].push(v.value);
			}
			else
				obj[v.name] = v.value;
		});
		$.extend(obj, {'token' : that.sectionTarget.subforms[subformName].token, 'stato' : "I"});
		
		if (!APP.utils.isset(that.subformRows[subformName]))
			that.subformRows[subformName] = [];
		that.subformRows[subformName].push(obj);
		that.setActionButtonsClick($(table.find("tbody").find("tr")[newRowIndex[0]]), obj);
		that.sectionTarget.subforms[subformName].token++;
	},
	
	editSubformItem: function(form, tableDiv, btn, subformName)
	{
		var that = this;
		var table = tableDiv.find(".datatable").dataTable();
		var serializedData = form.serializeArray();
		var tr = btn.parents("tr");
		var data = {};
		var ths = table.find("th");
		$.each(ths, function(i,v)
		{
			data[$(v).attr("name")] = "";
		});
		
		var obj = {};
		$.each(form.find(":input").not(':button'), function(i,v)
		{
			switch(v.tagName)
			{
				case "SELECT":
					var t = $(v.selectedOptions);
					var value = "";
					var text = "";
					$.each(t, function(ii, vv)
					{
						value += $(vv).val()+",";
						text += $(vv).text()+"<br><br>";
					});
					value = value.substr(0, value.length-1);
					text = text.substr(0, text.length-8);
					var name = $(v).attr("name");
					data[name] = text;
					break;
				case "INPUT": case "TEXTAREA":
					var name = $(v).attr("name");
					if (!APP.utils.isset(name) || name == "")
						break;
					if ($(v).hasClass("fileupload"))
					{
						var value = "";
						$.each(APP.fileuploader.myFiles, function(ii, vv)
						{
							if (vv.stato === "D")
								return true;
							
							if (vv.type)
							{
								if (vv.type.split("/")[0] === "image")
									value += '<img src="'+vv.thumbnail_url+'" alt="">|';
								else
									value += '<i class="icon icon-file-alt"></i>' + vv[APP.fileuploader.inputName]+"|";
							}
							else
							{
								if (APP.utils.isImageFile(vv[APP.fileuploader.inputName]))
								{
									var index = APP.utils.getIndexFromField(that.sectionTarget.subforms[subformName].columns, "name", APP.fileuploader.inputName);
									var tu = APP.utils.getThumbnailUrl(that.sectionTarget.subforms[subformName].columns[index].urls, vv);
									if (tu)
										value += '<img src="'+tu+'" alt="">|';
									else
										value += '<i class="icon icon-file-alt"></i>' + vv[APP.fileuploader.inputName]+"|";
								}
								else
									value += '<i class="icon icon-file-alt"></i>' + vv[APP.fileuploader.inputName]+"|";
							}
						});
						//APP.fileuploader.myFiles = {};
						value = value.substr(0, value.length-1);
						data[APP.fileuploader.inputName] = APP.utils.replaceAll('|', '<br>', value);
						obj[APP.fileuploader.inputName] = APP.fileuploader.myFiles[APP.fileuploader.myFiles.length-1][APP.fileuploader.inputName];
						break;
					}
					var value = $(v).val();
					data[name] = value;
					break;
				default:
					console.log("Aggiungi questo tagName: "+v.tagName);
			}
		});
		$.each(serializedData, function(i, v)
		{
			if (v.name.substr(v.name.length-2) == "[]")
			{
				var name = v.name.substr(0, v.name.length-2);
				if (!$.isArray(obj[name]))
					obj[name] = [];
				obj[name].push(v.value);
			}
			else
				obj[v.name] = v.value;
		});
		var btnData = btn.data();
		obj.stato = (APP.utils.isset(btnData[that.sectionTarget.subforms[subformName].primary_key]))? "U" : btnData.stato;
		obj.token = btnData.token;
		var index = APP.utils.getIndexFromField(that.subformRows[subformName], "token", obj.token);
		if (index == -1)
			return;
		//that.subformRows[index] = obj;
		$.extend(that.subformRows[subformName][index], obj);
				
		data.actions = that.generateActionButtonsString(obj.token, subformName);
		var dataArr = [];
		$.each(data, function(i,v)
		{
			dataArr.push(v);
		});
		table.fnUpdate(dataArr, tr[0]);
		that.setActionButtonsClick(tr, obj);
	},
	
	removeSubformItem: function(btn, subformName)
	{
		var that = this;
		var btnData = $(btn).data();
		var token = btnData.token;
		var index = APP.utils.getIndexFromField(that.subformRows[subformName], "token", token);
		if (index == -1)
			return;
		if (APP.utils.isset(that.subformRows[subformName][index][that.sectionTarget.subforms[subformName].primary_key]))
			that.subformRows[subformName][index].stato = "D";
		else
			that.subformRows[subformName].splice(index, 1);
		
		var table = btn.parents("table").first().dataTable();
		var tr = btn.parents("tr");
		table.fnDeleteRow(tr[0]);
	},

	createFormDialog: function(params)
	{
		var that = this;
		var identifier = APP.utils.isset(params.token)? params.token : null;
		
		var enctype = APP.utils.isset(that.sectionTarget.subforms[params.subformName].enctype)? 'enctype="'+that.sectionTarget.subforms[params.subformName].enctype+'"' : '';
		var form = $('<form class="form-horizontal" '+enctype+' role="form"></form>');
		if (APP.utils.isset(APP.config.getToken) && $.isFunction(APP.config.getToken))
			form.append('<input type="hidden" name="csrf_token" class="tokenInput subform" value="'+APP.config.getToken(params.subformName)+'">');
		params.div.html(form);
		
		$.each(that.sectionTarget.subforms[params.subformName].columns, function(j1, v)
		{
			var required = (APP.utils.isset(v.required) && v.required)? " required " : "";
			var inp = null;
			
			var valore = "";
			var obj = null;
			if (params.type == "edit" || params.type == "show")
			{
				var index = APP.utils.getIndexFromField(that.subformRows[params.subformName], "token", identifier);
				if (index == -1)
					return;
				obj = that.subformRows[params.subformName][index];
				if (v.data_type == "integer")
				{
					if ($.isArray(obj[v.name]))
					{
						$.each(obj[v.name], function(ii,vv)
						{
							var number = parseInt(vv);
							if (isNaN(number))
								number = "";
							obj[v.name][ii] = number;
						});
					}
					else
					{
						var number = parseInt(obj[v.name]);
						if (isNaN(number))
							number = "";
						obj[v.name] = number;
					}
				}
				valore = obj[v.name];
			}
			
			valore = (APP.utils.isset(obj) && APP.utils.isset(obj[v.name]))? obj[v.name] : "";
			if (!APP.utils.isset(valore) || valore === "")
				valore = APP.utils.getSecondaryValue(v);
			
			if (($.type(v.form_show) === "boolean" && !v.form_show) || ($.isPlainObject(v.form_show) && ((!v.form_show.insert && !APP.utils.isset(identifier)) || (!v.form_show.update && APP.utils.isset(identifier))))) // se deve essere visualizzato nel form
			//if (!v.form_show || (!v.editable && (!APP.utils.isset(valore) || APP.utils.isEmptyString(valore)))) // se deve essere visualizzato nel form
				return true;
			
			inp = APP.utils.getInputFormat(identifier, obj, required, params.subformName, that.sectionTarget.subforms[params.subformName], that, v, valore, null);
			inp.find(":input").addClass("subform");
			
			var displayOnOff = (v.form_input_type == "hidden")? "style='display: none'" : "";
			
			var ctrlGrp = $("<div class='form-group'>\
								<label class='control-label col-md-4' for='APP-"+v.name+"' "+displayOnOff+">"+v.label+":</label>\
								<div class='controls col-md-7' "+displayOnOff+"></div>\
								<div class='descrInput col-md-1'></div>\
							</div>");
							
			ctrlGrp.find(".controls").append(inp);
			if (APP.utils.isset(v.description))
				ctrlGrp.find(".descrInput").append($('<span id="description_'+v.name+'" data-toggle="tooltip" title="'+v.description+'" data-placement="auto" data-container="#subformContent" class="tooltipElement text-muted" style="padding-left: 5px"><i class="icon icon-info-sign"></i></span>'));
			form.append(ctrlGrp);
			
			if (($.type(v.editable) === "boolean" && !v.editable) || ($.isPlainObject(v.editable) && ((!v.editable.insert && !APP.utils.isset(identifier)) || (!v.editable.update && APP.utils.isset(identifier)))) || (APP.utils.isset(identifier) && $.inArray("update", that.sectionTarget.subforms[params.subformName].capabilities) === -1) || (!APP.utils.isset(identifier) && $.inArray("insert", that.sectionTarget.subforms[params.subformName].capabilities) === -1))//if (!v.editable || $.inArray("update", sectionTarget.capabilities) === -1)
				params.div.find("#APP-"+v.name).attr("disabled", true);
		});
	},
	
	formValidator: function(form, url, mtype)
	{
		var that = this;
		var result = false;
		APP.utils.resetFormErrors(form);
		mtype = (mtype === "add")? "PUT" : ((mtype === "edit")? "POST" : null);
		
		if (form.find(".textEditor").length > 0)
			tinyMCE.triggerSave();
		
		if (APP.utils.isset(url) && APP.utils.isset(mtype))
		{
			
			var d = form.serializeArray();
			form.find(':disabled').each( function() {
				if ($(this).hasClass("fileupload"))
					return true;
				var o = {};
				o.name = $(this).attr('name');
				o.value = $(this).val();
				d.push(o);
			});
			if (form.attr("enctype") === "multipart/form-data")
				d.push(APP.fileuploader.preserialize(form));
			/*if (form.find(".subformTable").length>0)
				d.push(APP.subforms.preserialize());*/
			
			$.ajax({
				type: mtype,
				url: url,
				data: d,
				async: false,
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, form))
					{
						result = true;
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
		else
		{
			var inputs = $(form).find(":input");
			result = true;
			$.each(inputs, function()
			{
				var value = $(this).val();
				if (this.required && (APP.utils.isEmptyString(value) || !APP.utils.isset(value)))
				{
					result = false;
					APP.utils.renderError($(this), APP.i18n.translate("required_field"));
				}
			});
		}
		
		return result;
	},

	onActionButtonClick: function(btn, div)
	{
		var that = this;
		var data = btn.data();
		var type = btn.attr("name");
		var tkn = null;
		
		$("#modalSubform").find(".modal-title").html(APP.i18n.translate(type));
		var closeBtn = $('<button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true">'+APP.i18n.translate("cancel")+'</button>');
		var savebtn = $('<button class="btn btn-primary" type="button"><i class="icon icon-ok"></i> '+APP.i18n.translate("apply")+'</button>');
		
		switch(type)
		{
			case "add":
				tkn = that.sectionTarget.subforms[data.subformname].token;
				savebtn.click(function()
				{
					var form = $("#modalSubform").find("form");
					if (!that.formValidator(form, that.sectionTarget.subforms[data.subformname].subformValidationUrl, type))
						return; 
					that.addSubformItem(form, div, data.subformname); 
					$("#modalSubform").modal("hide");
				});
				break;
			case "edit":
				tkn = data.token;
				savebtn.click(function()
				{
					var form = $("#modalSubform").find("form");
					if (!that.formValidator(form, that.sectionTarget.subforms[data.subformname].subformValidationUrl, type))
						return; 
					that.editSubformItem(form, $("#div_"+data.subformname), btn, data.subformname);
					$("#modalSubform").modal("hide");
				});
				break;
			case "show":
				tkn = data.token;
				savebtn.hide();
				break;
			case "remove":
				var btns = $('<button class="btn btn-primary" type="button" style="width: 60px">'+APP.i18n.translate("yes")+'</button><button class="btn btn-default" data-dismiss="modal" aria-hidden="true" style="width: 60px">'+APP.i18n.translate("no")+'</button>');
				var yesBtn = $(btns[0]);
				yesBtn.click(function()
				{
					that.removeSubformItem(btn, data.subformname);
					$("#defaultMessageDialog").modal("hide");
				});
				var deleteConfirmMsg = (APP.utils.isset(that.sectionTarget.messages) && APP.utils.isset(that.sectionTarget.messages['delete_confirm']))? that.sectionTarget.messages['delete_confirm'] : APP.i18n.translate("remove_confirm");
				APP.utils.showMsg({
					title: APP.i18n.translate("warning"),
					content: deleteConfirmMsg,
					buttons: btns
				});
				return;
			default:
				console.log("Aggiungi questo tipo: "+type);
		}
				
		$("#modalSubform").find(".modal-footer").html(savebtn);
		$("#modalSubform").find(".modal-footer").append(closeBtn);
		that.createFormDialog({ 'div': $("#subformContent"), 'type': type, 'token': tkn, 'subformName': data.subformname});
		$("#modalSubform").one('shown.bs.modal', function(){	
			APP.utils.setLookForm($("#subformContent").find("form"), null);
		});
		$("#modalSubform").modal("show");
		
	},

	showSubformTable: function(params)
	{
		var that = this;
		that.sectionTarget = params.sectionTarget;
		var subformName = params.subformName;
		that.subformRows[subformName] = that.sectionTarget.subforms[subformName].values;
			
		if (!APP.utils.isset(that.sectionTarget.subforms))
		{
			alert("non e' settato l'array subforms in "+that.sectionTarget.resource);
			return;
		}
				
		var s = $(	'<div id="div_'+subformName+'">\
						<div class="table-responsive">\
							<table name="'+subformName+'" class="table table-striped table-bordered table-hover datatable subformTable">\
								<thead><tr></tr></thead>\
								<tbody></tbody>\
							</table>\
						</div>\
						<div id="modalSubform" class="modal fade" role="dialog">\
							<div class="modal-dialog">\
								<div class="modal-content">\
									<div class="modal-header">\
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
										<h4 class="modal-title"></h4>\
									</div>\
									<div class="modal-body" id="subformContent" ></div>\
									<div class="modal-footer"></div>\
								</div>\
							</div>\
						</div>\
					</div>');
		
		var div = $("#APP-"+ subformName);
		if ($.inArray("insert",  that.sectionTarget.subforms[ subformName].capabilities) > -1)
		{
			var addSpan = $('<span id="add_'+subformName+'" name="add" data-subformname="'+subformName+'" class="btn btn-primary" style="margin-bottom: 20px; margin-right: 40px; float: left"><i class="icon-plus icon-white"></i> '+APP.i18n.translate("add")+'</span>');
			s.prepend(addSpan);
		}
		div.html(s);
		$("#modalSubform").modal({'show': false});
		that.setActionButtonsClick(div, subformName);
		
		var thead = div.find("thead").find("tr");
		var tbody =  div.find("tbody");
		var cols = [];
		
		var obj = that.sectionTarget.subforms[subformName];
		var valori = {};
		
		$.each(obj.columns, function(i, v)
		{
			if (!v.subform_table_show)
				return true;
			
			var suffix = (v.foreign_mode == "multiselect")? "[]" : "";
			
			if (APP.utils.isset(v.description))
			{
				var tthh = $('<th name="'+v.name+suffix+'" class="table-th" data-container="#APP-'+subformName+'" data-toggle="tooltip " data-placement="top" title="'+v.description+'">'+v.label+'</th>');
				tthh.tooltip();
				thead.append(tthh);
			}
			else
				thead.append('<th name="'+v.name+suffix+'" class="table-th">'+v.label+'</th>');
				
			if (!v.hasOwnProperty("foreign_key") && v.form_input_type == "combobox")
				valori[v.name] = APP.utils.getForeignValue(v, null);
			cols.push(v);
		});
		thead.append("<th name='actions'>"+APP.i18n.translate("actions")+"</th>");
		
		if (APP.utils.isset(obj.values))
		{
			$.each(obj.values, function(i, v)
			{
				var tr =  $("<tr></tr>");
				tr.css("cursor", "pointer");
				tr.data(v);
				$.each(cols, function(j, k)
				{
					if (APP.utils.isset(k.foreign_key))
					{
						var fValues = APP.config.localConfig[k.foreign_key];
						var data = (APP.utils.isset(v[k.name]))? v[k.name] : [];
						var str = "";
						
						if (!$.isArray(data))
							data = [data];
						
						$.each(data, function(x, y)
						{
							y = (!$.isPlainObject(y))? y : y[that.sectionTarget.subforms[subformName].primary_key];
							var oi = APP.utils.getIndexFromField(fValues, that.sectionTarget.subforms[subformName].primary_key, y);
							if (oi > -1)
								str += APP.config.getValue(fValues[oi], k.foreign_toshow, k.foreign_toshow_params)+", ";//str += APP.config.getValue(k.name, fValues[oi])+", "; 
						});
						str = str.substr(0, str.length-2);
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
							y = (!$.isPlainObject(y))? y : y[that.sectionTarget.subforms[subformName].primary_key];
							var oi = APP.utils.getIndexFromField(fValues, that.sectionTarget.subforms[subformName].primary_key, y);
							if (oi > -1)
								str += APP.config.getValue(fValues[oi], k.foreign_toshow, k.foreign_toshow_params)+", ";//str += APP.config.getValue(k.name, fValues[oi])+", "; 
						});
						str = str.substr(0, str.length-2);
						tr.append("<td class='table-td'>"+APP.utils.displayData(str, k)+"</td>");
						return true;
					}
					if (!APP.utils.isset(v[k.name]))
					{
						var str = APP.utils.getSecondaryValue(k); 
						tr.append("<td class='table-td'>"+APP.utils.displayData(str, k)+"</td>");
						return true;
					}
					if (k.form_input_type === "input" && k.data_type === "file")
					{
						var tipo = (APP.utils.isImageFile(v[k.name]))? "image" : null;
						var stringa = "";
						
						switch(tipo)
						{
							case "image":
								var tu = APP.utils.getThumbnailUrl(k.urls, v);
								if (tu)
									stringa = '<img src="/'+tu+'" style="">';
								else
									stringa = '<i class="icon icon-file-alt">'+v[k.name]+'</i>';
								break;
							default:
								stringa = '<i class="icon icon-file-alt">'+v[k.name]+'</i>';
								break;
						}
						
						tr.append("<td class='table-td'>"+stringa+"</td>");
                        return true;
					}
					tr.append("<td class='table-td'>"+APP.utils.displayData(v[k.name], k)+"</td>");
				});
				var tiddi = $('<td></td>');
				tiddi.append(that.generateActionButtonsString(that.sectionTarget.subforms[subformName].token, subformName));
				tr.append(tiddi);
				$.extend(v, {'token' : that.sectionTarget.subforms[subformName].token});
				//subformRows.push(v);
				that.sectionTarget.subforms[subformName].token++;
				that.setActionButtonsClick(tr, v);
				tbody.append(tr);
			});
		}
		
		if (APP.utils.isset(obj.sortable) && obj.sortable === true)
			tbody.sortable({
				stop: function( event, ui )
				{
					var sfn = $(ui.item[0]).parents("table:first").attr("name");
					var sfrCopy = that.subformRows[sfn];
					if (sfrCopy.length===0)
						return;
					that.subformRows[sfn] = [];
					$.each(this.children, function(){
						var d = $(this).data();
						delete d['sortableItem'];
						var fieldKey = that.sectionTarget.subforms[sfn].primary_key;
						var elIndex = APP.utils.getIndexFromField(sfrCopy, fieldKey, d[fieldKey]);
						d.token = sfrCopy[elIndex].token;
						that.subformRows[sfn].push(d);
					});
				}
			});
		
		//$(".datatable tbody tr").click(function(e){ that.onSelectRow(e); });
		div.find(".datatable").dataTable({ 
			"bRetrieve": true,
			"bPaginate": false,
			"bFilter": false,
			"bSort": false,
			/*"aoColumnDefs": [
				{ 'bSortable': false, 'aTargets': [ -1 ] },
			],
			"bJQueryUI": true,
			"sPaginationType": "bootstrap",
			"iDisplayLength" : params.idl,*/
			"oLanguage": APP.utils.getDataTableLanguage(),
		});
	},
});