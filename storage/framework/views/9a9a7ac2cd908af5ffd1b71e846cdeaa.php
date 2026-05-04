<?php
    $connectionName = \Illuminate\Support\Facades\DB::getDefaultConnection();
    $isMasterConnection = $connectionName === 'mysql';
    $isLoggedIn = auth()->check();
    $user = auth()->user();
    
    if (!$isLoggedIn) {
        $isLoggedIn = session()->has('user_id');
        $user = $isLoggedIn ? (object) [
            'role_id' => session('user_role'),
            'role' => null
        ] : null;
    }
    
    $roleId = $isLoggedIn ? $user->role_id : null;
    $isSuperAdmin = $isLoggedIn && 
                   $roleId == 3 && 
                   $isMasterConnection;

    $roleName = null;
    if ($isLoggedIn && !$isMasterConnection) {
        $roleName = optional($user->role)->role_name;
    }
?>
<!-- Mobile Header Bar -->
<div class="mobile-header-bar d-md-none">
    <div class="mobile-menu-button">
        <button class="mobile-toggle-btn" type="button" id="mobileMenuToggle" aria-label="Toggle navigation">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </div>
    
    <div class="mobile-header-logo">
        <img src="<?php echo e(asset('img/logoformenu.svg')); ?>" alt="Workorio Logo">
    </div>
    
    <!-- Mobile Header Actions (Bell + Logout) -->
    <div class="d-flex align-items-center gap-2">
        <!-- Mobile Notification Bell in Header -->
        <div id="mobile-notification-bell" style="position: relative;">
            <button id="mobile-bell-btn" class="btn btn-sm text-dark p-1" title="Notifications" style="background: transparent; border: none; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                <span id="mobile-notification-count" style="position: absolute; top: 5px; right: 5px; background: #ff4444; color: white; border-radius: 50%; width: 16px; height: 16px; font-size: 10px; display: none; align-items: center; justify-content: center;">0</span>
            </button>
            <div id="mobile-notification-dropdown" style="position: absolute; top: 50px; right: 0; background: white; border: 1px solid #ddd; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 280px; max-height: 300px; display: none; z-index: 1100; overflow: hidden;">
                <div style="padding: 10px 12px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: bold; font-size: 14px; color: #333;">Notifications</span>
                    <div>
                        <button id="mobile-mark-all-read-btn" class="btn btn-sm btn-link p-0 me-2" style="font-size: 11px; text-decoration: none;" title="Mark all as read">Mark all read</button>
                        <button id="mobile-clear-all-btn" class="btn btn-sm btn-link p-0 text-danger" style="font-size: 11px; text-decoration: none;" title="Clear all">Clear all</button>
                    </div>
                </div>
                <div id="mobile-notification-list" style="max-height: 240px; overflow-y: auto;"></div>
            </div>
        </div>

        <!-- Mobile Logout Power Button -->
        <form method="POST" action="<?php echo e(route($isSuperAdmin ? 'superadmin.logout' : 'logout')); ?>" class="m-0">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-sm text-dark p-1" title="Logout" style="background: transparent; border: none; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-power" style="font-size: 1.25rem;"></i>
            </button>
        </form>
    </div>
</div>

<!-- Mobile Sidebar Overlay -->
<div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>

<!-- Mobile Sidebar -->
<div class="mobile-sidebar" id="mobileSidebar">
    <div class="mobile-sidebar-header">
        <div class="d-flex justify-content-between align-items-center">
            <img src="<?php echo e(asset('img/logoblack.png')); ?>" alt="Triserv Logo" class="img-fluid" style="max-height: 30px;">
            <div class="d-flex align-items-center">
                <button class="btn-close-mobile" id="closeMobileSidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </div>
    
    <div class="mobile-sidebar-content">
        <?php if($isLoggedIn): ?>
        <!-- Dashboard Link -->
        <?php if(!$isSuperAdmin): ?>
        <a href="<?php echo e(url('/dashboard')); ?>" class="mobile-menu-item" title="Dashboard">
            <i class="bi bi-speedometer2 me-3"></i>
            <span>Dashboard</span>
        </a>
        <?php endif; ?>

        <!-- TENANT SECTION - Only Super Admin -->
        <?php if($isSuperAdmin): ?>
        <a href="<?php echo e(route('tenant')); ?>" class="mobile-menu-item" title="Tenant Management">
            <i class="bi bi-building me-3"></i>
            <span>Tenant Management</span>
        </a>
        <?php endif; ?>

        <!-- Dynamic sections based on tenant features and role -->
        <?php ($sections = \App\Services\MenuBuilder::build()); ?>
        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($section['route'])): ?>
                <a href="<?php echo e(route($section['route'])); ?>" class="mobile-menu-item" title="<?php echo e($section['title']); ?>">
                    <i class="<?php echo e($section['icon']); ?> me-3"></i>
                    <span><?php echo e($section['title']); ?></span>
                </a>
            <?php else: ?>
                <div class="mobile-menu-section">
                    <a class="mobile-menu-toggle" data-bs-toggle="collapse" href="#mobileMenu_<?php echo e($section['key']); ?>" role="button" aria-expanded="false" aria-controls="mobileMenu_<?php echo e($section['key']); ?>" title="<?php echo e($section['title']); ?>">
                        <i class="<?php echo e($section['icon']); ?> me-3"></i>
                        <span><?php echo e($section['title']); ?></span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="mobileMenu_<?php echo e($section['key']); ?>">
                        <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route($item['route'])); ?>" class="mobile-menu-subitem" title="<?php echo e($item['tooltip'] ?? $item['title']); ?>">
                                <i class="<?php echo e($item['icon']); ?> me-3"></i>
                                <span><?php echo e($item['title']); ?></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php endif; ?>
    </div>
</div>

<style>
/* Mobile Header Bar */
.mobile-header-bar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 60px;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 15px;
    z-index: 1045;
}

.mobile-header-logo img {
    height: 28px;
    max-width: 150px;
    object-fit: contain;
}

/* Push content down on mobile */
@media (max-width: 768px) {
    .main {
        padding-top: 75px !important;
    }
}

/* Mobile Menu Button Styles - Adapted for Header */
.mobile-menu-button {
    /* Previously fixed, now static/relative within flex container */
    position: relative;
    z-index: auto;
    transition: opacity 0.3s ease;
}

.mobile-menu-button.hidden {
    opacity: 0;
    pointer-events: none;
}

.mobile-toggle-btn {
    background: none;
    border: none;
    padding: 10px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.mobile-toggle-btn:hover {
    transform: scale(1.1);
}

/* Hamburger Lines */
.hamburger-line {
    width: 20px;
    height: 2px;
    background-color: black;
    margin: 2px 0;
    transition: all 0.3s ease;
    border-radius: 1px;
}

/* Toggle Animation */
.mobile-toggle-btn.active .hamburger-line:nth-child(1) {
    transform: rotate(45deg) translate(3px, 3px);
}

.mobile-toggle-btn.active .hamburger-line:nth-child(2) {
    opacity: 0;
    transform: scaleX(0);
}

.mobile-toggle-btn.active .hamburger-line:nth-child(3) {
    transform: rotate(-45deg) translate(3px, -3px);
}

/* Mobile Sidebar Styles */
.mobile-sidebar {
    position: fixed;
    top: 0;
    left: -300px;
    width: 300px;
    height: 100vh;
    display: flex;
    flex-direction: column;
    background: white;
    z-index: 1050;
    transition: left 0.3s ease-in-out;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.mobile-sidebar.show {
    left: 0;
}

.mobile-sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1040;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
}

.mobile-sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

.mobile-sidebar-header {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    background: #f8f9fa;
    flex-shrink: 0;
}

.btn-close-mobile {
    background: none;
    border: none;
    color: #495057;
    font-size: 1.2rem;
    padding: 0.25rem;
    cursor: pointer;
}

.mobile-sidebar-content {
    padding: 1rem 0;
    flex: 1;
    overflow-y: auto;
    padding-bottom: 100px;
}

.mobile-menu-item,
.mobile-menu-toggle {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: #000;
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    transition: background-color 0.2s ease;
    font-family: Cairo !important;
}



.mobile-menu-item:hover,
.mobile-menu-toggle:hover {
    background-color: #e9ecef;
    color: #495057;
    text-decoration: none;
}

.mobile-menu-section {
    margin-bottom: 0.5rem;
}

.mobile-menu-subitem {
    display: flex;
    align-items: center;
    padding: 0.5rem 1.5rem 0.5rem 3rem;
    color: #000;
    text-decoration: none;
    transition: background-color 0.2s ease;
    font-family: Cairo;
}

.mobile-menu-subitem:hover {
    background-color: #e9ecef;
    color: #495057;
    text-decoration: none;
}



/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .mobile-sidebar {
        width: 280px;
        left: -280px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const closeMobileSidebar = document.getElementById('closeMobileSidebar');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');

    // Toggle sidebar
    mobileMenuToggle.addEventListener('click', function() {
        if (mobileSidebar.classList.contains('show')) {
            // Close sidebar
            closeSidebar();
        } else {
            // Open sidebar
            openSidebar();
        }
    });

    // Open sidebar
    function openSidebar() {
        mobileSidebar.classList.add('show');
        mobileSidebarOverlay.classList.add('show');
        mobileMenuToggle.classList.add('active');
        mobileMenuToggle.parentElement.classList.add('hidden'); // Hide button
        document.body.style.overflow = 'hidden'; // Prevent body scroll
    }

    // Close sidebar
    function closeSidebar() {
        mobileSidebar.classList.remove('show');
        mobileSidebarOverlay.classList.remove('show');
        mobileMenuToggle.classList.remove('active');
        mobileMenuToggle.parentElement.classList.remove('hidden'); // Show button
        document.body.style.overflow = ''; // Restore body scroll
    }

    closeMobileSidebar.addEventListener('click', closeSidebar);
    mobileSidebarOverlay.addEventListener('click', closeSidebar);

    // Close sidebar when clicking on menu items (optional)
    document.querySelectorAll('.mobile-menu-item, .mobile-menu-subitem').forEach(item => {
        item.addEventListener('click', function() {
            setTimeout(closeSidebar, 150); // Small delay for better UX
        });
    });

    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileSidebar.classList.contains('show')) {
            closeSidebar();
        }
    });
});
</script>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/layouts/mobile_nav.blade.php ENDPATH**/ ?>