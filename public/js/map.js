// LEAFLET

$.extend(APP.map,
{
	globalData: {},
	previousMapId: null,
	currentMapId: null,
	sidebar: {
		div: null,
		control: null,
	},
	
	currentPosition: {
		id: null,
		marker: null,
		centerize: false,
	},	
	
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
		//this.removeGeolocation();
		$.each(this.globalData, function(i,v){
			v.map.remove();
		});
		this.globalData = {};
		this.currentMapId = null;
		this.previousMapId = null;
	},
	
	reverseMap: function()
	{
		var that = this;
		that.globalData[that.currentMapId].map.remove();
		that.currentMapId = that.previousMapId;
		that.previousMapId = null;
	},
	
	/*
	removeGeolocation: function()
	{
		var that = this;
		navigator.geolocation.clearWatch(that.currentPosition.id);
		if (APP.utils.isset(that.currentMapId) && APP.utils.isset(that.globalData[that.currentMapId].map) && APP.utils.isset(that.currentPosition.marker))
			that.globalData[that.currentMapId].map.removeLayer(that.currentPosition.marker);
		that.currentPosition = {
			id: null,
			marker: null,
			centerize: false,
		};
	},
	
	setGeolocation: function(elem)
	{
		var that = this;
		if (navigator.geolocation)
		{
			elem.click(function()
			{
				
				var li = elem.parents("li:first").toggleClass("active");
				$('body').find("#main_navbar_admin").collapse('hide');
				if (li.hasClass("active"))
				{
					if (!APP.utils.isset(that.currentMapId) || !APP.utils.isset(that.globalData[that.currentMapId].map))
						return;
					if (APP.utils.isset(that.currentPosition.id))
					{
						that.removeGeolocation();
					}
					that.currentPosition.id = navigator.geolocation.watchPosition(function(position)
					{
						var coords = [position.coords.latitude, position.coords.longitude];
						if (!APP.utils.isset(that.currentPosition.marker) || that.currentPosition.centerize)
							that.globalData[that.currentMapId].map.panTo(coords);
						if (!APP.utils.isset(that.currentPosition.marker))
						{
							that.currentPosition.marker = new L.Marker(coords, {bounceOnAdd: true});
							that.currentPosition.marker.bindPopup(APP.i18n.translate("you_are_here"));
							that.currentPosition.marker.addTo(that.globalData[that.currentMapId].map);
						}
						else
							that.currentPosition.marker.setLatLng(coords);
					});
				}
				else
					that.removeGeolocation();
				
			});
		}
		else 
			alert("Geolocation is not supported by this browser.");
	},
	*/
	
	getCurrentViewLayers: function(map)
	{
		var that = this;
		
		var bounds = map.getBounds();
				
		$.each(that.globalData[that.currentMapId].addedLayers, function(i, v)
		{
			var l = v.layer;
			if ($.isFunction(l.getBounds))
			{
				var  lb = l.getBounds();
				if (bounds.intersects(lb))
				{
					$('#leafletSidebar').find("#item_"+i).show();
				}
				else
				{
					$('#leafletSidebar').find("#item_"+i).hide();
				}
			}
			else
			{
				if ($.isFunction(l.getLatLng))
				{
					var  lb = l.getLatLng();
					if (bounds.contains(lb))
					{
						$('#leafletSidebar').find("#item_"+i).show();
						
					}
					else
					{
						$('#leafletSidebar').find("#item_"+i).hide();
						
					}
				}
			}			
		});
		var uiac = $('#leafletSidebar').find(".ui-accordion-content");
		$.each(uiac, function(i,v)
		{
			var anchors = $(v).find("a.list-group-item");
			var length = 0;
			$.each(anchors, function(j,k)
			{
				var display = $(k).css("display");
				if (display!="none")
					length++;
			});
			var header = $(v).prev();
			header.find(".badge").text(length);
		});
	},
	
	getCurrentMap: function()
	{
		return this.globalData[this.currentMapId].map;
	},
	
	getLayer: function(id)
	{
		var that = this;
		return that.globalData[that.currentMapId].addedLayers[id].layer;
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
		
		if (!APP.utils.isset(this.globalData[this.currentMapId].globalExtent))
			this.globalData[this.currentMapId].globalExtent = {};
		
		var value;
		if (APP.utils.isset(extent.maxx))
		{
			value = parseFloat(extent.maxx);
			if (!this.globalData[this.currentMapId].globalExtent.hasOwnProperty("maxx") || this.globalData[this.currentMapId].globalExtent.maxx < value)
				this.globalData[this.currentMapId].globalExtent.maxx = value;
		}
		
		if (APP.utils.isset(extent.maxy))
		{
			value = parseFloat(extent.maxy);
			if (!this.globalData[this.currentMapId].globalExtent.hasOwnProperty("maxy") || this.globalData[this.currentMapId].globalExtent.maxy < value)
				this.globalData[this.currentMapId].globalExtent.maxy = value;
		}
		
		if (APP.utils.isset(extent.minx))
		{
			value = parseFloat(extent.minx);
			if (!this.globalData[this.currentMapId].globalExtent.hasOwnProperty("minx") || this.globalData[this.currentMapId].globalExtent.minx > value)
				this.globalData[this.currentMapId].globalExtent.minx = value;
		}
		
		if (APP.utils.isset(extent.miny))
		{
			value = parseFloat(extent.miny);
			if (!this.globalData[this.currentMapId].globalExtent.hasOwnProperty("miny") || this.globalData[this.currentMapId].globalExtent.miny > value)
				this.globalData[this.currentMapId].globalExtent.miny = value;
		}
	},
	
	highlightLayer: function(id)
	{
		var that = this;
		var selectedOpacity = 1;
		var unselectedOpacity = 0.4;
		var defaultPathOpacity = 0.5;
		var defaultMarkerOpacity = 1;
		var selectedZIndex = 1000;
		var unselectedZIndex = 0;
		
		var setOpacity = function(layer, op)
		{
			if (layer.setOpacity && $.isFunction(layer.setOpacity))
				(op !== null)? layer.setOpacity(op) : layer.setOpacity(defaultMarkerOpacity);
			else
				(op !== null)? layer.setStyle({opacity: op}) : layer.setStyle({opacity: defaultPathOpacity});
		};
		
		var setZIndex = function(layer, z)
		{
			if (layer.setZIndexOffset && $.isFunction(layer.setZIndexOffset))
				(z !== null)? layer.setZIndexOffset(z) : layer.setOpacity(unselectedZIndex);
		};
		
		$.each(that.globalData[this.currentMapId].addedLayers, function(i,v)
		{
			if (id === null)
			{
				setOpacity(v.layer, null);
				setZIndex(v.layer, unselectedZIndex);
			}
			else
			{
				if (i === id)
				{
					setOpacity(v.layer, selectedOpacity);
					setZIndex(v.layer, selectedZIndex);
				}
				else
				{
					setOpacity(v.layer, unselectedOpacity);
					setZIndex(v.layer, unselectedZIndex);
				}
			}
		});
	},
	
	preserialize: function(name)
	{
		var value = "";
		
		if (this.globalData && this.currentMapId && this.globalData[this.currentMapId] && this.globalData[this.currentMapId].drawnItems && this.globalData[this.currentMapId].drawnItems.getLayers().length>0)
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
		
		L.control.scale().addTo(this.globalData[this.currentMapId].map);
	},
	
	setExtent: function(extent)
	{
		var that = this;
		if (!this.isset(extent) || $.isEmptyObject(extent) || !this.isset(this.currentMapId))
			return;
			
		that.globalData[that.currentMapId].map.fitBounds([
			[parseFloat(extent.miny), parseFloat(extent.minx)],
			[parseFloat(extent.maxy), parseFloat(extent.maxx)]
		]);
		
		//this.globalData[this.currentMapId].map.invalidateSize(true);
	},
	
	addLayer: function(obj)
	{
		var that = this;
		var foundLayer = false;
		
		var myId = APP.utils.getIndexFromField(that.globalData[that.currentMapId].addedLayers, "id", obj.id);
		if (myId > -1)
			that.globalData[that.currentMapId].addedLayers[myId].visible = true;
		else
			that.globalData[that.currentMapId].addedLayers[obj.id] = { id: obj.id, layer: obj.layer, visible: true, max_scale: obj.max_scale};
			
		if (!that.globalData[that.currentMapId].map.hasLayer(that.globalData[that.currentMapId].addedLayers[obj.id].layer))
				that.globalData[that.currentMapId].addedLayers[obj.id].layer.addTo(that.globalData[that.currentMapId].map);
	},
	
	showLayer: function(id)
	{
		var that = this;
		var index = APP.utils.getIndexFromField(that.globalData[that.currentMapId].addedLayers, "id", id);
		if (index === -1)
			return false;
		
		that.globalData[that.currentMapId].addedLayers[index].visible = true;
		if (!that.globalData[that.currentMapId].map.hasLayer(that.globalData[that.currentMapId].addedLayers[index].layer))
			that.globalData[that.currentMapId].addedLayers[index].layer.addTo(that.globalData[that.currentMapId].map);
	},
	
	showAllLayers: function()
	{
		var that = this;
		$.each(that.globalData[that.currentMapId].addedLayers, function(i, v){
			that.showLayer(i);
		});
	},
	
	hideLayer: function(id)
	{
		var that = this;
		var index = APP.utils.getIndexFromField(that.globalData[that.currentMapId].addedLayers, "id", id);
		if (index !== -1)
		{
			that.globalData[that.currentMapId].addedLayers[index].visible = false;
			if (that.globalData[that.currentMapId].map.hasLayer(that.globalData[that.currentMapId].addedLayers[index].layer))
				that.globalData[that.currentMapId].map.removeLayer(that.globalData[that.currentMapId].addedLayers[index].layer);
		}
	},
	
	hideAllLayers: function()
	{
		var that = this;
		$.each(that.globalData[that.currentMapId].addedLayers, function(i, v){
			that.hideLayer(i);
		});
	},
	
	removeLayer: function(id)
	{
		var that = this;
		
		var index = APP.utils.getIndexFromField(that.globalData[that.currentMapId].addedLayers, "id", id);
		if (index !== -1)
		{
			if (that.globalData[that.currentMapId].map.hasLayer(that.globalData[that.currentMapId].addedLayers[index].layer))
				that.globalData[that.currentMapId].map.removeLayer(that.globalData[that.currentMapId].addedLayers[index].layer);
			delete that.globalData[that.currentMapId].addedLayers[index];
		}
	},
	
	removeAllLayers: function()
	{
		var that = this;
		
		$.each(that.globalData[that.currentMapId].addedLayers, function(i,v)
		{
			that.removeLayer(i);
		});
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
			if (!this.globalData[mapId].drawnItems)
				this.globalData[mapId].drawnItems = new L.FeatureGroup();
			if (!this.globalData[mapId].map.hasLayer(this.globalData[mapId].drawnItems))
				this.globalData[mapId].map.addLayer(this.globalData[mapId].drawnItems);
			
			if (!APP.utils.isset(options))
			{
				options = {
					edit: {
						edit: false
					}
				};
			}

			if (!options.edit.featureGroup)
				options.edit.featureGroup = that.globalData[mapId].drawnItems;
			
			if (this.globalData[mapId].drawControl)
				this.globalData[mapId].map.removeControl(this.globalData[mapId].drawControl); 
			
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
		this.previousMapId = (APP.utils.isset(this.globalData[this.currentMapId]))? this.currentMapId : this.previousMapId;
		this.currentMapId = (APP.utils.isset(this.globalData[newMapId]))? newMapId : this.currentMapId;
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
	
	setMap: function(O)
	{	
		var that = this;
		var div = O.container;
		var id = div.attr('id');
		
		if (this.isset(this.currentMapId) && (this.currentMapId == id))
			return;
			
		this.globalData[id] = {
			map: {},
			addedLayers: {},
			layerGroups: {},			
			drawControl: null,
			drawnItems: null,
			miniMapControl: null,
		};
		
		this.changeMap(id);
		// add an OpenStreetMap tile layer
		
		var bgl = APP.config.getControlLayers();
		
		that.globalData[id].map = new L.map(id, {
			'center': (O.center)? O.center : new L.LatLng(0,0),//new L.LatLng(44.160534,11.04126),
			'zoom': (O.zoom)? O.zoom : 9,
			'layers': bgl.initialLayers,
		});			
		that.setGlobalExtent(APP.config.localConfig.default_extent);
		if (!O.center)
			that.setExtent(that.globalData[id].globalExtent);
		
		L.control.layers(bgl.baselayers,bgl.overlays).addTo(that.globalData[id].map);

		//MOUSE COORDINATES
		if (APP.utils.isset(L.control.coordinates))
		{
			L.control.coordinates({
			    position:"bottomleft", //optional default "bootomright"
			    //decimals:2, //optional default 4
			    //decimalSeperator:".", //optional default "."
			    //labelTemplateLat:"Latitude: {y}", //optional default "Lat: {y}"
			    //labelTemplateLng:"Longitude: {x}", //optional default "Lng: {x}"
			    //enableUserInput:true, //optional default true
			    //useDMS:false, //optional default false
			    //useLatLngOrder: true, //ordering of labels, default false-> lng-lat
			    //markerType: L.marker, //optional default L.marker
			    //markerProps: {} //optional default {}
			}).addTo(that.globalData[id].map);
		}
		
		that.setMapControls();
		
		if (APP.utils.isset(L.Hash))
			var hash = new L.Hash(that.globalData[id].map);
		
		if (APP.utils.isset(L.Control.DefaultExtent))
		{
			L.control.defaultExtent({
				title: APP.i18n.translate('Zoom to default extent'),
			}).setCenter(that.globalData[id].map.getCenter()).setZoom(that.globalData[id].map.getZoom()).addTo(that.globalData[id].map);
		}
		
		// locate control
		if (APP.utils.isset(L.control.locate))
		{
			L.control.locate({
				position: 'bottomright',  // set the location of the control
				icon: "icon-location-arrow",
				locateOptions: {
					enableHighAccuracy: true,
					timeout: 60000,
				},
				onLocationError: function(err) {
					console.log(err.message);
				}
			}).addTo(that.globalData[id].map);
		}
		
		// sidebar
		if (L.control.sidebar && $("#leafletSidebar").length > 0)
		{
			that.sidebar.control = L.control.sidebar('leafletSidebar', {
				position: 'left'
			});
			that.sidebar.control.addTo(that.globalData[id].map);
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

