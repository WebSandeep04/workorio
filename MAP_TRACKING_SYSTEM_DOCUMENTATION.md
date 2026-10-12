# Comprehensive Documentation: Multi-Provider Map Tracking & Quota Fallback System

This document provides a detailed end-to-end breakdown of how the Multi-Provider Map Tracking System works across the backend, database, and frontend in Workorio.

---

## 1. High-Level Architecture Overview

The system allows the live employee tracking page to seamlessly work across **three map engines**:
1. **Google Maps** (Primary / High accuracy, if configured with key and within quota)
2. **Ola Maps** (Secondary / Primary cost-effective alternative, if key is valid and within quota)
3. **OpenStreetMap via Leaflet** (Zero-cost, permanent fallback when commercial quotas are exhausted)

The switch between providers happens **automatically and transparently** without disrupting live employee location rendering, cluster centroids, or polyline route drawing.

```mermaid
flowchart TD
    A[User visits /tracking] --> B[TrackingController@index]
    B --> C[MapProviderService::getActiveProviderConfig]
    C --> D{Google Key present & < Limit?}
    D -- Yes --> E[Active: Google Maps]
    D -- No --> F{Ola Key present & < Limit?}
    F -- Yes --> G[Active: Ola Maps]
    F -- No --> H[Active: OpenStreetMap Leaflet]
    E --> I[Send $mapConfig to tracking.index Blade View]
    G --> I
    H --> I
    I --> J[Dynamic SDK Loader in Blade]
    J --> K[TrackingMap Adapter JS Class]
    K --> L[Initialize Active Map Engine]
    L --> M[POST /tracking/map-loaded API Ping]
    M --> N[(map_api_usages Table Incremented)]
    L --> O[Fetch & Render Employee Locations & Routes]
```

---

## 2. Core Components & Where They Live

| Layer | File / Resource | Responsibility |
|---|---|---|
| **Environment** | `.env` & `.env.example` | Stores API keys and monthly quota limits. |
| **Config** | `config/services.php` | Exposes map configurations to Laravel application. |
| **Database** | `map_api_usages` Table | Records provider requests grouped by monthly billing cycles. |
| **Model** | `app/Models/MapApiUsage.php` | Eloquent model for database interactions. |
| **Backend Service** | `app/Services/MapProviderService.php` | Evaluates quotas, resolves active provider, records increments. |
| **Controller** | `app/Http/Controllers/TrackingController.php` | Passes config to view and serves the load tracking endpoint. |
| **Route** | `routes/web.php` | Registers `POST /tracking/map-loaded`. |
| **Frontend Adapter** | `public/js/tracking-map.js` | Adapter/Facade JS class (`TrackingMap`) abstracting all 3 map APIs. |
| **Blade View** | `resources/views/tracking/index.blade.php` | UI page, dynamic script loading, and UI badge indicator. |

---

## 3. Database Schema & Quota Tracking

### MySQL Table Definition
Run this statement on each tenant database:

```sql
CREATE TABLE IF NOT EXISTS `map_api_usages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_count` bigint unsigned NOT NULL DEFAULT '0',
  `billing_cycle_start` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `map_api_usages_provider_billing_cycle_start_unique` (`provider`, `billing_cycle_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### How Billing Cycles Work
- Every month starts with a fresh cycle date (`billing_cycle_start = 'YYYY-MM-01'`).
- In month `2026-10-01`, when Google Maps or Ola Maps is used, its `request_count` increases by 1 each time the map page is loaded.
- At the start of next month (`2026-11-01`), a new record is created automatically with `request_count = 0`, resetting monthly limits automatically without needing a manual cron reset.

---

## 4. Provider Selection & Fallback Logic

The provider resolution logic runs inside `App\Services\MapProviderService`:

1. **Step 1: Check Google Maps**
   - Reads `GOOGLE_MAPS_API_KEY` and `GOOGLE_MAPS_LIMIT` from config.
   - If an API key is provided and `GOOGLE_MAPS_LIMIT > 0`:
     - Fetches current month's count from `map_api_usages` where `provider = 'google'`.
     - If `request_count < GOOGLE_MAPS_LIMIT`, Google Maps is selected.
2. **Step 2: Check Ola Maps**
   - If Google Maps is exhausted or not configured, it evaluates Ola Maps.
   - Reads `OLA_MAPS_API_KEY` and `OLA_MAPS_LIMIT`.
   - Fetches current month's count from `map_api_usages` where `provider = 'ola'`.
   - If `request_count < OLA_MAPS_LIMIT`, Ola Maps is selected.
3. **Step 3: Fallback to OpenStreetMap (Leaflet)**
   - If both Google Maps and Ola Maps limits are reached (or no keys are set), it selects `osm`.
   - OpenStreetMap requires no API keys and has zero billing cost.

The resolved payload passed to Blade view looks like:
```json
{
  "provider": "ola",
  "api_key": "iLBQa55RFiKpDxyF0h9mf8IEC37Xe4e09CyNwtlT",
  "usage_id": 1,
  "request_count": 1420,
  "limit": 50000
}
```

---

## 5. Frontend Abstraction: The `TrackingMap` Adapter

Different map providers have completely different APIs:
- **Ola Maps**: Uses MapLibre vector style with GeoJSON sources and layers.
- **Google Maps**: Uses `google.maps.Map`, `OverlayView`, and `google.maps.Polyline`.
- **OpenStreetMap**: Uses Leaflet `L.map`, `L.divIcon`, and `L.polyline`.

The `TrackingMap` class in `public/js/tracking-map.js` exposes one standard interface so the view does not care which map is loaded:

### Standard Methods

| Method | Description |
|---|---|
| `init(centerLat, centerLng, zoom)` | Asynchronously initializes the active SDK onto container `#map`. |
| `addCustomMarker(lat, lng, element)` | Injects custom DOM element (avatar, initials, tooltip label) at lat/lng. |
| `clearMarkers()` | Removes all active markers from the map instance and cleans memory. |
| `drawRoute(coordinates, options)` | Takes an array of `[longitude, latitude]` points and renders a smoothed polyline. |
| `clearRoute()` | Removes the active polyline / geojson route layer. |
| `fitBounds(minLng, minLat, maxLng, maxLat, padding)` | Zooms and pans the camera so all active employee markers fit nicely on screen. |
| `recordUsage(endpointUrl, csrfToken)` | Fires an async POST ping to `/tracking/map-loaded` to increment backend quota. |

---

## 6. Detailed Step-by-Step Execution Lifecycle

1. **Page Request**:
   - User navigates to `/tracking`.
   - `TrackingController@index` asks `MapProviderService` for active configuration.
   - Blade receives `$mapConfig`.

2. **Conditional Asset Loading**:
   - Blade evaluates `@if($mapConfig['provider'] === '...')` and loads only the needed script:
     - Google: loads `maps.googleapis.com/maps/api/js`.
     - Ola: loads `olamaps-web-sdk.umd.js` and `style.css`.
     - OSM: loads `leaflet.js` and `leaflet.css`.
   - Loads `js/tracking-map.js`.

3. **Map Initialization**:
   - JS calls `await trackingMap.init(26.4983, 80.3429, 12)`.
   - JS fires `trackingMap.recordUsage('/tracking/map-loaded', csrfToken)` in the background.

4. **Quota Increment**:
   - Server receives the ping, executes `MapApiUsage::increment('request_count')` atomically, and responds `{ success: true }`.

5. **Location Fetching & Rendering**:
   - `fetchLocations()` requests `/tracking/fetch-locations?employee_id=...&date=...`.
   - Returns GPS locations from database.
   - GPS smoothing algorithm filters out drift, stationary jitter, and unrealistic speed jumps.
   - For each coordinate, `trackingMap.addCustomMarker(lat, lng, element)` is called.
   - For single-employee mode, `trackingMap.drawRoute(coordinates)` renders the route line.
   - `trackingMap.fitBounds(...)` ensures all coordinates are visible.

6. **Automatic Auto-Refresh**:
   - `setInterval(fetchLocations, 30000)` queries new positions every 30 seconds and refreshes markers without reloading the map or consuming additional map session load quotas.

---

## 7. How to Configure & Test Each Provider

You can control which provider is active at any time via `.env`:

### Test Google Maps:
```env
GOOGLE_MAPS_API_KEY=AIzaSy...your_valid_key
GOOGLE_MAPS_LIMIT=10000
```
- Refresh `/tracking`. The top badge will display **Google Maps**.

### Test Ola Maps:
Leave `GOOGLE_MAPS_API_KEY` empty or set `GOOGLE_MAPS_LIMIT=0`:
```env
GOOGLE_MAPS_API_KEY=
OLA_MAPS_API_KEY=iLBQa55RFiKpDxyF0h9mf8IEC37Xe4e09CyNwtlT
OLA_MAPS_LIMIT=50000
```
- Refresh `/tracking`. The top badge will display **Ola Maps**.

### Test OpenStreetMap Fallback:
Set limits lower than current usage (e.g., `OLA_MAPS_LIMIT=0` and `GOOGLE_MAPS_LIMIT=0`):
```env
GOOGLE_MAPS_LIMIT=0
OLA_MAPS_LIMIT=0
```
- Refresh `/tracking`. The top badge will display **OpenStreetMap**, and Leaflet will render with zero API key dependencies.
