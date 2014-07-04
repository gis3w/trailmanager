// LEAFLET

$.extend(APP.map,
{
	globalData: {},
	currentMapId: null,
	
	cautionIcon: L.icon({
		iconUrl: '/public/img/map/caution.png',
		iconAnchor: [16, 37],
	}),
	
	startIcon: L.icon({
		iconUrl: '/public/img/map/start.png',
		iconAnchor: [16, 37],
	}),
	
	stopIcon: L.icon({
		iconUrl: '/public/img/map/finish.png',
		iconAnchor: [16, 37],
	}),
	
	selectedMarker: L.icon({
		iconUrl: '/public/img/map/selected_marker.png',
		iconAnchor: [12, 41]
	}),
	
	unselectedMarker: new L.Icon.Default,
	
	isset: function(o)
	{
		if(typeof o == 'undefined')
			return false
		else
			if(o==null)
				return false;
		return true;
	},
	
	finish: function()
	{
		$.each(this.globalData, function(i,v){
			v.map.remove();
		});
		this.globalData = {};
		this.currentMapId = null;
	},
	
	resizeMap: function()
	{
		if (!this.isset(this.currentMapId) || ( this.currentMapId.indexOf("mapboxDiv") === 0))
			return;
		var div = $(this.globalData[this.currentMapId].map.getContainer());
		if (div.length > 0)
			$(div[0]).height(div.parent().height()-120);
		
		this.globalData[this.currentMapId].map.invalidateSize(true);
	},
	
	setGlobalExtent: function(extent)
	{
		var that = this;
		if (!APP.utils.isset(extent))
			return;
			
		if (!APP.utils.isset(APP.config.localConfig.default_extent))
		{
			alert("Walter dovresti inserire la voce default_extent nel config");
			return;
		}
		
		var value;
		if (APP.utils.isset(extent.maxx))
		{
			value = parseFloat(extent.maxx);
			if (!this.globalData[1].globalExtent.hasOwnProperty("maxx") || this.globalExtent.maxx < value)
				this.globalExtent.maxx = value;
		}
		
		if (APP.utils.isset(extent.maxy))
		{
			value = parseFloat(extent.maxy);
			if (!this.globalExtent.hasOwnProperty("maxy") || this.globalExtent.maxy < value)
				this.globalExtent.maxy = value;
		}
		
		if (APP.utils.isset(extent.minx))
		{
			value = parseFloat(extent.minx);
			if (!this.globalExtent.hasOwnProperty("minx") || this.globalExtent.minx > value)
				this.globalExtent.minx = value;
		}
		
		if (APP.utils.isset(extent.miny))
		{
			value = parseFloat(extent.miny);
			if (!this.globalExtent.hasOwnProperty("miny") || this.globalExtent.miny > value)
				this.globalExtent.miny = value;
		}
	},
	
	preserialize: function(name)
	{
		var value = "";

		if (APP.utils.isset(this.globalData[this.currentMapId].drawnItems) && this.globalData[this.currentMapId].drawnItems.getLayers().length>0)
		{
			var gj = this.globalData[this.currentMapId].drawnItems.toGeoJSON();
			value = JSON.stringify(gj);
		}
		
		return {"name": name, "value": value};
	},
	
	setMapControls: function()
	{
		if (APP.config.localConfig.menu)
			this.addFullScreenControl();
	},
	
	setExtent: function(extent)
	{
		if (!this.isset(extent) || $.isEmptyObject(extent) || !this.isset(this.currentMapId))
			return;
			
		this.globalData[this.currentMapId].map.fitBounds([
			[parseFloat(extent.miny), parseFloat(extent.minx)],
			[parseFloat(extent.maxy), parseFloat(extent.maxx)]
		]);
	},
	
	removeAllLayers: function()
	{
		var that = this;
		var ls = that.globalData[that.currentMapId].map.removeLayer();
	},
	
	changeColors: function(newColor)
	{
		var that = this;
		if (that.globalData && that.globalData[that.currentMapId] && that.globalData[that.currentMapId].map)
		{
			var ls = that.globalData[that.currentMapId].map._layers;
			$.each(ls, function(){
				if (this.setStyle)
					this.setStyle({color: newColor})
			});
			$.each(L.drawLocal.draw.handlers, function(i,v){
				var obj = {};
				obj[i] = {
					shapeOptions: {
						color: newColor
					}
				}
				that.globalData[that.currentMapId].drawControl.setDrawingOptions(obj);
			});
		}
	},
	
	toggleDrawEditor: function(mapId, b, options)
	{
		var that = this;
		
		if (b)
		{
			if (!APP.utils.isset(options))
			{
				this.globalData[mapId].drawnItems = new L.FeatureGroup();
				this.globalData[mapId].map.addLayer(this.globalData[mapId].drawnItems);
			
				options = {
					edit: {
						featureGroup: that.globalData[mapId].drawnItems,
						edit: false
					}
				};
			}	
			
			this.globalData[mapId].drawControl = new L.Control.Draw(options);			
			this.globalData[mapId].map.addControl(this.globalData[mapId].drawControl);
		}
		else
		{
			if (!APP.utils.isset(this.globalData[mapId].drawControl))
				return;
			this.globalData[mapId].map.removeControl(this.globalData[mapId].drawControl); 
			this.globalData[mapId].drawControl = null;
			if (this.globalData[mapId].map.hasLayer(this.globalData[mapId].drawnItems))
				this.globalData[mapId].map.removeLayer(this.globalData[mapId].drawnItems);
			this.globalData[mapId].drawnItems = null;
		}
	},
	
	destroyMap: function(id)
	{
		if (APP.utils.isset(this.globalData[id]) && APP.utils.isset(this.globalData[id].map))
		{
			this.globalData[id].map.remove();
			delete this.globalData[id];
			this.currentMapId = null;
		}
	},
	
	changeMap: function(newMapId)
	{
		this.currentMapId = (APP.utils.isset(this.globalData[newMapId]))? newMapId : null;
	},
	
	updateLayerGroups: function(groupId, mapId, items)
	{
		var that = this;
		var ls = that.globalData[mapId].layerGroups;
		var map = that.globalData[mapId].map;
		var elemId = groupId;
		
		if (!APP.utils.isset(ls))
			ls = {};
		if (APP.utils.isset(ls[elemId]))
		{
			$.each(ls[elemId], function(){
				map.removeLayer(this);
			});
		}
		ls[elemId] = [];
		$.each(items, function(){
			ls[elemId].push(L.geoJson(this.geoJSON));
			ls[elemId][ls[elemId].length-1].addTo(map);
		});
	},
	
	setMap: function(div)
	{	
		var that = this;
		var id = div.attr('id');
		
		if (this.isset(this.currentMapId) && (this.currentMapId == id))
			return;
			
		this.globalData[id] = {
			map: {},
			layerGroups: {},			
			drawControl: null,
			drawnItems: null,
			miniMapControl: null,
		};
		
		this.changeMap(id);
		// add an OpenStreetMap tile layer
		
		var baseLayers = {};
		var layers = [];
		var defLayName = null;
		var defLayUrl = null;
		var defaultLayer = null;
		if (APP.utils.isset(APP.config.localConfig))
		{
			$.each(APP.config.localConfig.background_layer, function(i,v)
			{
				var l = null;
				switch(v.source)
				{
					case "GOOGLE":
						l = new L.Google();
						break;
					case "BING":
						l = new L.BingLayer();
						break;					
					default:
						l = new L.tileLayer(v.url, {minZoom: 5, maxZoom: 19, attribution: v.description});
				}
				if (v.def)
				{
					defLayUrl = v.url;
					defaultLayer = l;
					defLayName = v.name;
				}
				else
				{
					layers.push(l);
					baseLayers[v.name] = l;
				}
			});
		}
		else
		{
			defLayUrl = "http://{s}.tile.osm.org/{z}/{x}/{y}.png";
			defaultLayer = new L.tileLayer(defLayUrl, {minZoom: 3, maxZoom: 19, attribution: "Mappa stradale"});
			defLayName = "Openstreetmap";
		}
		
		layers.push(defaultLayer);
		
		that.globalData[id].map = new L.map(id, {
			'center': new L.LatLng(44.160534,11.04126),
			'zoom': 9,
			'layers': [defaultLayer],
		});
		
		baseLayers[defLayName] = defaultLayer;
		L.control.layers(baseLayers).addTo(that.globalData[id].map);	

		that.setMapControls();
		
		//MOUSE COORDINATES
		if (APP.utils.isset(L.control.coordinates))
		{
			L.control.coordinates({
				position:"bottomleft", //optional default "bootomright"
				//decimals:2, //optional default 4
				//decimalSeperator:".", //optional default "."
				//labelTemplateLat:"Latitude: {y}", //optional default "Lat: {y}"
				//labelTemplateLng:"Longitude: {x}", //optional default "Lng: {x}"
				//enableUserInput:true, //optional default true,
				//useDMS:false //optional default false
			}).addTo(that.globalData[id].map);
		}
		
		//MINIMAP
		if (APP.utils.isset(L.Control.MiniMap))
		{
			L.extend(L.Control.MiniMap.prototype,{
				hideText: APP.i18n.translate("hide_minimap"),
				showText: APP.i18n.translate("show_minimap")
			});
			var miniMapLayer = new L.TileLayer(defLayUrl, {minZoom: 0, maxZoom: 13});
			that.globalData[id].miniMapControl = new L.Control.MiniMap(miniMapLayer, { toggleDisplay: true }).addTo(that.globalData[id].map);
		}
		
		//FULLSCREEN
		if (APP.utils.isset(L.Control.FullScreen))
		{
			L.control.fullscreen({
				position: 'topleft',
				forceSeparateButton:true,
			}).addTo(that.globalData[id].map);
		}
		
		//if (APP.utils.isset(L.Control.EraseALL))
		{
			L.Control.EraseALL = L.Control.extend({
				options: {
					position: 'topleft',
					buttonText: 'X',
					buttonTitle: 'Erase every feature',
					layer: new L.FeatureGroup()
				},
					
				onAdd: function (map)
				{
					var eraseName = 'leaflet-control-eraseall',
						container = L.DomUtil.create('div', eraseName + ' leaflet-bar');

					this._map = map;

					this._eraseButton  = this._createButton(
							this.options.buttonText, this.options.buttonTitle,
							eraseName + '-act',  container, this._eraseAll,  this);

					return container;
				},
					
				_eraseAll: function (e) {
					this.options.layer.clearLayers();
				},
							  
				_createButton: function (html, title, className, container, fn, context)
				{
					var link = L.DomUtil.create('a', className, container);
					link.innerHTML = html;
					link.href = '#';
					link.title = title;

					var stop = L.DomEvent.stopPropagation;

					L.DomEvent
						.on(link, 'click', stop)
						.on(link, 'mousedown', stop)
						.on(link, 'dblclick', stop)
						.on(link, 'click', L.DomEvent.preventDefault)
						.on(link, 'click', fn, context)
						.on(link, 'click', this._refocusOnMap, context);

					return link;
				},
			});
		}
	},
	
	addFullScreenControl: function()
	{
		L.Control.FullScreen = L.Control.extend({
			options: {
				position: 'topleft',
				title: APP.i18n.translate('Full Screen'),
				forceSeparateButton: false
			},
			
			onAdd: function (map) {
				var className = 'leaflet-control-zoom-fullscreen', container;
				
				if (map.zoomControl && !this.options.forceSeparateButton) {
					container = map.zoomControl._container;
				} else {
					container = L.DomUtil.create('div', 'leaflet-bar');
				}
				
				this._createButton(this.options.title, className, container, this.toogleFullScreen, map);

				return container;
			},
			
			_createButton: function (title, className, container, fn, context) {
				var link = L.DomUtil.create('a', className, container);
				link.href = '#';
				link.title = title;

				L.DomEvent
					.addListener(link, 'click', L.DomEvent.stopPropagation)
					.addListener(link, 'click', L.DomEvent.preventDefault)
					.addListener(link, 'click', fn, context);
				
				L.DomEvent
					.addListener(container, fullScreenApi.fullScreenEventName, L.DomEvent.stopPropagation)
					.addListener(container, fullScreenApi.fullScreenEventName, L.DomEvent.preventDefault)
					.addListener(container, fullScreenApi.fullScreenEventName, this._handleEscKey, context);
				
				L.DomEvent
					.addListener(document, fullScreenApi.fullScreenEventName, L.DomEvent.stopPropagation)
					.addListener(document, fullScreenApi.fullScreenEventName, L.DomEvent.preventDefault)
					.addListener(document, fullScreenApi.fullScreenEventName, this._handleEscKey, context);

				return link;
			},
			
			toogleFullScreen: function () {
				this._exitFired = false;
				var container = this._container;
				if (this._isFullscreen) {
					if (fullScreenApi.supportsFullScreen) {
						fullScreenApi.cancelFullScreen(container);
					} else {
						L.DomUtil.removeClass(container, 'leaflet-pseudo-fullscreen');
					}
					this.invalidateSize();
					this.fire('exitFullscreen');
					this._exitFired = true;
					this._isFullscreen = false;
				}
				else {
					if (fullScreenApi.supportsFullScreen) {
						fullScreenApi.requestFullScreen(container);
					} else {
						L.DomUtil.addClass(container, 'leaflet-pseudo-fullscreen');
					}
					this.invalidateSize();
					this.fire('enterFullscreen');
					this._isFullscreen = true;
				}
			},
			
			_handleEscKey: function () {
				if (!fullScreenApi.isFullScreen(this) && !this._exitFired) {
					this.fire('exitFullscreen');
					this._exitFired = true;
					this._isFullscreen = false;
				}
			}
		});

		L.Map.addInitHook(function () {
			if (this.options.fullscreenControl) {
				this.fullscreenControl = L.control.fullscreen(this.options.fullscreenControlOptions);
				this.addControl(this.fullscreenControl);
			}
		});

		L.control.fullscreen = function (options) {
			return new L.Control.FullScreen(options);
		};

		/* 
		Native FullScreen JavaScript API
		-------------
		Assumes Mozilla naming conventions instead of W3C for now

		source : http://johndyer.name/native-fullscreen-javascript-api-plus-jquery-plugin/

		*/

		var 
			fullScreenApi = { 
				supportsFullScreen: false,
				isFullScreen: function() { return false; }, 
				requestFullScreen: function() {}, 
				cancelFullScreen: function() {},
				fullScreenEventName: '',
				prefix: ''
			},
			browserPrefixes = 'webkit moz o ms khtml'.split(' ');
		
		// check for native support
		if (typeof document.exitFullscreen != 'undefined') {
			fullScreenApi.supportsFullScreen = true;
		} else {
			// check for fullscreen support by vendor prefix
			for (var i = 0, il = browserPrefixes.length; i < il; i++ ) {
				fullScreenApi.prefix = browserPrefixes[i];
				if (typeof document[fullScreenApi.prefix + 'CancelFullScreen' ] != 'undefined' ) {
					fullScreenApi.supportsFullScreen = true;
					break;
				}
			}
		}
		
		// update methods to do something useful
		if (fullScreenApi.supportsFullScreen) {
			fullScreenApi.fullScreenEventName = fullScreenApi.prefix + 'fullscreenchange';
			fullScreenApi.isFullScreen = function() {
				switch (this.prefix) {	
					case '':
						return document.fullScreen;
					case 'webkit':
						return document.webkitIsFullScreen;
					default:
						return document[this.prefix + 'FullScreen'];
				}
			}
			fullScreenApi.requestFullScreen = function(el) {
				return (this.prefix === '') ? el.requestFullscreen() : el[this.prefix + 'RequestFullScreen']();
			}
			fullScreenApi.cancelFullScreen = function(el) {
				return (this.prefix === '') ? document.exitFullscreen() : document[this.prefix + 'CancelFullScreen']();
			}
		}

		// jQuery plugin
		if (typeof jQuery != 'undefined') {
			jQuery.fn.requestFullScreen = function() {
				return this.each(function() {
					var el = jQuery(this);
					if (fullScreenApi.supportsFullScreen) {
						fullScreenApi.requestFullScreen(el);
					}
				});
			};
		}

		// export api
		window.fullScreenApi = fullScreenApi;
	},
});

