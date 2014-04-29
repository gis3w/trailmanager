$.extend(APP.fileuploader,{
	start: function()
	{
		this.urls = {};
		this.bMultiple = true;
		this.validationOptions = {};
		this.myFiles = [];
		this.inputName = null;
	},
	
	displayTagFromFilename: function(fileObj)
	{
		var that = this;
		var thumbnail = "";
		
		var tipo = null;
		if (!fileObj.type)
		{
			/*
			if (!APP.utils.isset(source))
			{
				var index = APP.utils.getIndexFromField(that.myFiles, "name", filename);
				source = that.myFiles[index].url;
			}
				
			var imageExtensions = ['jpg', 'jpeg', 'gif', 'png'];
			var exts = filename.split(".");
			var extension = exts[exts.length-1].toLowerCase();
			if ($.inArray(extension, imageExtensions) !== -1)
				tipo = "image";
			*/
			return '<i class="icon icon-file-alt icon-large"></i>';
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
	
	getTrString: function(fileObj)
	{
		var that = this;
		
		var thumbnail = that.displayTagFromFilename(fileObj);
			
		var downUrl = this.urls.download;
		if (!APP.utils.isset(fileObj.url))
		{
			$.each(this.urls.download_options, function(i, v)
			{
				downUrl = APP.utils.replaceAll(i, fileObj[v], downUrl);
			});
		}
		else
			downUrl = fileObj.url;
			
		var filename = $('<span class="text-info fileDownloadSimpleRichExperience" style="cursor: pointer">'+fileObj[this.inputName]+'</span>');
		filename.click(function(){
			$.fileDownload(downUrl)
			.fail(function () {
				APP.utils.showNoty({title: APP.i18n.translate("error"), type: "error", content: APP.i18n.translate("download_failure")});
			});
		});
		
		var display = ($.inArray("delete", this.capabilities) > -1)? "" : "display:none";
		
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
		tr.find(".filenameContainer").html(filename);
				
		return tr;
	},
	
	onFileAdd: function(data)
	{
		var table = $("#fileToUploadTable");
		
		if (!data.hasOwnProperty(this.inputName))
			data[this.inputName] = data.name;
		data.stato = "I";
		
		this.myFiles.push(data);
		
		var str = this.getTrString(data);
		
		str.find("button.cancel").click(function(){
			APP.fileuploader.onFileRemove($(this));
		});
		if ($.inArray("delete", this.capabilities) === -1)
			str.find("button.cancel").hide();
		
		var tbody = table.find("tbody");
		tbody.append(str);
		
		if (!this.bMultiple)
		{
			$(".fileinput-button").addClass("disabled");
			$(".fileinput-button").find("input").attr("disabled", true);
		}
		
		table.find(".tooltipElement").tooltip();
		
		if (!table.is(":visible"))
			table.show();
	},

	onFileRemove: function(btn)
	{
		var that = this;
		var tr = btn.parents("tr").first();
		var table = tr.parents("table").first();
		var filename = tr.find(".filenameContainer").text();
		
		var onFileRemoved = function()
		{
			tr.remove();
			if (table.find("tr").length === 1)
			{
				table.hide();
				$(".fileinput-button").find("input").removeAttr("disabled");
				$(".fileinput-button").removeClass("disabled");
			}
		};
		
		var index = APP.utils.getIndexFromField(this.myFiles, this.inputName, filename);
		if (index == -1)
			return;
		
		if (!APP.utils.isset(this.myFiles[index].id))
		{
			if (APP.utils.isset(this.myFiles[index].delete_url) && APP.utils.isset(this.myFiles[index].delete_type))
			{
				$.ajax({
					type: this.myFiles[index].delete_type,
					url: this.myFiles[index].delete_url,
					dataType: 'json',
					success: function(data)
					{
						if (!APP.utils.checkError(data.error, null))
						{
							that.myFiles.splice(index,1);
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
				this.myFiles[index].stato = "D";
				onFileRemoved();
			}
		}
		else
		{
			this.myFiles[index].stato = "D";
			onFileRemoved();
		}
	},
	
	getString: function(params) // name, value["file1", "file2"], options, urls, multiple
	{
		this.start();
		var that = this;
		this.inputName = params.name;
		this.validationOptions = params.options;
		this.urls = params.urls;
		this.bMultiple = params.multiple;
		this.capabilities = params.capabilities;
		var mulAr = (this.bMultiple === true)? ["[]", "multiple"] : ["",""];
		var obj = params.v;
		
		var tableDiv = $(	'<div class="row">\
								<div class="table-responsive col-md-12" style="margin: 5px 0px 0px 0px">\
									<table style="display: none;" id="fileToUploadTable" class="table table-striped table-hover">\
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
			if ($.isArray(v[that.inputName]))
			{
				$.each(v[that.inputName], function(ii,vv)
				{
					if (vv === "")
						return true;
					var o = {};
					$.extend(o, v);
					o[that.inputName] = vv;
					that.myFiles.push(o);
					tbody.append(that.getTrString(o));
				});
			}
			else
			{
				that.myFiles.push(v);
				tbody.append(that.getTrString(v));
			}
		});
		var disClass = (that.myFiles.length>0 && !this.bMultiple)? "disabled": "";
		
		var div = $("<div></div>");
		
		div.append('<div class="row" style="height: 40px">\
						<div class="col-md-5">\
							<span id="APP-'+this.inputName+'" class="btn btn-warning fileinput-button '+disClass+'">\
								<span><i class="icon icon-search"></i> '+APP.i18n.translate("select")+'</i></span>\
								<input type="file" class="fileupload" name="'+this.inputName+'[]" '+mulAr[1]+' '+disClass+' >\
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
		
		var obj = {
			url: this.urls.data,
			dataType: 'json',
			add: function (e, data) {
				form.find('#progress').show();
				APP.utils.toggleLoadingImage(false);
				APP.utils.resetFormErrors(form);
				var jqXHR = data.submit()
				.success(function (result, textStatus, jqXHR) {
					if (!APP.utils.checkError(result.error, form))
						that.onFileAdd(result.data[that.inputName][0]);
					else
						APP.utils.showErrMsg(result);
					
				})
				.error(function (jqXHR, textStatus, errorThrown) {
					var t;
				})
				.complete(function (result, textStatus, jqXHR) {
					setTimeout(function(){
						$('#progress').fadeOut(1000, function(){
							$('#progress .progress-bar').css('width',0);
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
				$('#progress .progress-bar').css('width',progress + '%');
				
				//APP.utils.toggleLoadingImage(true);
			},
			previewMaxWidth: 100,
			previewMaxHeight: 100,
			previewCrop: true
		};
		if (!$.isEmptyObject(this.validationOptions))
		{
			$.extend(obj, this.validationOptions);
		}
		var inps = form.find("input.fileupload");
		inps.fileupload(obj);
		form.find("button.fileupload.cancel").click(function(){ APP.fileuploader.onFileRemove($(this)); });
		form.find(".tooltipElement").tooltip();
	},
	
	preserialize: function(form)
	{
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
	}
});