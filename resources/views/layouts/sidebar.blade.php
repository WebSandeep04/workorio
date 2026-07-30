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
                <img src="{{ asset('img/logoformenu.svg') }}" alt="Triserv Logo" class="brand-logo">
            </div>
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
        @endphp
        @if(!$isSuperAdmin)
        <span class="menu-title">Main Menu</span>
        @endif

        <div class="side-items">
            <div class="menu-section">
                @if($isLoggedIn && !$isSuperAdmin)
                    <a href="{{ url('/dashboard') }}" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}" title="Dashboard">
                        <span class="link-label"><img src="{{ asset('img/icons/home.png') }}" alt=""><span>Dashboard</span></span>
                        <i class="bi bi-arrow-right-short"></i>
                    </a>
                @endif
            </div>

            @if($isSuperAdmin)
                <div class="menu-section">
                    <span class="menu-heading">Super Admin</span>
                    <a href="{{ route('tenant') }}" class="sidebar-link {{ request()->routeIs('tenant') ? 'active' : '' }}" title="Tenant Management">
                        <span class="link-label"><i class="bi bi-people"></i><span>Tenant Management</span></span>
                    </a>
                </div>
            @endif

            @php
                $sections = \App\Services\MenuBuilder::build();
            @endphp
            @foreach($sections as $section)
                @if(isset($section['route']))
                    @php
                        $baseRoute = explode('.', $section['route'])[0];
                        $isSectionActive = request()->routeIs($section['route']) || request()->routeIs($baseRoute . '.*');
                    @endphp
                    <div class="menu-section">
                        <a href="{{ route($section['route']) }}" class="sidebar-link {{ $isSectionActive ? 'active' : '' }}" title="{{ $section['title'] }}">
                            <span class="link-label"><i class="{{ $section['icon'] }}"></i><span>{{ $section['title'] }}</span></span>
                        </a>
                    </div>
                @else
                    @php
                        $sectionHasActive = collect($section['items'] ?? [])->contains(function ($item) {
                            if (!isset($item['route'])) return false;
                            $baseRoute = explode('.', $item['route'])[0];
                            return request()->routeIs($item['route']) || request()->routeIs($baseRoute . '.*');
                        });
                    @endphp
                    <div class="menu-section">
                        <a class="sidebar-link sidebar-dropdown {{ $sectionHasActive ? 'active' : '' }}" data-section-key="{{ $section['key'] }}" data-bs-toggle="collapse" href="#menu_{{ $section['key'] }}" role="button" aria-expanded="{{ $sectionHasActive ? 'true' : 'false' }}" aria-controls="menu_{{ $section['key'] }}" title="{{ $section['title'] }}">
                            <span class="link-label"><i class="{{ $section['icon'] }}"></i><span>{{ $section['title'] }}</span></span>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse sidebar-sub {{ $sectionHasActive ? 'show' : '' }}" id="menu_{{ $section['key'] }}">
                            @foreach($section['items'] as $item)
                                @php
                                    $itemActive = false;
                                    if(isset($item['route'])){
                                        $baseRoute = explode('.', $item['route'])[0];
                                        $itemActive = request()->routeIs($item['route']) || request()->routeIs($baseRoute . '.*');
                                    }
                                @endphp
                                <a href="{{ route($item['route']) }}" class="sidebar-sublink {{ $itemActive ? 'active' : '' }}" title="{{ $item['tooltip'] ?? $item['title'] }}">
                                    <i class="{{ $item['icon'] }}"></i><span>{{ $item['title'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

    </div>
</div>

