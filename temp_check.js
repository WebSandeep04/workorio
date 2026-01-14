
let currentPage = 1;
let allTasks = [];

// Load summary stats
function loadSummaryStats() {
  if (!allTasks || allTasks.length === 0) return;
  
  const total = allTasks.length;
  const inProgress = allTasks.filter(t => {
    const statusName = (t.status?.name || '').toLowerCase();
    return statusName.includes('progress') || statusName.includes('ongoing');
  }).length;
  const completed = allTasks.filter(t => t.is_done || (t.status?.name || '').toLowerCase().includes('complete')).length;
  const pending = allTasks.filter(t => (t.status?.name || '').toLowerCase().includes('pending')).length;
  const today = allTasks.filter(t => {
    if (!t.created_at) return false;
    const createdDate = new Date(t.created_at).toDateString();
    const todayDate = new Date().toDateString();
    return createdDate === todayDate;
  }).length;

  $('#totalTasks').text(total);
  $('#inProgressTasks').text(inProgress);
  $('#completedTasks').text(completed);
  $('#pendingTasks').text(pending);
  $('#todayTasks').text(today);
}

function loadTasks(page = 1) {
  $('#taskstable tbody').html(`
    <tr>
      <td colspan="12" class="loading-state">
        <i class="bi bi-arrow-repeat"></i>
        <p class="mt-2 mb-0">Loading tasks...</p>
      </td>
    </tr>
  `);

  $.ajax({
    url: "{{ route('all-tasks.fetch') }}",
    type: "GET",
    dataType: 'json',
    success: function(data) {
      allTasks = data || [];
      
      // Apply filters
      let filteredTasks = applyFilters(allTasks);
      
      // Update summary stats
      loadSummaryStats();
      
      // Render table
      renderTasksTable(filteredTasks, page);
    },
    error: function(xhr, status, error) {
      console.error('Error loading tasks:', xhr.responseText, status, error);
      $('#taskstable tbody').html(`
        <tr>
          <td colspan="12" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            <p class="mt-2">Failed to load tasks. Please try again.</p>
          </td>
        </tr>
      `);
    }
  });
}

function applyFilters(tasks) {
  let filtered = [...tasks];
  
  const filterUser = $('#filter_user').val();
  const filterStatus = $('#filter_status').val();
  const filterPriority = $('#filter_priority').val();
  const filterType = $('#filter_type').val();
  const filterDateFrom = $('#filter_date_from').val();
  const filterDateTo = $('#filter_date_to').val();
  const searchTerm = $('#search').val().toLowerCase();
  
  if (filterUser) {
    filtered = filtered.filter(task => {
      const assigned = Array.isArray(task.assigned_users) ? task.assigned_users : [];
      if (assigned.some(user => String(user.id) === String(filterUser))) {
        return true;
      }
      return String(task.user_id || '') === String(filterUser);
    });
  }
  
  if (filterStatus) {
    if (filterStatus === 'done') {
      filtered = filtered.filter(task => task.is_done == 1 || task.is_done == true);
    } else {
      filtered = filtered.filter(task => task.task_status_id == filterStatus && (!task.is_done || task.is_done == 0 || task.is_done == false));
    }
  }
  
  if (filterPriority) {
    filtered = filtered.filter(task => {
      const priorityId = task.task_priority_id ?? (task.priority ? task.priority.id : null);
      return String(priorityId || '') === String(filterPriority);
    });
  }
  
  if (filterType) {
    filtered = filtered.filter(task => (task.task_type || 'task') === filterType);
  }
  
  if (filterDateFrom) {
    const fromDate = new Date(filterDateFrom);
    filtered = filtered.filter(task => {
      if (!task.created_at) return false;
      return new Date(task.created_at) >= fromDate;
    });
  }
  
  if (filterDateTo) {
    const toDate = new Date(filterDateTo);
    toDate.setHours(23,59,59,999);
    filtered = filtered.filter(task => {
      if (!task.created_at) return false;
      return new Date(task.created_at) <= toDate;
    });
  }
  
  if (searchTerm) {
    filtered = filtered.filter(task => {
      const taskName = (task.task_name || '').toLowerCase();
      const taskDesc = (task.task || '').toLowerCase();
      const customerName = (task.customer?.name || '').toLowerCase();
      const creatorName = (task.creator?.name || '').toLowerCase();
      
      return taskName.includes(searchTerm) || 
             taskDesc.includes(searchTerm) || 
             customerName.includes(searchTerm) ||
             creatorName.includes(searchTerm);
    });
  }
  
    return filtered;
}

function isDateOverdue(dateString) {
  if (!dateString) return false;
  const now = new Date();
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  
  let due;
  // Handle YYYY-MM-DD string as local date
  if (typeof dateString === 'string' && dateString.length >= 10) {
     const parts = dateString.substring(0, 10).split('-');
     if (parts.length === 3) {
         due = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
     } else {
         due = new Date(dateString);
         due.setHours(0,0,0,0);
     }
  } else {
     due = new Date(dateString);
     due.setHours(0,0,0,0);
  }
  return due < today;
}

function renderTasksTable(tasks, page = 1) {
  const perPage = 10;
  const start = (page - 1) * perPage;
  const end = start + perPage;
  const paginatedTasks = tasks.slice(start, end);
  
  let overdueCount = 0;
  let html = '';
  
  if (paginatedTasks.length === 0) {
    html = `<tr>
      <td colspan="12" class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>No Tasks Found</h5>
        <p>No tasks available at the moment.</p>
      </td>
    </tr>`;
  } else {
    paginatedTasks.forEach(function(task) {
      // Check Overdue
      let isOverdue = false;
      const statusName = (task.status && task.status.name) ? task.status.name.toLowerCase() : '';
      const isTaskCompleted = task.is_done || statusName === 'done' || statusName.includes('completed') || statusName.includes('complete');
      
      if (task.due_date && !isTaskCompleted) {
         if (isDateOverdue(task.due_date)) {
             isOverdue = true;
             overdueCount++;
         }
      }
      const rowClass = isOverdue ? 'row-overdue' : '';

      // Status badge
      let statusBadge = '';
      if (task.is_done) {
        statusBadge = '<span class="badge bg-success">Done</span>';
      } else if (task.status) {
        let statusColor = task.status.color || '#6c757d';
        statusBadge = `<span class="badge" style="background-color: ${statusColor}">${task.status.name}</span>`;
      } else {
        statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
      }
      
      // Priority badge
      let priorityBadge = 'N/A';
      if (task.priority) {
        let priorityColor = task.priority.color || '#6c757d';
        priorityBadge = `<span class="badge" style="background-color: ${priorityColor}">${task.priority.name}</span>`;
      }
      
      // Assigned users
      let assignedTo = 'N/A';
      if (task.assigned_users && task.assigned_users.length > 0) {
        assignedTo = task.assigned_users.map(u => u.name).join(', ');
      } else if (task.user) {
        assignedTo = task.user.name;
      }
      
      // Type badge
      let typeBadge = task.task_type || 'task';
      let typeColor = '#6c757d';
      if (typeBadge === 'qc') typeColor = '#0dcaf0';
      else if (typeBadge === 'cp') typeColor = '#dc3545';
      else typeColor = '#0d6efd';
      
      // Due date
      let dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('en-GB') : 'N/A';
      
      // Created at
      let createdAt = task.created_at ? new Date(task.created_at).toLocaleDateString('en-GB') : 'N/A';
      
      // Images count
      let imagesCount = 0;
      if (task.images && Array.isArray(task.images)) {
        imagesCount = task.images.length;
      }
      let imagesDisplay = imagesCount > 0 ? `<span class="badge bg-info">${imagesCount}</span>` : 'N/A';
      
      // Done toggle button
      let doneBtn = task.is_done 
        ? `<button class="btn btn-sm btn-secondary" onclick="toggleDone(${task.id})" title="Mark as Pending"><i class="bi bi-x-circle"></i></button>`
        : `<button class="btn btn-sm btn-success" onclick="toggleDone(${task.id})" title="Mark as Done"><i class="bi bi-check-circle"></i></button>`;
      
      html += `
        <tr class="${rowClass}">
          <td>
            <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none" title="${assignedTo}">
              ${assignedTo.length > 7 ? assignedTo.substring(0, 7) + '...' : assignedTo}
            </a>
          </td>
          <td>
            <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none" title="${task.customer?.name || 'N/A'}">
              ${(task.customer?.name || 'N/A').length > 7 ? (task.customer?.name || 'N/A').substring(0, 7) + '...' : (task.customer?.name || 'N/A')}
            </a>
          </td>
          <td>
            <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-primary fw-bold text-decoration-none" title="${task.task_name || ''}">
              ${(task.task_name || 'N/A').length > 7 ? (task.task_name || 'N/A').substring(0, 7) + '...' : (task.task_name || 'N/A')}
            </a>
          </td>
          <td><span class="badge" style="background-color: ${typeColor}">${typeBadge.toUpperCase()}</span></td>
          <td>${priorityBadge}</td>
          <td>${statusBadge}</td>
          <td>${dueDate}</td>
          <td>
            <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none" title="${task.creator?.name || 'N/A'}">
              ${(task.creator?.name || 'N/A').length > 7 ? (task.creator?.name || 'N/A').substring(0, 7) + '...' : (task.creator?.name || 'N/A')}
            </a>
          </td>
          <td>${createdAt}</td>
          <td>
            <button class="btn btn-sm btn-primary" onclick="editTask(${task.id})" title="Edit">
              <i class="bi bi-pencil"></i>
            </button>
            ${doneBtn}
            <button class="btn btn-sm btn-poke" onclick="pokeTask(this, ${task.id})" title="Poke Assigned User">
              <i class="bi bi-bell"></i>
            </button>
            <button class="btn btn-sm btn-danger" onclick="deleteTask(${task.id})" title="Delete">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
      `;
    });
  }
  
  console.log('Total overdue tasks on this page:', overdueCount);

  // Debug: Total overdue in entire filtered list
  const totalOverdue = tasks.filter(t => {
       const sName = (t.status && t.status.name) ? t.status.name.toLowerCase() : '';
       const isDone = t.is_done || sName === 'done' || sName.includes('completed') || sName.includes('complete');
       return t.due_date && !isDone && isDateOverdue(t.due_date);
  }).length;
  console.log('Total overdue in entire filtered list:', totalOverdue);

  $('#taskstable tbody').html(html);
  
  // Render pagination
  const totalPages = Math.ceil(tasks.length / perPage);
  renderPagination(page, totalPages);
  
  // Update range info
  const from = tasks.length > 0 ? start + 1 : 0;
  const to = Math.min(end, tasks.length);
  updateRangeInfo(from, to, tasks.length);
}

function renderPagination(current, last) {
  let pagination = $('#paginationLinks');
  pagination.empty();
  
  if (last <= 1) return;
  
  pagination.append(`
    <li class="page-item ${current === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${current - 1}">
        <i class="bi bi-chevron-left"></i> Previous
      </a>
    </li>
  `);
  
  pagination.append(`
    <li class="page-item active">
      <span class="page-link">${current} / ${last}</span>
    </li>
  `);
  
  pagination.append(`
    <li class="page-item ${current === last ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${current + 1}">
        Next <i class="bi bi-chevron-right"></i>
      </a>
    </li>
  `);
}

function updateRangeInfo(from, to, total) {
  const $info = $('#tasksRangeInfo');
  if (!$info.length) return;
  
  const totalValue = Number(total) || 0;
  const safeStart = totalValue === 0 ? 0 : (Number(from) || 1);
  const safeEnd = totalValue === 0 ? 0 : (Number(to) || safeStart);
  
  $info.text(`Showing ${safeStart}-${safeEnd} from ${totalValue} data`);
}

// Event handlers
$(document).on('click', '#paginationLinks .page-link', function(e) {
  e.preventDefault();
  const page = $(this).data('page');
  if (page && page > 0) {
    currentPage = page;
    const filtered = applyFilters(allTasks);
    renderTasksTable(filtered, page);
  }
});

$(document).on('change', '#filter_user, #filter_status, #filter_priority, #filter_type, #filter_date_from, #filter_date_to', function() {
  currentPage = 1;
  const filtered = applyFilters(allTasks);
  renderTasksTable(filtered, currentPage);
});

let searchTimeout;
$('#search').on('keyup', function() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(function() {
    currentPage = 1;
    const filtered = applyFilters(allTasks);
    renderTasksTable(filtered, currentPage);
  }, 300);
});

// Load data on page load
$(document).ready(function() {
  // Store users globally for edit modal
  window.globalUsers = [];
  
  // Load users
  $.get("{{ route('task.users') }}", function(data) {
    let options = '<option value="">All Users</option>';
    if (data && data.length > 0) {
      window.globalUsers = data; // Store globally
      $.each(data, function(i, user) {
        options += `<option value="${user.id}">${user.name}</option>`;
      });
    }
    $('#filter_user').html(options);
  });
  
  // Load statuses
  $.get("{{ route('task.statuses') }}", function(data) {
    let options = '<option value="">All Statuses</option><option value="done">Done</option>';
    if (data && data.length > 0) {
      $.each(data, function(i, status) {
        options += `<option value="${status.id}">${status.name}</option>`;
      });
    }
    $('#filter_status').html(options);
  });
  
  // Load priorities
  $.get("{{ route('task.priorities') }}", function(data) {
    let options = '<option value="">All Priorities</option>';
    if (data && data.length > 0) {
      $.each(data, function(i, priority) {
        options += `<option value="${priority.id}">${priority.name}</option>`;
      });
    }
    $('#filter_priority').html(options);
  });
  
  loadTasks();
});

// Action button functions
    
    window.viewTaskDetails = function(id) {
      const task = allTasks.find(t => t.id === id);
      if (task) {
        // Assigned users logic reuse
        let assignedTo = 'N/A';
        if (task.assigned_users && task.assigned_users.length > 0) {
            assignedTo = task.assigned_users.map(u => u.name).join(', ');
        } else if (task.user) {
            assignedTo = task.user.name;
        }

        $('#view_assigned_to').text(assignedTo);
        $('#view_customer').text(task.customer?.name || 'N/A');
        $('#view_created_by').text(task.creator?.name || 'N/A');
        $('#view_task_name').text(task.task_name || 'N/A');
        $('#view_task_description').text(task.task || 'No description provided.');

        // Images logic
        const imagesContainer = $('#view_task_images_container');
        const imagesDiv = $('#view_task_images');
        imagesDiv.empty();

        if (task.images && task.images.length > 0) {
            task.images.forEach(function(img) {
                let imageUrl = '';
                
                // Prioritize the secure route if ID exists to avoid 403 on storage links
                if (img.id) {
                     imageUrl = `/task/${task.id}/image/${img.id}`;
                } else if (img.url) {
                    imageUrl = img.url;
                } else if (img.image_path) {
                    imageUrl = `/storage/${img.image_path}`;
                }

                if (imageUrl) {
                    imagesDiv.append(`
                        <a href="${imageUrl}" target="_blank" class="d-block border rounded overflow-hidden" style="width: 80px; height: 80px;">
                            <img src="${imageUrl}" class="w-100 h-100" style="object-fit: cover;" alt="Task Image"
                                onerror="handleViewImageError(this, ${task.id}, ${img.id || 'null'}, '${img.image_path || ''}')">
                        </a>
                    `);
                }
            });
            imagesContainer.show();
        } else {
            imagesContainer.hide();
        }

        $('#viewTaskModal').modal('show');
      }
    };

    // Store selected edit images
    let selectedEditImages = [];

    // Delete task image
    window.deleteTaskImage = function(taskId, imageId, containerId) {
        if (!confirm('Are you sure you want to delete this image?')) {
            return;
        }

        $.ajax({
            url: `/task/${taskId}/image/${imageId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Remove image container from DOM
                const container = document.getElementById(containerId);
                if (container) {
                    container.style.transition = 'opacity 0.3s';
                    container.style.opacity = '0';
                    setTimeout(() => {
                        container.remove();
                    }, 300);
                }
                alert('Image deleted successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error deleting image:', xhr.responseText, status, error);
                alert('Error: Failed to delete image');
            }
        });
    };

    // Handle image load errors
    window.handleImageError = function(imageId, taskId, imageIdNum) {
        const img = document.getElementById(imageId);
        const placeholder = document.getElementById(imageId + '-placeholder');
        
        if (img) {
            img.style.display = 'none';
            // Try alternative URL if available
            if (imageIdNum && img.src.includes('/storage/')) {
                const altUrl = `/task/${taskId}/image/${imageIdNum}`;
                img.src = altUrl;
                img.style.display = 'block';
                img.onerror = function() {
                    this.style.display = 'none';
                    if (placeholder) placeholder.style.display = 'block';
                };
            } else {
                if (placeholder) placeholder.style.display = 'block';
            }
        }
    };

    window.handleViewImageError = function(img, taskId, imageId, imagePath) {
        if (img.dataset.retried) return;
        img.dataset.retried = true;

        // If currently using route URL, try storage URL, and vice versa
        if (img.src.includes('/task/')) {
            img.src = `/storage/${imagePath}`;
        } else {
            img.src = `/task/${taskId}/image/${imageId}`;
        }
    };

    function addPastedFilesToSelectedEdit(files) {
        if (!files || files.length === 0) return;
        files.forEach((file) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                selectedEditImages.push({ file, name: file.name, size: file.size, preview: e.target.result });
                // Render immediately into edit preview
                $('#editImagePreview').append(`
                    <div class="d-inline-block me-2 mb-2" style="position: relative;">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                        <small class="d-block text-truncate" style="max-width: 80px;" title="${file.name}">${file.name}</small>
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        });
    }

    function filesFromClipboard(event) {
        const items = (event.clipboardData || event.originalEvent?.clipboardData)?.items || [];
        const files = [];
        for (let i = 0; i < items.length; i++) {
            const it = items[i];
            if (it.kind === 'file') {
                const file = it.getAsFile();
                if (file && file.type && file.type.startsWith('image/')) {
                    files.push(file);
                }
            }
        }
        return files;
    }

    // Paste on edit image preview
    $('#editImagePreview').on('paste', function(event) {
        const files = filesFromClipboard(event);
        if (files.length > 0) {
            event.preventDefault();
            addPastedFilesToSelectedEdit(files);
        }
    });

    // Handle file input change
    $('#edit_task_images').on('change', function(e) {
        const preview = $('#editImagePreview');
        const files = e.target.files || [];
        if (files.length > 0) {
            preview.append('<small class="text-muted d-block mb-2">New images to add:</small>');
            Array.from(files).forEach((file) => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.append(`
                        <div class="d-inline-block me-2 mb-2" style="position: relative;">
                            <img src="${ev.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                            <small class="d-block text-truncate" style="max-width: 80px;" title="${file.name}">${file.name}</small>
                        </div>
                    `);
                    // Add to selected images array if needed, but for input[type=file] we can rely on FormData
                    // However, we want to unify them. 
                    // To keep it simple: we'll append files from input to FormData directly on submit
                    selectedEditImages.push({ file: file }); 
                };
                reader.readAsDataURL(file);
            });
        }
    });

    // Clean up on modal close
    $('#editTaskModal').on('hidden.bs.modal', function() {
        selectedEditImages = [];
        $('#edit_task_images').val('');
        $('#editImagePreview').empty();
        $('#existingImages').empty();
        $('#editTaskForm')[0].reset();
    });

window.editTask = function(id) {
  console.log('Editing task:', id);

  // Reset images
  selectedEditImages = [];
  $('#edit_task_images').val('');
  $('#editImagePreview').empty();
  $('#existingImages').empty();

  // Load edit modal dropdowns
  function loadEditModalDropdowns() {
    // Load customers
    $.get("{{ route('task.customers') }}", function(data) {
      let options = '<option value="">Select Customer</option>';
      if (data && data.length > 0) {
        $.each(data, function(i, customer) {
          options += `<option value="${customer.id}">${customer.name}</option>`;
        });
      }
      $('#edit_customer_id').html(options);
    });

    // Load statuses
    $.get("{{ route('task.statuses') }}", function(data) {
      let options = '<option value="">Select Status</option>';
      if (data && data.length > 0) {
        $.each(data, function(i, status) {
          options += `<option value="${status.id}">${status.name}</option>`;
        });
      }
      $('#edit_task_status_id').html(options);
    });

    // Load priorities
    $.get("{{ route('task.priorities') }}", function(data) {
      let options = '<option value="">Select Priority</option>';
      if (data && data.length > 0) {
        $.each(data, function(i, priority) {
          options += `<option value="${priority.id}">${priority.name}</option>`;
        });
      }
      $('#edit_task_priority_id').html(options);
    });
  }

  loadEditModalDropdowns();

  $.get("{{ route('all-tasks.fetch') }}", function(data) {
    let task = (data || []).find(t => t.id === id);
    if (task) {
      $('#edit_task_id').val(task.id);

      setTimeout(function() {
        // Load user checkboxes with selected users
        const editAssignees = Array.isArray(task.assigned_users) && task.assigned_users.length
          ? task.assigned_users.map(user => String(user.id))
          : (task.user_id ? [String(task.user_id)] : []);
        
        // Render checkboxes with selected users
        const container = $('#globalEditAssignUsersContainer');
        if (window.globalUsers && window.globalUsers.length) {
          const selectedSet = new Set(editAssignees);
          const html = window.globalUsers.map(user => {
            const id = String(user.id);
            const checked = selectedSet.has(id) ? 'checked' : '';
            return `
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="${id}" id="globalEditAssignUsersContainer_${id}" name="user_ids[]" ${checked}>
                <label class="form-check-label" for="globalEditAssignUsersContainer_${id}">${user.name}</label>
              </div>`;
          }).join('');
          container.html(html);
        }

        $('#edit_customer_id').val(task.customer_id);
        $('#edit_task_name').val(task.task_name || '');
        $('#edit_task').val(task.task || '');
        $('#edit_task_status_id').val(task.task_status_id);
        $('#edit_task_priority_id').val(task.task_priority_id);
        $('#edit_due_date').val(task.due_date ? task.due_date.substring(0, 10) : '');

        const taskType = task.task_type || 'task';
        $(`input[name="edit_task_type"][value="${taskType}"]`).prop('checked', true);

        // Display existing images
        const existingImagesDiv = $('#existingImages');
        existingImagesDiv.empty();
        if (task.images && task.images.length > 0) {
            existingImagesDiv.append('<small class="text-muted d-block mb-2">Existing images:</small>');
            task.images.forEach(function(img, idx) {
                let imageUrl = '';
                if (img.url) {
                    imageUrl = img.url;
                } else if (img.image_path) {
                    imageUrl = `/storage/${img.image_path}`;
                    if (img.id) {
                        imageUrl = `/task/${task.id}/image/${img.id}`;
                    }
                }

                const imageName = img.original_name || 'Image';
                const imageId = `edit-img-${task.id}-${idx}`;

                existingImagesDiv.append(`
                    <div class="d-inline-block me-2 mb-2 position-relative" id="img-container-${task.id}-${img.id}" style="border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                        <button type="button" 
                                class="btn btn-sm btn-danger position-absolute" 
                                style="top: 5px; right: 5px; padding: 2px 6px; font-size: 0.75rem; z-index: 10; line-height: 1; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;"
                                onclick="deleteTaskImage(${task.id}, ${img.id}, 'img-container-${task.id}-${img.id}')" 
                                title="Remove Image">
                            <i class="bi bi-x" style="font-size: 0.8rem; font-weight: bold;"></i>
                        </button>
                        ${imageUrl ? `
                            <img id="${imageId}" 
                                    src="${imageUrl}" 
                                    class="img-thumbnail" 
                                    style="width: 80px; height: 80px; object-fit: cover; display: block; cursor: pointer;"
                                    onclick="window.open('${imageUrl}', '_blank')"
                                    onerror="handleImageError('${imageId}', '${task.id}', ${img.id || 'null'})">
                            <div id="${imageId}-placeholder" style="width: 80px; height: 80px; display: none; background: #f0f0f0; border: 1px dashed #ccc; text-align: center; line-height: 80px; font-size: 0.7rem; color: #999;">
                                <i class="bi bi-image"></i>
                            </div>
                        ` : `
                            <div style="width: 80px; height: 80px; background: #f0f0f0; border: 1px dashed #ccc; text-align: center; line-height: 80px; font-size: 0.7rem; color: #999;">
                                <i class="bi bi-image"></i>
                            </div>
                        `}
                        <small class="d-block text-truncate mt-1" style="max-width: 80px;" title="${imageName}">${imageName}</small>
                    </div>
                `);
            });
        }

      }, 200);

      $('#editTaskModal').modal('show');
    } else {
      alert('Task not found.');
    }
  }).fail(function() {
    alert('Error loading task data');
  });
};

// Handle edit form submission
$('#editTaskForm').on('submit', function(e) {
  e.preventDefault();

  const taskId = $('#edit_task_id').val();
  const formData = new FormData(this);

  formData.append('_method', 'PUT');
  formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

  const taskType = $('input[name="edit_task_type"]:checked').val() || 'task';
  formData.set('task_type', taskType);

  // Append images
  if (selectedEditImages.length > 0) {
      selectedEditImages.forEach((img) => {
          // Avoid duplicating files if they were added via standard input
          // But since we control the array, let's just append
          formData.append('images[]', img.file);
      });
  }

  $.ajax({
    url: `/task/${taskId}`,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function(response) {
      console.log('Task updated:', response);
      alert(response.message || 'Task updated successfully!');
      $('#editTaskModal').modal('hide');
      $('#editTaskForm')[0].reset();
      selectedEditImages = [];
      $('#editImagePreview').empty();
      $('#existingImages').empty();
      loadTasks();
    },
    error: function(xhr, status, error) {
      console.error('Error updating task:', xhr.responseText, status, error);
      let errorMsg = 'Failed to update task';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMsg = xhr.responseJSON.message;
      } else if (xhr.responseJSON && xhr.responseJSON.errors) {
        errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
      }
      alert('Error: ' + errorMsg);
    }
  });
});

window.deleteTask = function(id) {
  if (!confirm('Are you sure you want to delete this task?')) return;
  
  console.log('Deleting task:', id);
  $.ajax({
    url: `/task/${id}`,
    type: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
      console.log('Task deleted:', response);
      alert(response.message || 'Task deleted successfully!');
      loadTasks();
    },
    error: function(xhr, status, error) {
      console.error('Error deleting task:', xhr.responseText, status, error);
      alert('Error: ' + (xhr.responseJSON?.message || 'Failed to delete task'));
    }
  });
};

window.pokeTask = function(btn, id) {
  if (!confirm('Send poke email to the assigned user for this task?')) return;
  
  const $btn = $(btn);
  const oldHtml = $btn.html();
  $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
  
  $.ajax({
    url: `/task/${id}/poke`,
    type: 'POST',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    dataType: 'json',
    success: function(response) {
      $btn.html('<i class="bi bi-check2-circle"></i>');
      const badge = $('<span class="poke-sent-badge">Poked</span>');
      $btn.after(badge);
      setTimeout(function(){
        badge.fadeOut(200, function(){ $(this).remove(); });
        $btn.prop('disabled', false).html(oldHtml);
      }, 1400);
    },
    error: function(xhr) {
      let msg = 'Failed to send poke';
      if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
      alert('Error: ' + msg);
      $btn.prop('disabled', false).html(oldHtml);
    }
  });
};

window.toggleDone = function(id) {
  console.log('Toggling done status for task:', id);
  
  $.ajax({
    url: `/task/${id}/toggle-done`,
    type: 'POST',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    dataType: 'json',
    success: function(response) {
      console.log('Task status toggled:', response);
      // Reload tasks to reflect the change
      const filtered = applyFilters(allTasks);
      loadTasks();
    },
    error: function(xhr, status, error) {
      console.error('Error toggling task status:', xhr.responseText, status, error);
      alert('Error: Failed to update task status');
    }
  });
};
