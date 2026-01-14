@extends('layouts.app')

@section('title', 'Form Database Config')
@section('page_title', 'Database Configuration: ' . $form->name)

@push('styles')
<style>
    .config-card { 
        border-radius: 3px; 
        border: none;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }
    .config-header { 
        background: #434afa; 
        color: #fff; 
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        padding: 1rem 1.5rem;
    }
    .config-section { 
        background: #f8fafc; 
        border-radius: 3px; 
        padding: 1rem; 
        margin-bottom: 1.5rem; 
        border: 1px solid #e2e8f0;
    }
    .config-label { 
        font-weight: 600; 
        color: #1e293b; 
        margin-bottom: 0.5rem; 
    }
    .form-control {
        border-radius: 3px;
        border: 1px solid #cbd5e1;
        padding: 0.5rem 0.75rem;
    }
    .form-control:focus {
        border-color: #434afa;
        box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.1);
    }
    .btn {
        border-radius: 3px;
    }
    .btn-primary {
        background: #434afa;
        border: none;
    }
    .btn-primary:hover {
        background: #3538d4;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="card config-card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center config-header">
            <h5 class="mb-0 text-white"><i class="bi bi-gear me-2"></i>Database Configuration</h5>
            <!-- <a href="{{ route('formbuilder.index') }}" class="btn btn-sm btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a> -->
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- <div class="config-section">
                <div class="config-label">Form: <strong>{{ $form->name }}</strong></div>
                <p class="text-muted mb-3">Configure the database where form submissions will be saved. This form will insert data into the specified database.</p>
            </div> -->

            <form method="POST" action="{{ route('formbuilder.config.save', $form->id) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Database Host <span class="text-danger">*</span></label>
                        <input type="text" name="db_host" class="form-control" value="{{ old('db_host', $form->db_host ?? '127.0.0.1') }}" required>
                        @error('db_host') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Port</label>
                        <input type="text" name="db_port" class="form-control" value="{{ old('db_port', $form->db_port ?? '3306') }}" placeholder="3306">
                        @error('db_port') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Database Username <span class="text-danger">*</span></label>
                        <input type="text" name="db_username" class="form-control" value="{{ old('db_username', $form->db_username ?? '') }}" required>
                        @error('db_username') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Database Password</label>
                        <input type="password" name="db_password" class="form-control" value="{{ old('db_password', $form->db_password ?? '') }}" placeholder="Leave blank if no password">
                        @error('db_password') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Database Name <span class="text-danger">*</span></label>
                        <input type="text" name="db_database" class="form-control" value="{{ old('db_database', $form->db_database ?? '') }}" required placeholder="e.g., workorio, tenant_1">
                        @error('db_database') <div class="text-danger small">{{ $message }}</div> @enderror
                        <!-- <small class="text-muted">The database must contain an <code>indiamartleads</code> table.</small> -->
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Save Configuration
                    </button>
                    <button type="button" class="btn btn-primary ms-2" onclick="testConnection()" title="Test Connection" data-bs-toggle="tooltip" style="background-color: #434afa; border-color: #434afa; color: white;">
                        <i class="bi bi-lightning-charge"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function testConnection() {
    const host = document.querySelector('input[name="db_host"]').value;
    const port = document.querySelector('input[name="db_port"]').value || '3306';
    const username = document.querySelector('input[name="db_username"]').value;
    const password = document.querySelector('input[name="db_password"]').value;
    const database = document.querySelector('input[name="db_database"]').value;

    if (!host || !username || !database) {
        alert('Please fill in all required fields (Host, Username, Database Name)');
        return;
    }

    try {
        const res = await fetch(`{{ route('formbuilder.config.test', $form->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': `{{ csrf_token() }}`
            },
            body: JSON.stringify({ host, port, username, password, database })
        });
        const data = await res.json();
        if (res.ok && data.success) {
            alert('Connection successful! Database and table found.');
        } else {
            alert('Connection failed: ' + (data.message || 'Unknown error'));
        }
    } catch(e) {
        alert('Error testing connection: ' + e.message);
    }
}
</script>
@endpush

