@extends('layouts.app')

@section('title', 'Import New List')
@section('page_title', 'Lead Ingestion')

@push('styles')
<style>
    .import-wizard {
        max-width: 600px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
        font-family: Montserrat;
    }
    .wizard-header {
        background: #434AFA;
        padding: 1.5rem;
        color: #fff;
        text-align: center;
    }
    .wizard-steps {
        display: flex;
        justify-content: center;
        gap: 1rem;
        padding: 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .step-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
    }
    .step-circle.active { background: #434AFA; color: #fff; }
    .step-circle.completed { background: #10b981; color: #fff; }
    
    .wizard-body { padding: 2.5rem; }
    .step-content { display: none; }
    .step-content.active { display: block; animation: fadeIn 0.3s ease; }
    
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .form-group-modern { margin-bottom: 1.5rem; }
    .form-label-modern { display: block; font-size: 0.75rem; font-weight: 700; color: #475569; /* text-transform: uppercase; removed */ margin-bottom: 0.5rem; letter-spacing: 0.05em; }
    .form-control-modern { width: 100%; border: 2px solid #e2e8f0; border-radius: 8px; padding: 0.75rem; font-size: 0.9rem; transition: all 0.2s; }
    .form-control-modern:focus { border-color: #434AFA; outline: none; box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1); }

    .file-drop-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 3rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fbfcfe;
    }
    .file-drop-zone:hover { border-color: #434AFA; background: #f3f5ff; }
    .file-drop-zone i { font-size: 2.5rem; color: #94a3b8; display: block; margin-bottom: 1rem; }
    .file-name-display { margin-top: 1rem; font-weight: 600; color: #434AFA; font-size: 0.85rem; display: none; }

    .wizard-footer { padding: 1.5rem 2.5rem; background: #fbfcfe; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
    .btn-wizard { padding: 0.65rem 1.5rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; border: none; }
    .btn-next { background: #434AFA; color: #fff; }
    .btn-next:hover { background: #3339d6; transform: translateY(-1px); }
    .btn-prev { background: #e2e8f0; color: #475569; }
    
    .template-card {
        background: #fff8f0;
        border: 1px solid #feebc8;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .template-card i { color: #f6ad55; font-size: 1.2rem; }
    .template-card p { font-size: 0.75rem; color: #7b341e; margin: 0; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
    <div class="import-wizard">
        <div class="wizard-header">
            <h4 class="mb-0 fw-bold"><i class="bi bi-file-earmark-arrow-up me-2"></i> New Lead List</h4>
            <p class="mb-0 small opacity-75">Follow the steps to ingest your contacts</p>
        </div>
        
        <div class="wizard-steps">
            <div class="step-circle active" id="dot1">1</div>
            <div style="width: 40px; height: 2px; background: #e2e8f0; margin-top: 15px;"></div>
            <div class="step-circle" id="dot2">2</div>
        </div>

        <form action="{{ route('calling.list.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            <div class="wizard-body">
                <!-- Step 1: List Identity -->
                <div class="step-content active" id="step1">
                    <div class="form-group-modern">
                        <label for="listName" class="form-label-modern">Give your list a name</label>
                        <input type="text" name="name" id="listName" class="form-control-modern" placeholder="e.g. Real Estate HNI - Mumbai" required>
                    </div>
                    <div class="mt-4 p-3 bg-light rounded-3">
                        <small class="text-muted d-block mb-1">PRO TIP</small>
                        <p class="small mb-0 text-dark">A descriptive name helps you identify the lead source when creating future campaigns.</p>
                    </div>
                </div>

                <!-- Step 2: Data Upload -->
                <div class="step-content" id="step2">
                    <div class="alert alert-warning py-2 mb-3 border-0" style="background: #fff8f0; border-radius: 8px;">
                        <p class="small mb-0 text-dark"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i> <strong>Important:</strong> Please save your Excel file as <strong>CSV (Comma Delimited)</strong> before uploading.</p>
                    </div>
                    
                    <label class="form-label-modern">Upload Data Source (CSV Only)</label>
                    <div class="file-drop-zone" id="dropZone">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        <span class="fw-bold d-block text-dark">Select your CSV File</span>
                        <span class="text-muted small">Only .csv and .txt files are supported</span>
                        <div class="file-name-display" id="fileName"></div>
                        <input type="file" name="excel_file" id="fileInput" class="d-none" accept=".csv, .txt">
                    </div>

                    <div class="template-card">
                        <i class="bi bi-info-circle-fill"></i>
                        <div class="w-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p><strong>Recommended Headers:</strong> Name, Email, Phone, Address, City, State, Company Name, Contact Person, Legal Status, GST Number, Turnover.</p>
                                    <p class="mt-1">While only Name and Phone are mandatory, including these headers ensures all data is successfully mapped.</p>
                                </div>
                                <a href="{{ route('calling.list.download-template') }}" class="btn btn-sm btn-outline-primary fw-bold text-nowrap ms-2" style="font-size: 10px; border-radius: 4px;">
                                    <i class="bi bi-download me-1"></i> Dummy Template
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wizard-footer">
                <button type="button" class="btn-wizard btn-prev d-none" id="prevBtn">
                    <i class="bi bi-chevron-left"></i> Back
                </button>
                <div class="ms-auto">
                    <button type="button" class="btn-wizard btn-next" id="nextBtn">
                        Next <i class="bi bi-chevron-right"></i>
                    </button>
                    <button type="submit" class="btn-wizard btn-next d-none" id="submitBtn">
                        <i class="bi bi-check-circle-fill"></i> Ingest Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let currentStep = 1;

        $('#nextBtn').on('click', function() {
            if (currentStep === 1) {
                if (!$('#listName').val().trim()) {
                    Swal.fire('Error', 'Please enter a list name to continue.', 'error');
                    return;
                }
                
                $('#step1').removeClass('active');
                $('#step2').addClass('active');
                $('#dot1').removeClass('active').addClass('completed');
                $('#dot2').addClass('active');
                
                $(this).addClass('d-none');
                $('#submitBtn').removeClass('d-none');
                $('#prevBtn').removeClass('d-none');
                currentStep = 2;
            }
        });

        $('#prevBtn').on('click', function() {
            if (currentStep === 2) {
                $('#step2').removeClass('active');
                $('#step1').addClass('active');
                $('#dot1').addClass('active').removeClass('completed');
                $('#dot2').removeClass('active');
                
                $('#nextBtn').removeClass('d-none');
                $('#submitBtn').addClass('d-none');
                $(this).addClass('d-none');
                currentStep = 1;
            }
        });

        $('#dropZone').on('click', () => $('#fileInput').click());
        
        $('#fileInput').on('click', (e) => e.stopPropagation());

        $('#fileInput').on('change', function() {
            if (this.files && this.files[0]) {
                $('#fileName').text(this.files[0].name).show();
                $('.file-drop-zone').css('border-color', '#10b981').css('background', '#f0fdf4');
            }
        });

        $('#importForm').on('submit', function() {
            if (!$('#fileInput').val()) {
                Swal.fire('Error', 'Please select a file to import.', 'error');
                return false;
            }
            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
        });
    });
</script>
@endpush
