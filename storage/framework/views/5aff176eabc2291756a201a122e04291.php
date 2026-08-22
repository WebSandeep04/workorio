<div class="sidebar position-fixed">
    <div class="sidebar-inner">
        <button id="toggleSidebar" class="sidebar-toggle" type="button" aria-label="Toggle sidebar">
            <span class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="<?php echo e(asset('img/logoformenu.svg')); ?>" alt="Triserv Logo" class="brand-logo">
            </div>
        </div>

        <?php
            $connectionName = \Illuminate\Support\Facades\DB::getDefaultConnection();
            $isMasterConnection = $connectionName === 'mysql';
            $isLoggedIn = auth()->check();
            $user = auth()->user();
            $resolvedRole = null;

            if (!$isLoggedIn && session()->has('user_id')) {
                $query = \App\Models\User::query();
                if (!$isMasterConnection) {
                    $query->with('role');
                }
                $sessionUser = $query->find(session('user_id'));
                if ($sessionUser) {
                    $user = $sessionUser;
                    $isLoggedIn = true;
                }
            } elseif ($user && !$isMasterConnection) {
                $user->loadMissing('role');
            }

            if ($isLoggedIn && $user && !$isMasterConnection) {
                $resolvedRole = $user->role;

                if ($resolvedRole instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
                    $resolvedRole = $resolvedRole->getResults();
                }

                if (!$resolvedRole && $user->role_id) {
                    $resolvedRole = \App\Models\Role::find($user->role_id);
                }
            }

            $roleId = $isLoggedIn ? ($user->role_id ?? null) : null;
            $roleName = $resolvedRole->role_name ?? null;
            $displayName = $isLoggedIn ? ($user->name ?? ($user->email ?? 'User')) : 'Guest';
            $initials = strtoupper(
                \Illuminate\Support\Str::of($displayName)
                    ->replaceMatches('/[^A-Za-z]/', '')
                    ->substr(0, 1)
                    ->value() ?: 'U'
            );
            $roleLabel = $roleName ?: ($roleId == 3 ? 'Super Admin' : 'Team Member');

            $isSuperAdmin = $isLoggedIn &&
                           $roleId == 3 &&
                           $isMasterConnection;
        ?>
        <?php if(!$isSuperAdmin): ?>
        <span class="menu-title">Main Menu</span>
        <?php endif; ?>

        <div class="side-items">
            <div class="menu-section">
                <?php if($isLoggedIn && !$isSuperAdmin): ?>
                    <a href="<?php echo e(url('/dashboard')); ?>" class="sidebar-link <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>" title="Dashboard">
                        <span class="link-label"><img src="<?php echo e(asset('img/icons/home.png')); ?>" alt=""><span>Dashboard</span></span>
                        <i class="bi bi-arrow-right-short"></i>
                    </a>
                <?php endif; ?>
            </div>

            <?php if($isSuperAdmin): ?>
                <div class="menu-section">
                    <span class="menu-heading">Super Admin</span>
                    <a href="<?php echo e(route('tenant')); ?>" class="sidebar-link <?php echo e(request()->routeIs('tenant') ? 'active' : ''); ?>" title="Tenant Management">
                        <span class="link-label"><i class="bi bi-people"></i><span>Tenant Management</span></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php
                $hubs = \App\Services\MenuBuilder::build();
            ?>
            <?php $__currentLoopData = $hubs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $hubHasActive = collect($hub['sections'] ?? [])->contains(function ($section) {
                        return collect($section['items'] ?? [])->contains(function ($item) {
                            return isset($item['route']) && request()->routeIs($item['route']);
                        }) || (isset($section['route']) && request()->routeIs($section['route']));
                    });
                ?>
                <div class="menu-section">
                    <a class="sidebar-link sidebar-dropdown <?php echo e($hubHasActive ? '' : ''); ?>" data-bs-toggle="collapse" href="#hub_<?php echo e($hub['key']); ?>" role="button" aria-expanded="<?php echo e($hubHasActive ? 'true' : 'false'); ?>" aria-controls="hub_<?php echo e($hub['key']); ?>" title="<?php echo e($hub['title']); ?>">
                        <span class="link-label"><i class="<?php echo e($hub['icon']); ?>"></i><span><?php echo e($hub['title']); ?></span></span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse sidebar-sub <?php echo e($hubHasActive ? 'show' : ''); ?>" id="hub_<?php echo e($hub['key']); ?>">
                        <?php $__currentLoopData = $hub['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(isset($section['route'])): ?>
                                <a href="<?php echo e(route($section['route'])); ?>" class="sidebar-sublink <?php echo e(request()->routeIs($section['route']) ? 'active' : ''); ?>" title="<?php echo e($section['title']); ?>">
                                    <i class="<?php echo e($section['icon']); ?>"></i><span><?php echo e($section['title']); ?></span>
                                </a>
                            <?php else: ?>
                                <?php
                                    $sectionHasActive = collect($section['items'] ?? [])->contains(function ($item) {
                                        return isset($item['route']) && request()->routeIs($item['route']);
                                    });
                                ?>
                                <div class="menu-subsection">
                                    <a class="sidebar-link sidebar-dropdown <?php echo e($sectionHasActive ? '' : ''); ?>" style="padding-left: 0.8rem; font-size: 0.9em;" data-section-key="<?php echo e($section['key']); ?>" data-bs-toggle="collapse" href="#menu_<?php echo e($section['key']); ?>" role="button" aria-expanded="<?php echo e($sectionHasActive ? 'true' : 'false'); ?>" aria-controls="menu_<?php echo e($section['key']); ?>" title="<?php echo e($section['title']); ?>">
                                        <span class="link-label"><i class="<?php echo e($section['icon']); ?>"></i><span><?php echo e($section['title']); ?></span></span>
                                        <i class="bi bi-chevron-down"></i>
                                    </a>
                                    <div class="collapse sidebar-sub <?php echo e($sectionHasActive ? 'show' : ''); ?>" id="menu_<?php echo e($section['key']); ?>">
                                        <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $itemActive = isset($item['route']) && request()->routeIs($item['route']);
                                            ?>
                                            <a href="<?php echo e(route($item['route'])); ?>" class="sidebar-sublink <?php echo e($itemActive ? 'active' : ''); ?>" style="padding-left: 1.8rem;" title="<?php echo e($item['tooltip'] ?? $item['title']); ?>">
                                                <i class="<?php echo e($item['icon']); ?>"></i><span><?php echo e($item['title']); ?></span>
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

    </div>
</div>

<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>