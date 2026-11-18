<div class="matrix-table-card">
    <div class="table-responsive" style="max-height: 700px; overflow-y: auto;">
        <table class="table matrix-table align-middle">
            <thead class="sticky-top">
                <tr>
                    <th class="user-sticky-col">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>User / Employee ({{ count($users) }})</span>
                        </div>
                    </th>
                    @foreach($tableAlerts as $alert)
                        <th class="text-center column-header-box border-start">
                            <div class="d-flex flex-column align-items-center gap-1">
                                <div class="badge-category">{{ $alert->category }}</div>
                                <span class="fw-bold text-dark text-truncate" title="{{ $alert->name }}" style="max-width: 170px; font-size: 0.8rem;">
                                    {{ $alert->name }}
                                </span>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge-time"><i class="bi bi-clock me-1"></i>{{ $alert->default_time }}</span>
                                    <!-- Master Switch -->
                                    <div class="form-check form-switch m-0" title="Enable or Pause this report completely">
                                        <input class="form-check-input master-alert-toggle" type="checkbox" data-mail-code="{{ $alert->mail_code }}" {{ $alert->is_active ? 'checked' : '' }} style="cursor: pointer; width: 1.8rem; height: 0.95rem;">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <span class="badge bg-secondary badge-count-{{ $alert->mail_code }}" style="font-size: 0.65rem;" title="Assigned Recipients">
                                        {{ count($recipientMap[$alert->mail_code] ?? []) }}
                                    </span>
                                    <!-- Bulk Actions Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-xs btn-outline-secondary dropdown-toggle py-0 px-1" type="button" data-bs-toggle="dropdown" style="font-size: 0.7rem;">
                                            Quick
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.75rem;">
                                            <li><a class="dropdown-item btn-bulk-assign" href="#" data-mail-code="{{ $alert->mail_code }}" data-action="all_admins"><i class="bi bi-shield-check text-primary me-1"></i>All Admins Only</a></li>
                                            <li><a class="dropdown-item btn-bulk-assign" href="#" data-mail-code="{{ $alert->mail_code }}" data-action="all_active"><i class="bi bi-people text-success me-1"></i>All Active Staff</a></li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li><a class="dropdown-item text-danger btn-bulk-assign" href="#" data-mail-code="{{ $alert->mail_code }}" data-action="clear_all"><i class="bi bi-trash me-1"></i>Clear All</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $emp = $user->employee;
                        $roleName = $user->role ? $user->role->role_name : ($user->role_id == 1 ? 'Admin' : 'User');
                    @endphp
                    <tr>
                        <td class="user-sticky-col">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold me-2" style="width: 32px; height: 32px; font-size: 0.8rem; flex-shrink: 0; border: 1px solid #cbd5e1;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="text-truncate" style="max-width: 200px;">
                                    <div class="fw-semibold text-dark text-truncate" title="{{ $user->name }}" style="font-size: 0.82rem;">
                                        {{ $user->name }}
                                        @if($user->role_id == 1)
                                            <span class="badge bg-danger" style="font-size: 0.6rem;">Admin</span>
                                        @endif
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size: 0.7rem;" title="{{ $user->email }}">
                                        {{ $user->email ?: 'No email' }}
                                    </div>
                                    <div class="text-secondary" style="font-size: 0.68rem;">
                                        <span>{{ $emp ? $emp->employee_code : '-' }}</span>
                                        @if($emp && $emp->departmentRelation)
                                            • <span>{{ $emp->departmentRelation->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        @foreach($tableAlerts as $alert)
                            @php
                                $isMapped = isset($recipientMap[$alert->mail_code][$user->id]);
                            @endphp
                            <td class="text-center border-start">
                                <div class="form-check form-switch d-inline-block custom-switch-lg m-0">
                                    <input class="form-check-input recipient-toggle" 
                                           type="checkbox" 
                                           data-mail-code="{{ $alert->mail_code }}" 
                                           data-user-id="{{ $user->id }}" 
                                           {{ $isMapped ? 'checked' : '' }}
                                           title="Toggle {{ $user->name }} for {{ $alert->name }}">
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($tableAlerts) + 1 }}" class="text-center py-4 text-muted">
                            No employees found matching the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
