$.extend(APP.fileuploader,{
	fileRows: {},
	
	finish: function()
	{
		this.fileRows = {};
	},
	
	displayTagFromFilename: function(name, fileObj)
	{
		var that = this;
		var thumbnail = "";
		
		var tipo = null;
		if (!fileObj.type)
		{
            if (APP.utils.isImageFile(fileObj[name]))
			{
				var tu = APP.utils.getThumbnailUrl(that.fileRows[name].urls, fileObj);
				if (tu)
					return '<img src="'+tu+'" alt="">';
				return '<i class="icon icon-file-alt"></i>' + fileObj[name];
			}
			return '<i class="icon icon-file-alt"></i>' + fileObj[name];
		}
		else
			tipo = fileObj.type.split("/")[0];
					
		switch(tipo)
		{
			case "image":
				thumbnail = '<img src="'+fileObj.thumbnail_url+'" alt=""/>';
				break;
			default:
				thumbnail = '<i class="icon icon-file-alt icon-large"></i>';
				break;
		}
		
		return thumbnail;
	},
	
	getTrString: function(name, fileObj)
	{
		var that = this;
		
		var thumbnail = that.displayTagFromFilename(name, fileObj);
			
		var downUrl = that.fileRows[name].urls.download;
		if (!APP.utils.isset(fileObj.url))
		{
			$.each(that.fileRows[name].urls.download_options, function(i, v)
			{
				downUrl = APP.utils.replaceAll(i, fileObj[v], downUrl);
			});
		}
		else
			downUrl = fileObj.url;
			
		var filename = $('<span class="text-info fileDownloadSimpleRichExperience" style="cursor: pointer">'+fileObj[that.fileRows[name].inputName]+'</span>');
		filename.click(function(){
			$.fileDownload(downUrl)
			.fail(function () {
				APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("download_failure")});
			});
		});
		
		var display = ($.inArray("delete", that.fileRows[name].capabilities) > -1)? "" : "display:none";
		
		var tr = $('<tr>\
					<td style="vertical-align: middle">\
						<span class="pull-left">\
						'+thumbnail+'\
						</span>\
						<span class="filenameContainer" style="padding-left: 3px"></span>\
						<span class="pull-right">\
							<button type="button" class="close fileupload cancel tooltipElement" style="'+display+'" data-toggle="tooltip" title="'+APP.i18n.translate('remove')+'" aria-hidden="true">&times;</button>\
						</span>\
					</td>\
				</tr>');
		
		tr.data({inputName: name});
		tr.find(".filenameContainer").html(filename);
				
		return tr;
	},
	
	onFileAdd: function(form, inputName, data)
	{
		var that = this;
		var table = form.find("#APP-"+inputName).parents(".controls:first").find(".fileToUploadTable");
		
		if (!data.hasOwnProperty(inputName))
			data[inputName] = data.name;
		data.stato = "I";
		
		that.fileRows[inputName].myFiles.push(data);
		
		var str = this.getTrString(inputName, data);
		
		str.find("button.cancel").click(function(){
			APP.fileuploader.onFileRemove(form, $(this));
		});
		if ($.inArray("delete", that.fileRows[inputName].capabilities) === -1)
			str.find("button.cancel").hide();
		
		var tbody = table.find("tbody");
		tbody.append(str);
		
		if (!that.fileRows[inputName].bMultiple)
		{
			form.find("#APP-"+inputName).parents(".controls:first").find(".fileinput-button").addClass("disabled");
			form.find("#APP-"+inputName).parents(".controls:first").find(".fileinput-button").find("input").attr("disabled", true);
		}
		
		table.find(".tooltipElement").tooltip();
		
		if (!table.is(":visible"))
			table.show();
	},

	onFileRemove: function(form, btn)
	{
		var that = this;
		var tr = btn.parents("tr").first();
		var inputName = tr.data().inputName;
		var table = tr.parents("table").first();
		var filename = tr.find(".filenameContainer").text();
		
		var onFileRemoved = function()
		{
			tr.remove();
			if (table.find("tr").length === 1)
			{
				table.hide();
				form.find("#APP-"+inputName).parents(".row:first").find(".fileinput-button").find("input").removeAttr("disabled");
				form.find("#APP-"+inputName).parents(".row:first").find(".fileinput-button").removeClass("disabled");
			}
		};
		
		var index = APP.utils.getIndexFromField(that.fileRows[inputName].myFiles, inputName, filename);
		if (index == -1)
			return;
		
		if (!APP.utils.isset(that.fileRows[inputName].myFiles[index].id))
		{
			if (APP.utils.isset(that.fileRows[inputName].myFiles[index].delete_url) && APP.utils.isset(that.fileRows[inputName].myFiles[index].delete_type))
			{
				$.ajax({
					type: that.fileRows[inputName].myFiles[index].delete_type,
					url: that.fileRows[inputName].myFiles[index].delete_url,
					dataType: 'json',
					success: function(data)
					{
						if (!APP.utils.checkError(data.error, null))
						{
							that.fileRows[inputName].myFiles.splice(index,1);
							onFileRemoved();
						}
						else
							that.showErrMsg(data);
					},
					error: function(result)
					{ 
						APP.utils.showErrMsg(result, false); 
					}
				});
			}
			else
			{
				that.fileRows[inputName].myFiles[index].stato = "D";
				onFileRemoved();
			}
		}
		else
		{
			that.fileRows[inputName].myFiles[index].stato = "D";
			onFileRemoved();
		}
	},
	
	getString: function(params) // name, value["file1", "file2"], options, urls, multiple
	{
		var that = this;
		that.fileRows[params.name] = {};
		that.fileRows[params.name].inputName = params.name;
		that.fileRows[params.name].urls = params.urls;
		that.fileRows[params.name].bMultiple = params.multiple;
		that.fileRows[params.name].validationOptions = params.options;
		that.fileRows[params.name].myFiles = [];
		that.fileRows[params.name].capabilities = params.capabilities;
		var mulAr = (that.fileRows[params.name].bMultiple === true)? ["[]", "multiple"] : ["",""];
		var obj = params.v;
		
		var tableDiv = $(	'<div class="row">\
								<div class="table-responsive col-md-12" style="margin: 5px 0px 0px 0px">\
									<table style="display: none;" class="table table-striped table-hover fileToUploadTable">\
										<thead style="display: none">\
											<tr style=""><th colspan="1"></th></tr>\
										</thead>\
										<tbody class="files"></tbody>\
									</table>\
								</div>\
							</div>');
		
		var tbody = tableDiv.find("tbody");		
		
		$.each(params.value, function(i,v)
		{
			if (i == 0)
				tableDiv.find("table").show();
			if (!APP.utils.isset(v[that.fileRows[params.name].inputName]))
				return true;
			if ($.isArray(v[that.fileRows[params.name].inputName]))
			{
				$.each(v[that.fileRows[params.name].inputName], function(ii,vv)
				{
					if (vv === "")
						return true;
					var o = {};
					$.extend(o, v);
					o[that.fileRows[params.name].inputName] = vv;
					that.fileRows[params.name].myFiles.push(o);
					tbody.append(that.getTrString(params.name, o));
				});
			}
			else
			{
				that.fileRows[params.name].myFiles.push(v);
				tbody.append(that.getTrString(params.name, v));
			}
		});
		var disClass = (that.fileRows[params.name].myFiles.length>0 && !that.fileRows[params.name].bMultiple)? "disabled": "";
		
		var div = $("<div></div>");
		
		div.append('<div class="row" style="height: 40px">\
						<div class="col-md-5">\
							<span id="APP-'+that.fileRows[params.name].inputName+'" class="btn btn-warning fileinput-button '+disClass+'">\
								<span><i class="icon icon-search"></i> '+APP.i18n.translate("select")+'</i></span>\
								<input type="file" class="fileupload" name="'+that.fileRows[params.name].inputName+'[]" '+mulAr[1]+' '+disClass+' >\
							</span>\
						</div>\
						<div class="col-md-7">\
							<div id="progress" class="progress progress-striped" style="display: none">\
								<div class="progress-bar progress-bar-success"></div>\
							</div>\
						</div>\
					</div>');
					
		div.append(tableDiv);
		
		return div;
	},
	
	init: function(form)
	{
		var that = this;
		
		$.each(that.fileRows, function(key, value)
		{
			var chiave = key;
			var obj = {
				url: this.urls.data,
				dataType: 'json',
				add: function (e, data) {
					form.find("#APP-"+chiave).parents(".row:first").find('#progress').show();
					APP.utils.toggleLoadingImage(false);
					APP.utils.resetFormErrors(form);
					var jqXHR = data.submit()
					.success(function (result, textStatus, jqXHR) {
						if (!APP.utils.checkError(result.error, form))
							that.onFileAdd(form, chiave, result.data[chiave][0]);
						else
							APP.utils.showErrMsg(result);
						
					})
					.error(function (jqXHR, textStatus, errorThrown) {
						var t;
					})
					.complete(function (result, textStatus, jqXHR) {
						setTimeout(function(){
							form.find("#APP-"+chiave).parents(".row:first").find('#progress').fadeOut(1000, function(){
								form.find("#APP-"+chiave).parents(".row:first").find('#progress .progress-bar').css('width',0);
							});
						},1000);
					});
				},
				done: function (e, data) {
					
				},
				always: function(e, data) {
					
				},
				progressall: function (e, data) {
					var progress = parseInt(data.loaded / data.total * 100, 10);
					form.find("#APP-"+chiave).parents(".row:first").find('#progress .progress-bar').css('width',progress + '%');
					
					//APP.utils.toggleLoadingImage(true);
				},
				previewMaxWidth: 100,
				previewMaxHeight: 100,
				previewCrop: true
			};
			if (APP.utils.isset(this.validationOptions) && $.isPlainObject(this.validationOptions) && !$.isEmptyObject(this.validationOptions))
			{
				$.extend(obj, this.validationOptions);
			}
			var inp = form.find('input.fileupload[name="'+this.inputName+'[]"]');
			inp.fileupload(obj);
		});
		
		form.find("button.fileupload.cancel").click(function(){ APP.fileuploader.onFileRemove(form, $(this)); });
		form.find(".tooltipElement").tooltip();
	},
	
	preserialize: function(inputName)
	{
		var that = this;
		inputName = inputName.split("[]")[0];
		var str = "";
		$.each(that.fileRows[inputName].myFiles, function()
		{
			str += $.param(this)+";";
		});
		str = str.substr(0, str.length-1);
		return {'name': inputName, 'value': str};
	
	
		/*
		var that = this;
		//form.find(".fileupload").remove(); // tolgo l'input di default di Fileupload senza del quale il plugin non avrebbe potuto funzionare fino ad ora
		var filesToSend = [];
		$.each(this.myFiles, function(i,v)
		{
			var obj = {'name': v[that.inputName]};
			obj.stato = (APP.utils.isset(v.stato))? v.stato : "U";
			filesToSend.push(obj);
		});
		var str = JSON.stringify(filesToSend);
		return {"name": this.inputName, "value": str};
		*/
	}
});