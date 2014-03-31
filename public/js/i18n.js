$.extend(APP.i18n,
{
	locale: {},
	
	setLocale: function(data)
	{	
		if (!APP.utils.isset(data.data))
		{
			APP.utils.showErrMsg(data);
			return;
		}
		APP.i18n.locale = data.data;
	},
	
	loadLocale: function(language)
	{
		$.ajax({
			type: 'GET',
			async: false,
			url: APP.config.localConfig.urls.i18n+language,
			success: APP.i18n.setLocale,
			error: APP.utils.showErrMsg
		});
	},
	
	translate: function(key)
	{
		return (this.locale.hasOwnProperty(key))? this.locale[key] : key;
	}
});