<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Workorio - App Download</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #ffffff;
            color: #202124;
        }
        .header {
            padding: 16px 24px;
            border-bottom: 1px solid #dadce0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header img {
            height: 32px;
        }
        .app-header {
            display: flex;
            align-items: flex-start;
            margin-top: 24px;
            margin-bottom: 24px;
        }
        .app-icon {
            width: 72px;
            height: 72px;
            border-radius: 15px;
            box-shadow: 0 1px 2px rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
            margin-right: 24px;
            object-fit: contain; /* Changed from cover */
            background-color: white; /* Ensure transparency looks good */
            padding: 2px; /* Slight padding inside the box */
        }
        .app-title {
            font-family: 'Google Sans', sans-serif;
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 2px;
            color: #202124;
        }
        .developer-name {
            color: #01875f;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 12px;
            text-decoration: none;
        }
        .install-btn {
            background-color: #434afa;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-family: 'Google Sans', sans-serif;
            font-weight: 500;
            font-size: 14px;
            width: 100%;
            max-width: 300px;
        }
        .install-btn:hover {
            background-color: #666bf5ff;
            color: white;
        }
        .stats-row {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-right: 24px;
            margin-right: 24px;
            border-right: 1px solid #dadce0;
            min-width: 60px;
        }
        .stat-item:last-child {
            border-right: none;
        }
        .stat-value {
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .stat-label {
            font-size: 12px;
            color: #5f6368;
            margin-top: 4px;
        }
        .screenshot-scroller {
            display: flex;
            overflow-x: auto;
            gap: 12px;
            padding-bottom: 16px;
            margin-bottom: 24px;
            /* Hide scrollbar */
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .screenshot-scroller::-webkit-scrollbar {
            display: none;
        }
        .screenshot-img {
            height: 300px;
            width: auto;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(60,64,67,0.3);
            flex-shrink: 0;
            background-color: #f1f3f4; /* Placeholder color */
        }
        .scroll-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid #dadce0;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            box-shadow: 0 1px 2px rgba(60,64,67,0.3);
        }
        .scroll-left { left: -25px; }
        .scroll-right { right: -25px; }
        .description {
            color: #5f6368;
            font-size: 14px;
            line-height: 20px;
            margin-bottom: 24px;
        }
        .section-title {
            font-family: 'Google Sans', sans-serif;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .rating-star {
            font-size: 12px;
            margin-left: 2px;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .app-header {
                flex-direction: column;
            }
            .app-icon {
                margin-bottom: 16px;
            }
            .install-btn {
                width: 100%;
                max-width: none;
            }
        }
    </style>
</head>
<body>

<div class="header d-none d-md-flex">
    <div class="d-flex align-items-center">
        <!-- Using the same logo as login page if available -->
        <img src="/img/logoblack.png" alt="Workorio Logo">
    </div>
    <!-- <div>
        <i class="bi bi-search fs-5 text-secondary me-3"></i>
        <i class="bi bi-question-circle fs-5 text-secondary me-3"></i>
        <button class="btn btn-primary btn-sm" style="background-color: #1a73e8; border:none;">Sign in</button>
    </div> -->
</div>

<div class="container" style="max-width: 800px; margin-top: 20px;">
    
    <!-- Mobile Back Button Style -->
    <div class="d-md-none mb-3">
        <a href="/" class="text-decoration-none text-dark"><i class="bi bi-arrow-left fs-4"></i></a>
    </div>

    <!-- App Header -->
    <div class="app-header">
        <img src="/img/icon.png" onerror="this.src='/img/w.png'" class="app-icon" alt="App Icon">
        <div class="flex-grow-1">
            <h1 class="app-title">Workorio</h1>
            <a href="#" class="developer-name">Triserv 360 Business Solutions</a>
            <div class="d-none d-md-block" style="font-size: 12px; color: #5f6368;">Contains ads &bull; In-app purchases</div>
            
            <div class="mt-3 d-none d-md-block">
                <button class="install-btn" onclick="document.getElementById('downloadLink').click()">Download</button>
                <div class="mt-2 text-success small"><i class="bi bi-check-circle-fill me-1"></i> Verified by Triserv 360 Business Solutions</div>
            </div>
        </div>
    </div>

    <!-- Mobile Install Button & Stats -->
    <div class="d-md-none mb-4">
        <div class="stats-row justify-content-between px-2">
            <div class="stat-item text-center border-0">
                <div class="stat-value">4.8 <i class="bi bi-star-fill rating-star ms-1"></i></div>
                <div class="stat-label">2K reviews</div>
            </div>
            <div class="stat-item text-center border-0">
                 <div class="stat-value">15</div>
                <div class="stat-label">Downloads</div>
            </div>
             <div class="stat-item text-center border-0">
                 <div class="stat-value">E</div>
                <div class="stat-label">Everyone</div>
            </div>
        </div>
        <button class="install-btn" onclick="document.getElementById('downloadLink').click()">Download</button>
    </div>

    <!-- Stats Row (Desktop) -->
    <div class="stats-row d-none d-md-flex">
        <div class="stat-item">
            <div class="stat-value">4.8 <i class="bi bi-star-fill rating-star ms-1"></i></div>
            <div class="stat-label">2K reviews</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">15</div>
            <div class="stat-label">Downloads</div>
        </div>
        <div class="stat-item">
             <div class="stat-value"><div style="background:#000;color:white;font-size:10px;padding:1px 4px;border-radius:2px;font-weight:bold;">E</div></div>
            <div class="stat-label">Everyone</div>
        </div>
    </div>

    <!-- Screenshots -->
    <div style="position: relative;">
        <button class="scroll-btn scroll-left d-none d-md-flex" onclick="scrollScreenshots(-1)"><i class="bi bi-chevron-left"></i></button>
        <div class="screenshot-scroller" id="scroller">
            <!-- Using placeholder images or actual screenshots if available -->
            <img src="/img/appScreenshot/w1.jpeg" class="screenshot-img" alt="Workorio Dashboard">
            <img src="/img/appScreenshot/w2.jpeg" class="screenshot-img" alt="Workorio Attendance">
            <img src="/img/appScreenshot/w3.jpeg" class="screenshot-img" alt="Workorio Leads">
            <img src="/img/appScreenshot/w4.jpeg" class="screenshot-img" alt="Workorio Map">
            <img src="/img/appScreenshot/w5.jpeg" class="screenshot-img" alt="Workorio Reports">
            <img src="/img/appScreenshot/w6.jpeg" class="screenshot-img" alt="Workorio App 6">
            <img src="/img/appScreenshot/w7.jpeg" class="screenshot-img" alt="Workorio App 7">
            <img src="/img/appScreenshot/w8.jpeg" class="screenshot-img" alt="Workorio App 8">
            <img src="/img/appScreenshot/w9.jpeg" class="screenshot-img" alt="Workorio App 9">
            <img src="/img/appScreenshot/w10.jpeg" class="screenshot-img" alt="Workorio App 10">
            <img src="/img/appScreenshot/w11.jpeg" class="screenshot-img" alt="Workorio App 11">
            <img src="/img/appScreenshot/w12.jpeg" class="screenshot-img" alt="Workorio App 12">
            <img src="/img/appScreenshot/w13.jpeg" class="screenshot-img" alt="Workorio App 13">
            <img src="/img/appScreenshot/w14.jpeg" class="screenshot-img" alt="Workorio App 14">
        </div>
        <button class="scroll-btn scroll-right d-none d-md-flex" onclick="scrollScreenshots(1)"><i class="bi bi-chevron-right"></i></button>
    </div>

    <!-- Description -->
    <div class="mb-4">
        <div class="section-title">
            About this app
            <i class="bi bi-arrow-right text-secondary fs-5"></i>
        </div>
        <div class="description">
            The ultimate Business Management Solution for modern teams.
            <br><br>
            Workorio streamlines your daily operations with powerful tools for:
            <ul>
                <li>Lead Management & Sales Tracking</li>
                <li>Employee Attendance & Field Tracking</li>
                <li>Customer Subscriptions & Billing</li>
                <li>Task Management & Workflow Automation</li>
            </ul>
            Manage your business from anywhere with our secure, cloud-based platform.
        </div>
    </div>

    <!-- Data Safety -->
    <div class="mb-4">
        <div class="section-title">
            Data safety
            <i class="bi bi-arrow-right text-secondary fs-5"></i>
        </div>
        <div class="description">
            Safety starts with understanding how developers collect and share your data. Data privacy and security practices may vary based on your use, region, and age.
            <div class="card mt-3 p-3 border rounded-3">
                 <div class="d-flex align-items-start mb-2">
                     <i class="bi bi-shield-lock fs-5 me-3 text-secondary"></i>
                     <div>No data shared with third parties</div>
                 </div>
                 <div class="d-flex align-items-start">
                     <i class="bi bi-cloud-arrow-up fs-5 me-3 text-secondary"></i>
                     <div>Data collected is encrypted in transit</div>
                 </div>
            </div>
        </div>
    </div>

</div>

<!-- Hidden Download Link (Replace with actual APK path) -->
<a id="downloadLink" href="<?php echo e(asset('apk/app-release.apk')); ?>" style="display:none;" download></a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function scrollScreenshots(direction) {
        const scroller = document.getElementById('scroller');
        const scrollAmount = 300; // Approx 1.5 images width
        scroller.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
</script>
</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/download.blade.php ENDPATH**/ ?>