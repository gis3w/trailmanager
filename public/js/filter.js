$.extend(APP.filter,{

	filterDialogs: {},	// dlg, appliedForm
	
	hasActiveFilter: function(section)
	{
		if (this.filterDialogs.hasOwnProperty(section) && this.filterDialogs[section].filterToggle)
			return true;
		return false;
	},
	
	getActiveFilterString: function(section)
	{
		var str = "";
		if (!this.filterDialogs.hasOwnProperty(section) || !APP.utils.isset(this.filterDialogs[section].appliedForm))
			return str;
		
		$.each(this.filterDialogs[section].appliedForm, function(i,v){
			str += v.name+":"+v.value+",";
		});
		if (str.length > 0)
			str = str.substr(0, str.length-1);
		
		var filterString = (str.length > 0)? "?filter="+str : "";
		return filterString;
	},

	init: function(btn, section, cb)
	{	
		var that = this;
		
		btn.off('click').click(function(){
			that.openFilterPane(section);
		});
		
		if (that.filterDialogs.hasOwnProperty(section))
			return;
		that.filterDialogs[section] = {};
			
		that.filterDialogs[section].dlg = $('<div id="filterDialog_'+section+'" class="modal fade" role="dialog">\
												<div class="modal-dialog">\
													<div class="modal-content">\
														<div class="modal-header">\
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><strong>&times;</strong></button>\
															<h4 class="modal-title">'+APP.i18n.translate("search")+'</h4>\
														</div>\
														<div class="modal-body"></div>\
														<div class="modal-footer panel-footer">\
															<button type="button" id="filterButton" class="btn btn-success"><i class="icon icon-ok"></i> '+APP.i18n.translate("apply")+'</button>\
															<button type="button" id="removeFilterButton" class="btn btn-primary"><i class="icon icon-eraser"></i> '+APP.i18n.translate("reset")+'</button>\
															<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">'+APP.i18n.translate("close")+'</button>\
														</div>\
													</div>\
												</div>\
											</div>');
		
		that.filterDialogs[section].dlg.find("#filterButton").click(function()
		{
			var f = that.filterDialogs[section].dlg.find('.modal-body form');
			that.filterDialogs[section].appliedForm = f.serializeArray();
			var dirty = false;
			$.each(that.filterDialogs[section].appliedForm, function(i,v)
			{
				if (v.value !== "")
				{
					dirty = true;
					return false;
				}
			});
			
			that.filterDialogs[section].filterToggle = dirty;
			
			that.applyFilter(
			{
				form: f,
				baseUrl: APP.config.localConfig.urls[section],
				callback: function(result, fs)
				{
					if (!APP.utils.checkError(result.error, f))
					{
						that.filterDialogs[section].dlg.modal("hide");
						cb(result);
					}
					else
						APP.utils.showErrMsg(result);
				}
			});
			
		});
		that.filterDialogs[section].dlg.find("#removeFilterButton").first().click(function(e)
		{ 
			var f = that.filterDialogs[section].dlg.find('.modal-body form');
			APP.utils.resetFormErrors(f);
			f[0].reset();
			f.find(".chosen").trigger("chosen:updated");
			
			that.filterDialogs[section].appliedForm = f.serializeArray();
			that.filterDialogs[section].filterToggle = false;
			
			//$(this).off('click');
			
			that.applyFilter(
			{
				form: f,
				baseUrl: APP.config.localConfig.urls[section],
				callback: function(result, fs)
				{
					if (!APP.utils.checkError(result.error, f))
					{
						//that.filterDialogs[section].dlg.modal("hide");
						//that.filterDialogs[section].dlg.find(".modal-body").empty();
						cb(result);
					}
					else
						APP.utils.showErrMsg(result);
				}
			});
		});
		
		that.filterDialogs[section].dlg.modal({
			show: false
		});
		
		$("#filterDialogsDiv").append(that.filterDialogs[section].dlg);
	},
	
	openFilterPane: function(section)
	{
		var that = this;
		
		if (this.filterDialogs[section].dlg.find(".modal-body").children().length === 0)
		{
			$.ajax({
				type: 'GET',
				url: APP.config.localConfig.urls.filter+section,
				success: function(result)
				{
					if (result.status)
					{
						that.createFilterForm(result, section);
						var f = that.filterDialogs[section].dlg.find("form").first();
						that.filterDialogs[section].dlg.on('shown.bs.modal', function(){
							APP.utils.setLookForm(f, null);
							that.filterDialogs[section].dlg.off('shown.bs.modal');
						});
						that.filterDialogs[section].dlg.modal('show');
						
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
			APP.utils.resetFormErrors(this.filterDialogs[section].dlg.find(".modal-body form"));	
			
			var f = this.filterDialogs[section].dlg.find(".modal-body form");
			f[0].reset();
			f.find(".chosen").trigger("chosen:updated");
			
			if (APP.utils.isset(that.filterDialogs[section].appliedForm))
			{
				$.each(that.filterDialogs[section].appliedForm, function(i,v){
					var el = f.find("#APP-"+v.name);
					el.val(v.value);
					if (el.hasClass("chosen"))
						el.trigger("chosen:updated");			
				});
			}
			
			that.filterDialogs[section].dlg.modal('show');
		}
	},
	
	createFilterForm: function(data, section)
	{
		var that = this;
		var f = that.getFilterForm(data.data);
		var formDiv = that.filterDialogs[section].dlg.find(".modal-body");
		formDiv.html(f);
	},
	
	getFilterForm: function(data, formId)
	{
		var that = this;
		var f = $("<form class='form-horizontal' style='padding-top: 10px'></form>");
		
		if (APP.utils.isset(data))
		{
			$.each(data, function(i, v)
			{
				var fg = $("<div class='form-group'>\
							<label class='control-label col-md-4' for='APP-"+i+"'><small>"+v.label+":</small></label>\
							<div class='controls col-md-8'></div>\
						</div>");
				
				var inp = $(APP.utils.getFormField(i,v));
				fg.find(".controls").append(inp);
				f.append(fg);
			});
		}
		
		f.find(":input").addClass("input-sm");
		
		return f;
	},
	
	getFilterString: function(form)
	{
		var dataStr = form.serialize();
		var dataObj = APP.utils.stringQuery2Obj(dataStr);
		return APP.utils.obj2StringQuery(dataObj,':',',');
	},	
	
	applyFilter: function(obj)
	{
		var that = this;
		
		APP.utils.resetFormErrors(obj.form);
		var fs = this.getFilterString(obj.form);
		var filterString = APP.utils.isset(fs)? "?filter="+fs : "";
		$.ajax({
			type: 'GET',
			url: obj.baseUrl+filterString,
			success: function(result)
			{
				if (!APP.utils.checkError(result.error, obj.form))
					obj.callback(result, filterString);
				else
					APP.utils.showErrMsg(result);
			},
			error: function(result)
			{
				APP.utils.showErrMsg(result);
			}
		});
	},
});