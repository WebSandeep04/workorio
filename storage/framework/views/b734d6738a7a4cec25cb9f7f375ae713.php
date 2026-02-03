

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
            loadingOverlay.style.setProperty('display', 'flex', 'important');
        }

        function hideLoading() {
            loadingOverlay.style.setProperty('display', 'none', 'important');
        }

        function fetchLocations() {
            showLoading();
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
                .finally(() => hideLoading());
        }

        function updateMap(locations) {
            markersLayer.clearLayers();
            polylineLayer.clearLayers();

            if (locations.length === 0) {
                // optionally show a message or just keep map clear
                return;
            }

            var latlngs = [];
            var bounds = L.latLngBounds();

            locations.forEach(function(location) {
                var lat = parseFloat(location.latitude);
                var lng = parseFloat(location.longitude);
                var latlng = [lat, lng];
                
                latlngs.push(latlng);
                bounds.extend(latlng);

                var popupContent = `
                    <strong>Employee:</strong> ${location.employee.name}<br>
                    <strong>Time:</strong> ${new Date(location.tracked_at).toLocaleTimeString()}<br>
                    <strong>Date:</strong> ${new Date(location.tracked_at).toLocaleDateString()}
                `;

                L.marker(latlng).bindPopup(popupContent).addTo(markersLayer);
            });

            if (latlngs.length > 0) {
                var polyline = L.polyline(latlngs, {color: 'blue'}).addTo(polylineLayer);
                map.fitBounds(bounds);
            }
        }

        // Initial fetch
        fetchLocations();

        // Handle form submission
        document.getElementById('trackingFilterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetchLocations();
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/tracking/index.blade.php ENDPATH**/ ?>