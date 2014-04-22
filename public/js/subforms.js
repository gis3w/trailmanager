$.extend(APP.subforms,
{
	sectionTarget: null,
	subformName: null,
	subformRows: [],
	subformValidationUrl: null,
	token: 0,
	
	setTooltips: function(div)
	{
		div.find(".icon-eye-open").parents("button").first().tooltip({title: APP.i18n.translate("show")});
		div.find(".icon-pencil").parents("button").first().tooltip({title: APP.i18n.translate("edit")});
		div.find(".icon-trash").parents("button").first().tooltip({title: APP.i18n.translate("remove")});
		div.find(".icon-lock").parents("button").first().tooltip({title: APP.i18n.translate("off")});
		div.find(".icon-off").parents("button").first().tooltip({title: APP.i18n.translate("on")});
	},
	
	generateActionButtonsString: function(i)
	{
		var that = this;
		var str = '<div class="actionButtonsString">';
		if ($.inArray("update", that.sectionTarget.subforms[that.subformName].capabilities) > -1)
			str +=	"<button type='button' id='edit_"+i+"' name='edit' class='btn btn-default' ><i class='icon-pencil'></i></button>";
		else
			str += 	"<button type='button' id='show_"+i+"' name='show' class='btn btn-default' ><i class='icon-eye-open'></i></button>";
		if ($.inArray("delete", that.sectionTarget.subforms[that.subformName].capabilities) > -1)
			str +=	"<button type='button' id='remove_"+i+"' name='remove' class='btn btn-danger' ><i class='icon-trash'></i></button>"
		return str+"</div>";
	},
	
	preserialize_old: function()
	{
		var str = "";
		var rows = APP.subforms.subformRows;
		$.each(rows, function(i, obj)
		{
			var t;
			$.each(obj, function(j, valueArr)
			{
				var value = valueArr;
				if ($.isArray(valueArr))
				{
					value = "";
					$.each(valueArr, function(k, v)
					{
						value += v + "|";
					});
					value = value.substr(0, value.length-1);
				}
				str += j +":"+value+",";
			});
			str = str.substr(0, str.length-1) + ";";
		});
		str = str.substr(0, str.length-1);
		return {'name': APP.subforms.subformName, 'value': str};
	},
	
	preserialize_new: function()
	{
		var rows = APP.subforms.subformRows;
		var str = "";
		$.each(rows, function(i, obj)
		{
			str += $.param(obj)+";";
			//str = str.substr(0, str.length-1) + ";";
		});
		str = str.substr(0, str.length-1);
		return {'name': APP.subforms.subformName, 'value': str};
	},
	
	preserialize: function()
	{
		return this.preserialize_new();
		var str = "";
		var rows = APP.subforms.subformRows;
		/*
		$.each(rows, function(i, obj)
		{
			var t;
			$.each(obj, function(j, valueArr)
			{
				var value = valueArr;
				if ($.isArray(valueArr))
				{
					value = "";
					$.each(valueArr, function(k, v)
					{
						value += v + "|";
					});
					value = value.substr(0, value.length-1);
				}
				str += j +":"+value+",";
			});
			str = str.substr(0, str.length-1) + ";";
		});
		
		str = str.substr(0, str.length-1);*/
		str = JSON.stringify(rows);
		return {'name': APP.subforms.subformName, 'value': str};
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
	
	addSubformItem: function(form, tableDiv)
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
								value += '<img src="'+vv.thumbnail_url+'" alt=""> '+vv[APP.fileuploader.inputName]+'|';
							else
								value += '<i class="icon icon-file-alt"></i>' + vv[APP.fileuploader.inputName]+"|";
						});
						//APP.fileuploader.myFiles = {};
						value = value.substr(0, value.length-1);
						data[APP.fileuploader.inputName] = APP.utils.replaceAll('|', '<br>', value);
						obj[APP.fileuploader.inputName] = value.split('|');
						break;
					}
					var value = $(v).val();
					data[name] = value;
					break;
				default:
					console.log("Aggiungi questo tagName: "+v.tagName);
			}
		});
		data.actions = that.generateActionButtonsString(that.token);
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
		$.extend(obj, {'token' : that.token, 'stato' : "I"});
		
		that.subformRows.push(obj);
		that.setActionButtonsClick($(table.find("tbody").find("tr")[newRowIndex[0]]), obj);
		that.token++;
	},
	
	editSubformItem: function(form, tableDiv, btn)
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
							value += vv[APP.fileuploader.inputName]+"|";
						});
						value = value.substr(0, value.length-1);
						data[APP.fileuploader.inputName] = APP.utils.replaceAll('|','<br>',value);
						obj[APP.fileuploader.inputName] = value.split('|');
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
		obj.stato = (APP.utils.isset(btnData.id))? "U" : btnData.stato;
		obj.token = btnData.token;
		var index = APP.utils.getIndexFromField(that.subformRows, "token", obj.token);
		if (index == -1)
			return;
		//that.subformRows[index] = obj;
		$.extend(that.subformRows[index], obj);
				
		data.actions = that.generateActionButtonsString(obj.token);
		var dataArr = [];
		$.each(data, function(i,v)
		{
			dataArr.push(v);
		});
		table.fnUpdate(dataArr, tr[0]);
		that.setActionButtonsClick(tr, obj);
	},
	
	removeSubformItem: function(btn)
	{
		var that = this;
		var btnData = $(btn).data();
		var token = btnData.token;
		var index = APP.utils.getIndexFromField(that.subformRows, "token", token);
		if (index == -1)
			return;
		if (APP.utils.isset(that.subformRows[index].id))
			that.subformRows[index].stato = "D";
		else
			that.subformRows.splice(index, 1);
		
		var table = btn.parents("table").first().dataTable();
		var tr = btn.parents("tr");
		table.fnDeleteRow(tr[0]);
	},

	createFormDialog: function(params)
	{
		var that = this;
		var identifier = APP.utils.isset(params.token)? params.token : null;
		
		//var form = this.generalData.ctx.createFormTemplate(identifier, {}, that.sectionTarget.subforms[that.subformName], that.subformName, []);
		
		var enctype = APP.utils.isset(that.sectionTarget.subforms[that.subformName].enctype)? 'enctype="'+that.sectionTarget.subforms[that.subformName].enctype+'"' : '';
		var form = $('<form class="form-horizontal" '+enctype+' role="form"></form>');
		if (APP.utils.isset(APP.config.getToken) && $.isFunction(APP.config.getToken))
			form.append('<input type="hidden" name="csrf_token" class="tokenInput subform" value="'+APP.config.getToken(that.subformName)+'">');
		params.div.html(form);
		
		$.each(that.sectionTarget.subforms[that.subformName].columns, function(j1, v)
		{
			var required = (APP.utils.isset(v.required) && v.required)? " required " : "";
			var inp = null;
			
			var valore = "";
			var obj = null;
			if (params.type == "edit" || params.type == "show")
			{
				var index = APP.utils.getIndexFromField(that.subformRows, "token", identifier);
				if (index == -1)
					return;
				obj = that.subformRows[index];
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
			
			inp = APP.utils.getInputFormat(identifier, obj, required, that.subformName, that.sectionTarget.subforms[that.subformName], that, v, valore, null);
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
			
			if (($.type(v.editable) === "boolean" && !v.editable) || ($.isPlainObject(v.editable) && ((!v.editable.insert && !APP.utils.isset(identifier)) || (!v.editable.update && APP.utils.isset(identifier)))) || (APP.utils.isset(identifier) && $.inArray("update", that.sectionTarget.subforms[that.subformName].capabilities) === -1) || (!APP.utils.isset(identifier) && $.inArray("insert", that.sectionTarget.subforms[that.subformName].capabilities) === -1))//if (!v.editable || $.inArray("update", sectionTarget.capabilities) === -1)
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
				tkn = that.token;
				savebtn.click(function()
				{
					var form = $("#modalSubform").find("form");
					if (!that.formValidator(form, that.subformValidationUrl, type))
						return; 
					that.addSubformItem(form, div); 
					$("#modalSubform").modal("hide");
				});
				break;
			case "edit":
				tkn = data.token;
				savebtn.click(function()
				{
					var form = $("#modalSubform").find("form");
					if (!that.formValidator(form, that.subformValidationUrl, type))
						return; 
					that.editSubformItem(form, $("#div_"+that.subformName), btn);
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
					that.removeSubformItem(btn);
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
		that.createFormDialog({ 'div': $("#subformContent"), 'type': type, 'token': tkn});
		$("#modalSubform").one('shown.bs.modal', function(){	
			APP.utils.setLookForm($("#subformContent").find("form"), null);
		});
		$("#modalSubform").modal("show");
		
	},

	showSubformTable: function(params)
	{
		var that = this;
		that.subformRows = [];
		that.sectionTarget = params.sectionTarget;
		that.subformName = params.subformName;
		
		if (!APP.utils.isset(that.sectionTarget.subforms))
		{
			alert("non e' settato l'array subforms in "+that.sectionTarget.resource);
			return;
		}
				
		var s = $(	'<div id="div_'+that.subformName+'">\
						<div class="table-responsive">\
							<table class="table table-striped table-bordered table-hover datatable subformTable">\
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
		
		var div = $("#APP-"+that.subformName);
		if ($.inArray("insert", that.sectionTarget.subforms[that.subformName].capabilities) > -1)
		{
			s.prepend('<span id="add_'+that.subformName+'" name="add" class="btn btn-primary" style="margin-bottom: 20px; margin-right: 40px; float: left"><i class="icon-plus icon-white"></i> '+APP.i18n.translate("add")+'</span>');
		}
		div.html(s);
		$("#modalSubform").modal({'show': false});
		that.setActionButtonsClick(div, that.subformName);
		
		var thead = div.find("thead").find("tr");
		var tbody =  div.find("tbody");
		var cols = [];
		
		var obj = that.sectionTarget.subforms[that.subformName];
		var valori = {};
		
		$.each(obj.columns, function(i, v)
		{
			if (!v.subform_table_show)
				return true;
			
			var suffix = (v.foreign_mode == "multiselect")? "[]" : "";
			
			if (APP.utils.isset(v.description))
			{
				var tthh = $('<th name="'+v.name+suffix+'" class="table-th" data-container="#APP-'+that.subformName+'" data-toggle="tooltip " data-placement="top" title="'+v.description+'">'+v.label+'</th>');
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
							y = (!$.isPlainObject(y))? y : y.id;
							var oi = APP.utils.getIndexFromField(fValues, "id", y);
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
							y = (!$.isPlainObject(y))? y : y.id;
							var oi = APP.utils.getIndexFromField(fValues, "id", y);
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
					tr.append("<td class='table-td'>"+APP.utils.displayData(v[k.name], k)+"</td>");
				});
				tr.append("<td>"+that.generateActionButtonsString(that.token)+"</td>");
				$.extend(v, {'token' : that.token});
				that.subformRows.push(v);
				that.token++;
				that.setActionButtonsClick(tr, v);
				tbody.append(tr);
			});
		}
		
		if (APP.utils.isset(obj.sortable) && obj.sortable === true)
			tbody.sortable();
		
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