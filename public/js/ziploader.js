$.extend(APP.ziploader, 
{
	form: undefined,
	filename: "zipfile",
	hSettings: [3,9],
	
	createForm: function(div)
	{
		this.form = $(	'<form class="form-horizontal">\
							<div class="form-group">\
								<label for="APP-path_name" class="col-sm-'+this.hSettings[0]+' control-label">Nome sentiero</label>\
								<div class="controls col-sm-'+this.hSettings[1]+'">\
									<input type="text" class="form-control" id="APP-path_name" name="path_name" placeholder="Nome sentiero">\
								</div>\
							</div>\
							<div class="form-group">\
								<label for="APP-itinerary" class="col-sm-'+this.hSettings[0]+' control-label">Circuito escursionistico</label>\
								<div class="controls col-sm-'+this.hSettings[1]+'">\
									<select class="form-control" id="APP-itinerary" name="itinerary"></select>\
								</div>\
							</div>\
							<div class="form-group">\
								<label for="APP-'+this.filename+'" class="col-sm-'+this.hSettings[0]+' control-label">File</label>\
								<div class="controls col-sm-'+this.hSettings[1]+' '+this.filename+'">\
								</div>\
							</div>\
						</form>');

		div.append(this.form);
	},
	
	createFileuploader: function()
	{
		var file = APP.fileuploader.getString({"name": this.filename, "value": [], "multiple": false, "urls": {data: '/jx/admin/upload/pathzipfile'} });
		this.form.find("."+this.filename).append(file);
	},
	
	createSendButton: function()
	{
		var fg = $('<div class="form-group">\
						<label for="sendBtn" class="col-sm-'+this.hSettings[0]+' control-label"></label>\
						<div class="controls col-sm-'+this.hSettings[1]+'">\
							<button type="button" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Invia</button>\
						</div>\
					</div>');
		
		fg.find("button").click(function(){
			
		});
		
		this.form.append(fg);
	},
	
	getItineraries: function(callback)
	{
		var self = this;
		
		$.ajax({
			type: 'GET',
			url: '/jx/admin/itinerary',
			dataType: 'json',
			success: function(result)
			{
				var select = self.form.find("#APP-itinerary");
				$.each(result.data.items, function(i,v)
				{
					select.append('<option value="'+v.id+'">'+v.name+'</option>');
				});
				if ($.isFunction(callback)){
					callback();
				}
			}
		});
	},
	
	start: function(button, label, section, div)
	{
		var self = this;
		
		div.empty();
		
		this.createForm(div);
		this.createFileuploader();
		//this.createSendButton();
		this.getItineraries(function(){
			APP.utils.setLookForm(self.form);
		});
	}
});