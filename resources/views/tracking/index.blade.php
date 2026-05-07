@extends('layouts.app')

@section('title', 'Employee Live Tracking')
@section('page_title', 'Employee Live Tracking')

@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body { font-family: 'Montserrat', sans-serif !important; background-color: #f4f5f7; }
  
  .tracking-container {
    padding: 0.5rem;
    margin-top: 0.25rem;
  }
  
  /* Sidebar Styling */
  .tracking-sidebar {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    height: calc(100vh - 110px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  
  .sidebar-header {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    background: #ffffff;
    display: flex !important;
    flex-direction: column !important;
    gap: 0.75rem !important;
  }
  
  .sidebar-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 !important;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    width: 100% !important;
  }
  
  .sidebar-search-wrapper {
    position: relative;
    width: 100% !important;
    margin: 0 !important;
  }
  
  .sidebar-search-wrapper i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.9rem;
  }
  
  .sidebar-search-input {
    width: 100%;
    padding: 0.45rem 0.75rem 0.45rem 2.25rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.8rem;
    font-family: 'Montserrat', sans-serif;
    outline: none;
    transition: all 0.2s ease;
  }
  
  .sidebar-search-input:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.15);
  }
  
  .sidebar-date-wrapper {
    width: 100% !important;
    margin: 0 !important;
  }
  
  .sidebar-date-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 0.25rem;
    display: block;
    text-transform: uppercase;
  }
  
  .sidebar-date-input {
    width: 100%;
    padding: 0.45rem 0.75rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.8rem;
    font-family: 'Montserrat', sans-serif;
    outline: none;
    background: #f8fafc;
    color: #334155;
    font-weight: 500;
  }
  
  /* Employee List Styling */
  .employee-list {
    flex: 1;
    overflow-y: auto;
    padding: 0.75rem;
    background: #fafafb;
  }
  
  .employee-list::-webkit-scrollbar {
    width: 5px;
  }
  
  .employee-list::-webkit-scrollbar-track {
    background: #fafafb;
  }
  
  .employee-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 999px;
  }
  
  .employee-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.7rem;
    border-radius: 8px;
    border: 1px solid #f1f5f9;
    cursor: pointer;
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }
  
  .employee-card:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
    transform: translateY(-1px);
  }
  
  .employee-card.active {
    background: #eef2ff;
    border-color: #818cf8;
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.05);
  }
  
  .employee-avatar-wrapper {
    position: relative;
    width: 38px;
    height: 38px;
    flex-shrink: 0;
  }
  
  .employee-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    color: #475569;
    border: 1px solid #e2e8f0;
  }
  
  .status-indicator {
    position: absolute;
    bottom: -1px;
    right: -1px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #ffffff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.15);
  }
  
  .status-dot-active { background-color: #10b981; }
  .status-dot-break { background-color: #f59e0b; }
  .status-dot-inactive { background-color: #94a3b8; }
  
  .employee-details {
    flex: 1;
    min-width: 0;
  }
  
  .employee-name {
    font-weight: 600;
    font-size: 0.8rem;
    color: #1e293b;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .employee-meta {
    font-size: 0.68rem;
    color: #64748b;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .employee-status-badge {
    font-size: 0.6rem;
    font-weight: 600;
    padding: 0.15rem 0.4rem;
    border-radius: 9999px;
    text-transform: uppercase;
    display: inline-block;
    margin-top: 0.2rem;
  }
  
  .status-badge-active { background: #d1fae5; color: #065f46; }
  .status-badge-break { background: #fef3c7; color: #92400e; }
  .status-badge-inactive { background: #f1f5f9; color: #475569; }
  
  /* Map Styling */
  .map-card {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    height: calc(100vh - 110px);
    position: relative;
  }
  
  .map-stats-overlay {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    padding: 0.5rem 0.85rem;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    z-index: 10;
    border: 1px solid #e2e8f0;
    display: flex;
    gap: 0.85rem;
    font-size: 0.7rem;
  }
  
  .map-stat-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-weight: 600;
    color: #334155;
  }
  
  .map-stat-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
  }
</style>
@endpush

@section('content')
<div class="container-fluid tracking-container">
    <!-- Hidden Compatible Fields for Backend Integration -->
    <input type="hidden" id="employee_id" value="">
    <input type="hidden" id="date" value="{{ date('Y-m-d') }}">

    <div class="row g-3">
        <!-- Vertical Employee Sidebar Panel -->
        <div class="col-12 col-md-4 col-lg-3">
            <div class="tracking-sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-title">
                        <i class="bi bi-geo-alt-fill text-primary"></i> Live Tracking Panel
                    </div>

                    <div class="sidebar-date-wrapper">
                        <label class="sidebar-date-label">Select Track Date</label>
                        <input type="date" id="sidebarDate" class="sidebar-date-input" value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="sidebar-search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" id="sidebarSearch" class="sidebar-search-input" placeholder="Search employee...">
                    </div>
                </div>

                <div class="employee-list" id="employeeListContainer">
                    <!-- Reset Option (All Employees) -->
                    <div class="employee-card active" data-emp-id="">
                        <div class="employee-avatar-wrapper">
                            <div class="employee-avatar" style="background: #434AFA; color: white; border-color: #434AFA;">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="employee-details">
                            <h6 class="employee-name">All Employees</h6>
                            <p class="employee-meta">Display active positions</p>
                        </div>
                    </div>

                    <!-- Directory List -->
                    @foreach($employees as $employee)
                        @php
                            $initials = collect(explode(' ', $employee->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('');
                        @endphp
                        <div class="employee-card" data-emp-id="{{ $employee->id }}">
                            <div class="employee-avatar-wrapper">
                                @if($employee->profile_picture)
                                    <img src="{{ asset('storage/' . $employee->profile_picture) }}" class="employee-avatar" alt="{{ $employee->name }}" onerror="this.style.display='none'; document.getElementById('initials-{{ $employee->id }}').style.display='flex';">
                                    <div class="employee-avatar" id="initials-{{ $employee->id }}" style="display: none;">{{ strtoupper($initials) }}</div>
                                @else
                                    <div class="employee-avatar">{{ strtoupper($initials) }}</div>
                                @endif
                                <div class="status-indicator status-dot-inactive" id="dot-{{ $employee->id }}"></div>
                            </div>
                            <div class="employee-details">
                                <h6 class="employee-name" title="{{ $employee->name }}">{{ $employee->name }}</h6>
                                <p class="employee-meta" title="{{ $employee->designation ?? 'Employee' }}">{{ $employee->designation ?? 'Employee' }}</p>
                                <span class="employee-status-badge status-badge-inactive" id="badge-{{ $employee->id }}">Offline</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Live Map Interface Panel -->
        <div class="col-12 col-md-8 col-lg-9">
            <div class="map-card position-relative">
                <!-- Map Header Overlay Stats -->
                <div class="map-stats-overlay">
                    <div class="map-stat-item">
                        <span class="map-stat-dot" style="background-color: #10b981;"></span>
                        <span>Active: <span id="stat-active-count">0</span></span>
                    </div>
                    <div class="map-stat-item">
                        <span class="map-stat-dot" style="background-color: #f59e0b;"></span>
                        <span>On Break: <span id="stat-break-count">0</span></span>
                    </div>
                    <div class="map-stat-item">
                        <span class="map-stat-dot" style="background-color: #94a3b8;"></span>
                        <span>Offline: <span id="stat-inactive-count">0</span></span>
                    </div>
                </div>

                <!-- Spinner Overlay -->
                <div id="loadingOverlay" class="position-absolute w-100 h-100 d-none justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 1000;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading map data...</span>
                    </div>
                </div>

                <!-- Ola Maps Container -->
                <div id="map" style="height: 100%; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Ola Maps Web SDK -->
<script src="https://www.unpkg.com/olamaps-web-sdk@latest/dist/olamaps-web-sdk.umd.js"></script>
<link href="https://www.unpkg.com/olamaps-web-sdk@latest/dist/style.css" rel="stylesheet" />

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const API_KEY = 'iLBQa55RFiKpDxyF0h9mf8IEC37Xe4e09CyNwtlT';
        let myMap = null;
        let markers = [];

        // Initialize Ola Maps SDK
        const olaMaps = new OlaMapsSDK.OlaMaps({
            apiKey: API_KEY
        });

        // Initialize Map
        try {
            // Suppress harmless '3d_model' warning from Ola Maps SDK
            const originalError = console.error;
            console.error = function(...args) {
                const message = args.map(arg => {
                    if (arg instanceof Error) return arg.message + ' ' + (arg.stack || '');
                    if (arg && typeof arg === 'object') {
                        try { return JSON.stringify(arg); } catch(e) { return String(arg); }
                    }
                    return String(arg);
                }).join(' ');

                if (message.includes('3d_model') || message.includes('3d_model_data')) return;
                originalError.apply(console, args);
            };

            myMap = await olaMaps.init({
                style: "https://api.olamaps.io/tiles/vector/v1/styles/default-light-standard-mr/style.json",
                container: 'map',
                center: [80.3429, 26.4983],
                zoom: 12,
            });
            
            console.log("Ola Map loaded successfully");
            fetchLocations(); // Initial Fetch

        } catch (error) {
            console.error("Map Init Error:", error);
            document.getElementById('map').innerHTML = `<div class="alert alert-danger m-3">Map Initialization Failed: ${error.message}</div>`;
        }

        // --- Loading Spinner Control ---
        function showLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.add('d-flex');
                overlay.classList.remove('d-none');
            }
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.remove('d-flex');
                overlay.classList.add('d-none');
            }
        }

        // --- Fetch and Render Locations ---
        async function fetchLocations() {
            if (!myMap) return;
            showLoading();

            const employeeId = document.getElementById('employee_id').value;
            const date = document.getElementById('date').value;

            try {
                const response = await fetch(`{{ route('tracking.fetch-locations') }}?employee_id=${employeeId}&date=${date}`);
                const data = await response.json();

                if (data.success) {
                    updateMapMarkers(data.data, !!employeeId);
                    updateSidebarStatuses(data.employee_details);
                }
            } catch (error) {
                console.error("Error fetching locations:", error);
            } finally {
                hideLoading();
            }
        }

        // --- Sidebar Real-time Status Synchronization ---
        function updateSidebarStatuses(details) {
            if (!details) return;
            
            let activeCount = 0;
            let breakCount = 0;
            let inactiveCount = 0;

            const trackedEmployeeIds = Object.keys(details).map(Number);

            // Update employee card UI statuses
            document.querySelectorAll('.employee-card[data-emp-id]').forEach(card => {
                const empId = Number(card.getAttribute('data-emp-id'));
                if (!empId) return; // Skip "All Employees" reset card

                const dot = document.getElementById(`dot-${empId}`);
                const badge = document.getElementById(`badge-${empId}`);

                if (dot && badge) {
                    dot.className = 'status-indicator';
                    badge.className = 'employee-status-badge';

                    if (details[empId]) {
                        const info = details[empId];
                        const currentStatus = info.details.current_status || 'Punched In';

                        if (info.color === '#28a745') {
                            dot.classList.add('status-dot-active');
                            badge.classList.add('status-badge-active');
                            badge.textContent = currentStatus;
                            activeCount++;
                        } else if (info.color === '#ffc107') {
                            dot.classList.add('status-dot-break');
                            badge.classList.add('status-badge-break');
                            badge.textContent = currentStatus;
                            breakCount++;
                        } else {
                            dot.classList.add('status-dot-inactive');
                            badge.classList.add('status-badge-inactive');
                            badge.textContent = currentStatus;
                            inactiveCount++;
                        }
                    } else {
                        dot.classList.add('status-dot-inactive');
                        badge.classList.add('status-badge-inactive');
                        badge.textContent = 'Offline';
                        inactiveCount++;
                    }
                }
            });

            // Update stats counter overlay
            document.getElementById('stat-active-count').textContent = activeCount;
            document.getElementById('stat-break-count').textContent = breakCount;
            document.getElementById('stat-inactive-count').textContent = inactiveCount;
        }

        // --- GPS Filtering, Clustering, and Path Smoothing Helpers ---

        // Haversine formula to compute distance between two coordinates in meters
        function getDistanceInMeters(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // Earth radius in meters
            const phi1 = lat1 * Math.PI / 180;
            const phi2 = lat2 * Math.PI / 180;
            const deltaPhi = (lat2 - lat1) * Math.PI / 180;
            const deltaLambda = (lon2 - lon1) * Math.PI / 180;

            const a = Math.sin(deltaPhi / 2) * Math.sin(deltaPhi / 2) +
                      Math.cos(phi1) * Math.cos(phi2) *
                      Math.sin(deltaLambda / 2) * Math.sin(deltaLambda / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c;
        }

        // Ramer-Douglas-Peucker Simplification to reduce zig-zag points
        function simplifyPath(points, epsilon) {
            if (points.length <= 2) return points;

            let dmax = 0;
            let index = 0;
            const end = points.length - 1;

            for (let i = 1; i < end; i++) {
                const d = getOrthogonalDistance(points[i], points[0], points[end]);
                if (d > dmax) {
                    index = i;
                    dmax = d;
                }
            }

            if (dmax > epsilon) {
                const results1 = simplifyPath(points.slice(0, index + 1), epsilon);
                const results2 = simplifyPath(points.slice(index), epsilon);
                return results1.slice(0, results1.length - 1).concat(results2);
            } else {
                return [points[0], points[end]];
            }
        }

        function getOrthogonalDistance(p, lineStart, lineEnd) {
            const x = p[0], y = p[1];
            const x1 = lineStart[0], y1 = lineStart[1];
            const x2 = lineEnd[0], y2 = lineEnd[1];

            const num = Math.abs((y2 - y1) * x - (x2 - x1) * y + x2 * y1 - y2 * x1);
            const den = Math.sqrt(Math.pow(y2 - y1, 2) + Math.pow(x2 - x1, 2));
            return den === 0 ? 0 : num / den;
        }

        // Main coordinate filter for removing jitter, small movements, and unrealistic speed jumps
        function filterAndSmoothRoute(locations, isSingleEmployee) {
            if (!locations || locations.length === 0) return [];
            if (!isSingleEmployee) return locations;

            // Ensure chronological order
            const sorted = [...locations].sort((a, b) => new Date(a.tracked_at) - new Date(b.tracked_at));
            const filtered = [];
            let prevValid = null;

            sorted.forEach(loc => {
                const lat = parseFloat(loc.latitude);
                const lng = parseFloat(loc.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                if (prevValid) {
                    const prevLat = parseFloat(prevValid.latitude);
                    const prevLng = parseFloat(prevValid.longitude);
                    const dist = getDistanceInMeters(prevLat, prevLng, lat, lng);
                    const timeDiff = (new Date(loc.tracked_at) - new Date(prevValid.tracked_at)) / 1000; // in seconds

                    // 1. Minimum Movement Filter: Skip coordinates if movement is < 50 meters
                    // EXCEPT if more than 5 minutes have passed (to show periodic updates when stationary)
                    if (dist < 50 && timeDiff < 300) {
                        return;
                    }

                    // 2. Unrealistic Jump Detection: Ignore sudden spikes (> 200m within very short time)
                    if (timeDiff > 0) {
                        const speed = dist / timeDiff; // m/s
                        if (speed > 25 && dist > 150) { // Speed greater than 90 km/h with high distance is filtered
                            return;
                        }
                    }
                }

                filtered.push(loc);
                prevValid = loc;
            });

            if (filtered.length <= 2) return filtered;

            // 3. Stationary Centroid Clustering: Merge consecutive points within 20m of each other
            // to entirely eliminate stationary "starburst" jitter web patterns.
            const clustered = [];
            let i = 0;
            while (i < filtered.length) {
                const startPoint = filtered[i];
                let j = i + 1;
                let clusterSumLat = parseFloat(startPoint.latitude);
                let clusterSumLng = parseFloat(startPoint.longitude);
                let clusterCount = 1;

                while (j < filtered.length) {
                    const nextPoint = filtered[j];
                    const dist = getDistanceInMeters(
                        parseFloat(startPoint.latitude),
                        parseFloat(startPoint.longitude),
                        parseFloat(nextPoint.latitude),
                        parseFloat(nextPoint.longitude)
                    );

                    if (dist < 50) {
                        clusterSumLat += parseFloat(nextPoint.latitude);
                        clusterSumLng += parseFloat(nextPoint.longitude);
                        clusterCount++;
                        j++;
                    } else {
                        break;
                    }
                }

                if (clusterCount > 1) {
                    clustered.push({
                        ...startPoint,
                        latitude: (clusterSumLat / clusterCount).toFixed(8),
                        longitude: (clusterSumLng / clusterCount).toFixed(8),
                        tracked_at: filtered[j - 1].tracked_at
                    });
                } else {
                    clustered.push(startPoint);
                }
                i = j;
            }

            return clustered;
        }

        // --- Render Ola Map Markers & Filtered Route Paths ---
        function updateMapMarkers(locations, isSingleEmployee) {
            // Clear existing map markers
            markers.forEach(marker => marker.remove());
            markers = [];

            // Clear existing route line layer and source if they exist
            if (myMap.getLayer('route-line')) {
                myMap.removeLayer('route-line');
            }
            if (myMap.getSource('route-source')) {
                myMap.removeSource('route-source');
            }

            if (!locations || locations.length === 0) return;

            // Clean, filter and cluster points to remove drift and starburst patterns
            const cleanedLocations = filterAndSmoothRoute(locations, isSingleEmployee);
            if (cleanedLocations.length === 0) return;

            let pointsToRender = [];

            if (!isSingleEmployee) {
                // "All Employees" mode: Show ONLY the latest location for each
                const latestByEmployee = {};
                cleanedLocations.forEach(loc => {
                    if (!latestByEmployee[loc.employee_id] || new Date(loc.tracked_at) > new Date(latestByEmployee[loc.employee_id].tracked_at)) {
                        latestByEmployee[loc.employee_id] = loc;
                    }
                });
                pointsToRender = Object.values(latestByEmployee);
            } else {
                // "Single Employee" mode: Show ALL valid points (history path)
                pointsToRender = cleanedLocations;
            }

            let minLng = 180, maxLng = -180, minLat = 90, maxLat = -90;
            let hasValidPoints = false;

            pointsToRender.forEach((loc, index) => {
                const lat = parseFloat(loc.latitude);
                const lng = parseFloat(loc.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                // Update viewport boundary parameters
                if (lng < minLng) minLng = lng;
                if (lng > maxLng) maxLng = lng;
                if (lat < minLat) minLat = lat;
                if (lat > maxLat) maxLat = lat;
                hasValidPoints = true;

                const lngLat = [lng, lat];
                const isLatest = !isSingleEmployee || (index === pointsToRender.length - 1);

                // Build modern custom marker elements
                const container = document.createElement('div');
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.alignItems = 'center';
                container.style.transform = 'translate(-50%, -100%)';
                container.style.cursor = 'pointer';

                const dot = document.createElement('div');
                dot.style.width = isLatest ? '16px' : '10px';
                dot.style.height = isLatest ? '16px' : '10px';
                dot.style.backgroundColor = isLatest ? '#434AFA' : '#6366f1';
                dot.style.borderRadius = '50%';
                dot.style.border = '2px solid white';
                dot.style.boxShadow = '0 2px 6px rgba(0,0,0,0.25)';

                const label = document.createElement('div');
                const timeStr = new Date(loc.tracked_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                label.innerHTML = `
                    <div style="background: white; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 11px; white-space: nowrap; margin-bottom: 4px; font-family: 'Montserrat', sans-serif; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 2px;">${loc.employee.name}</div>
                        <div style="color: #64748b; font-size: 10px;">Updated: <span style="color: #1e293b; font-weight: 600;">${timeStr}</span></div>
                    </div>
                `;
                
                if (isLatest) {
                    container.appendChild(label);
                    container.appendChild(dot);
                    container.style.zIndex = '1000';
                } else {
                    label.style.display = 'none';
                    container.appendChild(label);
                    container.appendChild(dot);
                    container.onmouseenter = () => label.style.display = 'block';
                    container.onmouseleave = () => label.style.display = 'none';
                }

                const marker = olaMaps.addMarker({ element: container, anchor: 'bottom' })
                    .setLngLat(lngLat)
                    .addTo(myMap);

                markers.push(marker);
            });

            // Draw a smooth connecting route path line in Single Employee mode
            if (isSingleEmployee) {
                let coordinates = pointsToRender
                    .map(loc => [parseFloat(loc.longitude), parseFloat(loc.latitude)])
                    .filter(coord => !isNaN(coord[0]) && !isNaN(coord[1]));

                // Apply Ramer-Douglas-Peucker simplification to eliminate zig-zag jitter lines
                if (coordinates.length > 5) {
                    coordinates = simplifyPath(coordinates, 0.00012); // ~13m tolerance
                }

                if (coordinates.length > 1) {
                    myMap.addSource('route-source', {
                        'type': 'geojson',
                        'data': {
                            'type': 'Feature',
                            'properties': {},
                            'geometry': {
                                'type': 'LineString',
                                'coordinates': coordinates
                            }
                        }
                    });

                    myMap.addLayer({
                        'id': 'route-line',
                        'type': 'line',
                        'source': 'route-source',
                        'layout': {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        'paint': {
                            'line-color': '#434AFA',
                            'line-width': 4,
                            'line-opacity': 0.8
                        }
                    });
                }
            }

            // Adjust map viewport boundary to enclose all active markers nicely
            if (hasValidPoints) {
                if (minLng === maxLng) { minLng -= 0.01; maxLng += 0.01; }
                if (minLat === maxLat) { minLat -= 0.01; maxLat += 0.01; }
                myMap.fitBounds([[minLng, minLat], [maxLng, maxLat]], { padding: 60 });
            }
        }

        // --- Sidebar Interactions & Inputs ---
        
        // Search Filter
        document.getElementById('sidebarSearch').addEventListener('keyup', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.employee-card[data-emp-id]').forEach(card => {
                const empId = card.getAttribute('data-emp-id');
                if (empId === '') return; // Skip reset card

                const name = card.querySelector('.employee-name').textContent.toLowerCase();
                const meta = card.querySelector('.employee-meta').textContent.toLowerCase();

                if (name.includes(query) || meta.includes(query)) {
                    card.style.setProperty('display', 'flex', 'important');
                } else {
                    card.style.setProperty('display', 'none', 'important');
                }
            });
        });

        // Date Change
        document.getElementById('sidebarDate').addEventListener('change', function() {
            document.getElementById('date').value = this.value;
            fetchLocations();
        });

        // Click Employee Card
        document.querySelectorAll('.employee-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.employee-card').forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                const empId = this.getAttribute('data-emp-id');
                document.getElementById('employee_id').value = empId;
                fetchLocations();
            });
        });

        // Auto Refresh map positions every 30 seconds
        setInterval(fetchLocations, 30000);
    });
</script>
@endsection
