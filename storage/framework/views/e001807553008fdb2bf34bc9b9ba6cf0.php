<?php
    $headerUser = auth()->user();
    $headerName = $headerUser->name ?? (session('user_name') ?? 'Guest');
    $headerRole = optional(optional($headerUser)->role)->role_name ?? (session('user_role') ?? 'Team Member');
    if (is_numeric($headerRole)) {
        $resolvedRole = \App\Models\Role::find((int)$headerRole);
        $headerRole = $resolvedRole->role_name ?? 'Team Member';
    }
    $headerInitial = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $headerName), 0, 1) ?: 'U');
    
    // Determine logout route based on user type
    $isSuperAdmin = $headerUser && $headerUser->role_id == 3 && \Illuminate\Support\Facades\DB::getDefaultConnection() === 'mysql';
    $isTenantUser = session()->has('tenant_id');
    $logoutRoute = $isSuperAdmin ? 'superadmin.logout' : 'logout';
?>
<div class="app-header d-none d-md-flex align-items-center justify-content-between">
    <div class="app-header-text">
        <h4 class="header-title"><?php echo $__env->yieldContent('page_title', 'Dashboard'); ?></h4>
    </div>
    <div class="app-header-actions">
        <div class="header-icon">
            <i class="bi bi-globe"></i>
        </div>
        <div class="header-icon" id="notification-bell" style="position: relative;">
            <button id="bell-btn" class="border-0 bg-transparent p-0 w-100 h-100 d-flex align-items-center justify-content-center" style="cursor: pointer;">
                <i class="bi bi-bell"></i>
            </button>
            <span id="notification-count" style="position: absolute; top: -2px; right: -2px; background: #ff4444; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; display: none; align-items: center; justify-content: center; font-weight: 600;">0</span>
            <div id="notification-dropdown" style="position: absolute; top: 50px; right: 0; background: white; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 350px; max-height: 400px; display: none; z-index: 1000; overflow: hidden;">
                <div style="padding: 12px 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa;">
                    <span style="font-weight: 600; font-size: 15px; color: #333;">Notifications</span>
                    <div>
                        <button id="mark-all-read-btn" class="btn btn-sm btn-link p-0 me-2" style="font-size: 12px; text-decoration: none; color: #5b59f7;" title="Mark all as read">Mark all read</button>
                        <button id="clear-all-btn" class="btn btn-sm btn-link p-0 text-danger" style="font-size: 12px; text-decoration: none;" title="Clear all">Clear all</button>
                    </div>
                </div>
                <div id="notification-list" style="max-height: 340px; overflow-y: auto;"></div>
            </div>
        </div>
        <div class="header-icon">
            <i class="bi bi-envelope"></i>
        </div>
        <form method="POST" action="<?php echo e(route($logoutRoute)); ?>" class="m-0">
            <?php echo csrf_field(); ?>
            <button type="submit" class="header-icon header-logout-btn" title="Logout">
                <i class="bi bi-power"></i>
            </button>
        </form>
        <div class="header-profile minimal dropdown">
            <button class="btn dropdown-toggle d-flex align-items-center gap-2 p-0 border-0 bg-transparent" type="button" id="headerProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="gap: 12px;">
                <div class="profile-avatar">
                    <img src="<?php echo e(asset('img/avatar.png')); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                </div>
                <div class="profile-meta">
                    <span class="profile-name"><?php echo e($headerName); ?></span>
                    <small class="profile-role"><?php echo e($headerRole); ?></small>
                </div>
                <!-- <i class="bi bi-chevron-down"></i> -->
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="headerProfileDropdown" style="border-radius: 8px;">
                <li>
                    <a class="dropdown-item py-2" href="<?php echo e(route('profile.index')); ?>">
                        <i class="bi bi-person me-2 text-primary"></i> My Profile
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/layouts/header.blade.php ENDPATH**/ ?>