@php
    $headerUser = auth()->user();
    if (!$headerUser && session()->has('user_id')) {
        $headerUser = \App\Models\User::find(session('user_id'));
    }

    $headerName = $headerUser->name ?? (session('user_name') ?? 'Guest');
    $headerRole = optional(optional($headerUser)->role)->role_name ?? (session('user_role') ?? 'Team Member');
    if (is_numeric($headerRole)) {
        $resolvedRole = \App\Models\Role::find((int)$headerRole);
        $headerRole = $resolvedRole->role_name ?? 'Team Member';
    }
    $headerInitial = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $headerName), 0, 1) ?: 'U');
    
    // Determine logout route and master connection status
    $isMasterConnection = \Illuminate\Support\Facades\DB::getDefaultConnection() === 'mysql';
    $isSuperAdmin = $headerUser && $headerUser->role_id == 3 && $isMasterConnection;
    $logoutRoute = $isSuperAdmin ? 'superadmin.logout' : 'logout';

    // Find Employee for Profile Picture
    $headerEmployee = null;
    if (!$isMasterConnection && $headerUser) {
        $headerEmployee = $headerUser->employee;
        if (!$headerEmployee) {
            $headerEmployee = \App\Models\Employee::where('email', $headerUser->email)->first();
        }
    }

    $displayDesignation = 'Team Member';
    if ($headerEmployee) {
        if ($headerEmployee->designationRelation) {
            $displayDesignation = $headerEmployee->designationRelation->title;
        } elseif ($headerEmployee->designation) {
            $displayDesignation = $headerEmployee->designation;
        }
    }
@endphp

<!-- Modern Nexora-Style Top Bar -->
<header class="app-header d-none d-md-flex align-items-center justify-content-between">
    <!-- Left: Sidebar Toggle + Breadcrumb Title -->
    <div class="d-flex align-items-center gap-3">
        <button id="toggleSidebar" class="header-toggle-btn" type="button" aria-label="Toggle sidebar" title="Toggle Sidebar (⌘B)">
            <i class="bi bi-layout-sidebar"></i>
        </button>
        <div class="header-breadcrumb-area">
            <span class="header-page-title">@yield('page_title', 'Dashboard')</span>
        </div>
    </div>

    <!-- Right: Quick Search, Notifications, Actions -->
    <div class="d-flex align-items-center gap-2">
        
        <!-- Search Trigger Button with Command Pill (Nexora Style) -->
        <button type="button" class="header-search-box d-none d-lg-flex align-items-center" id="openSpotlightSearchBtn">
            <i class="bi bi-search search-icon"></i>
            <span class="search-placeholder-text">Search leads, contacts, tasks...</span>
            <kbd class="search-kbd">⌘K</kbd>
        </button>

        @if($headerRole === 'admin')
        <a href="{{ route('role-master') }}" class="header-action-icon" title="Software Setup">
            <i class="bi bi-gear"></i>
        </a>
        @endif

        <!-- Notifications -->
        <div class="position-relative" id="notification-bell">
            <button id="bell-btn" class="header-action-icon position-relative" aria-label="Notifications" type="button">
                <i class="bi bi-bell"></i>
                <span id="notification-count" class="notification-badge" style="display: none;">0</span>
            </button>
            
            <!-- Notification Dropdown -->
            <div id="notification-dropdown" style="display: none; position: absolute; top: 46px; right: 0; width: 380px; max-width: 90vw; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.15), 0 0 1px 1px rgba(15, 23, 42, 0.05); z-index: 99999; overflow: hidden;">
                <div style="padding: 12px 16px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; background: #FFFFFF;">
                    <span style="font-weight: 700; font-size: 14px; color: #0F172A;">Notifications</span>
                    <div style="display: flex; gap: 8px;">
                        <button id="mark-all-read-btn" type="button" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 12px; font-weight: 500; color: #434AFA;">Mark all read</button>
                        <button id="clear-all-btn" type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-danger" style="font-size: 12px; font-weight: 500;">Clear all</button>
                    </div>
                </div>
                <div id="notification-list" style="max-height: 360px; overflow-y: auto; background: #FFFFFF;"></div>
            </div>
        </div>

        <!-- Create Quick Action Button (Nexora Style with #434AFA) -->
        <div class="dropdown">
            <button class="btn btn-create-header d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-plus-lg fs-7"></i>
                <span>Create</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end modern-action-dropdown">
                @if(Route::has('lead'))
                <li>
                    <a class="dropdown-item" href="{{ route('lead') }}">
                        <i class="bi bi-person-plus nav-dropdown-icon"></i>
                        <span>New Lead</span>
                    </a>
                </li>
                @endif
                
                @if(Route::has('worklog.index'))
                <li>
                    <a class="dropdown-item" href="{{ route('worklog.index') }}">
                        <i class="bi bi-check2-square nav-dropdown-icon"></i>
                        <span>New Task</span>
                    </a>
                </li>
                @elseif(Route::has('all-task'))
                <li>
                    <a class="dropdown-item" href="{{ route('all-task') }}">
                        <i class="bi bi-check2-square nav-dropdown-icon"></i>
                        <span>New Task</span>
                    </a>
                </li>
                @endif

                @if(Route::has('customer'))
                <li>
                    <a class="dropdown-item" href="{{ route('customer') }}">
                        <i class="bi bi-people nav-dropdown-icon"></i>
                        <span>New Customer</span>
                    </a>
                </li>
                @endif

                @if(Route::has('whatsapp-campaigns.index'))
                <li>
                    <a class="dropdown-item" href="{{ route('whatsapp-campaigns.index') }}">
                        <i class="bi bi-chat-dots nav-dropdown-icon"></i>
                        <span>WhatsApp Campaign</span>
                    </a>
                </li>
                @endif

                @if(Route::has('followup'))
                <li>
                    <a class="dropdown-item" href="{{ route('followup') }}">
                        <i class="bi bi-journal-text nav-dropdown-icon"></i>
                        <span>Log Follow-up</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

    </div>
</header>

<!-- Nexora-Style Spotlight Command Modal (Ctrl+K / Cmd+K) -->
<div class="spotlight-backdrop" id="spotlightBackdrop" style="display: none;">
    <div class="spotlight-dialog">
        <!-- Search Input -->
        <div class="spotlight-header">
            <i class="bi bi-search spotlight-search-icon"></i>
            <input type="text" class="spotlight-input" id="spotlightInput" placeholder="Search leads, contacts, tasks, modules..." autocomplete="off">
            <span class="spotlight-esc-badge" id="closeSpotlightBtn">ESC</span>
        </div>

        <!-- Search Results / Quick Suggestions -->
        <div class="spotlight-body" id="spotlightBody">
            <div class="spotlight-group">
                <span class="spotlight-group-title">Quick Actions</span>
                @if(Route::has('lead'))
                <a href="{{ route('lead') }}" class="spotlight-item">
                    <div class="spotlight-item-left">
                        <i class="bi bi-person-plus spotlight-icon text-primary"></i>
                        <span class="spotlight-text">All Leads & Prospects</span>
                    </div>
                    <span class="spotlight-badge">Leads</span>
                </a>
                @endif
                @if(Route::has('customer'))
                <a href="{{ route('customer') }}" class="spotlight-item">
                    <div class="spotlight-item-left">
                        <i class="bi bi-people spotlight-icon text-success"></i>
                        <span class="spotlight-text">Customer Directory</span>
                    </div>
                    <span class="spotlight-badge">Contacts</span>
                </a>
                @endif
                @if(Route::has('followup'))
                <a href="{{ route('followup') }}" class="spotlight-item">
                    <div class="spotlight-item-left">
                        <i class="bi bi-telephone-outbound spotlight-icon text-warning"></i>
                        <span class="spotlight-text">Daily Follow-ups & Calling</span>
                    </div>
                    <span class="spotlight-badge">Calling</span>
                </a>
                @endif
                @if(Route::has('whatsapp-inbox.index'))
                <a href="{{ route('whatsapp-inbox.index') }}" class="spotlight-item">
                    <div class="spotlight-item-left">
                        <i class="bi bi-whatsapp spotlight-icon text-success"></i>
                        <span class="spotlight-text">WhatsApp Inbox</span>
                    </div>
                    <span class="spotlight-badge">Messages</span>
                </a>
                @endif
            </div>

            <div class="spotlight-group">
                <span class="spotlight-group-title">Navigation Shortcuts</span>
                <a href="{{ url('/dashboard') }}" class="spotlight-item">
                    <div class="spotlight-item-left">
                        <i class="bi bi-grid-1x2 spotlight-icon text-secondary"></i>
                        <span class="spotlight-text">Main Dashboard</span>
                    </div>
                    <span class="spotlight-badge">Overview</span>
                </a>
                @if(Route::has('role-master'))
                <a href="{{ route('role-master') }}" class="spotlight-item">
                    <div class="spotlight-item-left">
                        <i class="bi bi-gear spotlight-icon text-secondary"></i>
                        <span class="spotlight-text">Software Configuration</span>
                    </div>
                    <span class="spotlight-badge">Settings</span>
                </a>
                @endif
            </div>
        </div>

        <div class="spotlight-footer">
            <div class="d-flex align-items-center gap-3">
                <span><kbd class="spotlight-kbd">↑</kbd> <kbd class="spotlight-kbd">↓</kbd> to navigate</span>
                <span><kbd class="spotlight-kbd">↵</kbd> to select</span>
                <span><kbd class="spotlight-kbd">esc</kbd> to close</span>
            </div>
        </div>
    </div>
</div>

<!-- Spotlight Modal JS with Ctrl+K / Cmd+K Interception -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const backdrop = document.getElementById('spotlightBackdrop');
    const input = document.getElementById('spotlightInput');
    const triggerBtn = document.getElementById('openSpotlightSearchBtn');
    const closeBtn = document.getElementById('closeSpotlightBtn');

    function openSpotlight() {
        if (!backdrop) return;
        backdrop.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            if (input) {
                input.value = '';
                input.focus();
            }
        }, 50);
    }

    function closeSpotlight() {
        if (!backdrop) return;
        backdrop.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Intercept Ctrl+K or Cmd+K globally so Google Chrome address bar DOES NOT OPEN!
    window.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault(); // Stop browser address bar search
            e.stopPropagation();
            if (backdrop.style.display === 'flex') {
                closeSpotlight();
            } else {
                openSpotlight();
            }
        } else if (e.key === 'Escape' && backdrop && backdrop.style.display === 'flex') {
            closeSpotlight();
        }
    });

    if (triggerBtn) {
        triggerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openSpotlight();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSpotlight);
    }

    if (backdrop) {
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) {
                closeSpotlight();
            }
        });
    }

    // Live filter inside spotlight items
    if (input) {
        input.addEventListener('input', function() {
            const query = input.value.toLowerCase().trim();
            const items = document.querySelectorAll('.spotlight-item');
            items.forEach(item => {
                const text = item.innerText.toLowerCase();
                if (text.includes(query)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
