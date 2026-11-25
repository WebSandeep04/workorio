<aside class="sidebar position-fixed">
    <div class="sidebar-inner">
        
        <!-- Top Workspace / App Brand (Nexora Style - Clean No Black BG) -->
        <div class="sidebar-header-brand">
            <a href="{{ url('/dashboard') }}" class="brand-workspace-card text-decoration-none">
                <div class="brand-avatar-box">
                    <img src="{{ asset('img/w.png') }}" alt="W" class="brand-avatar-img">
                </div>
                <div class="brand-workspace-info">
                    <span class="workspace-name">Workorio</span>
                    <span class="workspace-sub">CRM & Workspace</span>
                </div>
            </a>
        </div>

        @php
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
            $userEmail = $isLoggedIn ? ($user->email ?? 'user@workorio.com') : 'guest@workorio.com';
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

            $employeeObj = null;
            if (!$isMasterConnection && $user) {
                $employeeObj = $user->employee ?: \App\Models\Employee::where('email', $user->email)->first();
            }
            $logoutRoute = $isSuperAdmin ? 'superadmin.logout' : 'logout';
        @endphp

        <!-- Navigation Scrollable Area (Accordion: Only 1 Hub Open at a time) -->
        <nav class="side-items" id="sidebarNavAccordion">
            
            <!-- General Section -->
            <div class="menu-group">
                <span class="group-label">General</span>
                @if($isLoggedIn && !$isSuperAdmin)
                    <a href="{{ url('/dashboard') }}" class="sidebar-nav-item {{ request()->is('dashboard') ? 'active' : '' }}" title="Dashboard">
                        <i class="bi bi-grid-1x2 nav-icon"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                @endif

                @if($isSuperAdmin)
                    <a href="{{ route('tenant') }}" class="sidebar-nav-item {{ request()->routeIs('tenant') ? 'active' : '' }}" title="Tenant Management">
                        <i class="bi bi-buildings nav-icon"></i>
                        <span class="nav-text">Tenant Management</span>
                    </a>
                @endif
            </div>

            <!-- Dynamic 4 Main Featured Hubs (Collapsible Accordion: SalesHub, Workforce, WhatsApp Management, Software Setup) -->
            @php
                $hubs = \App\Services\MenuBuilder::build();
                // Determine which hub has an active route currently
                $activeHubKey = null;
                foreach($hubs as $hub) {
                    $hasActive = collect($hub['sections'] ?? [])->contains(function ($section) {
                        return collect($section['items'] ?? [])->contains(function ($item) {
                            return isset($item['route']) && request()->routeIs($item['route']);
                        }) || (isset($section['route']) && request()->routeIs($section['route']));
                    });
                    if ($hasActive) {
                        $activeHubKey = $hub['key'];
                        break;
                    }
                }
                // Default to first hub (sales_hub) if none active
                if (!$activeHubKey && !empty($hubs) && !request()->is('dashboard')) {
                    $activeHubKey = $hubs[0]['key'];
                }
            @endphp

            @foreach($hubs as $hub)
                @php
                    $isThisHubOpen = ($activeHubKey === $hub['key']);
                @endphp
                <div class="menu-group hub-group-accordion">
                    <!-- Hub Accordion Trigger Header -->
                    <button class="hub-header-trigger d-flex align-items-center justify-content-between w-100 {{ $isThisHubOpen ? '' : 'collapsed' }}" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#hub_collapse_{{ $hub['key'] }}" 
                            aria-expanded="{{ $isThisHubOpen ? 'true' : 'false' }}">
                        <span class="d-flex align-items-center gap-2 overflow-hidden">
                            <i class="{{ $hub['icon'] ?? 'bi bi-grid' }} hub-icon" title="{{ $hub['title'] }}"></i>
                            <span class="group-label m-0 p-0">{{ $hub['title'] }}</span>
                        </span>
                        <i class="bi bi-chevron-down hub-chevron"></i>
                    </button>

                    <!-- Hub Body Container (data-bs-parent ensures only 1 hub stays open) -->
                    <div class="collapse hub-collapse-body {{ $isThisHubOpen ? 'show' : '' }}" 
                         id="hub_collapse_{{ $hub['key'] }}" 
                         data-bs-parent="#sidebarNavAccordion">
                        <div class="pt-2 d-flex flex-column gap-1">
                            @foreach($hub['sections'] as $section)
                                @if(isset($section['route']))
                                    <a href="{{ route($section['route']) }}" class="sidebar-nav-item {{ request()->routeIs($section['route']) ? 'active' : '' }}" title="{{ $section['title'] }}">
                                        <i class="{{ $section['icon'] }} nav-icon"></i>
                                        <span class="nav-text">{{ $section['title'] }}</span>
                                    </a>
                                @else
                                    @php
                                        $sectionHasActive = collect($section['items'] ?? [])->contains(function ($item) {
                                            return isset($item['route']) && request()->routeIs($item['route']);
                                        });
                                    @endphp
                                    <div class="menu-accordion">
                                        <button class="sidebar-nav-item sidebar-collapse-trigger {{ $sectionHasActive ? 'active-parent' : '' }} {{ $sectionHasActive ? '' : 'collapsed' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#menu_{{ $section['key'] }}" 
                                                aria-expanded="{{ $sectionHasActive ? 'true' : 'false' }}">
                                            <i class="{{ $section['icon'] }} nav-icon"></i>
                                            <span class="nav-text">{{ $section['title'] }}</span>
                                            <i class="bi bi-chevron-right chevron-icon"></i>
                                        </button>
                                        <div class="collapse sub-nav-collapse {{ $sectionHasActive ? 'show' : '' }}" id="menu_{{ $section['key'] }}">
                                            @foreach($section['items'] as $item)
                                                @php
                                                    $itemActive = isset($item['route']) && request()->routeIs($item['route']);
                                                @endphp
                                                <a href="{{ route($item['route']) }}" class="sub-nav-item {{ $itemActive ? 'active' : '' }}" title="{{ $item['tooltip'] ?? $item['title'] }}">
                                                    <span class="sub-nav-dot"></span>
                                                    <span class="sub-nav-text">{{ $item['title'] }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

        </nav>

        <!-- Bottom User Card with Nexora Side Popover Menu -->
        <div class="sidebar-footer-user">
            <div class="dropend w-100 position-relative">
                <button class="user-profile-button" type="button" id="sidebarUserDropdown" data-bs-toggle="dropdown" data-bs-offset="10, 0" aria-expanded="false">
                    <div class="user-avatar-wrap">
                        @if($employeeObj && $employeeObj->profile_picture)
                            <img src="{{ route('profile.picture', $employeeObj->id) }}?t={{ time() }}" alt="Avatar" class="user-avatar-img" onerror="this.onerror=null; this.src='{{ asset('img/avatar.png') }}';">
                        @else
                            <div class="user-avatar-initial">{{ $initials }}</div>
                        @endif
                    </div>
                    <div class="user-info-meta">
                        <span class="user-display-name">{{ $displayName }}</span>
                        <span class="user-display-email">{{ $userEmail }}</span>
                    </div>
                    <div class="user-more-icon">
                        <i class="bi bi-chevron-expand"></i>
                    </div>
                </button>
                
                <!-- Nexora Style Side Flyout Popover Menu -->
                <div class="dropdown-menu modern-user-dropdown" aria-labelledby="sidebarUserDropdown">
                    <div class="dropdown-header-info d-flex align-items-center gap-2">
                        <div class="user-avatar-wrap" style="width: 32px; height: 32px;">
                            @if($employeeObj && $employeeObj->profile_picture)
                                <img src="{{ route('profile.picture', $employeeObj->id) }}?t={{ time() }}" alt="Avatar" class="user-avatar-img">
                            @else
                                <div class="user-avatar-initial" style="font-size: 12px;">{{ $initials }}</div>
                            @endif
                        </div>
                        <div class="overflow-hidden">
                            <span class="fw-bold text-dark d-block text-truncate" style="font-size: 13px;">{{ $displayName }}</span>
                            <span class="text-muted d-block text-truncate" style="font-size: 11px;">{{ $userEmail }}</span>
                        </div>
                    </div>
                    <div class="dropdown-divider my-2"></div>
                    <a class="dropdown-item py-2" href="{{ route('profile.index') }}">
                        <i class="bi bi-person me-2 text-secondary"></i> Account
                    </a>
                    <a class="dropdown-item py-2" href="{{ route('profile.change-password') }}">
                        <i class="bi bi-shield-lock me-2 text-secondary"></i> Security
                    </a>
                    @if(Route::has('role-master'))
                    <a class="dropdown-item py-2" href="{{ route('role-master') }}">
                        <i class="bi bi-sliders me-2 text-secondary"></i> Settings
                    </a>
                    @endif
                    <div class="dropdown-divider my-2"></div>
                    <form method="POST" action="{{ route($logoutRoute) }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger py-2 d-flex align-items-center">
                            <i class="bi bi-box-arrow-right me-2"></i> Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</aside>
