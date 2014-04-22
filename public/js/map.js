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
		if (!this.isset(this.currentMapId) || ( this.currentMapId == "mapboxDiv"))
			return;
		var div = $(this.globalData[this.currentMapId].map.getContainer());
		if (div.length > 0)
			$(div[0]).height(div.parent().height()-120);
		
		this.globalData[this.currentMapId].map.invalidateSize(true);
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
	
	setExtent: function(extent)
	{
		if (!this.isset(extent) || $.isEmptyObject(extent) || !this.isset(this.currentMapId))
			return;
			
		this.globalData[this.currentMapId].map.fitBounds([
			[parseFloat(extent.miny), parseFloat(extent.minx)],
			[parseFloat(extent.maxy), parseFloat(extent.maxx)]
		]);
	},
	
	/*
	updateGeoJsonTracks: function(id, data)
	{
		var jObj = {
			"type": "Feature",
			"geometry": data
		};
		
		if (!APP.utils.isset(jObj.geometry))
			return;
		
		if (!APP.utils.isset(this.globalData[this.currentMapId].layers[id].layer))
			this.globalData[this.currentMapId].layers[id].layer = L.geoJson(jObj, {}).addTo(this.globalData[this.currentMapId].map);
		else
			this.globalData[this.currentMapId].layers[id].layer.addData(jObj);
	},
	*/
	
	removeMarkers: function(params)
	{
		var that = this;
		var obj = (jQuery.type(params) === "boolean")? this.globalData[this.currentMapId].layers : params;
		
		$.each(obj, function(i, v)
		{
			if (APP.utils.isset(v.markers))
				$.each(v.markers, function(j, k)
				{
					$.each(k, function(j1, k1)
					{
						that.globalData[that.currentMapId].map.removeLayer(k1);
					});
				});
			if (APP.utils.isset(v.tracks))
				$.each(v.tracks, function(j, k)
				{
					$.each(k, function(j1, k1)
					{
						that.globalData[that.currentMapId].map.removeLayer(k1);
					});
				});
			if (APP.utils.isset(that.globalData[that.currentMapId].layers[i]))
				//that.layers.splice(i,1);
				delete that.globalData[that.currentMapId].layers[i];
		});
	},
	
	addWMSLayer: function(url, options, boundsObj, div, T)
	{
		this.setMap(div);
		L.tileLayer.wms(url, options).addTo(this.globalData[this.currentMapId].map);
		
		if (this.isset(boundsObj.extent))
		{
			this.setExtent(boundsObj.extent);
			
			if (this.isset(boundsObj.startstopoint))
			{
				L.marker([parseFloat(boundsObj.startstopoint.start.lat),parseFloat(boundsObj.startstopoint.start.lon)], {icon: this.startIcon}).addTo(this.globalData[this.currentMapId].map);
				L.marker([parseFloat(boundsObj.startstopoint.stop.lat),parseFloat(boundsObj.startstopoint.stop.lon)], {icon: this.stopIcon}).addTo(this.globalData[this.currentMapId].map);
			}
		}
	},
	
	setGeoJsonMap: function(geojsonFeature, div, T, type)
	{
		var that = this;
		this.setMap(div);
		
		switch(type)
		{
			case "Point":
				$.each(geojsonFeature, function(i, v)
				{
					if (!that.isset(v.properties) || !that.isset(v.properties.id))
						return true;
					if (!APP.utils.isset(that.globalData[that.currentMapId].layers[v.properties.id]))
						that.globalData[that.currentMapId].layers[v.properties.id] = {};
					if (!APP.utils.isset(that.globalData[that.currentMapId].layers[v.properties.id].markers))
						that.globalData[that.currentMapId].layers[v.properties.id].markers = {};
					if (!APP.utils.isset(that.globalData[that.currentMapId].layers[v.properties.id].markers[v.properties.layerType]))
						that.globalData[that.currentMapId].layers[v.properties.id].markers[v.properties.layerType] = {};
						
					var markerIcon = null;
					var defaultLayerType = false;
					switch(v.properties.layerType)
					{
						case "last_realtime":
							markerIcon = that.unselectedMarker;
							break;
						case "start":
							markerIcon = that.startIcon;
							break;
						case "stop":
							markerIcon = that.stopIcon;
							break;
						default: // 
							defaultLayerType = true;
							markerIcon = that.unselectedMarker;
					}
					if (defaultLayerType)
					{
						if (!APP.utils.isset(that.globalData[that.currentMapId].layers[v.properties.id].markers[v.properties.layerType][v.properties.subType]))
							that.globalData[that.currentMapId].layers[v.properties.id].markers[v.properties.layerType][v.properties.subType] = [];
						that.globalData[that.currentMapId].layers[v.properties.id].markers[v.properties.layerType][v.properties.subType][that.globalData[that.currentMapId].layers[v.properties.id].markers[v.properties.layerType][v.properties.subType].length] = L.marker([parseFloat(v.geometry.coordinates[1]), parseFloat(v.geometry.coordinates[0])], {icon: markerIcon})
						.on("click", function(e){
							T.onSelRow(v.properties.id);
							//T.bindPU(this, v.properties.id);
						})
						.addTo(that.globalData[that.currentMapId].map);
					}
					else
					{
						if ($.isEmptyObject(that.globalData[that.currentMapId].layers[v.properties.id].markers[v.properties.layerType]))
						{
							that.globalData[that.currentMapId].layers[v.properties.id].markers[v.properties.layerType][0] = L.marker([parseFloat(v.geometry.coordinates[1]), parseFloat(v.geometry.coordinates[0])], {icon: markerIcon})
							.on("click", function(e){
								T.onSelRow(v.properties.id);
								//T.bindPU(this, v.properties.id);
							})
							.addTo(that.globalData[that.currentMapId].map);
						}
						else
							that.globalData[that.currentMapId].layers[v.properties.id].markers[v.properties.layerType][0].setLatLng([parseFloat(v.geometry.coordinates[1]), parseFloat(v.geometry.coordinates[0])]);
					}
				});
				break;
			case "LineString":
				$.each(geojsonFeature, function(i, v)
				{
					if (!that.isset(v.properties) || !that.isset(v.properties.id))
						return true;
					var id = v.properties.id;
					if (!APP.utils.isset(that.globalData[that.currentMapId].layers[id]))
						that.globalData[that.currentMapId].layers[id] = {};
					if (!APP.utils.isset(that.globalData[that.currentMapId].layers[id].tracks))
						that.globalData[that.currentMapId].layers[id].tracks = {};
					if (!APP.utils.isset(that.globalData[that.currentMapId].layers[id].tracks[v.properties.layerType]))
						that.globalData[that.currentMapId].layers[id].tracks[v.properties.layerType] = [];
					if (!APP.utils.isset(that.globalData[that.currentMapId].layers[id].tracks[v.properties.layerType][that.globalData[that.currentMapId].layers[id].tracks[v.properties.layerType].length]))
						that.globalData[that.currentMapId].layers[id].tracks[v.properties.layerType][that.globalData[that.currentMapId].layers[id].tracks[v.properties.layerType].length] = L.geoJson(v, {}).addTo(that.globalData[that.currentMapId].map);
					else
						that.globalData[that.currentMapId].layers[id].tracks[v.properties.layerType][that.globalData[that.currentMapId].layers[id].tracks[v.properties.layerType].length].addData(v);					
				});
				break;
			default:
				console.log("altro tipo GeojsonMap: "+type);
				break;
		}
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
	
	setMap: function(div)
	{	
		var that = this;
		var id = div.attr('id');
		
		if (this.isset(this.currentMapId) && (this.currentMapId == id))
			return;
			
		this.globalData[id] = {
			map: {},
			layers: {},			
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
	}
});

