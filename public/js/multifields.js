$.extend(APP.multifields,
{
	sectionTarget: null,
	multifieldRows: {},
	
	finish: function()
	{
		this.multifieldRows = {};
	},
	
	setTooltips: function(div)
	{
		div.find(".icon-eye-open").parents("button").first().tooltip({title: APP.i18n.translate("show")});
		div.find(".icon-pencil").parents("button").first().tooltip({title: APP.i18n.translate("edit")});
		div.find(".icon-trash").parents("button").first().tooltip({title: APP.i18n.translate("remove")});
		div.find(".icon-lock").parents("button").first().tooltip({title: APP.i18n.translate("off")});
		div.find(".icon-off").parents("button").first().tooltip({title: APP.i18n.translate("on")});
	},
	
	generateActionButtonsString: function(i, multifieldName)
	{
		var that = this;
		var str = $('<div class="actionButtonsString"></div>');
		str.css('white-space','nowrap');
		if ($.inArray("update", that.sectionTarget.multifields[multifieldName].capabilities) > -1)
			str.append("<button type='button' id='edit_"+i+"' data-multifieldname='"+multifieldName+"' name='edit' class='btn btn-default' ><i class='icon-pencil'></i></button>");
		else
			str.append("<button type='button' id='show_"+i+"' data-multifieldname='"+multifieldName+"' name='show' class='btn btn-default' ><i class='icon-eye-open'></i></button>");
		if ($.inArray("delete", that.sectionTarget.multifields[multifieldName].capabilities) > -1)
			str.append("<button type='button' id='remove_"+i+"' data-multifieldname='"+multifieldName+"' name='remove' class='btn btn-danger' ><i class='icon-trash'></i></button>");
		//str.find("button").data({multifieldName: multifieldName});
		return str.html();
	},
	
	validation: function(param, i, v)
	{
		var nameAndIndexArray = i.split("-row");
		if (nameAndIndexArray.length === 1)
			return true;
		el = param.find('[id="APP-'+nameAndIndexArray[0]+'"]');
		el = $(el[parseInt(nameAndIndexArray[1])]);
	
		var span = '<span class="help-block"><small>'+v+'</small></span>';
		var cgd = el.parents("div").first();
		cgd.addClass("has-error");
		var hi = cgd.find(".help-block");
		//(hi.length > 0)? hi.html(span) : cgd.find(".controls").append(span);
		if (hi.length > 0)
			hi.html(span)
		else
		{
			if (el.is(":radio"))
				el.parents("div:first").append(span);
			else
				el.after(span);
		}
	},
	
	preserialize: function(multifieldName)
	{
		var that = this;
		var fromServer = that.multifieldRows[multifieldName];
		fromServer = (APP.utils.isset(fromServer))? fromServer : [];
		
		that.multifieldRows[multifieldName] = [];
		
		$("#APP-"+multifieldName).find(".has-error").removeClass("has-error");
		
		$.each($("#APP-"+multifieldName).find("table tbody tr"), function(){
			var obj = {};
			$.each($(this).find(":input"), function(){
				obj[$(this).attr("name")] = $(this).val();
			});
			
			if ($.isEmptyObject(obj))
				return true;
				
			obj.stato = (obj.id)? "U" : "I";
			that.multifieldRows[multifieldName].push(obj);
			that.sectionTarget.multifields[multifieldName].token++;
		});
		
		$.each(fromServer, function(i,v)
		{
			var myIndex = APP.utils.getIndexFromField(that.multifieldRows[multifieldName], "id", ""+v.id);
			if (myIndex === -1)
			{
				v.stato = "D";
				that.multifieldRows[multifieldName].push(v)
			}
		});
		
		var str = "";
		$.each(that.multifieldRows[multifieldName], function()
		{
			str += $.param(this)+";";
		});
		str = str.substr(0, str.length-1);
		
		return {'name': multifieldName, 'value': str};
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
	
	addMultifieldItem: function(form, tableDiv, multifieldName)
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
		data.actions = that.generateActionButtonsString(that.sectionTarget.multifields[multifieldName].token, multifieldName);
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
		$.extend(obj, {'token' : that.sectionTarget.multifields[multifieldName].token, 'stato' : "I"});
		
		if (!APP.utils.isset(that.multifieldRows[multifieldName]))
			that.multifieldRows[multifieldName] = [];
		that.multifieldRows[multifieldName].push(obj);
		that.setActionButtonsClick($(table.find("tbody").find("tr")[newRowIndex[0]]), obj);
		that.sectionTarget.multifields[multifieldName].token++;
	},
	
	editMultifieldItem: function(form, tableDiv, btn, multifieldName)
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
									var index = APP.utils.getIndexFromField(that.sectionTarget.multifields[multifieldName].columns, "name", APP.fileuploader.inputName);
									var tu = APP.utils.getThumbnailUrl(that.sectionTarget.multifields[multifieldName].columns[index].urls, vv);
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
		obj.stato = (APP.utils.isset(btnData[that.sectionTarget.multifields[multifieldName].primary_key]))? "U" : btnData.stato;
		obj.token = btnData.token;
		var index = APP.utils.getIndexFromField(that.multifieldRows[multifieldName], "token", obj.token);
		if (index == -1)
			return;
		//that.multifieldRows[index] = obj;
		$.extend(that.multifieldRows[multifieldName][index], obj);
				
		data.actions = that.generateActionButtonsString(obj.token, multifieldName);
		var dataArr = [];
		$.each(data, function(i,v)
		{
			dataArr.push(v);
		});
		table.fnUpdate(dataArr, tr[0]);
		that.setActionButtonsClick(tr, obj);
	},
	
	removeMultifieldItem: function(btn, multifieldName)
	{
		var that = this;
		var btnData = $(btn).data();
		var token = btnData.token;
		var index = APP.utils.getIndexFromField(that.multifieldRows[multifieldName], "token", token);
		if (index == -1)
			return;
		if (APP.utils.isset(that.multifieldRows[multifieldName][index][that.sectionTarget.multifields[multifieldName].primary_key]))
			that.multifieldRows[multifieldName][index].stato = "D";
		else
			that.multifieldRows[multifieldName].splice(index, 1);
		
		var table = btn.parents("table").first().dataTable();
		var tr = btn.parents("tr");
		table.fnDeleteRow(tr[0]);
	},

	createFormDialog: function(params)
	{
		var that = this;
		var identifier = APP.utils.isset(params.token)? params.token : null;
		
		var enctype = APP.utils.isset(that.sectionTarget.multifields[params.multifieldName].enctype)? 'enctype="'+that.sectionTarget.multifields[params.multifieldName].enctype+'"' : '';
		var form = $('<form class="form-horizontal" '+enctype+' role="form"></form>');
		if (APP.utils.isset(APP.config.getToken) && $.isFunction(APP.config.getToken))
			form.append('<input type="hidden" name="csrf_token" class="tokenInput multifield" value="'+APP.config.getToken(params.multifieldName)+'">');
		params.div.html(form);
		
		$.each(that.sectionTarget.multifields[params.multifieldName].columns, function(j1, v)
		{
			var required = (APP.utils.isset(v.required) && v.required)? " required " : "";
			var inp = null;
			
			var valore = "";
			var obj = null;
			if (params.type == "edit" || params.type == "show")
			{
				var index = APP.utils.getIndexFromField(that.multifieldRows[params.multifieldName], "token", identifier);
				if (index == -1)
					return;
				obj = that.multifieldRows[params.multifieldName][index];
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
			
			inp = APP.utils.getInputFormat(identifier, obj, required, params.multifieldName, that.sectionTarget.multifields[params.multifieldName], that, v, valore, null);
			inp.find(":input").addClass("multifield");
			
			var displayOnOff = (v.form_input_type == "hidden")? "style='display: none'" : "";
			
			var ctrlGrp = $("<div class='form-group'>\
								<label class='control-label col-md-4' for='APP-"+v.name+"' "+displayOnOff+">"+v.label+":</label>\
								<div class='controls col-md-7' "+displayOnOff+"></div>\
								<div class='descrInput col-md-1'></div>\
							</div>");
							
			ctrlGrp.find(".controls").append(inp);
			if (APP.utils.isset(v.description))
				ctrlGrp.find(".descrInput").append($('<span id="description_'+v.name+'" data-toggle="tooltip" title="'+v.description+'" data-placement="auto" data-container="#multifieldContent" class="tooltipElement text-muted" style="padding-left: 5px"><i class="icon icon-info-sign"></i></span>'));
			form.append(ctrlGrp);
			
			if (($.type(v.editable) === "boolean" && !v.editable) || ($.isPlainObject(v.editable) && ((!v.editable.insert && !APP.utils.isset(identifier)) || (!v.editable.update && APP.utils.isset(identifier)))) || (APP.utils.isset(identifier) && $.inArray("update", that.sectionTarget.multifields[params.multifieldName].capabilities) === -1) || (!APP.utils.isset(identifier) && $.inArray("insert", that.sectionTarget.multifields[params.multifieldName].capabilities) === -1))//if (!v.editable || $.inArray("update", sectionTarget.capabilities) === -1)
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
			/*if (form.find(".multifieldTable").length>0)
				d.push(APP.multifields.preserialize());*/
			
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
		
		$("#modalMultifield").find(".modal-title").html(APP.i18n.translate(type));
		var closeBtn = $('<button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true">'+APP.i18n.translate("cancel")+'</button>');
		var savebtn = $('<button class="btn btn-primary" type="button"><i class="icon icon-ok"></i> '+APP.i18n.translate("apply")+'</button>');
		
		switch(type)
		{
			case "add":
				tkn = that.sectionTarget.multifields[data.multifieldname].token;
				savebtn.click(function()
				{
					var form = $("#modalMultifield").find("form");
					if (!that.formValidator(form, that.sectionTarget.multifields[data.multifieldname].multifieldValidationUrl, type))
						return; 
					that.addMultifieldItem(form, div, data.multifieldname); 
					$("#modalMultifield").modal("hide");
				});
				break;
			case "edit":
				tkn = data.token;
				savebtn.click(function()
				{
					var form = $("#modalMultifield").find("form");
					if (!that.formValidator(form, that.sectionTarget.multifields[data.multifieldname].multifieldValidationUrl, type))
						return; 
					that.editMultifieldItem(form, $("#div_"+data.multifieldname), btn, data.multifieldname);
					$("#modalMultifield").modal("hide");
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
					that.removeMultifieldItem(btn, data.multifieldname);
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
				
		$("#modalMultifield").find(".modal-footer").html(savebtn);
		$("#modalMultifield").find(".modal-footer").append(closeBtn);
		that.createFormDialog({ 'div': $("#multifieldContent"), 'type': type, 'token': tkn, 'multifieldName': data.multifieldname});
		$("#modalMultifield").one('shown.bs.modal', function(){	
			APP.utils.setLookForm($("#multifieldContent").find("form"), null);
		});
		$("#modalMultifield").modal("show");
		
	},
	
	creaLinea: function(params)
	{
		var that = this;
		var table = params.table;
		var arrayValues = [];
		var emptyValues = [];
		
		$.each(that.sectionTarget.multifields[params.multifieldName].columns, function(i,v)
		{
			if (!v.subform_table_show)
				return true;
			emptyValues.push("");
			var identifier = APP.utils.isset(params.res)? params.res.id : null;
			var prn = (APP.utils.isset(params.res) &&  APP.utils.isset(params.res[v.name]))?  params.res[v.name] : null;
			if (!APP.utils.isset(prn) || prn === "")
				prn = APP.utils.getSecondaryValue(v);
			var inp = APP.utils.getInputFormat(identifier, params.res, ((APP.utils.isset(v.required))? v.required : ""), v.label, that.sectionTarget.multifields[params.multifieldName], params.ctx, v, prn, null);
			if (inp.is(":input"))
			{
				inp.addClass("multifield");
				if (v.change)
				{
					APP.utils.setChangeProperty(inp);
					inp.data().mode = APP.utils.isset(identifier)? "update" : "insert";
				}
				inp.attr("disabled", !v.editable);
			}
			else
			{
				inp.find(":input").addClass("multifield");
				if (v.change)
				{
					APP.utils.setChangeProperty(inp.find(":input"));
					inp.find(":input").data().mode = APP.utils.isset(identifier)? "update" : "insert";
				}
				inp.find(":input").attr("disabled", !v.editable)
			}			
			
			arrayValues.push(inp);
		});
		emptyValues.push("");
		var closeBtn = $('<span class="close" aria-hidden="true">&times;</span>');
		closeBtn.click(function(){
			var myTable = $(this).parents("table:first");
			var myRow = $(this).parents("tr:first");
			myTable.dataTable().fnDeleteRow(myRow[0]);
		});
		arrayValues.push(closeBtn);
		var trIndex = table.dataTable().fnAddData(emptyValues);
		var myTr = table.dataTable().fnGetNodes(trIndex[0]);
		var tdLength = $(myTr).find("td").length;
		
		$.each($(myTr).find("td"), function(i,v){
			$(this).html(arrayValues[i]);
		});
	},

	showMultifieldTable: function(params)
	{
		var that = this;
		that.sectionTarget = params.sectionTarget;
		var multifieldName = params.multifieldName;
		that.multifieldRows[multifieldName] = that.sectionTarget.multifields[multifieldName].values;
			
		if (!APP.utils.isset(that.sectionTarget.multifields))
		{
			alert("non e' settato l'array multifields in "+that.sectionTarget.resource);
			return;
		}
				
		var s = $(	'<div id="div_'+multifieldName+'">\
						<div class="table-responsive">\
							<table name="'+multifieldName+'" class="table table-striped table-bordered table-hover datatable multifieldTable">\
								<thead><tr></tr></thead>\
								<tbody></tbody>\
							</table>\
						</div>\
						<div id="modalMultifield" class="modal fade" role="dialog">\
							<div class="modal-dialog">\
								<div class="modal-content">\
									<div class="modal-header">\
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
										<h4 class="modal-title"></h4>\
									</div>\
									<div class="modal-body" id="multifieldContent" ></div>\
									<div class="modal-footer"></div>\
								</div>\
							</div>\
						</div>\
					</div>');
		
		var div = params.div;
		if ($.inArray("insert",  that.sectionTarget.multifields[multifieldName].capabilities) > -1)
		{
			var addSpan = $('<span id="add_'+multifieldName+'" name="add" data-multifieldname="'+multifieldName+'" class="btn btn-primary" style="margin-bottom: 20px; margin-right: 40px; float: left"><i class="icon-plus icon-white"></i> '+APP.i18n.translate("add")+'</span>');
			s.prepend(addSpan);
		}
		div.html(s);
		$("#modalMultifield").modal({'show': false});
		params.table = div.find("table");
		div.find("#add_"+multifieldName).click(function(){ that.creaLinea(params); });
		
		var thead = div.find("thead").find("tr");
		var tbody =  div.find("tbody");
		var cols = [];
		
		var obj = that.sectionTarget.multifields[multifieldName];
		var valori = {};
		
		$.each(obj.columns, function(i, v)
		{
			if (!v.subform_table_show)
				return true;
			
			var suffix = (v.foreign_mode == "multiselect")? "[]" : "";
			
			if (APP.utils.isset(v.description))
			{
				var tthh = $('<th name="'+v.name+suffix+'" style="width:'+v['table_col_width_%']+'%" class="table-th" data-container="#APP-'+multifieldName+'" data-toggle="tooltip " data-placement="top" title="'+v.description+'">'+v.label+'</th>');
				tthh.tooltip();
				thead.append(tthh);
			}
			else
				thead.append('<th name="'+v.name+suffix+'" class="table-th" style="width:'+v['table_col_width_%']+'%">'+v.label+'</th>');
				
			if (!v.hasOwnProperty("foreign_key") && v.form_input_type == "combobox")
				valori[v.name] = APP.utils.getForeignValue(v, null);
			cols.push(v);
		});
		thead.append("<th name='actions'></th>");
		
		div.find(".datatable").dataTable({ 
			"bRetrieve": true,
			"bPaginate": false,
			"bFilter": false,
			"bSort": false,
			/*"aoColumnDefs": [
				{ 'bSortable': false, 'aTargets': [ -1 ] },
			],
			"bJQueryUI": true,
			"sPaginationType": "bootstrap",*/
			"iDisplayLength" : params.idl,
			"oLanguage": APP.utils.getDataTableLanguage(),
		});
		
		//obj.values = [{id: 1, mansione_id: 7, sorveglianza_sanitaria: true},{id: 2, mansione_id: 7, sorveglianza_sanitaria: false}];
		
		if (APP.utils.isset(params.res))
		{
			$.each(params.res, function(i, v)
			{
				that.creaLinea({"multifieldName": params.multifieldName, "sectionTarget": params.sectionTarget, "ctx": params.ctx, "res": v, "idl": params.idl, "table": div.find(".datatable")});
			});
		}
		
		if (APP.utils.isset(obj.sortable) && obj.sortable === true)
			tbody.sortable({
				stop: function( event, ui )
				{
					var sfn = $(ui.item[0]).parents("table:first").attr("name");
					var sfrCopy = that.multifieldRows[sfn];
					if (sfrCopy.length===0)
						return;
					that.multifieldRows[sfn] = [];
					$.each(this.children, function(){
						var d = $(this).data();
						delete d['sortableItem'];
						var fieldKey = that.sectionTarget.multifields[sfn].primary_key;
						var elIndex = APP.utils.getIndexFromField(sfrCopy, fieldKey, d[fieldKey]);
						d.token = sfrCopy[elIndex].token;
						that.multifieldRows[sfn].push(d);
					});
				}
			});		
	},
});