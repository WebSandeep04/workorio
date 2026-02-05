

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- <h1 class="page-title">Employee Tracking</h1> -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="trackingFilterForm" class="row align-items-end">
                        <div class="col-md-4">
                            <label for="employee_id" class="form-label">Select Employee</label>
                            <select class="form-select" id="employee_id" name="employee_id">
                                <option value="">All Employees</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="date" class="form-label">Select Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card position-relative">
                <div id="loadingOverlay" class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 1000; display: none !important;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 75vh; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<?php $__env->startPush('styles'); ?>
<style>
    .pulse-marker {
        background: #28a745;
        border-radius: 50%;
        height: 15px;
        width: 15px;
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 1);
        transform: scale(1);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }

    .last-updated-badge {
        font-size: 0.8rem;
        padding: 2px 8px;
        border-radius: 12px;
        background-color: #f1f3f5;
        color: #495057;
        display: inline-block;
        margin-top: 8px;
        border: 1px solid #dee2e6;
    }

    .current-location-marker {
        color: #28a745;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 4px;
    }
    
    .tracking-popup {
        padding: 5px;
        min-width: 180px;
    }
</style>
<?php $__env->stopPush(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the map
        var map = L.map('map').setView([20.5937, 78.9629], 5); // Default center (India)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var markersLayer = L.layerGroup().addTo(map);
        var polylineLayer = L.layerGroup().addTo(map);
        var loadingOverlay = document.getElementById('loadingOverlay');

        function showLoading() {
            if (loadingOverlay) loadingOverlay.style.setProperty('display', 'flex', 'important');
        }

        function hideLoading() {
            if (loadingOverlay) loadingOverlay.style.setProperty('display', 'none', 'important');
        }

        function fetchLocations(isAutoRefresh = false) {
            if (!isAutoRefresh) showLoading();
            
            var employeeId = document.getElementById('employee_id').value;
            var date = document.getElementById('date').value;

            fetch(`<?php echo e(route('tracking.fetch-locations')); ?>?employee_id=${employeeId}&date=${date}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateMap(data.data);
                    }
                })
                .catch(error => console.error('Error fetching locations:', error))
                .finally(() => {
                    if (!isAutoRefresh) hideLoading();
                });
        }

        function formatTime(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function getRelativeTime(dateStr) {
            const now = new Date();
            const past = new Date(dateStr);
            const diffMs = now - past;
            const diffMins = Math.floor(diffMs / 60000);
            
            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return diffMins + 'm ago';
            const diffHours = Math.floor(diffMins / 60);
            if (diffHours < 24) return diffHours + 'h ago';
            return past.toLocaleDateString();
        }

        function updateMap(locations) {
            markersLayer.clearLayers();
            polylineLayer.clearLayers();

            if (locations.length === 0) return;

            // Group by employee
            var employeeTracks = {};
            locations.forEach(function(loc) {
                if (!employeeTracks[loc.employee_id]) {
                    employeeTracks[loc.employee_id] = [];
                }
                employeeTracks[loc.employee_id].push(loc);
            });

            var bounds = L.latLngBounds();
            var hasPoints = false;

            Object.keys(employeeTracks).forEach(function(empId) {
                var track = employeeTracks[empId];
                var latlngs = [];
                
                track.forEach(function(loc, index) {
                    var lat = parseFloat(loc.latitude);
                    var lng = parseFloat(loc.longitude);
                    var latlng = [lat, lng];
                    latlngs.push(latlng);
                    bounds.extend(latlng);
                    hasPoints = true;

                    var isLatest = (index === track.length - 1);
                    var date = new Date(loc.tracked_at);
                    
                    var popupContent = `
                        <div class="tracking-popup">
                            <div style="border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 8px;">
                                <strong style="font-size: 1.1em; color: #333;">${loc.employee.name}</strong>
                            </div>
                            ${isLatest ? '<div class="current-location-marker"><i class="bi bi-geo-alt-fill"></i> Current Position</div>' : ''}
                            <div style="color: #666; font-size: 0.9em;">
                                <i class="bi bi-clock"></i> ${formatTime(loc.tracked_at)} | <i class="bi bi-calendar3"></i> ${date.toLocaleDateString()}
                            </div>
                            <div class="last-updated-badge">
                                <i class="bi bi-arrow-repeat"></i> Updated ${getRelativeTime(loc.tracked_at)}
                            </div>
                        </div>
                    `;

                    if (isLatest) {
                        var pulseIcon = L.divIcon({
                            className: 'custom-div-icon',
                            html: '<div class="pulse-marker"></div>',
                            iconSize: [15, 15],
                            iconAnchor: [7, 7]
                        });
                        
                        var marker = L.marker(latlng, { icon: pulseIcon }).addTo(markersLayer);
                        marker.bindPopup(popupContent);
                        
                        // Auto open popup if single employee view
                        if (Object.keys(employeeTracks).length === 1) {
                            marker.openPopup();
                        }
                    } else {
                        L.circleMarker(latlng, {
                            radius: 4,
                            color: '#434afa',
                            fillColor: '#434afa',
                            fillOpacity: 0.6,
                            weight: 1
                        }).bindPopup(popupContent).addTo(markersLayer);
                    }
                });

                if (latlngs.length > 1) {
                    L.polyline(latlngs, {
                        color: '#434afa',
                        weight: 2,
                        opacity: 0.6,
                        dashArray: '5, 10'
                    }).addTo(polylineLayer);
                }
            });

            if (hasPoints) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }

        // Initial fetch
        fetchLocations();

        // Handle form submission
        document.getElementById('trackingFilterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetchLocations();
        });

        // Auto-refresh every 30 seconds
        setInterval(() => fetchLocations(true), 30000);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/tracking/index.blade.php ENDPATH**/ ?>