$.extend(APP.modals,{
	allModals: {},
	
	create: function(o)
	{
		var that = this;
		
		var myModal = null;
		if (o.container && o.id)
			myModal = o.container.find("#"+o.id);
		if (myModal.length > 0)
			myModal.remove();
		var fade = (!APP.utils.isset(o.bFade) || o.bFade)? "fade" :"";
			
		myModal = $('<div class="modal '+fade+'">\
						<div class="modal-dialog">\
							<div class="modal-content">\
								<div class="modal-header">\
									<h3></h3>\
								</div>\
								<div class="modal-body"></div>\
								<div class="modal-footer"></div>\
							</div>\
						</div>\
					</div>');
		if (!APP.utils.isset(o.id))
			return false;
		myModal.attr("id", o.id);
		
		if (o.size)
			myModal.find(".modal-dialog").removeClass().addClass("modal-dialog modal-"+o.size);
		if (APP.utils.isset(o.backdrop))
			myModal.attr("data-backdrop", o.backdrop);
		if (APP.utils.isset(o.keyboard))
			myModal.attr("data-keyboard", o.keyboard);
		if (APP.utils.isset(o.bTopCloseButton) && o.bTopCloseButton)
			myModal.find(".modal-header").prepend('<button type="button" class="btn-lg close" data-dismiss="modal" aria-hidden="true"><span aria-hidden="true">&times;</span></span></button>');
		if (o.header)
			myModal.find(".modal-header h3").html(o.header);
		if (o.body)
			myModal.find(".modal-body").html(o.body);
		if (o.footer)
			myModal.find(".modal-footer").html(o.footer);
		else
			myModal.find(".modal-footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">'+APP.i18n.translate('close')+'</button>');
		
		myModal.on('show.bs.modal', function()
		{
			that.onShow(o.id, o.onShow);
		});
		myModal.on('hide.bs.modal', function()
		{
			that.onHide(o.id, o.onHide);
		});
		myModal.on('shown.bs.modal', function()
		{
			that.onShown(o.id, o.onShown);
		});
		myModal.on('hidden.bs.modal', function()
		{
			that.onHidden(o.id, o.onHidden);
		});
		
		if (o.container)
			o.container.append(myModal);
		
		that.allModals[o.id] = {mdl: myModal, opt: o};
		
		return that.allModals[o.id].mdl;
	},
	
	remove: function(arrIds) // es. ['registrationModal', 'loginModal', ...]
	{ 
		var that = this;
		
		if (!APP.utils.isset(arrIds))
			return false;
		$.each(arrIds, function(i,v)
		{
			if (APP.utils.isset(that.allModals[v]))
			{
				that.allModals[v].mdl.modal("hide");
				that.allModals[v].mdl.remove();
				delete that.allModals[v];
			}
		});
		return true;
	},
	
	// events
	onShow: function(id, callback)
	{
		var that = this;
		$.each(that.allModals, function(i,v)
		{
			if (i != id)
			{
				that.allModals[i].mdl.removeClass("fade").modal("hide");
				if (!APP.utils.isset(that.allModals[i].opt.bFade) || that.allModals[i].opt.bFade)
					that.allModals[i].mdl.addClass("fade");
			}
		});
		if (APP.utils.isset(callback) && $.isFunction(callback))
			callback();
	},
	
	onShown: function(id, callback)
	{
		if (APP.utils.isset(callback) && $.isFunction(callback))
			callback();
	},
	
	onHide: function(id, callback)
	{
		if (APP.utils.isset(callback) && $.isFunction(callback))
			callback();
	},
	
	onHidden: function(id, callback)
	{
		if (APP.utils.isset(callback) && $.isFunction(callback))
			callback();
	}
});