/**
 * TrackingMap Unified Map Adapter
 * Supports Ola Maps, Google Maps, and OpenStreetMap (Leaflet)
 */
class TrackingMap {
    constructor(config) {
        this.provider = config.provider || 'osm';
        this.apiKey = config.api_key || '';
        this.usageId = config.usage_id || null;
        this.containerId = config.container_id || 'map';
        this.mapInstance = null;
        this.olaMapsSDK = null;
        this.markers = [];
        this.routeLayer = null; // Polyline or Leaflet layer
        this.googleOverlayClass = null;
        this.ready = false;
    }

    /**
     * Check if map is ready
     */
    isReady() {
        return this.ready && this.mapInstance !== null;
    }

    /**
     * Initialize map based on the active provider
     */
    async init(centerLat = 26.4983, centerLng = 80.3429, zoom = 12) {
        const container = document.getElementById(this.containerId);
        if (!container) {
            throw new Error(`Map container element #${this.containerId} not found`);
        }

        if (this.provider === 'google') {
            await this._initGoogleMaps(centerLat, centerLng, zoom);
        } else if (this.provider === 'ola') {
            await this._initOlaMaps(centerLat, centerLng, zoom);
        } else if (this.provider === 'osm') {
            await this._initOsmLeaflet(centerLat, centerLng, zoom);
        } else {
            throw new Error(`Unsupported map provider: ${this.provider}`);
        }

        this.ready = true;
        return this.mapInstance;
    }

    /**
     * Ola Maps initialization
     */
    async _initOlaMaps(centerLat, centerLng, zoom) {
        if (typeof OlaMapsSDK === 'undefined') {
            throw new Error("Ola Maps Web SDK is not loaded.");
        }

        // Suppress harmless '3d_model' warning in some Ola SDK releases
        const originalError = console.error;
        console.error = function (...args) {
            const message = args.map(arg => {
                if (arg instanceof Error) return arg.message + ' ' + (arg.stack || '');
                if (arg && typeof arg === 'object') {
                    try { return JSON.stringify(arg); } catch (e) { return String(arg); }
                }
                return String(arg);
            }).join(' ');

            if (message.includes('3d_model') || message.includes('3d_model_data')) return;
            originalError.apply(console, args);
        };

        this.olaMapsSDK = new OlaMapsSDK.OlaMaps({
            apiKey: this.apiKey
        });

        this.mapInstance = await this.olaMapsSDK.init({
            style: "https://api.olamaps.io/tiles/vector/v1/styles/default-light-standard-mr/style.json",
            container: this.containerId,
            center: [centerLng, centerLat],
            zoom: zoom
        });

        // Safe mock to prevent _getUIString TypeError inside some Ola Maps SDK builds
        if (this.mapInstance && !this.mapInstance._getUIString) {
            this.mapInstance._getUIString = () => '';
        }

        // Wait for Ola Vector style to be fully loaded
        if (this.mapInstance) {
            await new Promise((resolve) => {
                if (typeof this.mapInstance.isStyleLoaded === 'function' && this.mapInstance.isStyleLoaded()) {
                    resolve();
                } else {
                    const onLoaded = () => {
                        try {
                            this.mapInstance.off('load', onLoaded);
                            this.mapInstance.off('style.load', onLoaded);
                        } catch (e) { }
                        resolve();
                    };
                    this.mapInstance.once('load', onLoaded);
                    this.mapInstance.once('style.load', onLoaded);
                    setTimeout(onLoaded, 3000);
                }
            });
        }
    }

    /**
     * Google Maps initialization
     */
    async _initGoogleMaps(centerLat, centerLng, zoom) {
        if (typeof google === 'undefined' || !google.maps) {
            throw new Error("Google Maps JavaScript API is not loaded.");
        }

        const container = document.getElementById(this.containerId);
        this.mapInstance = new google.maps.Map(container, {
            center: { lat: centerLat, lng: centerLng },
            zoom: zoom,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
            zoomControl: true
        });

        // Setup custom overlay subclass for DOM elements
        this.googleOverlayClass = class extends google.maps.OverlayView {
            constructor(lat, lng, element, map) {
                super();
                this.position = new google.maps.LatLng(lat, lng);
                this.element = element;
                this.element.style.position = 'absolute';
                this.setMap(map);
            }
            onAdd() {
                const pane = this.getPanes().overlayMouseTarget;
                if (pane) pane.appendChild(this.element);
            }
            draw() {
                const projection = this.getProjection();
                if (!projection) return;
                const point = projection.fromLatLngToDivPixel(this.position);
                if (point) {
                    this.element.style.left = point.x + 'px';
                    this.element.style.top = point.y + 'px';
                }
            }
            onRemove() {
                if (this.element && this.element.parentNode) {
                    this.element.parentNode.removeChild(this.element);
                }
            }
            remove() {
                this.setMap(null);
            }
        };
    }

    /**
     * OpenStreetMap / Leaflet initialization
     */
    async _initOsmLeaflet(centerLat, centerLng, zoom) {
        if (typeof L === 'undefined') {
            throw new Error("Leaflet Library is not loaded.");
        }

        this.mapInstance = L.map(this.containerId).setView([centerLat, centerLng], zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(this.mapInstance);
    }

    /**
     * Clear all current markers
     */
    clearMarkers() {
        if (this.provider === 'ola') {
            this.markers.forEach(marker => {
                if (marker && typeof marker.remove === 'function') {
                    marker.remove();
                }
            });
        } else if (this.provider === 'google') {
            this.markers.forEach(marker => {
                if (marker && typeof marker.remove === 'function') {
                    marker.remove();
                } else if (marker && typeof marker.setMap === 'function') {
                    marker.setMap(null);
                } else if (marker && marker.map) {
                    marker.map = null;
                }
            });
        } else if (this.provider === 'osm') {
            this.markers.forEach(marker => {
                if (this.mapInstance && marker) {
                    this.mapInstance.removeLayer(marker);
                }
            });
        }
        this.markers = [];
    }

    /**
     * Add custom HTML element marker to map
     */
    addCustomMarker(lat, lng, element) {
        if (!this.mapInstance) return null;

        if (this.provider === 'ola') {
            const marker = this.olaMapsSDK.addMarker({
                element: element,
                anchor: 'bottom'
            }).setLngLat([lng, lat]).addTo(this.mapInstance);

            this.markers.push(marker);
            return marker;
        }

        if (this.provider === 'google') {
            // Check if AdvancedMarkerElement is supported and container has mapId
            if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
                try {
                    const marker = new google.maps.marker.AdvancedMarkerElement({
                        map: this.mapInstance,
                        position: { lat: lat, lng: lng },
                        content: element
                    });
                    this.markers.push(marker);
                    return marker;
                } catch (e) {
                    // Fall back to OverlayView if map lacks Map ID
                }
            }

            if (this.googleOverlayClass) {
                const overlay = new this.googleOverlayClass(lat, lng, element, this.mapInstance);
                this.markers.push(overlay);
                return overlay;
            }
        }

        if (this.provider === 'osm') {
            const icon = L.divIcon({
                html: element,
                className: 'custom-tracking-leaflet-marker',
                iconAnchor: [0, 0]
            });
            const marker = L.marker([lat, lng], { icon: icon }).addTo(this.mapInstance);
            this.markers.push(marker);
            return marker;
        }

        return null;
    }

    /**
     * Clear active route line
     */
    clearRoute() {
        if (this.provider === 'ola') {
            if (!this.mapInstance) return;
            if (typeof this.mapInstance.isStyleLoaded === 'function' && !this.mapInstance.isStyleLoaded()) return;
            try {
                if (typeof this.mapInstance.getLayer === 'function' && this.mapInstance.getLayer('route-line')) {
                    this.mapInstance.removeLayer('route-line');
                }
            } catch (e) { }
            try {
                if (typeof this.mapInstance.getSource === 'function' && this.mapInstance.getSource('route-source')) {
                    this.mapInstance.removeSource('route-source');
                }
            } catch (e) { }
        } else if (this.provider === 'google') {
            if (this.routeLayer) {
                this.routeLayer.setMap(null);
                this.routeLayer = null;
            }
        } else if (this.provider === 'osm') {
            if (this.routeLayer && this.mapInstance) {
                this.mapInstance.removeLayer(this.routeLayer);
                this.routeLayer = null;
            }
        }
    }

    /**
     * Draw route connecting coordinates: array of [lng, lat]
     */
    drawRoute(coordinates, options = {}) {
        if (!coordinates || coordinates.length < 2) return;
        this.clearRoute();

        const color = options.color || '#434AFA';
        const width = options.width || 4;
        const opacity = options.opacity || 0.8;

        if (this.provider === 'ola') {
            if (!this.mapInstance) return;

            // Wait if style is not ready yet
            if (typeof this.mapInstance.isStyleLoaded === 'function' && !this.mapInstance.isStyleLoaded()) {
                const onLoaded = () => {
                    this.drawRoute(coordinates, options);
                };
                this.mapInstance.once('load', onLoaded);
                this.mapInstance.once('style.load', onLoaded);
                return;
            }

            try {
                const geojsonData = {
                    'type': 'Feature',
                    'properties': {},
                    'geometry': {
                        'type': 'LineString',
                        'coordinates': coordinates
                    }
                };

                const existingSource = this.mapInstance.getSource('route-source');
                if (existingSource && typeof existingSource.setData === 'function') {
                    existingSource.setData(geojsonData);
                } else {
                    this.mapInstance.addSource('route-source', {
                        'type': 'geojson',
                        'data': geojsonData
                    });
                }

                if (!this.mapInstance.getLayer('route-line')) {
                    this.mapInstance.addLayer({
                        'id': 'route-line',
                        'type': 'line',
                        'source': 'route-source',
                        'layout': {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        'paint': {
                            'line-color': color,
                            'line-width': width,
                            'line-opacity': opacity
                        }
                    });
                }
            } catch (err) {
                console.warn('Ola drawRoute warning:', err);
            }
        } else if (this.provider === 'google') {
            const path = coordinates.map(([lng, lat]) => ({ lat: lat, lng: lng }));
            this.routeLayer = new google.maps.Polyline({
                path: path,
                geodesic: true,
                strokeColor: color,
                strokeOpacity: opacity,
                strokeWeight: width,
                map: this.mapInstance
            });
        } else if (this.provider === 'osm') {
            const latLngs = coordinates.map(([lng, lat]) => [lat, lng]);
            this.routeLayer = L.polyline(latLngs, {
                color: color,
                weight: width,
                opacity: opacity,
                lineJoin: 'round',
                lineCap: 'round'
            }).addTo(this.mapInstance);
        }
    }

    /**
     * Fit bounding box on visible coordinates
     */
    fitBounds(minLng, minLat, maxLng, maxLat, padding = 70, maxZoom = 15) {
        if (!this.mapInstance) return;

        if (minLng === maxLng) { minLng -= 0.01; maxLng += 0.01; }
        if (minLat === maxLat) { minLat -= 0.01; maxLat += 0.01; }

        const paddingOptions = typeof padding === 'object' ? padding : {
            top: typeof padding === 'number' ? padding + 25 : 95,
            bottom: typeof padding === 'number' ? padding : 70,
            left: typeof padding === 'number' ? padding : 70,
            right: typeof padding === 'number' ? padding + 25 : 95
        };

        if (this.provider === 'ola') {
            this.mapInstance.fitBounds([[minLng, minLat], [maxLng, maxLat]], {
                padding: paddingOptions,
                maxZoom: maxZoom
            });
        } else if (this.provider === 'google') {
            const bounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(minLat, minLng),
                new google.maps.LatLng(maxLat, maxLng)
            );
            this.mapInstance.fitBounds(bounds, padding);
            if (maxZoom) {
                const listener = google.maps.event.addListenerOnce(this.mapInstance, 'idle', () => {
                    if (this.mapInstance.getZoom() > maxZoom) {
                        this.mapInstance.setZoom(maxZoom);
                    }
                });
            }
        } else if (this.provider === 'osm') {
            const padY = typeof padding === 'number' ? padding : 70;
            const padX = typeof padding === 'number' ? padding : 70;
            this.mapInstance.fitBounds([[minLat, minLng], [maxLat, maxLng]], {
                padding: [padY, padX],
                maxZoom: maxZoom
            });
        }
    }

    /**
     * Pan / Fly to specific coordinates smoothly
     */
    flyTo(lat, lng, zoom = 30) {
        if (!this.mapInstance) return;
        try {
            if (this.provider === 'ola') {
                if (typeof this.mapInstance.flyTo === 'function') {
                    this.mapInstance.flyTo({ center: [lng, lat], zoom: zoom });
                }
            } else if (this.provider === 'google') {
                if (typeof this.mapInstance.panTo === 'function') {
                    this.mapInstance.panTo({ lat: lat, lng: lng });
                    this.mapInstance.setZoom(zoom);
                }
            } else if (this.provider === 'osm') {
                if (typeof this.mapInstance.flyTo === 'function') {
                    this.mapInstance.flyTo([lat, lng], zoom);
                } else if (typeof this.mapInstance.setView === 'function') {
                    this.mapInstance.setView([lat, lng], zoom);
                }
            }
        } catch (e) {
            console.warn('flyTo failed:', e);
        }
    }

    /**
     * Invalidate and recalculate container dimensions (e.g. after sidebar opens or closes)
     */
    invalidateSize() {
        if (!this.mapInstance) return;
        try {
            if (this.provider === 'osm') {
                if (typeof this.mapInstance.invalidateSize === 'function') {
                    this.mapInstance.invalidateSize();
                }
            } else if (this.provider === 'ola') {
                if (typeof this.mapInstance.resize === 'function') {
                    this.mapInstance.resize();
                }
            } else if (this.provider === 'google') {
                if (typeof google !== 'undefined' && google.maps && google.maps.event) {
                    google.maps.event.trigger(this.mapInstance, 'resize');
                }
            }
        } catch (e) {
            console.warn('Map resize/invalidateSize error:', e);
        }
    }

    /**
     * Clean up and destroy current map instance
     */
    destroy() {
        this.clearMarkers();
        this.clearRoute();
        if (this.mapInstance) {
            try {
                if (typeof this.mapInstance.remove === 'function') {
                    this.mapInstance.remove();
                }
            } catch (e) {
                console.warn('Map cleanup error:', e);
            }
            this.mapInstance = null;
        }
        const container = document.getElementById(this.containerId);
        if (container) {
            container.innerHTML = '';
            container.className = container.className.replace(/\bleaflet-[^\s]+/g, '').trim();
            container._leaflet_id = null;
            container.style.height = '100%';
            container.style.width = '100%';
            container.style.position = 'relative';
            container.style.zIndex = '1';
        }
        this.ready = false;
    }

    /**
     * Switch map provider dynamically (e.g. from Free OSM to Quota Ola/Google)
     */
    async switchProvider(newConfig, centerLat = 26.4983, centerLng = 80.3429, zoom = 12) {
        this.destroy();
        this.provider = newConfig.provider || 'osm';
        this.apiKey = newConfig.api_key || '';
        this.usageId = newConfig.usage_id || null;
        return await this.init(centerLat, centerLng, zoom);
    }

    /**
     * Send tracking ping to increment provider usage quota in backend
     */
    recordUsage(endpointUrl, csrfToken) {
        if (!endpointUrl) return;

        fetch(endpointUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify({
                usage_id: this.usageId,
                provider: this.provider
            })
        }).catch(err => {
            console.warn('Map usage record ping failed:', err);
        });
    }
}

