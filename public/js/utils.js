$.extend(APP.utils,{
	
	isset: function(o)
	{
		if(typeof o == 'undefined')
			return false
		else
			if(o===null)
				return false;
		return true;
	},
	
	addslashes: function(str) {
		str=str.replace(/\\/g,'\\\\');
		str=str.replace(/\'/g,'\\\'');
		str=str.replace(/\"/g,'\\"');
		str=str.replace(/\0/g,'\\0');
		return str;
	},
	
	stripslashes: function(str) {
		str=str.replace(/\\'/g,'\'');
		str=str.replace(/\\"/g,'"');
		str=str.replace(/\\0/g,'\0');
		str=str.replace(/\\\\/g,'\\');
		return str;
	},
	
	capitalize: function (string)
	{
		return string.charAt(0).toUpperCase() + string.slice(1);
	},
	
	isEmptyString: function (elemento)
	{
		elemento = elemento.toString();
		for (var i = 0; i < elemento.length; i++) 
			if ((elemento.charAt(i) != ' ') && (elemento.charAt(i) != '\t'))
				return false;
		return true;
	},
	
	replaceAll: function (find, rep, str)
	{
		/*var re = new RegExp(find, 'g');
		var result = str.replace(re, replace);
		return result;*/
		while (str.indexOf(find) !== -1)
			str = str.replace(find, rep);
		
		return str;
	},
	
	showErrMsg: function(param, dialogMode)
	{	
		var msgString = "";
		var titleString = "";
		if (this.isset(param.error) && this.isset(param.error.errcode))
		{
			var errCodeInt = parseInt(param.error.errcode);
			switch(errCodeInt)
			{
				case 403:
					window.location.href = APP.config.localConfig.urls.logout;
					return;
				case 10000:
					titleString = param.error.errmsg;
					break;
				default:
					msgString += param.error.errmsg+"<BR>";
					titleString = APP.i18n.translate("error")+" "+param.error.errcode;
					$.each(param.error.errdata, function(i, v)
					{
						msgString += v + "<br>";
					});
					break;
			}
		}
		else
		{
			titleString = APP.i18n.translate("error");
			msgString = param.status+" "+param.statusText;
			console.log(titleString+": "+msgString);
			return;
		}
		
		if (this.isset(param.statusText) && param.statusText == "abort")
			return;
		if (this.isset(dialogMode) && dialogMode === true)
			this.showMsg({title: titleString, content: msgString});
		else
			this.showNoty({title: titleString, content: msgString, type: "error"});
	},
	
	showMsg: function(obj)
	{
		var div = this.isset(obj.div)? obj.div : $("#defaultMessageDialog");	
		
		div.find(".modal-title").html(obj.title);
		div.find(".modal-body").html(obj.content);
		if (this.isset(obj.buttons))
			div.find(".modal-footer").html(obj.buttons);		
		
		if (this.isset(obj.onCloseEvent) && $.isFunction(obj.onCloseEvent))
			div.on('hidden', obj.onCloseEvent);
		else
			div.on('hidden', false);
			
		div.modal('show');
	},
	
	showNoty: function(obj)
	{
		var n = noty({
			text: "<strong>"+obj.title+"</strong><br>"+obj.content,
			type: obj.type,
			dismissQueue: true,
			animation: {
		        open: {height: 'toggle'}, // or Animate.css class names like: 'animated bounceInLeft'
		        close: {height: 'toggle'}, // or Animate.css class names like: 'animated bounceOutLeft'
		        easing: 'swing',
		        speed: 200 // opening & closing animation speed
		    },
			modal: this.isset(obj.modal)? obj.modal : false,
			layout: this.isset(obj.layout)? obj.layout : 'topCenter',
			timeout: this.isset(obj.timeout)? obj.timeout : 3000,
			buttons: this.isset(obj.buttons)? obj.buttons : false,
		});
	},
	
	createModal: function(o)
	{
		var myModal = null;
		if (o.container && o.id)
			myModal = o.container.find("#"+o.id);
		if (myModal.length > 0)
			myModal.remove();
			
		myModal = $('<div class="modal fade">\
						<div class="modal-dialog">\
							<div class="modal-content">\
								<div class="modal-header">\
									<button type="button" class="btn-lg close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span></button>\
									<h3 class="lead"></h3>\
								</div>\
								<div class="modal-body">\
								</div>\
								<div class="modal-footer">\
									<button type="button" class="btn btn-default" data-dismiss="modal">'+APP.i18n.translate('close')+'</button>\
								</div>\
							</div>\
						</div>\
					</div>');
			
		if (o.id)
			myModal.attr("id", o.id);
		if (o.size)
			myModal.find(".modal-dialog").removeClass().addClass("modal-dialog modal-"+o.size);
		if (o.header)
			myModal.find(".modal-header h3").html(o.header);
		if (o.body)
			myModal.find(".modal-body").html(o.body);
		if (o.onShown && $.isFunction(o.onShown))
		{
			myModal.on('shown.bs.modal', function(){
				o.onShown();
			});
		}
		if (o.onHidden && $.isFunction(o.onHidden))
		{
			myModal.on('hidden.bs.modal', function(){
				o.onHidden();
			});
		}
		if (o.container)
			o.container.append(myModal);
		
		return myModal;
	},
	
	setTableRow: function(obj)
	{
		var that = this;
		
		var v = obj.model;
		var valori = obj.valori;
		var datastruct = obj.datastruct;
		var tr = obj.row;
		var table;
		
		$.each(datastruct.columns, function(j, k)
		{
			if (!k.table_show)
				return true;
			
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
			if (!v.hasOwnProperty("foreign_key") && v.form_input_type == "combobox" && !APP.utils.isset(v.slave_of))
				valori[v.name] = APP.utils.getForeignValue(v, null);
			
			if (k.form_input_type == "combobox" && !APP.utils.isset(k.slave_of))
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
			
			if (!APP.utils.isset(v[k.name]))
			{
				var str = APP.utils.getSecondaryValue(k); 
				
				tr.append("<td class='table-td "+classesAndStyles[0]+"' style='"+classesAndStyles[1]+"'>"+APP.utils.displayData(str, k)+"</td>");
				return true;
			}
			tr.append("<td class='table-td "+classesAndStyles[0]+"' style='"+classesAndStyles[1]+"'>"+APP.utils.displayData(v[k.name], k)+"</td>");
		});
		return tr;
	},
	
	getThumbnailUrl: function(urls, v)
	{
		if (urls.thumbnail)
		{
			var url = urls.thumbnail;
			$.each(urls.thumbnail_options, function(laChiave, ilValore){
				url = APP.utils.replaceAll(laChiave, v[ilValore], url);
			});
			return url
		}
		return null
	},
	
	isImageFile: function(filename)
	{
		if (filename && !this.isEmptyString(filename))
		{
			var imageExtensions = ['jpg', 'jpeg', 'gif', 'png'];
			var exts = filename.split(".");
			var extension = exts[exts.length-1].toLowerCase();
			if ($.inArray(extension, imageExtensions) !== -1)
				return true;
		}
		return false;
	},
	
	getStyleByCssParams: function(fieldName, css_params)
	{
		var resArr = ["",""]; // classes, styles
		
		if (this.isset(css_params) && css_params.hasOwnProperty(fieldName))
		{
			$.each(css_params[fieldName], function(k,v)
			{
				switch(k)
				{
					case "background":
						resArr[1] += " background-color:"+v+";";
						break;
					default:
						resArr[1] += " "+k+":"+v+";";
				}
			});
		}
		return resArr;
	},
	
	setLookTable: function(tableDiv){
		var filter = tableDiv.find(".dataTables_filter");
		var label = filter.find("label");
		var input = filter.find("input").clone(true);
		input.addClass("form-control");
		input.attr("placeholder",label.text());
		label.html(input);
		
		/*
		---
		
		tableDiv.find(".dataTables_wrapper").addClass("form-horizontal");
		var filter = tableDiv.find(".dataTables_filter");
		filter.addClass("form-group");
		filter.width(400);
		var ch = filter.children();
		var labelText = ch.text();
		
		var newLabel = $('<label for="filterTable" class="col-lg-2 control-label">'+labelText+'</label>');
		var newInputDiv = $('<div class="col-lg-10"></div>');
		
		var input = ch.find("input");
		input.attr("id","filterTable");
		input.addClass("form-control");
		newInputDiv.html(input);
		
		filter.html(newLabel).append(newInputDiv);*/
	},
	
	chosenFieldInit: function(elem)
	{
		var defaultObj = {
			allow_single_deselect: true,
			disable_search_threshold: 5,
			no_results_text: APP.i18n.translate("no result"),
		};
		elem.chosen(defaultObj);
	},
	
	setLookForm: function(form, id)
	{
		var that = this;
	
		if (form.find(".textEditor"))
		{
			$.each(form.find(".textEditor"), function()
			{
				var myId = $(this).attr("id");
				var g = tinymce.get(myId);
				if (g)
					tinymce.remove("#"+myId);
				var oo = {
					selector: '#'+myId,
					inline: false,
					menubar: false,
					readonly : $(this).attr("disabled"),
				};
				var myToolbar = $(this).data().toolbar;
				if (myToolbar && $.isArray(myToolbar) && myToolbar.length > 0)
				{
					oo.toolbar = "";
					$.each(myToolbar, function(x,y)
					{
						oo.toolbar += y;
						if (x < myToolbar.length-1)
							oo.toolbar += " | ";
					});
				}
				var myPlugins = $(this).data().plugins;
				if (myPlugins && $.isArray(myPlugins) && myPlugins.length > 0)
				{
					oo.plugins = "";
					$.each(myPlugins, function(x,y)
					{
						oo.plugins += y;
						if (x < myPlugins.length-1)
							oo.plugins += " ";
					});
				}
				tinymce.init(oo);
			});		
			
			//tinymce.render();
			/*
			else
			tinymce.EditorManager.execCommand('mceRemoveEditor', true, ".textEditor");
			tinymce.EditorManager.execCommand('mceAddEditor', true, ".textEditor");
			tinyMCE.execCommand("mceAddControl", true, ".textEditor");
			
			/*
			
			*/
		}
		
		if (form.find(".mapBoxColor").length > 0 && form.find(".mapboxDiv").length>0)
		{
			APP.map.changeColors(form.find(".mapBoxColor").val());
		}
		
		if (form.find(".c3chart").length>0)
		{
			$.each(form.find(".c3chart"), function(i,v)
			{
				v = $(v);
				switch (v.getAttr("data-chartType"))
				{
					case "line":
						var chart = c3.generate({
							bindto: v,
						    data: {
						        url: APP.config.urls.heightsprofilepath+'/'+v.attr('name')
						    }
						});
						break;
					default:
						break;
				}
			});
		}
		
		form.find(".datepicker").datepicker({ dateFormat: 'dd/mm/yy' });
		var timepickerInputs = form.find(".time");
		$.each(timepickerInputs, function(i,v)
		{
			$(v).timepicker({'timeFormat': "H:i"});
		});
		var chosenInputs = form.find(".chosen");
		$.each(chosenInputs, function(i,v)
		{
			that.chosenFieldInit($(v));
		});
		if (form.find(".fileupload").length > 0)
			APP.fileuploader.init(form);
			
		var ip = form.find(".input-group-prefix"); // prepend
		/*
		if (ip.length>0)
		{
			$.each(ip, function(i,v)
			{
				var input = $(v).find("input").first();
				var w1 = input.width();
				var w2 = 0;//$(v).find("span.add-on").first().width(); non va perche' c'e' anche il padding
				input.width(w1-w2);
			});
		}
		var ia = form.find(".input-group-suffix"); // append
		if (ia.length>0)
		{
			$.each(ia, function(i,v)
			{
				var input = $(v).find("input").first();
				var w1 = input.width();
				var w2 = 0;//$(v).find("span.add-on").first().width(); non va perche' c'e' anche il padding
				//input.width(w1-w2);
			});
		}
		*/
		
		//form.find(".minicolors").minicolors();	
		
		var inps = form.find(":input");
		$.each(inps, function(i,v)
		{
			that.setChangeProperty($(v));
			$(v).data().mode = that.isset(id)? "update" : "insert";
		});		
		if (APP.utils.isset(id))
		{
			$.each(inps, function(i,v)
			{
				v = $(v);
				var data = v.data();
				if (APP.utils.isset(data) && APP.utils.isset(data.change))
				{
					v.data().mode = "first_update";
					v.change();
					/*var fields = data.sectionTarget.columns;
					var ind = that.getIndexFromField(fields, "name", v.attr("name"));
					if (ind > -1 && !fields[ind].editable.update)
					{
						v.attr("disabled", true);
					}*/
				}
			});
		}
		
		if (form.hasClass("wizard"))
		{
			var settings = form.data().jWizardSettings;
			var fb = form.find("#formButtons").clone(true,true);
			form.find("#formButtons").remove();
			form.jWizard(settings);
			form.prepend(fb.show());
			form.find(".ui-widget-content").addClass("ui-corner-all");
			//form.find(".jw-button-prev").prepend('<i class="icon icon-arrow-left"></i> ');
			//form.find(".jw-button-next").append(' <i class="icon icon-arrow-right"></i>');
			form.find(".jw-button-finish").prepend('<i class="icon icon-ok"></i> ');
			form.find(".chosen-container").css("width",'100%');
			
		}
		
		form.find(".tooltipElement").tooltip();
		
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
	},
	
	checkError: function(error, param)
	{
		switch(error.errcode)
		{
			case 0:
				return false;
			case 10000: // errore di validazione
				/*
				$.each(error.errdata, function(i, v)
				{
					APP.utils.renderError(param.find("#APP-"+i), v);
				});
				*/
				$.each(error.errdata, function(i, v)
				{
					var el = param.find("#APP-"+i);
					if (el.length > 0)
						APP.utils.renderError(el, v);
					else
					{
						// potrebbe essere un multifield
						APP.multifields.validation(param, i, v);
					}
					
				});
				break;
			default:
				break;
		}
		return true;
	},
	
	resetFormErrors: function(form)
	{
		var cGroups = form.find(".form-group");
		$.each(cGroups, function(i, v)
		{
			v = $(v);
			v.removeClass("has-error");
			v.find(".help-block").remove();
		});
	},
	
	renderError: function(el, v){
		var cgd = el.parents(".form-group").first();
		cgd.addClass("has-error");
		var span = '<span class="help-block"><small>'+v+'</small></span>';
		var hi = cgd.find(".help-block");
		(hi.length > 0)? hi.html(span) : el.parent().append(span);
	},
	
	confirmMsg: function(text, btnLabels, callbacks)
	{
		var that = this;
		
		APP.utils.showNoty({
			layout: 'center',
			modal: true,
			title: APP.i18n.translate("Confirm"),
			content: text,
			type: 'confirm',
			timeout: false,
			buttons: [{
				addClass: 'btn btn-success',
				text: btnLabels['yes'],
				onClick: function($noty) {
					$noty.close();
					if (that.isset(callbacks['yes']) && $.isFunction(callbacks['yes']))
						callbacks['yes']();
				}
			},
			{
				addClass: 'btn btn-default',
				text: btnLabels['no'],
				onClick: function($noty) {
					$noty.close();
					if (that.isset(callbacks['no']) && $.isFunction(callbacks['no']))
						callbacks['no']();
				}
			}]
		});
		
		/*
		var confirm = $("body").find("#confirmModal");
		if (confirm.length > 0)
			confirm.remove();
				
		confirm = $('<div id="confirmModal" class="modal fade" role="dialog">\
							<div class="modal-dialog">\
								<div class="modal-content">\
									<div class="modal-header">\
										<button type="button" class="close negativeResponse" aria-hidden="true">&times;</button>\
										<h4 class="modal-title">'+APP.i18n.translate("Confirm")+'</h4>\
									</div>\
									<div class="modal-body">\
										<p>'+text+'</p>\
									</div>\
									<div class="modal-footer">\
										<button type="button" class="btn btn-success"><i class="icon icon-ok"></i> '+btnLabels['yes']+'</button>\
										<button type="button" class="btn btn-default negativeResponse">'+btnLabels['no']+'</button>\
									</div>\
								</div>\
							</div>\
						</div>');
		
		
		confirm.find(".btn-success").click(function()
		{
			confirm.modal('hide');
			if (that.isset(callbacks['yes']) && $.isFunction(callbacks['yes']))
				callbacks['yes']();
		});
		confirm.find(".negativeResponse").click(function()
		{
			confirm.modal('hide');
			if (that.isset(callbacks['no']) && $.isFunction(callbacks['no']))
				callbacks['no']();
		});
		$("body").prepend(confirm);
		confirm.modal('show');
		*/
	},
	
	
	/*
							var div = $("#"+sectionTarget.resource+"Container");							
							var saveConfirm = div.find("#saveConfirmModal");
							if (saveConfirm.length > 0)
								saveConfirm.remove();
								
							saveConfirm = $('<div id="saveConfirmModal" class="modal fade" role="dialog">\
												<div class="modal-dialog">\
													<div class="modal-content">\
														<div class="modal-header">\
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
															<h4 class="modal-title">'+APP.i18n.translate("Confirm")+'</h4>\
														</div>\
														<div class="modal-body">\
															<p>'+APP.i18n.translate("save_required")+'</p>\
														</div>\
														<div class="modal-footer">\
															<button type="button" class="btn btn-success"><i class="icon icon-ok"></i> '+APP.i18n.translate("save")+'</button>\
															<button type="button" class="btn btn-default" data-dismiss="modal">'+APP.i18n.translate("cancel")+'</button>\
														</div>\
													</div>\
												</div>\
											</div>');
						
							div.append(saveConfirm);
							saveConfirm.find(".btn-success").click(function()
							{
								saveConfirm.modal('hide');
								context.formSubmit(identifier, sectionTarget.resource, function(){ printNow() });
							});
							saveConfirm.modal('show');
	*/
	
	
	setChangeProperty: function(elem)
	{
		var that = this;
		if (!APP.utils.isset(elem.data().change))
			return;
		
		var form = elem.parents("form").first();
		var sectionTarget = elem.data().sectionTarget;
			
		elem.change(function()
		{
			$.ajax({
				type: 'GET',
				url: elem.data().change+$(this).val(),
				dataType: 'json',
				success: function(data)
				{
					if (!APP.utils.checkError(data.error, null))
					{
						$.each(data.data, function(fieldName, fieldObj)
						{
							var fieldToChange = (elem.hasClass("multifield"))? elem.parents("tr:first").find("#APP-"+fieldName) : form.find("#APP-"+fieldName); //form.find("#APP-"+fieldName);
							
							$.each(fieldObj, function(action, actionObj)
							{
								if (that.isset(actionObj.available))
								{
									var ind = $.inArray(elem.data().mode, actionObj.available);
									if (ind === -1)
										return true;
								}
							
								switch(action)
								{
									case "value":
										if (fieldToChange.is('select'))
										{
											var oldValue = fieldToChange.val();
											var arr = [];
											$.each(fieldObj.value.items, function(j, k)
											{
												var label = APP.config.getValue(k, fieldObj.value.label_toshow, fieldObj.value.label_toshow_params);
												var valueField = (APP.utils.isset(k[fieldObj.value.value_field]))? fieldObj.value.value_field : "id";
												if (!APP.utils.isset(k[valueField]) || APP.utils.isEmptyString(k[valueField]) || !APP.utils.isset(label) || APP.utils.isEmptyString(label))
													return true;
													
												arr.push({name: label, value: k[valueField]});
											});
											
											fieldToChange.html(that.createOptions(arr));
											if (fieldToChange.find("option").length === 1)
												fieldToChange.attr("disabled", true);
											else
												fieldToChange.val(oldValue);
											break;
										}
										if (fieldToChange.is('input') && fieldToChange.hasClass("mapbox"))
										{
											APP.map.updateLayerGroups(elem.attr("id"), 'mapboxDiv-'+fieldToChange.attr("name"), fieldObj.value.items);
											break;
										}
										fieldToChange.val(fieldObj.value.items[0]);
										break;
									case "notify":
										var to = (APP.utils.isset(actionObj.timeout))? actionObj.timeout : 3000; 
										APP.utils.showNoty({title: actionObj.title, type: actionObj.type, content: actionObj.msg, "timeout": to});
										break;
									case "required":
										if (actionObj.value)
										{
											fieldToChange.attr("required", true);
											if (fieldToChange.parents(".form-group").first().find(".control-label").find(".requiredSymbol").length === 0)
												fieldToChange.parents(".form-group").first().find(".control-label").prepend(that.getRequiredSymbol());
										}
										else
										{
											fieldToChange.attr("required", false);
											fieldToChange.parents(".form-group").first().find(".control-label").find(".requiredSymbol").remove();
										}
										break;
									case "disabled":
										if (actionObj.value)
										{
											fieldToChange.attr("disabled", true);
										}
										else
										{
											fieldToChange.attr("disabled", false);
										}
										break;
									case "hidden":
										if (actionObj.value)
										{
											fieldToChange.parents(".form-group:first").fadeOut(600);
										}
										else
										{
											fieldToChange.parents(".form-group:first").fadeIn(600);
										}
										break;
									default:
										console.log("Francesco, aggiungi questa action: "+action);
								}
							});
							if (fieldToChange.hasClass("chosen"))
								fieldToChange.trigger("chosen:updated");
						});
					}
					else
						APP.utils.showErrMsg(data);
				},
				error: function(result)
				{
					APP.utils.showErrMsg(result);
				},
				complete: function(result)
				{
					if (elem.data().mode === "first_update")
						elem.data().mode = "update";
				},
			});
		});	
	},
	
	getColorFromActivity: function(ai)
	{
		var defaultColour = "yellow";
		ai = parseInt(ai);
		if (isNaN(ai))
			return defaultColour;
		var index = this.getIndexFromField(APP.config.localConfig.sessione_decodifica_attivita, "id", ai);
		if (index > -1)
		{
			if (this.isset(APP.config.localConfig.sessione_decodifica_attivita[index]) && this.isset(APP.config.localConfig.sessione_decodifica_attivita[index].color))
				return APP.config.localConfig.sessione_decodifica_attivita[index].color;
			else
				return defaultColour;
		}
		else
			return defaultColour;
	},
	
	createOptions: function(obj)
	{
		var htmlString = "<option value=''></option>";
		
		$.each(obj, function(i, v)
		{
			htmlString += "<option value='"+v.value+"'>"+v.name+"</option>";
		});
		
		return htmlString;
	},
	
	getSyncJxValues: function(urlValue)
	{
		var that = this;
		var result = null;
		$.ajax({
			type: 'GET',
			url: urlValue,
			async: false,
			dataType: 'json',
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
					result = data;
				else
					APP.utils.showErrMsg(data);
			},
			error: function(result){ 
				APP.utils.showErrMsg(result); 
			}
		});
		return result;
	},	
	
	getDataFromUrl: function(url_values)
	{
		var data = [];
		if (APP.utils.isset(url_values))
		{
			var jxValues = this.getSyncJxValues(url_values);
			if (APP.utils.isset(jxValues))
				data = jxValues.data.items;
		}
		return data;
	},
	
	getForeignValue: function(column, valori, par)
	{
		var that = this;
		var urlV = column.url_values;
		var data = null;
		
		if (APP.utils.isset(column.url_values_params))
		{
			$.each(column.url_values_params, function(i,v)
			{
				if (APP.utils.isset(par))
				{
					if ($.isPlainObject(par))
						urlV = that.replaceAll(i, par[v], urlV);
					else
						urlV = that.replaceAll(i, par, urlV);
				}
				else
				{
					if (typeof v == "object")
					{
						$.each(APP.config.breadCrumb, function(ii, vv)
						{
							if (!that.isset(vv.data) || !that.isset(vv.level) || vv.level !== v.level)
								return true;
							
							urlV = that.replaceAll(i, vv.data[v.field], urlV);
							return false;
						});
					}
					else
					{
						urlV = that.replaceAll(i, APP.config.breadCrumb[APP.config.breadCrumb.length-1].data[v.field], urlV);
					}
				}
			});
		}
		if (APP.utils.isset(column.default_value))
		{
			var t = column.default_value;
			var livello = "";
			if (that.currentSection == "fleetgroup")
			{
				var node = APP.orgchart.getNodeFromId(APP.orgchart.selectedItem);
				livello = "&livello="+(APP.utils.isset(options.livello)? options.livello : node.livello);
			}
			switch (t)
			{
				case "data": // in questo caso il valore di default lo possiedo gia' e, inoltre devo ugualmente fare una richiesta con url_values
					data = that.getDataFromUrl(urlV+livello);
					if (that.isset(valori) && valori != "")
					{
						if ($.isArray(valori))
						{
							$.each(valori, function(i,v)
							{
								data.push(v);
							});
						}
						else
						{
							if (typeof valori == "object")
								data.push(valori);
						}
					}
					break;
				case "data-id":
					var idsString = "";
					var tempData = [];
					if (that.isset(valori) && valori != "")
					{
						if ($.isArray(valori))
						{
							$.each(valori, function(i,v)
							{
								idsString += v.id+",";
								tempData.push(v);
							});
						}
					}
					else
					{
						data = [];
						break;
					}
					idsString = idsString.substring(0, idsString.length-1);
					if (idsString != "")
						data = that.getDataFromUrl(urlV+"&default_current_id="+idsString+livello);
					$.merge(data, tempData);
					break;
				case "url":
					data = that.getDataFromUrl(urlV+livello);
					break;
				default:
					console.log("Aggiungi questo default_value: "+t);
			}
		}
		else
			data = that.getDataFromUrl(urlV);
			
		return data;
	},
	
	setHomeReport: function(params) // panelPerRow, container, section, callback
	{
		var that = this;		
		
		var createPanel = function(obj)
		{
			var spanNumber = parseInt(12/params.panelPerRow);
			var panelClass = APP.utils.isset(obj.panelClass)? obj.panelClass : 'panel-default';
			var well = $('<div class="panel '+panelClass+'"></div>');
			well.append('<div class="panel-heading">'+obj.title+'</div>');
			well.append('<div class="panel-body" style="padding:0px;"><ul class="list-group" style="margin: 0px"></ul></div>');
			
			var appLi = function(label, badge, well)
			{
				var li = $('<li class="list-group-item"></li>');
				li.append(APP.utils.capitalize(APP.i18n.translate(label)));
				li.prepend('<span class="badge">'+badge+'</span>');
				well.find('.list-group').append(li);
			};		
			
			$.each(obj.items, function(i,v)
			{
				if ($.isPlainObject(v))
				{
					$.each(v, function(j,k)
					{
						switch(i)
						{
							/*
							case "state": 
								var index = APP.utils.getIndexFromField(APP.config.localConfig.states, "id", parseInt(j));
								if (index > -1)
								{
									var ooo = APP.config.localConfig.states[index];
									j = APP.i18n.translate(i)+': <span class="label" style="background: '+ooo.color+'">'+ooo.name+'</span>';
								}
								break;
							*/
							default:
								j = APP.i18n.translate(i)+': '+j;
								break;
						}
						appLi(j,k,well);
					});
				}
				else
					appLi(i,v,well);
			});
		
			var spanx = $('<div class="col-md-'+spanNumber+'"></div>');
			spanx.append(well);
			return spanx;
		};
		
		var displayHomeItems = function(data)
		{
			if (data.length === 0)
				return;
			
			var numPanels = data.length;
			var numRows = Math.ceil(numPanels/params.panelPerRow);

			var div = $('<div class="homeitems"></div>');
			var row = null;
			$.each(data, function(j,k)
			{
				if (j === 0 || j%params.panelPerRow === 0)
				{
					row = $('<div class="row"></div>');
					div.append(row);
				}
				row.append(createPanel(k));
			});
			
			return div;
		};
		
		$.ajax({
			type: 'GET',
			url: APP.config.localConfig.urls[params.section],
			dataType: 'json',
			success: function(data)
			{
				if (!APP.utils.checkError(data.error, null))
				{
					if (APP.utils.isset(data.data) && APP.utils.isset(data.data.general))
					{
						var arr = [];
						var getPanelColor = function(iter)
						{
							var cl = ['default','primary','success', 'warning', 'info', 'danger'];
							return APP.utils.isset(cl[iter])? cl[iter] : cl[0];
						};
						var counter = 0;
						$.each(data.data.general, function(i,v)
						{
							if (!$.isPlainObject(v))
								return true;
							var o = {};
							o.title = (APP.utils.isset(v.title))? v.title : APP.utils.capitalize(APP.i18n.translate(i));
							o.items = v;
							o.panelClass = "panel-"+getPanelColor(counter);
							arr.push(o);
							counter++;
						});
						params.container.append(displayHomeItems(arr));
						
						if ($.isFunction(params.callback))
							params.callback();
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
	},
	
	getOptionsSelect: function(valori, name, options, sectionTarget, par)
	{
		var that = this;
		var inp = "";
		
		var ind = APP.utils.getIndexFromField(sectionTarget.columns, "name", name);
		if (ind > -1)
		{
			var data = null;
			if (APP.utils.isset(sectionTarget.columns[ind].foreign_key))
			{
				var fValues = APP.config.localConfig[sectionTarget.columns[ind].foreign_key];
				data = (APP.utils.isset(fValues))? fValues : [];
				name = sectionTarget.columns[ind].foreign_key;
			}
			else
			{
				data = this.getForeignValue(sectionTarget.columns[ind], valori, par);
			}
			var groups = {};
			var group = sectionTarget.columns[ind].foreign_group_field;
			var valueField = this.isset(sectionTarget.columns[ind].foreign_value_field)? sectionTarget.columns[ind].foreign_value_field : "id";
			
			$.each(data, function(j, k)
			{
				var label = APP.config.getValue(k, sectionTarget.columns[ind].foreign_toshow, sectionTarget.columns[ind].foreign_toshow_params);
				if (!APP.utils.isset(k[valueField]) || APP.utils.isEmptyString(k[valueField]) || !APP.utils.isset(label) || APP.utils.isEmptyString(label))
					return true;
				var string = "";
				var selected = "";
				if (!$.isArray(valori))
					valori = [valori];
				if ((typeof valori[0] == "object") && (valori[0] !== null))
					selected = (APP.utils.getIndexFromField(valori, valueField, k[valueField]) === -1)? "" : "selected";
				else
					selected = ($.inArray(k[valueField], valori) === -1)? "" : "selected";
				
				if (APP.utils.isset(group))
				{
					if (!APP.utils.isset(groups[k[group]]))
						groups[k[group]] = [];
					
					groups[k[group]].push("<option value='"+k[valueField]+"' "+selected+">"+label+"</option>")
				}
				else
					inp += "<option value='"+k[valueField]+"' "+selected+">"+label+"</option>";
			});
			if (!$.isEmptyObject(groups))
			{
				$.each(groups, function(i,v)
				{
					if (!APP.utils.isset(i) || i == "")
						return true;
					inp += '<optgroup label="'+i+'">';
					
					$.each(v, function(j,k)
					{
						inp += k;
					});
					inp += '</optgroup>';
				});
			}
		}
		return inp;
	},
	
	getRequiredSymbol: function()
	{
		return '<small class="requiredSymbol"><i class="icon icon-asterisk text-danger"></i></small> ';
	},
	
	getInputFormat: function(identifier, obj, required, sectionLabel, sectionTarget, context, v, valore, form)
	{
		var that = this;
		var inp = null;
		
		switch(v.form_input_type)
		{
			case "input":
			{
				switch(v.data_type)
				{
					case "boolean":
						var tValue = (valore === true || valore == "true")? " selected " : "";
						var fValue = (valore === false || valore == "false")? " selected " : "";
						inp =	"<select id='APP-"+v.name+"' name='"+v.name+"' class='form-control chosen' data-placeholder='"+APP.i18n.translate("click_to_select")+"' "+required+">\
									<option value=''></option>\
									<option value='true' "+tValue+">"+APP.i18n.translate("yes")+"</option>\
									<option value='false' "+fValue+">"+APP.i18n.translate("no")+"</option>\
								</select>";
						break;
					case "subform":
						if (!this.isset(sectionTarget.subforms))
							sectionTarget.subforms = {};
						if (!this.isset(sectionTarget.subforms[v.name]))
							sectionTarget.subforms[v.name] = {};
							
						inp = "<div id='APP-"+v.name+"'></div>";
						
						var uri = APP.config.localConfig.urls['dStruct']+"?tb="+v.name;
						
						$.ajax({
							type: 'GET',
							url: uri,
							dataType: 'json',
							success: function(data)
							{
								if (!APP.utils.checkError(data.error, null))
								{
									sectionTarget.subforms[v.name] = APP.utils.setBaseStructure(v.name, sectionLabel);
									context.loadStructure(data, sectionTarget.subforms[v.name]);
									sectionTarget.subforms[v.name].subformValidationUrl = (that.isset(v.validation_url))? v.validation_url : null;
									sectionTarget.subforms[v.name].token = 0;
									context.loadData(APP.config.localConfig.urls[v.name]+"?filter="+v.foreign_key+":"+identifier, sectionTarget.subforms[v.name], function(obj){
										var oggetto = {};
										$.extend(oggetto, {"subformName": v.name, "sectionTarget": sectionTarget, "ctx": context, "res": obj, "idl": APP.config.default_iDisplayLength});
										APP.subforms.showSubformTable(oggetto); 
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
						break;
					case "multifield":
						if (!this.isset(sectionTarget.multifields))
							sectionTarget.multifields = {};
						if (!this.isset(sectionTarget.multifields[v.name]))
							sectionTarget.multifields[v.name] = {};
							
						inp = "<div id='APP-"+v.name+"'></div>";
						
						var uri = APP.config.localConfig.urls['dStruct']+"?tb="+v.name;
						
						$.ajax({
							type: 'GET',
							url: uri,
							dataType: 'json',
							success: function(data)
							{
								if (!APP.utils.checkError(data.error, null))
								{
									sectionTarget.multifields[v.name] = APP.utils.setBaseStructure(v.name, sectionLabel);
									context.loadStructure(data, sectionTarget.multifields[v.name]);
									sectionTarget.multifields[v.name].multifieldValidationUrl = (that.isset(v.validation_url))? v.validation_url : null;
									sectionTarget.multifields[v.name].token = 0;
									
									var index = APP.utils.getIndexFromField(sectionTarget.values, "id", identifier);
									var obj = null;
									if (index > -1)
									{
										obj = sectionTarget.values[index][v.name];
										sectionTarget.multifields[v.name].values = sectionTarget.values[index][v.name];
									}
									
									var oggetto = {};
									$.extend(oggetto, {"multifieldName": v.name, "sectionTarget": sectionTarget, "ctx": context, "res": obj, "idl": APP.config.default_iDisplayLength, "div": inp});
									APP.multifields.showMultifieldTable(oggetto);
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
					case "subtable":
						inp = "<div id='APP-"+v.name+"' class='table-responsive'></div>";
						
						var myUrl = v.url_values;
						if (v.url_values_params)
						{
							$.each(v.url_values_params, function(j,k){
								myUrl = that.replaceAll(j, obj[k], myUrl);
							});
						}
						
						$.ajax({
							type: 'GET',
							url: myUrl,//v.url_values+"?filter="+v.foreign_key+":"+identifier,
							dataType: v.ajax_mode,
							success: function(data)
							{
								var table = null;
								if (v.ajax_mode == "json")
								{
									table = $(	'<table class="table table-striped table-bordered table-condensed datatable">\
													<thead><tr></tr></thead>\
													<tbody></tbody>\
												</table>');
									
									var theadtr = table.find("thead tr");
									var tbody = table.find("tbody");
									var cols = [];
									if (data.data.items.length === 0)
									{
										form.find("#APP-"+v.name).html("<p><i>"+APP.i18n.translate("no result")+"</i></p>");
										return;
									}
									$.each(data.data.items, function(ii, vv)
									{
										if (ii === 0)
										{
											$.each(vv, function(iii, vvv)
											{
												cols.push(iii);
												theadtr.append("<td>"+iii+"</td>");
											});
										}
										var tr = $("<tr></tr>");
										$.each(cols, function(iii, vvv)
										{
											tr.append("<td>"+vv[vvv]+"</td>");
										});
										tbody.append(tr);
									});
								}
								else
									table = $(data);
								
								form.find("#APP-"+v.name).html(table);
								if (v.datatable)
								{
									form.find("#APP-"+v.name).find("table").dataTable({
										"bProcessing": true,
										"bFilter": false,
										"bLengthChange": false,
										"bInfo": false,
										"bPaginate": false,
										"iDisplayLength": false,
										"oLanguage": APP.utils.getDataTableLanguage()
									});
								}
							},
							error: function(result){ APP.utils.showErrMsg(result); }
						});
						break;
					case "file":
						//var oid = (this.isset(obj))? obj.id : null;
						//inp = APP.fileuploader.getString({"name": v.name, "value": obj, "multiple": v.multiple, "id": oid, "urls": v.urls, "options": v.data_options});
						var tmpArr = [];
						if($.isArray(obj))
							tmpArr = obj;
						else
							if (!$.isEmptyObject(obj))
								tmpArr.push(obj);
								
						inp = APP.fileuploader.getString({"name": v.name, "value": tmpArr, "multiple": v.multiple, "urls": v.urls, "options": v.data_options, "capabilities": sectionTarget.capabilities});
						break;
					default:
						inp = "<input type='text' class='form-control' id='APP-"+v.name+"' name='"+v.name+"' "+required+">";
						inp = $(inp).val(valore);
				}
				break;
			}
			case "subdatastruct":
				if (APP.utils.isset(valore))
				{
					inp = $('<div id="APP-'+v.name+'"></div>');
					
					$.ajax({
						type: 'GET',
						url: valore,
						dataType: 'json',
						async: false,
						success: function(data)
						{
							if (!APP.utils.checkError(data.error, null) && APP.utils.isset(data.data) && APP.utils.isset(data.data.groups) && data.data.groups.length > 0)
							{							
								$.each(data.data.groups, function(iGr, vGr){
									var grdiv = $('<div class="form-group"></div>');
									
									grdiv.append('<label class="control-label" style="text-align:left">'+vGr.name+'</label>');
									
									$.each(vGr.fields, function(iFd, vFd){
										if (!APP.utils.isset(data.data.fields[vFd].name))
											data.data.fields[vFd].name = vFd;
										
										var vvaalloorree = (APP.utils.isset(data.data.values) && APP.utils.isset(data.data.values[vFd]))? data.data.values[vFd] : "";
										var inppp = that.getInputFormat(null, {}, required, sectionLabel, data.data, context, data.data.fields[vFd], vvaalloorree, form)
										inppp.css("margin-bottom",10);
										if (APP.utils.isset(data.data.fields[vFd].css_class))
										{
											if (inppp.is(":input"))
												inppp.addClass(data.data.fields[vFd].css_class);
											else
												inppp.find(":input").addClass(data.data.fields[vFd].css_class);
										}
										if (APP.utils.isset(obj[v.name+"_values"]) && APP.utils.isset(obj[v.name+"_values"][vFd]))
										{
											if (data.data.fields[vFd].form_input_type === "radiobutton")
											{
												var radios = inppp.find("#APP-"+vFd);
												$(radios[parseInt(obj[v.name+"_values"][vFd])-1]).attr("checked",true);
											}
											else
											{
												if (inppp.is(":input"))
													inppp.val(obj[v.name+"_values"][vFd]);
												else
													inppp.find(":input").val(obj[v.name+"_values"][vFd]);
											}
										}
										if (APP.utils.isset(data.data.fields[vFd].label))
											grdiv.append('<small>'+data.data.fields[vFd].label+'</small>');
										grdiv.append(inppp);
									});
									inp.append(grdiv);
								});
							}
							else
								APP.utils.showErrMsg(data);
						},
						error: function(result){ APP.utils.showErrMsg(result); }
					});
				}
				break;
			case "password":
				inp = "<input type='password' class='form-control' id='APP-"+v.name+"' name='"+v.name+"' "+required+">";
				inp = $(inp).val(valore);
				break;
			case "radioswitch":
				inp = $('<div class="form-group"></div>');
				
				$.each(v.values, function(radioI, radioV){
					inp.append('<label for="APP-'+v.name+'_'+radioI+'">'+radioV+'</label>');
					inp.append('<input id="APP-'+v.name+'" type="radio" class="bootstrapSwitch"  name="'+v.name+'">');
					inp.append('<br>');
				});
				inp.find(".bootstrapSwitch").bootstrapSwitch();
				break;
			case "radiobutton":
				inp = $('<div></div>');
				
				if (v.radio_inline)
				{					
					$.each(v.values, function(radioI, radioV){
						var rblabel = $('<label class="radio-inline"></label>');
						rblabel.append('<input type="radio" id="APP-'+v.name+'" name="'+v.name+'" value="'+radioI+'">');
						rblabel.append(radioV);
						inp.append(rblabel);
					});
				}
				else
				{
					$.each(v.values, function(radioI, radioV){
						var divRadio = $('<div class="radio"></div>');
						var rblabel = $('<label></label>');
						rblabel.append('<input type="radio" id="APP-'+v.name+'_'+radioI+'" name="'+v.name+'" value="'+radioI+'">');
						rblabel.append(radioV);
						divRadio.append(rblabel);
						inp.append(divRadio);
					});
				}
				break;
			case "file":
				alert("per il campo file, il form_input_type deve essere 'input' ed il data_type 'file'");
				return;
			case "hidden":
				inp = "<input type='hidden' id='APP-"+v.name+"' name='"+v.name+"' "+required+">";
				inp = $(inp).val(valore);
				break;
			case "datebox":
				if (that.isset(valore) && !that.isEmptyString(valore))
					valore = that.convertiData(valore);
				inp = "<input type='text' id='APP-"+v.name+"' class='form-control datepicker' name='"+v.name+"' value='"+valore+"' "+required+">";				
				//inp = "<input type='date' id='APP-"+v.name+"' class='form-control' name='"+v.name+"' value='"+valore+"' "+required+">";
				break;
			case "timebox":
				inp = "<input type='text' id='APP-"+v.name+"' class='form-control time' name='"+v.name+"' value='"+valore+"' "+required+">";
				//inp = "<input type='time' id='APP-"+v.name+"' class='form-control' name='"+v.name+"' value='"+valore+"' "+required+">";
				break;
			case "datetimebox":
				inp = "<input type='text' class='form-control' id='APP-"+v.name+"' name='"+v.name+"' "+required+">";
				valore = that.convertiDateTime(valore);
				inp = $(inp).val(valore);
				break;
			case "textarea":
				inp = $("<textarea id='APP-"+v.name+"' name='"+v.name+"' rows='9' class='form-control' "+required+"></textarea>");
				inp.text(valore);
				if (APP.utils.isset(v.editor) && v.editor === true)
				{
					inp.addClass("textEditor");
					inp.data({toolbar: v.editor_buttons, plugins: v.editor_plugins});
				}
				break;
			case "cleartext":
				inp = $('<p id="APP-'+v.name+'" name="'+v.name+'"></p>');
				inp.text(valore);
				break;
			case "htmltext":
				inp = $('<p id="APP-'+v.name+'" name="'+v.name+'"></p>');
				inp.html(valore);
				break;
			case "combobox":
				inp = $("<select id='APP-"+v.name+"' class='form-control chosen' data-placeholder='"+APP.i18n.translate("click_to_select")+"' "+required+"></select>");
				
				if (APP.utils.isset(v.foreign_mode) && v.foreign_mode == 'multiselect')
				{
					inp.attr("name", v.name+"[]");
					inp.attr("multiple", true);
				}
				else
					inp.attr("name", v.name);
					
				inp.append("<option value=''></option>");
				
				if (that.isset(v.slave_of))
				{
					var parVal = "";
					var iinnddeexx = APP.utils.getIndexFromField(sectionTarget.columns, "name", v.slave_of);
					if (iinnddeexx > -1 && sectionTarget.columns[iinnddeexx].form_input_type == "mapbox")
					{
						if (identifier) 
						{
							parVal = (obj[v.slave_of] && $.isArray(obj[v.slave_of].coordinates))? {
								lon: obj[v.slave_of].coordinates[0],
								lat: obj[v.slave_of].coordinates[1]
							} : "";
						}
						else
						{
							parVal = (obj[v.slave_of] && $.isArray(obj[v.slave_of].geometry.coordinates))? {
								lon: obj[v.slave_of].geometry.coordinates[0],
								lat: obj[v.slave_of].geometry.coordinates[1]
							} : "";
						}
					}
					else
					{
						parVal = (form.find("#APP-"+v.slave_of).length > 0)? form.find("#APP-"+v.slave_of).val() : "";
					}
					
					if (that.isset(parVal) && !that.isEmptyString(parVal))
					{
						if (v.data_type === "integer" && sectionTarget.columns[iinnddeexx].form_input_type != "mapbox")
							parVal = parseInt(parVal);
						inp.append(this.getOptionsSelect(valore, v.name, obj, sectionTarget, parVal));
					}
					else
						inp.attr("disabled", true);
						
					if (inp.find("option").length === 1)
						inp.attr("disabled", true);
				}
				else
					inp.append(this.getOptionsSelect(valore, v.name, obj, sectionTarget));
				break;
			case "button":
				inp = null;
				var btn = $('<span id="APP-'+v.name+'" class="btn btn-lg btn-'+v.input_class+' tooltipElement" data-toggle="tooltip" data-placement="bottom" title="'+v.description+'">'+" "+v.label+'</span>');
				
				if (v.icon)
					btn.prepend('<span class="icon icon-'+v.icon+'"></span>');
				
				switch(v.data_type)
				{
					case "pdf_print":					
						btn.click(function()
						{
							/*
							var printNow = function()
							{
								var serializedArray = form.serializeArray();								
								
								$.each(form.find(":input"), function(j,k)
								{
									k = $(k);
									if (k.is(":disabled"))
										serializedArray.push({'name': k.attr("name"), 'value': k.val()});
										
									var ind = that.getIndexFromField(serializedArray, "name", k.attr("name"));
									
									if (k.is("select"))
									{
										if (ind > -1)
										{
											var str = "";
											var ids = [];
											$.each(k.find("option:selected"), function(j1,k1)
											{
												k1 = $(k1);
												str += $(k1).text()+", ";
												ids.push($(k1).val());
											});
											str = str.substr(0, str.length-2);
											serializedArray[ind].value = str;
											serializedArray.push({name: k.attr("name")+"___ids", value: ids});
										}
										return true;
									}
									if (k.is("textarea"))
									{
										if (ind > -1)
											serializedArray[ind].value = k.text();
										return true;
									}
								});
								
								$.fileDownload(v.url, {
									httpMethod: "POST",
									data: serializedArray
								})
								.fail(function () {
									APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("download_failure")});
								});
							};
							APP.utils.saveConfirm(function(){ context.formSubmit(identifier, sectionTarget.resource, function(){ printNow() }); });
							*/
							var urlV = v.url_values;
							if (that.isset(v.url_values_params))
							{
								$.each(v.url_values_params, function(j,k){
									urlV = that.replaceAll(j, form.find("#APP-"+k).val(), urlV);
								});
							}
							
							$.fileDownload(urlV, {
								httpMethod: "GET"
							})
							.fail(function () {
								APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("download_failure")});
							});
						});
						break;
					case "mailer":
						btn.click(function()
						{
							var labels = {
								'yes': APP.i18n.translate("save"),
								'no': APP.i18n.translate("cancel")
							};
							var callbacks = {
								'yes': function()
								{
									context.formSubmit(identifier, sectionTarget.resource, function()
									{
										APP.mailer.start();
									});
								},
								'no': null,
							};
							
							var saveRequiredMsg = (APP.utils.isset(that.sections[section].messages) && APP.utils.isset(that.sections[section].messages['save_required']))? that.sections[section].messages['save_required'] : APP.i18n.translate("save_required");
							APP.utils.confirmMsg(saveRequiredMsg, labels, callbacks);
						});
						APP.mailer.init({
							div: $("#"+sectionTarget.resource+"Container"),
							actionUrl: "application/Email.php?action=send", // quando premo su Invia
							addressesUrl: "application/Uffici.php",
							valuesUrl: "application/Reclami.php?action=emailvalues&id="+identifier,
						});
						break;
					default:
						break;
				}
				
				var fb = form.find("#formButtons");
				fb.append(btn);
				break;
			case "colorpicker":
				inp = "<input type='color' id='APP-"+v.name+"' name='"+v.name+"' class='form-control' value='"+valore+"' "+required+">";
				break;
			case "mapbox":
				inp = $("<div  style='width: 100%;'></div>");
				inp.append("<div id='mapboxDiv-"+v.name+"' class='mapbox mapboxDiv' style='width: 100%; height: 400px;'></div>");
				var tmpInp = $("<input type='hidden' id='APP-"+v.name+"' name='"+v.name+"' class='mapbox'>");
				if (valore != "")
					tmpInp.val(JSON.stringify(valore));
				inp.append(tmpInp);
				break;
			case "mapbox_color":
				inp = "<input type='color' id='APP-"+v.name+"' name='"+v.name+"' class='form-control mapBoxColor' value='"+valore+"' "+required+">";
				inp = $(inp);
				inp.change(function(){APP.map.changeColors($(this).val())});
				break;
			case "c3chart":
				inp = $('<div id="APP-'+v.name+'" name="'+v.name+'" class="c3chart" data-chartType="'+v.type+'"></div>');
				break;
			default:
				console.log("Aggiungi questo form_input_type: "+v.form_input_type);
		}
		
		if (inp === null)
			return null;
			
		inp = $(inp);
		
		if (this.isset(v.change))
		{
			inp.data({
				change: v.change,
				sectionTarget: sectionTarget
			});
		}
		
		/*if (that.isset(v.editable_on_insert) && that.isset(identifier))
		{
			if (v.editable_on_insert === true)
				$(inp).attr("disabled", false);
			else
				$(inp).attr("disabled", true);
		}
		if (that.isset(v.editable_on_edit) && !that.isset(identifier))
		{
			if (v.editable_on_edit === true)
				$(inp).attr("disabled", false);
			else
				$(inp).attr("disabled", true);
		}*/
				
		if (that.isset(v.slave_of) && v.form_input_type !== "combobox")
		{
			var parent = form.find("#APP-"+v.slave_of);
			if (parent.length === 0)
				return;
				
			var parVal = parent.val();
			if (!that.isset(parVal) || that.isEmptyString(parVal))
				inp.attr("disabled", true);
		}
		
		var globalDiv = $('<div></div>');		
		
		if (this.isset(v.prefix) || this.isset(v.suffix))
		{
			if (this.isset(v.prefix) && !this.isset(v.suffix))
			{
				var inpGrp = $('<div class="input-group input-group-prefix"></div>');
				inpGrp.append('<span class="input-group-addon">'+v.prefix+'</span>');
				inpGrp.append(inp)
				globalDiv.append(inpGrp);
			}
			if (this.isset(v.suffix) && !this.isset(v.prefix))
			{
				var inpGrp = $('<div class="input-group input-group-suffix"></div>');
				inpGrp.append(inp)
				inpGrp.append('<span class="input-group-addon">'+v.suffix+'</span>');
				globalDiv.append(inpGrp);
			}
			if (this.isset(v.suffix) && this.isset(v.prefix))
			{
				var inpGrp = $('<div class="input-group input-group-prefix input-group-suffix"></div>');
				inpGrp.append('<span class="input-group-addon">'+v.prefix+'</span>');
				inpGrp.append(inp)
				inpGrp.append('<span class="input-group-addon">'+v.suffix+'</span>');
				globalDiv.append(inpGrp);
			}
		}
		else
			globalDiv.append(inp);
				
		if (v.form_input_type == "combobox" && v.routing)
		{
			var rStr = v.routing;
			if (v.routing_values)
			{
				$.each(v.routing_values, function(j,k){
					rStr = that.replaceAll(j, obj[k], rStr);
				});
			}
			
			var rtBtn = $('<button type="button" class="btn btn-default btn-sm" data-routing="'+rStr+'">'+APP.i18n.translate("show")+'</button>');
			rtBtn.click(function(){
				var b = $(this);
				var routingString = b.attr("data-routing");
				if (that.isEmptyString(routingString))
					return false;
				//APP.config.bBack = true;
				//APP.config.backUrl = v.name;
				APP.config.workSpace.navigate(routingString, {trigger: true, replace: true});
			});
			globalDiv.addClass("row");
			var newInp = $('<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"></div>');
			inp.appendTo(newInp);
			globalDiv.append(newInp);
			var btnDiv = $('<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>');
			btnDiv.append(rtBtn);
			globalDiv.append(btnDiv);
		}
		
		return globalDiv;
	},
	
	getFormField: function(name, v)
	{
		var that = this;
		var uneditable = "";//(!that.isset(v.editable) || v.editable)? "" : " uneditable-input ";
		var required = this.isset(v.required)? " required " : "";
		var inp = null;
		var valore = this.isset(v.values)? v.values : "";
		v.name = name;
		
		switch(v.form_input_type)
		{
			case "input":
				if (v.sorttype == "boolean")
				{
					var tValue = (valore === true || valore == "true")? " selected " : " ";
					var fValue = (valore === false || valore == "false")? " selected " : " ";
					inp = "<select id='APP-"+v.name+"' name='"+v.name+"' class='form-control chosen "+uneditable+"' "+required+">\
							<option value='true' "+tValue+">SI</option>\
							<option value='false' "+fValue+">NO</option>\
						</select>";
				}
				else				
					inp = "<input type='text' id='APP-"+v.name+"' name='"+v.name+"' value='"+valore+"' class='form-control "+uneditable+"' "+required+">";
				break;
			case "datebox":
				valore = ($.isArray(valore))? valore : ["", ""];
				inp = "<div class='row'>\
							<div class='col-md-6'>\
								<input type='text' id='APP-"+v.name+"_from' class='form-control datepicker' placeholder='"+APP.i18n.translate("from")+"' name='"+v.name+"_from' value='"+valore[0]+"' "+required+">\
							</div>\
							<div class='col-md-6'>\
								<input type='text' id='APP-"+v.name+"_to' class='form-control datepicker' placeholder='"+APP.i18n.translate("to")+"' name='"+v.name+"_to' value='"+valore[1]+"' "+required+">\
							</div>\
						</div>"
				break;
			case "colorpicker":
				inp = "<input id='APP-"+v.name+"' name='"+v.name+"' class='form-control minicolors minicolors-input' value='"+valore+"' "+required+">";
				break;
			case "textarea":
				inp = "<textarea id='APP-"+v.name+"' name='"+v.name+"' class='form-control "+uneditable+"' "+required+">"+valore+"</textarea>";
				break;
			case "combobox":
				inp = "<select id='APP-"+v.name+"' name='"+v.name+"' data-placeholder='"+APP.i18n.translate("click_to_select")+"' class='form-control chosen "+uneditable+"' ";
				if (v.mode == 'multiselect')
					inp += " multiple ";
				if (APP.utils.isset(v.required))
					inp += v.required;
				inp += ">";
				inp += that.createOptions(valore, v.label);
				inp += "</select>";
				break;
			default:
				console.log("Aggiungi questo form_input_type: "+v.form_input_type);
				break;
		}
		return inp;
	},
	
	setManualPagination: function(table, numCol, pageObj, urlObj, callback)
	{
		var that = this;
		var MAX_PAGES_NUM = 10;
		var from = parseInt(pageObj.offset+1);
		var to = parseInt(from+pageObj.items_per_page-1);
		if (to > pageObj.tot_items)
			to = pageObj.tot_items;
		var tfoot = table.find("tfoot");
		tfoot.append(	"<td colspan='"+numCol+"'>\
							<div class='pagination pagination-right'>\
								<ul></ul>\
							</div>\
							<div>\
								"+APP.i18n.translate("rows")+": "+APP.i18n.translate("from")+" <b>"+from+"</b> "+APP.i18n.translate("to")+" <b>"+to+"</b> "+APP.i18n.translate("of")+" <b>"+pageObj.tot_items+"</b>\
							</div>\
						</td>");		
										
		if (pageObj.tot_pages > MAX_PAGES_NUM)
		{
			var schema = parseInt((pageObj.offset/(MAX_PAGES_NUM*pageObj.items_per_page)));
			if (from >= (MAX_PAGES_NUM*pageObj.items_per_page))
			{
				var firstLi = $("<li><a href='#'>&laquo;</a></li>");
				firstLi.data({"page": parseInt(schema*MAX_PAGES_NUM)});
				tfoot.find(".pagination ul").append(firstLi);
			}			
			var j = 0;
			for (var i = (schema*MAX_PAGES_NUM+1); ((j < MAX_PAGES_NUM) && (i <= pageObj.tot_pages)); j++)
			{
				var cl = (i === pageObj.page)? "active" : "";
				var li = $("<li class='"+cl+"'><a href='#'>"+i+"</a></li>");
				li.data({"page": i});
				tfoot.find(".pagination ul").append(li);
				i++;
			}
			var nextPage = parseInt((schema*MAX_PAGES_NUM)+1+MAX_PAGES_NUM);
			if (nextPage <= pageObj.tot_pages)
			{
				var lastLi = $("<li><a href='#'>&raquo;</a></li>");
				lastLi.data({"page": nextPage});
				tfoot.find(".pagination ul").append(lastLi);
			}
		}
		else
		{
			for (var i = 1; i <= pageObj.tot_pages; i++)
			{
				var cl = (i === pageObj.page)? "class='active'" : "";
				var li = $("<li "+cl+"><a href='#'>"+i+"</a></li>");
				li.data({"page": i});
				tfoot.find(".pagination ul").append(li);
			}
		}
		
		tfoot.find("li").click(function()
		{			
			if ($(this).hasClass("active"))
				return;
			
			$.ajax({
				type: 'GET',
				url: urlObj.base+urlObj.fs+"&page="+$(this).data().page,
				dataType: 'json',
				success: function(data)
				{
					callback(data.data, urlObj.fs);
				},
				error: function(result)
				{
					that.showErrMsg(result); 
				}
			});
		});
		
		table.dataTable({
			"bProcessing": true,
			"bServerSide": false,
			"oLanguage": APP.utils.getDataTableLanguage(),
			"bRetrieve": true,
			"bDestroy": true,
			"bPaginate": false,
			"bInfo": false,
			"bFilter": false,
			//"iDisplayLength" : ((obj.items_per_page === obj.tot_items) && (obj.tot_items > APP.config.default_iDisplayLength))? APP.config.default_iDisplayLength : obj.items_per_page,
		});
	},
	
	getSecondaryValue: function(param)
	{
		var that = this;
		var valore = "";
		if (this.isset(param.value))
		{
			$.each(APP.config.breadCrumb, function(i, v)
			{
				if (!that.isset(v.data) || !that.isset(v.level) || v.level !== param.value.level)
					return true;
				
				valore = v.data[param.value.field];
				return false;
			});
			return valore;
		}
		if (this.isset(param.default_value))
			if (param.default_value !== "")
				valore = param.default_value;
		return valore;
	},
	
	displayData: function(data, t)
	{
		switch(t.form_input_type)
		{
			case "input":
				switch(t.data_type)
				{
					case "boolean":
						data = this.boolToString(data);
						break;
					default:
						break;
				}
				break;
			case "datebox":
				data = this.convertiData(data);
				break;
			case "datetimebox":
				data = this.convertiDateTime(data);
				break;
			case "colorpicker": case "mapbox_color":
				data = "<div style='width: 100%; height: 30px; background-color: "+data+"'></div>";
				break;
			default:
				break;	
		}
		
		var preff = (APP.utils.isset(t.prefix))? t.prefix+" " : "";
		var suff = (APP.utils.isset(t.suffix))? " "+t.suffix : "";
		data = (data.length > APP.config.maxStringsLength)? data.substr(0,APP.config.maxStringsLength-1)+"... " : data;
		return preff+data+suff;
	},
	
	convertiData: function(dateString)
	{
		if (!this.isset(dateString))
			return "";
		var dateArr = dateString.split("-");
		return (dateArr.length == 3)? dateArr[2]+"/"+dateArr[1]+"/"+dateArr[0] : dateString;
	},
	
	convertiDateTime: function(datetimeString)
	{
		if (!this.isset(datetimeString))
			return "";
		var dateArr = datetimeString.split(" ");
		if ($.isArray(dateArr) && dateArr.length === 2)
			return this.convertiData(dateArr[0])+" "+dateArr[1];
		else
			return "";
	},
	
	boolToString: function(b)
	{
		if (b === true)
			return "<h5 style='margin: 0px'><span class='label label-success'> "+APP.i18n.translate("yes").toUpperCase()+" </span></h5>";
		if (b === false)
			return "<h5 style='margin: 0px'><span class='label label-danger'> "+APP.i18n.translate("no").toUpperCase()+" </span></h5>";
		return b;
	},
	
	getDataTableLanguage:  function()
	{
		return { 
			sSearch: APP.i18n.translate("search")+": ",
			//oPaginate: {sFirst: APP.i18n.translate("first"), sLast: APP.i18n.translate("last"), sPrevious: APP.i18n.translate("previous"), sNext: APP.i18n.translate("next")},
			oPaginate: {sFirst: APP.i18n.translate("start"), sLast: APP.i18n.translate("end"), sPrevious: '<i class="icon icon-chevron-left"></i>', sNext: '<i class="icon icon-chevron-right"></i>'},
			sInfo: APP.i18n.translate("rows")+": "+APP.i18n.translate("from")+" _START_ "+APP.i18n.translate("to")+" _END_ "+APP.i18n.translate("of")+" <b>_TOTAL_</b>",
			sInfoFiltered: "",//" - "+APP.i18n.translate("filtering from")+" _MAX_",
			sInfoEmpty: "",//APP.i18n.translate("No entries to show"),
			sEmptyTable : APP.i18n.translate("empty_table"),
			sLengthMenu: APP.i18n.translate("view")+" _MENU_ "+APP.i18n.translate("rows"),
			sProcessing: APP.i18n.translate("processing"),
			sZeroRecords: APP.i18n.translate("no result"),
		};
	},
	
	setBaseStructure: function(titolo, res)
	{
		return {
			title: titolo,
			resource: res,
			columns: [],
			form: null,
			groups: [],
		};
	},
	
	toggleLoadingImage: function(bShow)
	{
		var div = $("#loadingImageDiv");
		if (bShow)
		{
			if (div.is(":visible"))
				return;
			var h = $(window).height();
			var w = $(window).width();
			var divWidth = parseInt(div.css("width"));
			div.css("top",h/2);
			div.css("left",(w/2)-divWidth);
			div.show();
		}
		else
		{
			if (!div.is(":visible"))
				return;
			div.hide();
		}
	},
	
	dateFormatter: function(date)
	{
		var y = date.getFullYear();  
		var m = date.getMonth()+1;  
		var d = date.getDate();  
		return (d<10?('0'+d):d)+'/'+(m<10?('0'+m):m)+'/'+y; 
	},
	
	dateParser: function(s)
	{
		var t = Date.parse(s);
		if (!isNaN(t))
			return new Date(t);
		return new Date();
	},
	
	trimlast: function(str){
		return str.substr(0,str.length-1);
	},
	
	stringQuery2Obj: function(query){
		var that = this;
		var myobj = {};
		var re = /([^=&]+)=?([^&]*)?/g;
		while (match = re.exec(query)) {
			var value = ('undefined' != typeof match[2])? decodeURIComponent(match[2]) : '';
			myobj[match[1]] = value.replace(/\+/g," ");
		}
		return myobj;
	},
	
	obj2StringQuery: function(o,kvsep,sep){
		var kvsep = (kvsep!=null)?kvsep:'=';
		var sep = (sep!=null)?sep:'&';
		var query = '';
		var str = "";
		for (var k in o){
			str = o[k];
			if ($.isArray(o[k]))
			{
				str = "[";
				for (var i = 0; i < o[k].length; i++)
				{
					for (var j in o[k][i])
						str += j + ":" + o[k][i][j] + ",";
					str = str.substr(0, str.length-1) + ";";
				}
				if (o[k].length > 0)
					str = str.substr(0, str.length-1);
				str += "]";
					
				//o[k] = str;
			}
			
			if ($.isPlainObject(o[k]) && !$.isEmptyObject(o[k]))
			{
				str = "{";
				
				$.each(o[k], function(i,v)
				{
					str += i + ":" + v +",";
				});
				str = str.substr(0, str.length-1) + "}";					
				//o[k] = str;
			}
			if ($.isPlainObject(o[k]) && $.isEmptyObject(o[k]))
			{
				str = "{}";
				//o[k] = str;
			}
			
			query = query+k+kvsep+str+sep;
		}
		return this.trimlast(query);
	},
	
	updateBreadcrumb: function(action, params, context, pos) // str, params, level, srcElement
	{
		//params = (!this.isset(obj.params) || $.isEmptyObject(obj.params))? {"label": obj.str} : {"label": str, "level": level, "data": params};
		var bcUl = $("ul.breadcrumb");
		switch(action)
		{
			case "add":
				APP.config.breadCrumb.push(params);
				break;
			case "addAt":
				APP.config.breadCrumb.splice(pos, 0, params);
				break;
			case "remove":
				APP.config.breadCrumb.pop();
				break;
			case "replaceLast":
				APP.config.breadCrumb[APP.config.breadCrumb.length-1] = params;
				break;
			case "rebuild":
				APP.config.breadCrumb = [params];
				break;
			case "empty":
				APP.config.breadCrumb = [];
				bcUl.empty();
				break;
			case "refresh":
				bcUl.empty();
				break;
			default:
				console.log("Aggiungi voce "+action+" in APP.utils.updateBreadcrumb");
				return;
		}		
		
		$.each(APP.config.breadCrumb, function(i, v)
		{
			var active = (i === APP.config.breadCrumb.length-1)? "class='active'" : "";
			var leftIcon = (APP.utils.isset(v.icon))? '<i class="icon icon-'+v.icon+' pull-left" style="margin-top: 2px; margin-right: 3px"></i>' : '';
			var li = null;
			if (active !== "")
				li = $('<li '+active+'>'+leftIcon+v.label+'</li>');
			else
				li = $('<li><a href="#">'+leftIcon+v.label+'</a></li>');
			//v.section = $.extend({}, APP.config.currentConfigSection);
			v.window = i+1;
			li.data(v).click(function()
			{
				var selectedLi = $(this);
				var fromW = parseInt(APP.config.breadCrumb.length);
				var toW = parseInt(selectedLi.data().window);
				var diff = fromW-toW;
				if (isNaN(diff))
					return;
				
				for (var ii = 0; ii < diff; ii++)
				{
					if (APP.config.serverSide)
						APP.anagrafica_ss.destroyWindow();
					else
						APP.anagrafica.destroyWindow();
				}
			});
			if (i === 0)
				bcUl.html(li);
			else
				bcUl.append(li);
		});
	},

	getIndexFromField: function(array, field, value)
	{
		var j = -1;
		$.each(array,function(i,v)
		{
			if (v[field] === value)
			{
				j = i;
				return false;
			}
		});
		return j;
	},
	
	amIAdmin: function()
	{
		var mri = parseInt(APP.config.localConfig.authuser.main_role_id);
		var index = this.getIndexFromField(APP.config.localConfig.role, "id", mri);
		return (APP.i18n.translate(APP.config.localConfig.role[index]).name === APP.i18n.translate("admin_role"))? true : false;
	}
});