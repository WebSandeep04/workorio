
<?php $__env->startSection('title', 'Campaign Details'); ?>
<?php $__env->startSection('page_title', 'Campaign: ' . $whatsapp_campaign->name); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startPush('styles'); ?>
<style>
  .data-table-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    box-shadow: 0px 4px 4px 0px #0000000A;
    overflow: hidden;
  }
  .data-table-card .custom-table {
    margin-bottom: 0;
    font-family: Montserrat;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
  }
  .data-table-card .custom-table thead th {
    background: #fff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 700;
    padding: 0.6rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #f1f3f5;
    position: sticky;
    top: 0;
    z-index: 5;
    white-space: nowrap;
    font-family: Montserrat;
  }
  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    white-space: nowrap;
    font-family: Montserrat;
  }
  .data-table-card .custom-table tbody tr {
    transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }
  .data-table-card .custom-table tbody tr:hover {
    background: rgba(102, 126, 234, 0.08);
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }
  .data-table-card .custom-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
  }
</style>
<?php $__env->stopPush(); ?>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add Members to Campaign</h5>
            </div>
            <div class="card-body">
                <?php if(session('success')): ?>
                    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <form id="add_members_form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="campaign_id" value="<?php echo e($whatsapp_campaign->id); ?>">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Select Source</label>
                            <select class="form-select" id="source_type" name="source_type" required>
                                <option value="">-- Select Source --</option>
                                <option value="SalesRecord">Leads (Sales Records)</option>
                                <option value="Prospectus">Prospectus</option>
                                <option value="Customer">Customers</option>
                                <option value="Calling">Calling Data</option>
                                <option value="BusinessCardScan">Contact Mgmt (Business Cards)</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive data-table-card mt-3" id="source_data_container" style="display: none;">
                        <table class="table custom-table table-sm" id="source_table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select_all"></th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded here via AJAX -->
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between align-items-center mt-2 p-2" id="pagination_controls" style="display: none !important;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="prev_page" disabled>Previous</button>
                            <span id="page_info" class="text-muted small"></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="next_page" disabled>Next</button>
                        </div>
                        <div class="p-2 pt-0 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Add Selected Members</button>
                            <button type="button" class="btn btn-success btn-sm" id="add_all_members_btn">Add ALL Members from Source</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card data-table-card">
            <div class="card-header border-bottom">
                <h5 class="mb-0" style="font-family: Montserrat; font-weight: 600;">Campaign Members</h5>
            </div>
            <div class="table-responsive">
                <table class="table custom-table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Action (Manual)</th>
                        </tr>
                    </thead>
                        <tbody id="campaign_members_body">
                            <tr><td colspan="5" class="text-center py-4">Loading members...</td></tr>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let currentPage = 1;
    let selectedMembers = new Set();

    function fetchSourceData(page = 1) {
        let sourceType = document.getElementById('source_type').value;
        let container = document.getElementById('source_data_container');
        let tbody = document.querySelector('#source_table tbody');
        let paginationControls = document.getElementById('pagination_controls');
        
        if (!sourceType) {
            container.style.display = 'none';
            return;
        }

        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
        container.style.display = 'block';
        paginationControls.style.setProperty('display', 'none', 'important');
        document.getElementById('select_all').checked = false;

        fetch(`<?php echo e(route('whatsapp-campaigns.source-data')); ?>?source_type=${sourceType}&page=${page}`)
            .then(response => response.json())
            .then(response => {
                let data = response.data; // Laravel pagination wraps items in 'data'
                tbody.innerHTML = '';
                if (!data || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No records found.</td></tr>';
                    return;
                }
                
                data.forEach(item => {
                    if (item.phone) {
                        let isChecked = selectedMembers.has(item.id.toString()) ? 'checked' : '';
                        tbody.innerHTML += `
                            <tr>
                                <td><input type="checkbox" value="${item.id}" class="member_checkbox" ${isChecked}></td>
                                <td>${item.id}</td>
                                <td>${item.name || 'N/A'}</td>
                                <td>${item.phone}</td>
                            </tr>
                        `;
                    }
                });

                // Setup pagination UI
                paginationControls.style.setProperty('display', 'flex', 'important');
                document.getElementById('page_info').textContent = `Page ${response.current_page} of ${response.last_page} (Total: ${response.total})`;
                
                document.getElementById('prev_page').disabled = !response.prev_page_url;
                document.getElementById('next_page').disabled = !response.next_page_url;
                currentPage = response.current_page;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading data.</td></tr>';
            });
    }

    document.getElementById('source_type').addEventListener('change', function() {
        currentPage = 1;
        selectedMembers.clear();
        document.getElementById('select_all').checked = false;
        fetchSourceData(currentPage);
    });

    document.getElementById('prev_page').addEventListener('click', function() {
        fetchSourceData(currentPage - 1);
    });

    document.getElementById('next_page').addEventListener('click', function() {
        fetchSourceData(currentPage + 1);
    });

    document.getElementById('select_all').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.member_checkbox');
        let isChecked = this.checked;
        checkboxes.forEach(cb => {
            cb.checked = isChecked;
            if (isChecked) {
                selectedMembers.add(cb.value);
            } else {
                selectedMembers.delete(cb.value);
            }
        });
    });

    document.querySelector('#source_table tbody').addEventListener('change', function(e) {
        if (e.target.classList.contains('member_checkbox')) {
            if (e.target.checked) {
                selectedMembers.add(e.target.value);
            } else {
                selectedMembers.delete(e.target.value);
                document.getElementById('select_all').checked = false;
            }
        }
    });

    $(document).ready(function() {
        loadCampaignMembers();

        $('#add_members_form').on('submit', function(e) {
            e.preventDefault();
            
            if (selectedMembers.size === 0) {
                alert('Please select at least one member to add.');
                return;
            }

            let campaignId = $('#campaign_id').val();
            let submitBtn = $(this).find('button[type="submit"]');
            
            let formData = {
                _token: '<?php echo e(csrf_token()); ?>',
                source_type: $('#source_type').val(),
                member_ids: Array.from(selectedMembers)
            };
            
            submitBtn.prop('disabled', true).text('Adding...');

            $.ajax({
                url: `/whatsapp-campaigns/${campaignId}/add-members`,
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert(response.message);
                    loadCampaignMembers();
                    
                    // Reset selection
                    selectedMembers.clear();
                    document.getElementById('select_all').checked = false;
                    document.querySelectorAll('.member_checkbox').forEach(cb => cb.checked = false);
                },
                error: function(xhr) {
                    alert('Error adding members. Make sure you selected at least one member.');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text('Add Selected Members');
                }
            });
        });

        $('#add_all_members_btn').on('click', function() {
            let sourceType = $('#source_type').val();
            if (!sourceType) {
                alert('Please select a source type first.');
                return;
            }
            if(!confirm(`Are you sure you want to add ALL members from ${sourceType}? This may take a moment.`)) {
                return;
            }
            
            let campaignId = $('#campaign_id').val();
            let btn = $(this);
            btn.prop('disabled', true).text('Adding ALL...');

            $.ajax({
                url: `/whatsapp-campaigns/${campaignId}/add-members`,
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    source_type: sourceType,
                    select_all: 1
                },
                success: function(response) {
                    alert(response.message);
                    loadCampaignMembers();
                    
                    // Reset selection
                    selectedMembers.clear();
                    document.getElementById('select_all').checked = false;
                    document.querySelectorAll('.member_checkbox').forEach(cb => cb.checked = false);
                },
                error: function(xhr) {
                    alert('Error adding members.');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Add ALL Members from Source');
                }
            });
        });
    });

    function loadCampaignMembers() {
        let campaignId = $('#campaign_id').val();
        let tbody = $('#campaign_members_body');
        
        tbody.html('<tr><td colspan="5" class="text-center py-4">Loading...</td></tr>');

        $.ajax({
            url: `/whatsapp-campaigns/${campaignId}/fetch-members`,
            type: 'POST',
            data: { _token: '<?php echo e(csrf_token()); ?>' },
            success: function(response) {
                let members = response.members;
                let html = '';

                if (members.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">No members found.</td></tr>';
                } else {
                    members.forEach(member => {
                        let sourceName = member.source_type.split('\\').pop();
                        let phoneClean = member.phone_number ? member.phone_number.replace(/[^0-9]/g, '') : '';
                        
                        let actionHtml = '';
                        if (!member.phone_number) {
                            actionHtml = `<span class="text-danger small me-2">No Phone</span>`;
                        }

                        actionHtml += `
                            <button type="button" class="btn btn-sm btn-danger py-0 px-2 ms-1" onclick="removeMember(${member.id})" title="Remove">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;

                        html += `
                            <tr>
                                <td>${member.name || 'N/A'}</td>
                                <td>${member.phone_number}</td>
                                <td>${sourceName}</td>
                                <td><span class="badge bg-secondary">${member.status}</span></td>
                                <td>${actionHtml}</td>
                            </tr>
                        `;
                    });
                }
                tbody.html(html);
            },
            error: function() {
                tbody.html('<tr><td colspan="5" class="text-center text-danger">Error loading members.</td></tr>');
            }
        });
    }

    function removeMember(memberId) {
        if(confirm('Are you sure you want to remove this member?')) {
            $.ajax({
                url: `/whatsapp-campaigns/member/${memberId}`,
                type: 'DELETE',
                data: { _token: '<?php echo e(csrf_token()); ?>' },
                success: function(response) {
                    loadCampaignMembers();
                },
                error: function() {
                    alert('Error removing member.');
                }
            });
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/whatsapp_campaigns/show.blade.php ENDPATH**/ ?>