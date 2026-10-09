# Map Provider Integration & Quota Fallback Plan

This document outlines the step-by-step architectural plan to integrate Google Maps, Ola Maps, and OpenStreetMap (OSM) into the existing Workorio tracking module, featuring an automatic quota-based fallback mechanism.

## 1. Current State Analysis
Based on the codebase analysis (specifically `resources/views/tracking/index.blade.php`), the current implementation works as follows:
- **Map SDK:** The frontend directly loads the `olamaps-web-sdk.umd.js` script.
- **Initialization:** It initializes `OlaMapsSDK.OlaMaps` using a hardcoded `API_KEY`.
- **Functionality:** It uses Ola Maps specific methods (`myMap.addLayer`, `myMap.addSource`, `olaMaps.addMarker`) to draw the employee routes, clustered centroids, and markers.
- **Backend:** The `TrackingController` returns raw JSON coordinate data (`latitude`, `longitude`, `tracked_at`) and calculates point-to-point distances mathematically (Haversine formula).

**Conclusion:** The backend is already provider-agnostic, which is excellent. The challenge lies in abstracting the frontend so it can seamlessly switch between Google Maps, Ola Maps, and OSM (Leaflet) without rewriting the marker/routing logic three times.

---

## 2. Step-by-Step Implementation Plan

### Phase 1: Configuration & Database Schema (Backend)
To manage quotas and provider selection, we need a central configuration and usage tracking mechanism.

**Step 1: Environment Configuration**
Add provider credentials and limits to your `.env` file:
```env
# Map Limits
GOOGLE_MAPS_LIMIT=10000
OLA_MAPS_LIMIT=50000

# Map Keys
GOOGLE_MAPS_API_KEY=your_google_key
OLA_MAPS_API_KEY=your_ola_key
```

**Step 2: Usage Tracking Database**
Create a new migration for a `map_api_usages` table.
```php
Schema::create('map_api_usages', function (Blueprint $table) {
    $table->id();
    $table->string('provider'); // 'google', 'ola', 'osm'
    $table->integer('request_count')->default(0);
    $table->date('billing_cycle_start');
    $table->timestamps();
});
```

### Phase 2: Provider Selection Logic (Backend)
The backend must determine which provider is currently active based on the quotas before serving the view.

**Step 3: Map Provider Service**
Create a `MapProviderService` that:
1. Checks the current month's usage for `google` in `map_api_usages`.
2. If `google` < `GOOGLE_MAPS_LIMIT`, return `google`.
3. Else, check `ola` usage. If `ola` < `OLA_MAPS_LIMIT`, return `ola`.
4. Else, return `osm`.

**Step 4: Controller Update**
In `TrackingController@index`, inject the service and pass the active provider to the Blade view:
```php
$mapConfig = app(MapProviderService::class)->getActiveProviderConfig();
// returns: ['provider' => 'google', 'api_key' => '...', 'usage_id' => 1]

return view('tracking.index', compact('employees', 'mapConfig'));
```

### Phase 3: Frontend Abstraction (Javascript)
Currently, `index.blade.php` is tightly coupled to Ola Maps syntax. We must use the **Facade/Adapter Pattern** to standardize map commands.

**Step 5: Dynamic SDK Loading**
In `index.blade.php`, dynamically load the script based on the provider:
```html
@if($mapConfig['provider'] === 'google')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $mapConfig['api_key'] }}"></script>
@elseif($mapConfig['provider'] === 'ola')
    <script src="https://www.unpkg.com/olamaps-web-sdk@latest/dist/olamaps-web-sdk.umd.js"></script>
@elseif($mapConfig['provider'] === 'osm')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif
```

**Step 6: Unified Map Wrapper Class**
Create a standard JS wrapper inside the view (or in a separate `.js` file).
```javascript
class TrackingMap {
    constructor(provider, apiKey, containerId) {
        this.provider = provider;
        this.apiKey = apiKey;
        this.containerId = containerId;
        this.mapInstance = null;
        this.markers = [];
    }

    async initMap(centerLat, centerLng, zoom) {
        if (this.provider === 'google') {
            // Init Google Maps
        } else if (this.provider === 'ola') {
            // Current Ola Maps Init Logic
        } else if (this.provider === 'osm') {
            // Init Leaflet
        }
    }

    addMarker(lat, lng, iconHtml) {
        if (this.provider === 'google') { ... }
        if (this.provider === 'ola') { ... }
        if (this.provider === 'osm') { ... }
    }

    drawRoute(coordinates) {
        if (this.provider === 'google') { ... }
        if (this.provider === 'ola') { ... } // Re-use your addLayer/addSource logic
        if (this.provider === 'osm') { ... }
    }
}
```

**Step 7: Update `index.blade.php` Logic**
Replace direct Ola Maps calls (`myMap.addLayer(...)`) with the unified wrapper commands:
```javascript
const mapConfig = @json($mapConfig);
const trackingMap = new TrackingMap(mapConfig.provider, mapConfig.api_key, 'map');

await trackingMap.initMap(26.4983, 80.3429, 12);
// ... inside updateMapMarkers ...
trackingMap.drawRoute(cleanedLocations);
```

### Phase 4: Quota Incrementing (API Middleware)
To accurately track usage, you must increment the database counter.

**Step 8: API Route & Middleware**
Since tracking maps are single-page applications that fetch data repeatedly (`/tracking/fetch-locations`), the most accurate way to track map "loads" is via an API endpoint that the JS fires when `initMap` succeeds.
- Create an endpoint: `POST /api/tracking/map-loaded` (passes `usage_id`).
- This endpoint increments the `request_count` in the database.

---

## 3. Potential Risks and Edge Cases
1. **Visual Discrepancies:** Routes drawn in Google Maps look slightly different than Ola Maps or Leaflet because they use different rendering engines.
2. **Race Conditions:** If you have 50 users loading the map simultaneously right when the quota is at `9,999`, it might exceed the limit. **Solution:** Use Redis for atomic incrementing, or accept a margin of error (set your internal limit 100 requests lower than your actual Google billing limit).
3. **OSM Rate Limits:** Leaflet is free, but the default OpenStreetMap tile server has a strict fair-use policy. If you fall back to OSM and have heavy traffic, OSM might block your IP. **Solution:** Consider a cheap third-party tile provider (like MapTiler or Stadia Maps) for your OSM fallback instead of the default `tile.openstreetmap.org`.

## 4. Final Recommendation
Do not attempt to write all three implementations at once. 
1. First, build the Backend Quota Service. 
2. Second, build the `TrackingMap` wrapper but **only implement the Ola Maps logic inside it** (migrating the existing code into the wrapper).
3. Verify the tracking page still works perfectly with Ola Maps using the wrapper.
4. Finally, implement the Google Maps and OSM logic inside the wrapper.
