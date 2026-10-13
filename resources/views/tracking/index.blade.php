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
    z-index: 1050;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    font-size: 0.7rem;
    pointer-events: auto;
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

  /* Leaflet custom marker reset */
  .custom-tracking-leaflet-marker {
    background: transparent !important;
    border: none !important;
  }

  /* Vertical Timeline Styles */
  .timeline-container {
    position: relative;
    padding-left: 1.5rem;
    margin-top: 1rem;
    border-left: 2px dashed #cbd5e1;
  }
  
  .timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
  }
  
  .timeline-dot {
    position: absolute;
    left: calc(-1.5rem - 7px);
    top: 3px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #3b82f6;
    border: 2px solid #ffffff;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
  }
  
  .timeline-dot.stoppage {
    background-color: #f97316;
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.15);
  }
  
  .timeline-dot.start {
    background-color: #10b981;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
  }
  
  .timeline-dot.end {
    background-color: #ef4444;
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
  }
  
  .timeline-content {
    background: #ffffff;
    padding: 0.75rem 0.85rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  }
  
  .timeline-time {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 0.2rem;
  }
  
  .timeline-title {
    font-size: 12px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
  }
  
  .timeline-meta {
    font-size: 10px;
    color: #64748b;
    margin-top: 0.2rem;
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
                        @if(!empty($employee->is_tracking))
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
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Live Map Interface Panel -->
        <div class="col-12 col-md-8 col-lg-9">
            <div class="map-card position-relative">
                <!-- Map Content Row -->
                <div class="d-flex flex-row w-100 h-100" style="overflow: hidden;">
                    <!-- Map Container -->
                    <div id="mapContainer" class="position-relative" style="flex: 1; height: 100%; transition: all 0.3s ease;">
                        <!-- Map Canvas Container -->
                        <div id="map" style="height: 100%; width: 100%; position: relative; z-index: 1;"></div>

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
                            <div class="map-stat-item border-start ps-2">
                                <span id="providerModeBadge" class="badge rounded-pill" style="font-size: 10px; font-weight: 600; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;">
                                    <i class="bi bi-shield-check text-success"></i> Free Overview (0 Quota)
                                </span>
                            </div>
                        </div>

                        <!-- Spinner Overlay -->
                        <div id="loadingOverlay" class="position-absolute w-100 h-100 justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 1100; display: none !important;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading map data...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Details Sidebar Panel -->
                    <div id="trackingDetailsPanel" class="d-none border-start bg-light h-100 animate__animated animate__fadeInRight" style="width: 330px; overflow-y: auto; transition: all 0.3s ease; font-family: 'Montserrat', sans-serif; min-width: 330px; z-index: 5;">
                        <div class="p-3 bg-white border-bottom sticky-top shadow-sm d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13px;">
                                    <i class="bi bi-geo-alt-fill text-primary"></i> <span id="panelEmpName">Employee Details</span>
                                </h6>
                                <span class="badge bg-light text-muted border mt-1" id="panelEmpStatusBadge" style="font-size: 10px;">Offline</span>
                            </div>
                            <button type="button" class="btn-close btn-sm" id="btnCloseDetailsPanel" aria-label="Close" style="font-size: 10px;"></button>
                        </div>

                        <!-- Action buttons for Route -->
                        <div class="p-3 bg-white border-bottom d-flex flex-column gap-2">
                            <!-- On-Demand Quota Route Button -->
                            @php
                                $quotaProviderName = $mapConfig['provider'] === 'google' ? 'Google Maps' : ($mapConfig['provider'] === 'ola' ? 'Ola Maps' : 'Detailed Map');
                            @endphp
                            <button id="btnLoadQuotaRoute" type="button" class="btn btn-sm btn-primary d-flex align-items-center justify-content-center gap-2 fw-semibold shadow-sm" style="font-size: 11px; padding: 7px 12px; border-radius: 6px;">
                                <i class="bi bi-map-fill"></i> <span id="quotaRouteBtnText">View Full Route on {{ $quotaProviderName }}</span>
                            </button>

                            <!-- Return to Overview (shown when in quota mode) -->
                            <button id="btnReturnToFree" type="button" class="btn btn-sm btn-light border d-none align-items-center justify-content-center gap-2 text-muted fw-semibold" style="font-size: 11px; padding: 6px 10px; border-radius: 6px;">
                                <i class="bi bi-arrow-left"></i> Return to Overview
                            </button>
                        </div>

                        <!-- Compact Metrics Bar -->
                        <div class="px-3 py-2 bg-white border-bottom d-flex align-items-center gap-3 text-muted" style="font-size: 11px;">
                            <span class="d-flex align-items-center gap-1">
                                <i class="bi bi-speedometer2 text-primary"></i>
                                <span>Distance:</span>
                                <strong id="metricsDistance" class="text-dark" style="font-family: 'Montserrat', sans-serif;">0.00 km</strong>
                            </span>
                            <span class="text-secondary">|</span>
                            <span class="d-flex align-items-center gap-1">
                                <i class="bi bi-pause-circle text-warning"></i>
                                <span>Stays:</span>
                                <strong id="metricsStays" class="text-dark" style="font-family: 'Montserrat', sans-serif;">0</strong>
                            </span>
                        </div>
                        <div class="p-3" id="trackingTimelineContent">
                            <!-- Timeline logs rendered dynamically in JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 100% Free OpenStreetMap Engine (Always loaded for Free Overview) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Quota Provider SDK (Ola or Google) -->
@if(($mapConfig['provider'] ?? '') === 'google')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $mapConfig['api_key'] }}&libraries=marker"></script>
@elseif(($mapConfig['provider'] ?? '') === 'ola')
    <script src="https://www.unpkg.com/olamaps-web-sdk@latest/dist/olamaps-web-sdk.umd.js"></script>
    <link href="https://www.unpkg.com/olamaps-web-sdk@latest/dist/style.css" rel="stylesheet" />
@endif

<!-- Unified TrackingMap Adapter -->
<script src="{{ asset('js/tracking-map.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const quotaMapConfig = @json($mapConfig ?? ['provider' => 'osm', 'api_key' => '', 'usage_id' => null]);
        const freeMapConfig = { provider: 'osm', api_key: '', usage_id: null, container_id: 'map' };

        let currentMode = 'free'; // 'free' (OpenStreetMap) or 'quota' (Ola / Google)
        let quotaRecorded = false;
        let trackingMap = null;
        let rawLocations = [];
        let isSingleEmpActive = false;
        let showFullRoute = false;

        // Helper: Update Provider Badge & Button states in UI
        function updateProviderBadge() {
            const badge = document.getElementById('providerModeBadge');
            const btnReturn = document.getElementById('btnReturnToFree');
            const btnQuota = document.getElementById('btnLoadQuotaRoute');

            if (!badge) return;

            if (currentMode === 'quota') {
                const providerName = quotaMapConfig.provider === 'google' ? 'Google Maps' : (quotaMapConfig.provider === 'ola' ? 'Ola Maps' : 'OpenStreetMap');
                badge.style.background = '#eef2ff';
                badge.style.color = '#434AFA';
                badge.style.border = '1px solid #c7d2fe';
                badge.innerHTML = `<i class="bi bi-gem text-primary"></i> ${providerName} (Route Active)`;

                if (btnReturn) {
                    btnReturn.classList.remove('d-none');
                    btnReturn.classList.add('d-flex');
                }
                if (btnQuota) {
                    btnQuota.classList.add('d-none');
                }
            } else {
                badge.style.background = '#f0fdf4';
                badge.style.color = '#15803d';
                badge.style.border = '1px solid #bbf7d0';
                badge.innerHTML = `<i class="bi bi-shield-check text-success"></i> Free Overview (0 Quota)`;

                if (btnReturn) {
                    btnReturn.classList.remove('d-flex');
                    btnReturn.classList.add('d-none');
                }
                if (btnQuota) {
                    btnQuota.classList.remove('d-none');
                }
            }
        }

        // Initialize Map in 100% FREE mode (OpenStreetMap) on page load
        try {
            trackingMap = new TrackingMap(freeMapConfig);
            await trackingMap.init(26.4983, 80.3429, 12);
            console.log("Free Tracking Map initialized with [OpenStreetMap] engine (0 Quota used)");
            updateProviderBadge();

            // Fetch initial positions
            fetchLocations();
        } catch (error) {
            console.error("Map Init Error:", error);
            document.getElementById('map').innerHTML = `<div class="alert alert-danger m-3">Map Initialization Failed: ${error.message}</div>`;
        }

        // --- Loading Spinner Control ---
        function showLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.style.setProperty('display', 'flex', 'important');
            }
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.style.setProperty('display', 'none', 'important');
            }
        }

        // --- Fetch and Render Locations ---
        async function fetchLocations() {
            if (!trackingMap || !trackingMap.isReady()) return;
            showLoading();

            const employeeId = document.getElementById('employee_id').value;
            const date = document.getElementById('date').value;
            isSingleEmpActive = !!employeeId;

            try {
                const response = await fetch(`{{ route('tracking.fetch-locations') }}?employee_id=${employeeId}&date=${date}`);
                const data = await response.json();

                if (data.success) {
                    rawLocations = data.data || [];
                    updateMapMarkers(rawLocations, isSingleEmpActive);
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

        function getDistanceInMeters(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
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

        function filterAndSmoothRoute(locations, isSingleEmployee) {
            if (!locations || locations.length === 0) return [];
            if (!isSingleEmployee) return locations;

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
                    const timeDiff = (new Date(loc.tracked_at) - new Date(prevValid.tracked_at)) / 1000;

                    if (dist < 50 && timeDiff < 300) {
                        return;
                    }

                    if (timeDiff > 0) {
                        const speed = dist / timeDiff;
                        if (speed > 25 && dist > 150) {
                            return;
                        }
                    }
                }

                filtered.push(loc);
                prevValid = loc;
            });

            if (filtered.length <= 2) return filtered;

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
                        tracked_at: filtered[j - 1].tracked_at,
                        isClusterCentroid: true
                    });
                } else {
                    clustered.push(startPoint);
                }
                i = j;
            }

            return clustered;
        }

        // --- Render Map Markers & Route Paths ---
        function updateMapMarkers(locations, isSingleEmployee) {
            trackingMap.clearMarkers();
            trackingMap.clearRoute();

            const detailsPanel = document.getElementById('trackingDetailsPanel');

            if (!locations || locations.length === 0) {
                if (isSingleEmployee && detailsPanel) {
                    detailsPanel.classList.remove('d-none');
                    const timelineContainer = document.getElementById('trackingTimelineContent');
                    if (timelineContainer) {
                        timelineContainer.innerHTML = `
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-geo-alt-fill fs-3 d-block mb-2 text-secondary"></i>
                                <span class="small" style="font-size:11px;">No GPS entries recorded today</span>
                            </div>`;
                    }
                } else if (detailsPanel) {
                    detailsPanel.classList.add('d-none');
                }
                return;
            }

            const cleanedLocations = filterAndSmoothRoute(locations, isSingleEmployee);
            if (cleanedLocations.length === 0) {
                if (detailsPanel && !isSingleEmployee) detailsPanel.classList.add('d-none');
                return;
            }

            let pointsToRender = [];

            if (!isSingleEmployee) {
                const latestByEmployee = {};
                cleanedLocations.forEach(loc => {
                    if (!latestByEmployee[loc.employee_id] || new Date(loc.tracked_at) > new Date(latestByEmployee[loc.employee_id].tracked_at)) {
                        latestByEmployee[loc.employee_id] = loc;
                    }
                });
                pointsToRender = Object.values(latestByEmployee);
            } else {
                if (!showFullRoute) {
                    // Show ONLY current location (latest node) when employee is selected from left sidebar
                    const latestLoc = cleanedLocations[cleanedLocations.length - 1];
                    pointsToRender = [latestLoc];
                } else {
                    // Show all breadcrumb points along the route
                    pointsToRender = cleanedLocations;
                }
            }

            let minLng = 180, maxLng = -180, minLat = 90, maxLat = -90;
            let hasValidPoints = false;

            pointsToRender.forEach((loc, index) => {
                const lat = parseFloat(loc.latitude);
                const lng = parseFloat(loc.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                if (lng < minLng) minLng = lng;
                if (lng > maxLng) maxLng = lng;
                if (lat < minLat) minLat = lat;
                if (lat > maxLat) maxLat = lat;
                hasValidPoints = true;

                const isLatest = !isSingleEmployee || !showFullRoute || (index === pointsToRender.length - 1);

                // Build modern custom marker DOM
                const container = document.createElement('div');
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.alignItems = 'center';
                container.style.transform = 'translate(-50%, -100%)';
                container.style.cursor = 'pointer';

                // Click marker to focus and select employee in sidebar
                container.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const targetCard = document.querySelector(`.employee-card[data-emp-id="${loc.employee_id}"]`);
                    if (targetCard) targetCard.click();
                });

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

                trackingMap.addCustomMarker(lat, lng, container);
            });

            // Single Employee View: Route & Navigation setup
            if (isSingleEmployee) {
                if (detailsPanel) detailsPanel.classList.remove('d-none');

                // Update Employee details in the header
                const empId = document.getElementById('employee_id').value;
                const activeCard = document.querySelector(`.employee-card[data-emp-id="${empId}"]`);
                if (activeCard) {
                    const empName = activeCard.querySelector('.employee-name')?.textContent || 'Employee';
                    const empBadge = activeCard.querySelector('.employee-status-badge');
                    document.getElementById('panelEmpName').textContent = empName;
                    const panelBadge = document.getElementById('panelEmpStatusBadge');
                    if (panelBadge && empBadge) {
                        panelBadge.className = empBadge.className;
                        panelBadge.textContent = empBadge.textContent;
                    }
                }

                // Map camera positioning (focus / fitBounds)
                if (pointsToRender.length > 0) {
                    if (!showFullRoute) {
                        // Smoothly focus/fly to employee's single current location at street-level zoom (16)
                        const latestLoc = pointsToRender[pointsToRender.length - 1];
                        const lat = parseFloat(latestLoc.latitude);
                        const lng = parseFloat(latestLoc.longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            trackingMap.flyTo(lat, lng, 16);
                        }
                    } else {
                        // When viewing full route, fit bounds so entire route journey is framed
                        if (cleanedLocations.length > 1) {
                            let rMinLng = 180, rMaxLng = -180, rMinLat = 90, rMaxLat = -90;
                            cleanedLocations.forEach(l => {
                                const lat = parseFloat(l.latitude);
                                const lng = parseFloat(l.longitude);
                                if (!isNaN(lat) && !isNaN(lng)) {
                                    if (lng < rMinLng) rMinLng = lng;
                                    if (lng > rMaxLng) rMaxLng = lng;
                                    if (lat < rMinLat) rMinLat = lat;
                                    if (lat > rMaxLat) rMaxLat = lat;
                                }
                            });
                            trackingMap.fitBounds(rMinLng, rMinLat, rMaxLng, rMaxLat, 90, 15);
                        } else if (cleanedLocations.length === 1) {
                            trackingMap.flyTo(parseFloat(cleanedLocations[0].latitude), parseFloat(cleanedLocations[0].longitude), 15);
                        }
                    }
                }

                // Draw route line ONLY if full route is active
                if (showFullRoute && cleanedLocations.length > 1) {
                    let coordinates = cleanedLocations
                        .map(loc => [parseFloat(loc.longitude), parseFloat(loc.latitude)])
                        .filter(coord => !isNaN(coord[0]) && !isNaN(coord[1]));

                    if (coordinates.length > 5) {
                        coordinates = simplifyPath(coordinates, 0.00012);
                    }

                    if (coordinates.length > 1) {
                        trackingMap.drawRoute(coordinates);
                    }
                } else {
                    trackingMap.clearRoute();
                }

                // Calculate Distance & Stays dynamically (from all day's locations)
                let totalDistanceMeters = 0;
                let totalStaysCount = 0;

                for (let idx = 1; idx < cleanedLocations.length; idx++) {
                    const p1 = cleanedLocations[idx - 1];
                    const p2 = cleanedLocations[idx];
                    const dist = getDistanceInMeters(
                        parseFloat(p1.latitude), parseFloat(p1.longitude),
                        parseFloat(p2.latitude), parseFloat(p2.longitude)
                    );
                    totalDistanceMeters += dist;
                }

                cleanedLocations.forEach(loc => {
                    if (loc.isClusterCentroid) {
                        totalStaysCount++;
                    }
                });

                const totalKM = (totalDistanceMeters / 1000).toFixed(2);
                const distanceEl = document.getElementById('metricsDistance');
                const staysEl = document.getElementById('metricsStays');
                if (distanceEl) distanceEl.textContent = `${totalKM} km`;
                if (staysEl) staysEl.textContent = totalStaysCount;

                // Render Timeline (from all day's locations)
                let timelineHtml = '<div class="timeline-container">';

                if (cleanedLocations.length === 0) {
                    timelineHtml += `
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-geo-alt-fill fs-3 d-block mb-2"></i>
                            <span class="small" style="font-size:11px;">No logs recorded for today</span>
                        </div>`;
                } else {
                    const firstLoc = cleanedLocations[0];
                    const startTimeStr = new Date(firstLoc.tracked_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    timelineHtml += `
                        <div class="timeline-item">
                            <div class="timeline-dot start"></div>
                            <div class="timeline-content">
                                <div class="timeline-time">${startTimeStr}</div>
                                <h6 class="timeline-title">Track Started</h6>
                                <div class="timeline-meta" style="font-size: 9px;">Started today's live journey</div>
                            </div>
                        </div>`;

                    cleanedLocations.slice(1, -1).forEach(loc => {
                        const timeStr = new Date(loc.tracked_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        const isStop = loc.isClusterCentroid || false;
                        
                        timelineHtml += `
                            <div class="timeline-item">
                                <div class="timeline-dot ${isStop ? 'stoppage' : ''}"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">${timeStr}</div>
                                    <h6 class="timeline-title">${isStop ? '<span class="text-warning"><i class="bi bi-pause-circle-fill"></i> Stoppage / Stop</span>' : '<span class="text-primary"><i class="bi bi-arrow-right-circle-fill"></i> Location Logged</span>'}</h6>
                                    <div class="timeline-meta" style="font-size: 9px;">Lat: ${parseFloat(loc.latitude).toFixed(5)}, Lng: ${parseFloat(loc.longitude).toFixed(5)}</div>
                                </div>
                            </div>`;
                    });

                    if (cleanedLocations.length > 1) {
                        const lastLoc = cleanedLocations[cleanedLocations.length - 1];
                        const endTimeStr = new Date(lastLoc.tracked_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        timelineHtml += `
                            <div class="timeline-item">
                                <div class="timeline-dot end"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">${endTimeStr}</div>
                                    <h6 class="timeline-title">Latest Location</h6>
                                    <div class="timeline-meta" style="font-size: 9px;">Last active location node</div>
                                </div>
                            </div>`;
                    }
                }

                timelineHtml += '</div>';
                const containerEl = document.getElementById('trackingTimelineContent');
                if (containerEl) containerEl.innerHTML = timelineHtml;
            } else {
                if (detailsPanel) detailsPanel.classList.add('d-none');
            }

            // Adjust map viewport boundary in All Employees mode
            if (hasValidPoints && !isSingleEmployee) {
                trackingMap.fitBounds(minLng, minLat, maxLng, maxLat, 60);
            }

            // Invalidate size immediately and after panel transition so map spans full 100% width
            if (trackingMap) {
                setTimeout(() => trackingMap.invalidateSize(), 50);
                setTimeout(() => trackingMap.invalidateSize(), 350);
            }
        }

        // --- Button Handlers for On-Demand Quota Route & Navigation ---

        // 1. Switch to Quota / Premium Route View (Ola / Google)
        document.getElementById('btnLoadQuotaRoute').addEventListener('click', async function() {
            showLoading();

            try {
                showFullRoute = true;

                if (currentMode !== 'quota') {
                    currentMode = 'quota';

                    let centerLat = 26.4983, centerLng = 80.3429;
                    if (rawLocations.length > 0) {
                        const latest = rawLocations[rawLocations.length - 1];
                        if (latest.latitude && latest.longitude) {
                            centerLat = parseFloat(latest.latitude);
                            centerLng = parseFloat(latest.longitude);
                        }
                    }

                    await trackingMap.switchProvider(quotaMapConfig, centerLat, centerLng, 15);
                    console.log(`Switched to quota provider [${quotaMapConfig.provider}] at zoom 15`);

                    // Deduct / record quota ONLY ONCE when user activates quota view
                    if (!quotaRecorded) {
                        trackingMap.recordUsage("{{ route('tracking.map-loaded') }}", "{{ csrf_token() }}");
                        quotaRecorded = true;
                    }
                }

                updateProviderBadge();
                updateMapMarkers(rawLocations, isSingleEmpActive);
                
                setTimeout(() => {
                    if (trackingMap) trackingMap.invalidateSize();
                }, 100);
            } catch (err) {
                console.error("Failed to switch to quota map:", err);
                alert("Could not load " + (quotaMapConfig.provider || 'quota map') + ": " + err.message);
            } finally {
                hideLoading();
            }
        });

        // 2. Switch Back to Free Overview (All Employees)
        document.getElementById('btnReturnToFree').addEventListener('click', async function() {
            showLoading();

            try {
                showFullRoute = false;
                if (currentMode !== 'free') {
                    currentMode = 'free';
                    await trackingMap.switchProvider(freeMapConfig, 26.4983, 80.3429, 12);
                    console.log("Switched back to Free OpenStreetMap mode");
                    updateProviderBadge();
                }

                // Reset to "All Employees" selection
                const resetCard = document.querySelector('.employee-card[data-emp-id=""]');
                if (resetCard) {
                    resetCard.click();
                } else {
                    document.getElementById('employee_id').value = '';
                    fetchLocations();
                }

                setTimeout(() => {
                    if (trackingMap) trackingMap.invalidateSize();
                }, 100);
                setTimeout(() => {
                    if (trackingMap) trackingMap.invalidateSize();
                }, 350);
            } catch (err) {
                console.error("Failed to switch back to free map:", err);
            } finally {
                hideLoading();
            }
        });

        // 3. Close Details Panel
        document.getElementById('btnCloseDetailsPanel').addEventListener('click', function() {
            showFullRoute = false;
            const resetCard = document.querySelector('.employee-card[data-emp-id=""]');
            if (resetCard) resetCard.click();
        });

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
            card.addEventListener('click', async function() {
                document.querySelectorAll('.employee-card').forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                const empId = this.getAttribute('data-emp-id');
                document.getElementById('employee_id').value = empId;

                showFullRoute = false; // Always show ONLY current location on initial selection

                // Return to free mode if in quota mode
                if (currentMode === 'quota') {
                    currentMode = 'free';
                    await trackingMap.switchProvider(freeMapConfig, 26.4983, 80.3429, 12);
                    updateProviderBadge();
                }

                fetchLocations();

                // Trigger map resize as sidebar panel opens or closes
                if (trackingMap) {
                    setTimeout(() => trackingMap.invalidateSize(), 50);
                    setTimeout(() => trackingMap.invalidateSize(), 350);
                }
            });
        });

        // Auto-detect container resizing (e.g. sidebar open/close, window resize)
        if (window.ResizeObserver) {
            const resizeObs = new ResizeObserver(() => {
                if (trackingMap && trackingMap.isReady()) {
                    trackingMap.invalidateSize();
                }
            });
            const mapContainerEl = document.getElementById('mapContainer');
            if (mapContainerEl) resizeObs.observe(mapContainerEl);
        }

        // Auto Refresh map positions every 30 seconds
        setInterval(fetchLocations, 30000);
    });
</script>
@endsection

