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
                        <th class="text-center column-header-box border-start" data-category="{{ Str::slug($alert->category) }}">
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
                        $branchName = $emp && $emp->branch ? $emp->branch->name : '-';
                        $deptName = $emp && $emp->departmentRelation ? $emp->departmentRelation->name : '-';
                        $avatarLetter = strtoupper(substr($user->name, 0, 1));
                    @endphp
                    <tr>
                        <td class="user-sticky-col">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 30px; height: 30px; background: #e0e7ff; font-size: 0.75rem; flex-shrink: 0;">
                                    {{ $avatarLetter }}
                                </div>
                                <div class="user-info-text text-truncate" style="max-width: 220px;">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="user-name text-truncate" title="{{ $user->name }}">{{ $user->name }}</span>
                                        @if($user->role_id == 1 || ($user->role && strtolower($user->role->role_name) === 'admin'))
                                            <span class="badge bg-danger py-0 px-1" style="font-size: 0.6rem;">Admin</span>
                                        @endif
                                    </div>
                                    <div class="user-email text-truncate" title="{{ $user->email }}">{{ $user->email }}</div>
                                    <div class="user-meta text-truncate">
                                        {{ $emp ? ($emp->employee_code ?: 'Emp') : 'User' }} • {{ $branchName }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        @foreach($tableAlerts as $alert)
                            @php
                                $isAssigned = isset($recipientMap[$alert->mail_code][$user->id]);
                            @endphp
                            <td class="text-center checkbox-cell border-start" data-category="{{ Str::slug($alert->category) }}">
                                <input type="checkbox" 
                                       class="form-check-input recipient-toggle" 
                                       data-mail-code="{{ $alert->mail_code }}" 
                                       data-user-id="{{ $user->id }}"
                                       {{ $isAssigned ? 'checked' : '' }}
                                       title="{{ $user->name }} → {{ $alert->name }}">
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($tableAlerts) + 1 }}" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                            No employees or users found matching your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
