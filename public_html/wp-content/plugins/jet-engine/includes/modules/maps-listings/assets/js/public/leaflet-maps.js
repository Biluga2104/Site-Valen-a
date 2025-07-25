const JetLeafletPopup = function( data ) {
	
	this.popup = data.popup;
	this.popupContent = null;
	this.map = data.map || null;

	this.contentIsSet = function() {
		return null !== this.popupContent;
	}

	this.close = function() {
		// runs automatically
		return;
	}

	this.setMap = function( map ) {
		this.map = map;
	}

	this.draw = function() {
		// runs automatically
		return;
	}

	this.open = function( map, marker ) {
		// runs automatically
		return;
	}

	this.setContent = function( content ) {
		// Convert a content to an HTMLElement to store the HTML manipulation in a popup
		if ( typeof content.nodeType === 'undefined' ) {
			let contentHtml = document.createElement( 'div' );
			contentHtml.innerHTML = content;
			content = contentHtml;
		}

		this.popupContent = content;
		this.popup.setContent( content );
	}

	return this;

};

window.JetEngineMapsProvider = function() {

	this.getId = function() {
		return 'leaflet';
	}

	this.getContainer = function( map ) {
		return map.getContainer();
	}

	this.getBoundsJSON = function( map ) {
		const bounds = map.getBounds();

		if ( ! bounds ) {
			return;
		}

		return {
			east: bounds.getEast(),
			north: bounds.getNorth(),
			south: bounds.getSouth(),
			west: bounds.getWest()
		};
	}

	this.updateSyncBounds = function() {

		const map = this;

		const bounds = map.getBounds();

		if ( ! bounds ) {
			return;
		}

		window.JetEngineMaps.dispatchMapSyncEvent(
			map,
			{
				east: bounds.getEast(),
				north: bounds.getNorth(),
				south: bounds.getSouth(),
				west: bounds.getWest()
			}
		);
	}

	this.initSync = function( map ) {
		
		if ( map?.isJetEngineSyncInited || ! window.JetEngineMaps || ! window.JetSmartFilters ) {
			return;
		}

		map.on( 'zoomend', this.updateSyncBounds );
		map.on( 'moveend', this.updateSyncBounds );
		map.on( 'resize', this.updateSyncBounds );

		map.whenReady(
			() => {
				window.JetEngineMaps.dispatchMapSyncInitEvent( map );
			}
		);

		map.isJetEngineSyncInited = true;
	}

	this.initMap = function( container, settings ) {

		settings = settings || {};

		let settingsMap = {
			zoom: 'zoom',
			center: 'center',
			scrollWheelZoom: 'scrollwheel',
			zoomControl: 'zoomControl',
			maxZoom: 'maxZoom',
			minZoom: 'minZoom',
		};
		
		let parsedSettings = {}

		for ( const [ lKey, settingsKey ] of Object.entries( settingsMap ) ) {
			if ( undefined !== settings[ settingsKey ] ) {
				parsedSettings[ lKey ] = settings[ settingsKey ];
			}
		}

		if ( parsedSettings.center ) {
			parsedSettings.center = JetEngineLeaflet.latLng( parsedSettings.center.lat, parsedSettings.center.lng );
		}

		const map = JetEngineLeaflet.map( container, parsedSettings );

		const tileURL = window.JetPlugins.hooks.applyFilters( 'jet-engine.maps-listings.leaflet.tileURL', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' );
		const tileOptions = window.JetPlugins.hooks.applyFilters( 'jet-engine.maps-listings.leaflet.tileOptions', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		} );

		JetEngineLeaflet.tileLayer( tileURL, tileOptions ).addTo( map );

		this.initSync( map );

		return map;
	}

	this.initBounds = function() {
		const bounds = JetEngineLeaflet.latLngBounds( [] );
		return bounds;
	}

	this.getMarkerPosition = function( marker ) {
		return marker.getLatLng();
	}

	this.fitMapBounds = function( data ) {
		
		let center = null;
		
		try {
			center = data.bounds.getCenter();
		} catch ( e ) {
			console.log( 'Can`t define new map center without markers.' );
		}
		
		if ( center ) {
			data.map.fitBounds( data.bounds );
			return true;
		} else {
			return false;
		}
	}

	this.addMarker = function( data ) {
		
		var myIcon = JetEngineLeaflet.divIcon( { html: data.content, iconSize: [ 32, 32 ] } );
		var marker = JetEngineLeaflet.marker( [ data.position.lat, data.position.lng ], { icon: myIcon } );

		if ( ! data.markerClustering ) {
			marker.addTo( data.map );
		}
		
		return marker;
	}

	this.removeMarker = function( marker ) {
		marker.remove();
	}

	this.closePopup = function( infoBox, callback, map ) {
		map.on( 'popupclose', ( e ) => {
			if ( e.popup === infoBox.popup ) {
				callback();
			}
		} );
	}

	this.openPopup = function( trigger, callback, infobox, map, openOn ) {

		map.on( 'popupopen', ( e ) => {
			map.isInternalInteraction = true;
			if ( e.popup === infobox.popup ) {
				callback();
			}
		} );

		trigger.bindPopup( infobox.popup );

		if ( 'hover' === openOn ) {
			trigger.on( 'mouseover', function () {
				if ( ! trigger.isPopupOpen() ) {
					map.isInternalInteraction = true;
					trigger.openPopup();
				}
			} );
		}
	}

	this.triggerOpenPopup = function( trigger ) {
		trigger.openPopup();
	}


	this.setAutocenterBlock = function( e ) {
		const spiderfied = e.target._spiderfied || false;
		const clickedMarker = e.layer;
		
		e.target._map.jetPlugins.autoCenterBlock = spiderfied && spiderfied.getAllChildMarkers().includes( clickedMarker );
	}

	this.getMarkerCluster = function( data ) {
		let options = {};

		const optionsMap = {
			disableClusteringAtZoom: 'clusterMaxZoom',
			maxClusterRadius: 'clusterRadius',
		};

		for ( const [ optionKey, settingsKey ] of Object.entries( optionsMap ) ) {
			if ( undefined !== data[ settingsKey ] && '' !== data[ settingsKey ]  ) {
				options[ optionKey ] = data[ settingsKey ];
			}
		}

		var markersGrpup = JetEngineLeaflet.markerClusterGroup( options );
		markersGrpup.addLayers( data.markers );
		data.map.addLayer( markersGrpup );

		/**
		 * prevent auto center when opening popup from spiderfied cluster,
		 * as programmatical pan causes cluster to unspiderfy
		 * @see https://github.com/Crocoblock/issues-tracker/issues/13780
		 */

		markersGrpup.on( 'click mouseover', this.setAutocenterBlock );
		
		return markersGrpup;
	}

	this.addMarkers = function( markerCluster, markers ) {
		markerCluster.addLayers( markers );
	}

	this.removeMarkers = function( markerCluster, markers ) {
		markerCluster.removeLayers( markers );
	}

	this.markerOnClick = function( map, data, callback ) {

		data = data || {};

		data.map    = map;
		data.shadow = false;

		map.on( "click", ( event ) => {

			data.position = {
				lat: event.latlng.lat,
				lng: event.latlng.lng,
			};

			if ( callback ) {
				callback( this.addMarker( data ) );
			}

		} );
	}

	this.setCenterByPosition = function( data ) {
		data.map.setView( data.position, data.zoom );
	}

	this.setAutoCenter = function( data ) {
		data.map.isInternalInteraction = true;

		if ( ! this.fitMapBounds( data ) ) {
			if ( window.JetEngineMapData && window.JetEngineMapData.mapCenter ) {
				data.map.setView( window.JetEngineMapData.mapCenter, 10 );
			} else {
				data.map.fitWorld();
			}
			
		}
	}

	this.addPopup = function( data ) {
		
		const popup = JetEngineLeaflet.popup( {
			maxWidth: data.width,
			minWidth: data.width,
			keepInView: true,
			className: 'jet-map-box',
		} );

		return new JetLeafletPopup( {
			popup: popup
		} );
	}

	this.getMarkerMap = function( marker ) {
		return marker._map;
	}

	this.fitMapToMarker = function( marker, markersClusterer, zoom ) {
		markersClusterer.zoomToShowLayer( marker, () => {
			if ( true||! marker.__parent._icon ) {
				this.panTo( {
					map: markersClusterer._map,
					position: this.getMarkerPosition( marker ),
					zoom: zoom
				} );
	
				this.triggerOpenPopup( marker );
			}
		} );
	}

	this.panTo = function( data ) {
		var zoom = ( data.zoom && data.zoom > data.map.getZoom() ) ? data.zoom : data.map.getZoom();
		data.map.flyTo( data.position, zoom );
	}

}
