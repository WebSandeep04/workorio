

<?php $__env->startSection('title', 'Form Builder'); ?>
<?php $__env->startSection('page_title', isset($form) ? 'Edit Form: ' . $form->name : 'Create New Form'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Import fonts */
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

    body {
        font-family: 'Montserrat', sans-serif !important;
        background-color: #f4f5f7;
    }

    .fb-card { 
        border-radius: 8px; 
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        background: #fff;
        overflow: hidden;
    }
    
    .fb-header { 
        background: #434afa; 
        color: #fff; 
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .fb-header h5 {
        font-weight: 600;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }

    /* Header Buttons */
    .fb-header .btn {
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.4rem 1rem;
        font-family: 'Montserrat', sans-serif;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s;
    }
    
    .fb-header .btn-primary { 
        background: #ffffff; 
        color: #000000;
        border: 1px solid #fff; 
    }
    .fb-header .btn-primary:hover {
        background: #f1f5f9;
        color: #000000;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .fb-header .btn-outline-light {
        border-color: rgba(255,255,255,0.6);
        color: #fff;
    }
    .fb-header .btn-outline-light:hover {
        background: rgba(255,255,255,0.1);
        border-color: #fff;
    }
    
    /* Panels */
    .panel { 
        border: 1px solid #e2e8f0; 
        border-radius: 8px; 
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .panel-header { 
        background: #f8fafc; 
        border-bottom: 1px solid #e2e8f0; 
        padding: 0.85rem 1.25rem; 
        font-weight: 600; 
        color: #1e293b; 
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        font-size: 0.9rem;
    }
    .panel-body { padding: 1.25rem; }

    /* Forms */
    .form-control {
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: #434afa;
        box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.1);
    }
    .form-label {
        font-weight: 500;
        color: #475569;
        font-size: 0.85rem;
    }
    
    /* Field List */
    .fb-field-list { 
        max-height: 450px; 
        overflow-y: auto; 
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        padding: 1rem !important;
        background: #fcfcfc;
    }
    
    .fb-field-list .form-check {
        margin-bottom: 0.5rem;
        padding-left: 1.75rem;
    }
    .fb-field-list .form-check-input {
        width: 1.1em;
        height: 1.1em;
        margin-left: -1.75rem;
        border-color: #cbd5e1;
        cursor: pointer;
    }
    .fb-field-list .form-check-input:checked {
        background-color: #434afa;
        border-color: #434afa;
    }
    .fb-field-list .form-check-label {
        cursor: pointer;
        font-size: 0.9rem;
        color: #334155;
    }

    /* Chips */
    .chip { 
        display: inline-flex; 
        align-items: center; 
        background: #f0fdf4; /* Greenish tint for selection? Or Blue */
        background: #eff6ff;
        color: #434afa; 
        border: 1px solid #bfdbfe; 
        border-radius: 6px; 
        padding: .25rem .75rem; 
        font-size: .75rem; 
        margin: .25rem .25rem .25rem 0; 
        font-weight: 600;
        transition: all 0.2s;
    }
    .chip .x { 
        cursor: pointer; 
        opacity: .6; 
        margin-left: .5rem; 
        font-size: 1.1rem;
        line-height: .5;
    }
    .chip .x:hover { opacity: 1; color: #ef4444; }
    
    .badge-soft { 
        background: #e0e7ff; 
        color: #434afa; 
        border: 1px solid #c7d2fe; 
        font-weight: 600;
        border-radius: 4px;
        padding: 0.35em 0.65em;
    }

    .sticky-preview { position: sticky; top: 1.5rem; }
    
    /* Preview */
    #fb-preview {
        background: #fff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    #fb-preview .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #1e293b;
    }
    #fb-preview .btn-primary {
        background: #434afa;
        border: none;
        padding: .5rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(67, 74, 250, 0.2);
    }
    #fb-preview .btn-primary:hover {
        background: #3538d4;
        transform: translateY(-1px);
    }
    
    /* Scrollbar */
    .fb-field-list::-webkit-scrollbar {
        width: 6px;
    }
    .fb-field-list::-webkit-scrollbar-track {
        background: #f1f5f9; 
    }
    .fb-field-list::-webkit-scrollbar-thumb {
        background: #cbd5e1; 
        border-radius: 3px;
    }
    .fb-field-list::-webkit-scrollbar-thumb:hover {
        background: #94a3b8; 
    }
    
    .btn-theme-sm {
        background-color: #434afa;
        color: white;
        border: 1px solid transparent;
        font-weight: 500;
        padding: 0.25rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 4px;
    }
    .btn-theme-sm:hover {
        background-color: #3538d4;
        color: white;
        box-shadow: 0 2px 4px rgba(67, 74, 250, 0.2);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <div class="card fb-card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center fb-header">
            <h5 class="mb-0 text-white">
                <i class="bi bi-ui-checks me-2"></i>
                <?php echo e(isset($form) ? 'Edit Form' : 'Create New Form'); ?>

            </h5>
            <div>
                <button class="btn btn-primary btn-sm" id="fb-save-form">
                    <i class="bi bi-save me-1"></i><?php echo e(isset($form) ? 'Update Form' : 'Save Form'); ?>

                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="panel mb-3">
                        <div class="panel-header d-flex justify-content-between align-items-center">
                            <span>Form Settings</span>
                            <span class="badge badge-soft" id="fb-selected-count">0 selected</span>
                        </div>
                        <div class="panel-body">
                            <div class="mb-3">
                                <label class="form-label mb-1">Form Name</label>
                                <input type="text" id="fb-form-name" class="form-control form-control-sm"
                                       placeholder="Enter form name" value="<?php echo e(isset($form) ? $form->name : ''); ?>">
                            </div>
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <div class="fb-section-title m-0 fw-semibold text-secondary" style="font-size:0.9rem">Select Fields</div>
                                <div>
                                    <button class="btn btn-sm btn-theme-sm me-1" id="fb-select-all" title="Select All" data-bs-toggle="tooltip">
                                        <i class="bi bi-check-all" style="font-size: 1rem;"></i>
                                    </button>
                                    <button class="btn btn-sm btn-theme-sm" id="fb-clear-all" title="Clear All" data-bs-toggle="tooltip">
                                        <i class="bi bi-eraser" style="font-size: 1rem;"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="fb-field-list" class="fb-field-list border rounded p-2">
                                <!-- populated by JS -->
                            </div>
                            <div class="mt-2" id="fb-selected-chips"><!-- chips inserted here --></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="panel sticky-preview">
                        <div class="panel-header">Live Preview</div>
                        <div class="panel-body">
                            <div id="fb-preview" class="p-3 border rounded bg-light" style="min-height:420px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const isEditMode = <?php echo e(isset($form) ? 'true' : 'false'); ?>;
const formId = <?php echo e(isset($form) ? $form->id : 'null'); ?>;
const savedFields = <?php echo json_encode(isset($form) ? $form->fields : [], 15, 512) ?>;

async function fetchFields(){
    const res = await fetch(`<?php echo e(route('formbuilder.fields')); ?>`);
    return await res.json();
}

function toggleAll(state){
    document.querySelectorAll('#fb-field-list input[type="checkbox"]').forEach(cb => { cb.checked = state; });
    renderPreview();
}

function inferInputType(field){
    const name = (field.name || '').toLowerCase();
    const t = (field.data_type || '').toLowerCase();
    if (name.includes('email')) return 'email';
    if (name.includes('phone') || name.includes('mobile')) return 'tel';
    if (["int","bigint","integer","smallint","mediumint","tinyint","decimal","double","float"].includes(t)) return 'number';
    if (t === 'timestamp' || t === 'datetime') return 'datetime-local';
    if (t === 'date') return 'date';
    if (t.includes('text')) return 'textarea';
    return 'text';
}

function toTitle(str){
    let s = (str||'').replaceAll('_',' ').replaceAll('-', ' ');
    // remove leading 'sender ' prefix (case-insensitive)
    s = s.replace(/^\s*sender\s+/i, '');
    // split CamelCase boundaries before title-casing
    s = s.replace(/([a-z])([A-Z])/g, '$1 $2');
    s = s.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
    return s.trim();
}

async function loadAndRender(){
    const list = document.getElementById('fb-field-list');
    list.innerHTML = '<div class="text-muted">Loading fields…</div>';
    try{
        const data = await fetchFields();
        if (data.error){
            list.innerHTML = `<div class=\"text-danger\">${data.error}</div>`;
            return;
        }
        const cols = data.columns || [];
        const skip = ['id','created_at','updated_at','deleted_at'];
        list.innerHTML = '';
        
        // Create a map of saved field names for edit mode
        const savedFieldNames = new Set();
        if (isEditMode && savedFields.length > 0) {
            savedFields.forEach(f => savedFieldNames.add(f.name));
        }
        
        cols.forEach(col => {
            if (skip.includes(col.name)) return;
            const id = `col_${col.name}`;
            const wrap = document.createElement('div');
            wrap.className = 'form-check';
            const cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.className = 'form-check-input';
            cb.id = id;
            cb.dataset.name = col.name;
            cb.dataset.type = col.data_type;
            // In edit mode, check if field was previously selected; otherwise check required fields
            cb.checked = isEditMode ? savedFieldNames.has(col.name) : !!col.required;
            cb.addEventListener('change', () => { renderPreview(); updateSelectedMeta(); });
            cb.dataset.required = col.required ? '1' : '0';
            const label = document.createElement('label');
            label.className = 'form-check-label';
            const req = col.required ? ' <span class="text-danger">*</span>' : '';
            label.setAttribute('for', id);
            label.innerHTML = `${col.name}${req} <small class=\"text-muted\">(${col.data_type})</small>`;
            wrap.appendChild(cb);
            wrap.appendChild(label);
            list.appendChild(wrap);
        });
        renderPreview();
        updateSelectedMeta();
    }catch(e){
        list.innerHTML = `<div class=\"text-danger\">${e.message}</div>`;
    }
}

function renderPreview(){
    const preview = document.getElementById('fb-preview');
    preview.innerHTML = '';

    const form = document.createElement('form');
    form.className = 'row g-3';

    const selected = Array.from(document.querySelectorAll('#fb-field-list input[type="checkbox"]'))
        .filter(cb => cb.checked)
        .map(cb => ({ name: cb.dataset.name, data_type: cb.dataset.type, required: cb.dataset.required === '1' }));

    selected.forEach(field => {
        const group = document.createElement('div');
        group.className = 'col-12';
        const label = document.createElement('label');
        label.className = 'form-label fw-semibold';
        label.textContent = toTitle(field.name);
        let control;
        const type = inferInputType(field);
        if (type === 'textarea'){
            control = document.createElement('textarea');
            control.className = 'form-control';
            control.rows = 3;
        } else {
            control = document.createElement('input');
            control.type = type;
            control.className = 'form-control';
        }
        control.name = field.name;
        control.placeholder = `Enter ${toTitle(field.name)}`;
        group.appendChild(label);
        group.appendChild(control);
        form.appendChild(group);
    });

    const actions = document.createElement('div');
    actions.className = 'col-12 text-center';
    const btn = document.createElement('button');
    btn.type = 'submit';
    btn.className = 'btn btn-primary btn-sm';
    btn.textContent = 'Submit';
    actions.appendChild(btn);
    form.appendChild(actions);

    preview.appendChild(form);
}

// init
window.addEventListener('DOMContentLoaded', () => {
    loadAndRender();
    document.getElementById('fb-select-all').addEventListener('click', () => {
        document.querySelectorAll('#fb-field-list input[type="checkbox"]').forEach(cb=> cb.checked=true);
        renderPreview();
        updateSelectedMeta();
    });
    document.getElementById('fb-clear-all').addEventListener('click', () => {
        document.querySelectorAll('#fb-field-list input[type="checkbox"]').forEach(cb=> cb.checked=false);
        renderPreview();
        updateSelectedMeta();
    });
    document.getElementById('fb-save-form').addEventListener('click', saveForm);
});

async function saveForm(){
    const name = (document.getElementById('fb-form-name').value || '').trim();
    const selected = Array.from(document.querySelectorAll('#fb-field-list input[type="checkbox"]'))
        .filter(cb => cb.checked)
        .map(cb => ({ name: cb.dataset.name, type: cb.dataset.type, label: cb.dataset.name, required: cb.dataset.required === '1' }));

    if (!name){
        alert('Please enter a form name.');
        return;
    }
    if (selected.length === 0){
        alert('Please select at least one field.');
        return;
    }

    try{
        const url = isEditMode 
            ? `<?php echo e(url('/form-builder')); ?>/${formId}`
            : `<?php echo e(route('formbuilder.store')); ?>`;
        const method = isEditMode ? 'PUT' : 'POST';
        
        const res = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': `<?php echo e(csrf_token()); ?>`
            },
            body: JSON.stringify({ name, fields: selected })
        });
        const data = await res.json();
        if (!res.ok || !data.success){
            throw new Error(data.message || `Failed to ${isEditMode ? 'update' : 'save'} form`);
        }
        alert(`Form ${isEditMode ? 'updated' : 'saved'} successfully`);
        window.location.href = `<?php echo e(route('formbuilder.index')); ?>`;
    }catch(e){
        alert(e.message);
    }
}

function updateSelectedMeta(){
    const chipsWrap = document.getElementById('fb-selected-chips');
    const countBadge = document.getElementById('fb-selected-count');
    const selected = Array.from(document.querySelectorAll('#fb-field-list input[type="checkbox"]')).filter(cb=>cb.checked);
    countBadge.textContent = `${selected.length} selected`;
    chipsWrap.innerHTML = '';
    selected.slice(0, 12).forEach(cb => {
        const chip = document.createElement('span');
        chip.className = 'chip';
        chip.innerHTML = `${cb.dataset.name} <span class="x" title="Remove">×</span>`;
        chip.querySelector('.x').addEventListener('click', ()=>{
            cb.checked = false;
            renderPreview();
            updateSelectedMeta();
        });
        chipsWrap.appendChild(chip);
    });
    if (selected.length > 12){
        const more = document.createElement('span');
        more.className = 'chip';
        more.textContent = `+${selected.length - 12} more`;
        chipsWrap.appendChild(more);
    }
}
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/formbuilder/form.blade.php ENDPATH**/ ?>