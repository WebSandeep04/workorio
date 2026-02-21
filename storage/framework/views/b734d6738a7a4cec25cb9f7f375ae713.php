

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

<!-- Ola Maps Web SDK (The working one from test file) -->
<script src="https://www.unpkg.com/olamaps-web-sdk@latest/dist/olamaps-web-sdk.umd.js"></script>
<link href="https://www.unpkg.com/olamaps-web-sdk@latest/dist/style.css" rel="stylesheet" />

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const API_KEY = 'iLBQa55RFiKpDxyF0h9mf8IEC37Xe4e09CyNwtlT'; // Verified Key
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
                if (args[0] && typeof args[0] === 'string' && args[0].includes('Source layer "3d_model"')) return;
                originalError.apply(console, args);
            };

            myMap = await olaMaps.init({
                style: "https://api.olamaps.io/tiles/vector/v1/styles/default-light-standard-mr/style.json", // Verified Style
                container: 'map',
                center: [80.3429, 26.4983], // Your requested center
                zoom: 12,
            });
            
            console.log("Ola Map loaded successfully (Warnings suppressed)");
            fetchLocations(); // Initial Fetch

        } catch (error) {
            console.error("Map Init Error:", error);
            document.getElementById('map').innerHTML = `<div class="alert alert-danger m-3">Map Failed: ${error.message}</div>`;
        }

        // --- Data Fetching Logic ---

        function showLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.style.setProperty('display', 'flex', 'important');
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.style.setProperty('display', 'none', 'important');
        }

        async function fetchLocations() {
            if (!myMap) return;
            // showLoading(); 

            const employeeId = document.getElementById('employee_id').value;
            const date = document.getElementById('date').value;

            try {
                const response = await fetch(`<?php echo e(route('tracking.fetch-locations')); ?>?employee_id=${employeeId}&date=${date}`);
                const data = await response.json();

                if (data.success) {
                    updateMapMarkers(data.data, !!employeeId);
                }
            } catch (error) {
                console.error("Error fetching locations:", error);
            } finally {
                // hideLoading();
            }
        }

        function updateMapMarkers(locations, isSingleEmployee) {
            // Clear existing markers
            markers.forEach(marker => marker.remove());
            markers = [];

            if (!locations || locations.length === 0) return;

            let pointsToRender = [];

            if (!isSingleEmployee) {
                // "All Employees" mode: Show ONLY the latest location for each
                const latestByEmployee = {};
                locations.forEach(loc => {
                    // Assuming locations are sorted by time, or we check timestamps
                    // We'll replace any existing entry with a newer one
                    if (!latestByEmployee[loc.employee_id] || new Date(loc.tracked_at) > new Date(latestByEmployee[loc.employee_id].tracked_at)) {
                        latestByEmployee[loc.employee_id] = loc;
                    }
                });
                pointsToRender = Object.values(latestByEmployee);
            } else {
                // "Single Employee" mode: Show ALL points (history path)
                pointsToRender = locations;
            }

            let minLng = 180, maxLng = -180, minLat = 90, maxLat = -90;
            let hasValidPoints = false;

            pointsToRender.forEach((loc, index) => {
                const lat = parseFloat(loc.latitude);
                const lng = parseFloat(loc.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                // Update bounds logic
                if (lng < minLng) minLng = lng;
                if (lng > maxLng) maxLng = lng;
                if (lat < minLat) minLat = lat;
                if (lat > maxLat) maxLat = lat;
                hasValidPoints = true;

                const lngLat = [lng, lat];
                
                // Determine if this point should be highlighted (Latest)
                // In "All Employees" mode, ALL points are "latest" essentially.
                // In "Single Employee" mode, only the last point in the array is "latest".
                let isLatest = !isSingleEmployee || (index === pointsToRender.length - 1);

                // Create container for marker + label
                const container = document.createElement('div');
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.alignItems = 'center';
                container.style.transform = 'translate(-50%, -100%)'; // Anchor bottom-center
                container.style.cursor = 'pointer';

                // Dot styling
                const dot = document.createElement('div');
                dot.style.width = isLatest ? '16px' : '10px';
                dot.style.height = isLatest ? '16px' : '10px';
                dot.style.backgroundColor = isLatest ? '#dc3545' : '#0d6efd';
                dot.style.borderRadius = '50%';
                dot.style.border = '2px solid white';
                dot.style.boxShadow = '0 2px 4px rgba(0,0,0,0.3)';

                // Label (Name + Time)
                const label = document.createElement('div');
                const timeStr = new Date(loc.tracked_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                label.innerHTML = `
                    <div style="background:white; padding:4px 8px; border-radius:6px; border:1px solid #ccc; font-size:11px; white-space:nowrap; margin-bottom:4px; font-family:sans-serif; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                        <div style="font-weight:bold; color:#333; margin-bottom:2px;">${loc.employee.name}</div>
                        <div style="color:#666;">Last Updated: <span style="color:#000; font-weight:500;">${timeStr}</span></div>
                    </div>
                `;
                
                // Show label rules:
                // If isSingleEmployee: Show label only on the LATEST point. Hover for others.
                // If !isSingleEmployee (All): Show label for EVERY point (since they are all latest).
                
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

            // Fit Bounds
            if (hasValidPoints) {
                if (minLng === maxLng) { minLng -= 0.01; maxLng += 0.01; }
                if (minLat === maxLat) { minLat -= 0.01; maxLat += 0.01; }
                myMap.fitBounds([[minLng, minLat], [maxLng, maxLat]], { padding: 50 });
            }
        }

        // Form Submit
        document.getElementById('trackingFilterForm').addEventListener('submit', (e) => {
            e.preventDefault();
            fetchLocations();
        });

        // Auto Refresh every 30s
        setInterval(fetchLocations, 30000);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/tracking/index.blade.php ENDPATH**/ ?>