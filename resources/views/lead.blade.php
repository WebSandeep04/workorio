@extends('layouts.app')

@section('title', 'Create Lead')
@section('page_title', 'Sales Product')

@push('styles')
<style>
    .lead-page-wrap,
    .nexora-modal {
        --l-ink: #09090B;
        --l-ink-2: #3F3F46;
        --l-muted: #71717A;
        --l-border: #E4E7EC;
        --l-hairline: #F1F5F9;
        --l-surface-soft: #F8FAFC;
        --l-brand: #434AFA;
        --l-brand-hover: #3138D8;
    }

    .lead-page-wrap {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        max-width: 1180px;
        margin: 0 auto;
        padding: 4px 4px 28px;
        color: var(--l-ink);
    }

    /* Top Header */
    .lead-top-bar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 20px;
    }

    .lead-top-bar h2 {
        font-size: 21px;
        font-weight: 700;
        color: var(--l-ink);
        letter-spacing: -0.025em;
        margin: 0;
        line-height: 1.25;
    }

    .lead-top-bar p {
        font-size: 13px;
        color: var(--l-muted);
        margin: 3px 0 0;
    }

    .lead-top-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-nexora-ghost {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 36px;
        padding: 0 14px;
        border-radius: 10px;
        border: 1px solid var(--l-border);
        background: #FFFFFF;
        color: var(--l-ink-2);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-nexora-ghost:hover {
        background: var(--l-surface-soft);
        border-color: #CBD5E1;
        color: var(--l-ink);
    }

    .btn-nexora-soft {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 36px;
        padding: 0 14px;
        border-radius: 10px;
        border: 1px solid #C7D2FE;
        background: #EEF2FF;
        color: var(--l-brand);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-nexora-soft:hover {
        background: #E0E7FF;
        border-color: #A5B4FC;
        color: var(--l-brand-hover);
    }

    /* Main Form Card */
    .lead-card {
        background: #FFFFFF;
        border: 1px solid var(--l-border);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        overflow: visible;
    }

    /* Prospect Spotlight Strip */
    .prospect-strip {
        background: linear-gradient(180deg, #F8FAFF 0%, #FFFFFF 100%);
        border-bottom: 1px solid var(--l-hairline);
        border-radius: 16px 16px 0 0;
        padding: 20px 24px;
    }

    /* Nexora Searchable Custom Dropdown */
    .nx-select {
        position: relative;
        width: 100%;
    }

    .nx-select-trigger {
        width: 100%;
        height: 40px;
        padding: 0 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        background: #FFFFFF;
        border: 1px solid #D4D8E1;
        border-radius: 10px;
        font-size: 13.5px;
        color: var(--l-ink);
        cursor: pointer;
        text-align: left;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .nx-select-trigger:hover {
        border-color: #B8C0CC;
    }

    .nx-select.open .nx-select-trigger {
        border-color: var(--l-brand);
        box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.12);
    }

    .nx-select-value {
        flex: 1;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 500;
    }

    .nx-select-value.is-placeholder {
        color: #71717A;
        font-weight: 400;
    }

    .nx-select-icons {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
        color: #64748B;
    }

    .nx-select-clear {
        display: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #94A3B8;
        transition: background 0.12s ease, color 0.12s ease;
    }

    .nx-select-clear:hover {
        background: #F1F5F9;
        color: #09090B;
    }

    .nx-select.has-value .nx-select-clear {
        display: inline-flex;
    }

    .nx-select-chev {
        font-size: 12px;
        transition: transform 0.18s ease;
    }

    .nx-select.open .nx-select-chev {
        transform: rotate(180deg);
        color: var(--l-brand);
    }

    .nx-select-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 1050;
        background: #FFFFFF;
        border: 1px solid #E4E7EC;
        border-radius: 14px;
        box-shadow: 0 14px 34px -8px rgba(15, 23, 42, 0.16), 0 0 1px rgba(15, 23, 42, 0.06);
        padding: 6px;
        display: none;
    }

    .nx-select.open .nx-select-menu {
        display: block;
        animation: nxDropFade 0.14s ease-out;
    }

    @keyframes nxDropFade {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .nx-select-search-wrap {
        position: relative;
        padding: 2px 2px 6px;
        border-bottom: 1px solid #F1F5F9;
        margin-bottom: 4px;
    }

    .nx-select-search-wrap > i {
        position: absolute;
        left: 12px;
        top: calc(50% - 2px);
        transform: translateY(-50%);
        color: #94A3B8;
        font-size: 13px;
        pointer-events: none;
    }

    .nx-select-search {
        width: 100%;
        height: 36px;
        padding: 6px 32px 6px 32px;
        border: 1px solid #E4E7EC;
        border-radius: 9px;
        background: #F8FAFC;
        font-size: 13px;
        color: var(--l-ink);
        outline: none;
        transition: border-color 0.15s ease, background 0.15s ease;
    }

    .nx-select-search:focus {
        background: #FFFFFF;
        border-color: var(--l-brand);
        box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.1);
    }

    .nx-select-spinner {
        position: absolute;
        right: 12px;
        top: calc(50% - 2px);
        transform: translateY(-50%);
        display: none;
        color: var(--l-brand);
    }

    .nx-select-list {
        max-height: 250px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 2px;
        padding: 2px 0;
        scrollbar-width: thin;
        scrollbar-color: #CBD5E1 transparent;
    }

    .nx-select-list::-webkit-scrollbar { width: 4px; }
    .nx-select-list::-webkit-scrollbar-track { background: transparent; }
    .nx-select-list::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 999px; }

    .nx-select-item {
        width: 100%;
        border: none;
        background: transparent;
        padding: 8px 11px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        text-align: left;
        cursor: pointer;
        transition: background 0.12s ease;
    }

    .nx-select-item:hover,
    .nx-select-item.is-focused {
        background: #F4F4F5;
    }

    .nx-select-item.is-active {
        background: #EEF2FF;
    }

    .nx-select-item-main {
        min-width: 0;
        flex: 1;
    }

    .nx-select-item-name {
        font-size: 13px;
        font-weight: 500;
        color: #18181B;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .nx-select-item.is-active .nx-select-item-name {
        color: var(--l-brand);
        font-weight: 600;
    }

    .nx-select-item-sub {
        font-size: 11.5px;
        color: #71717A;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 1px;
    }

    .nx-select-check {
        color: var(--l-brand);
        font-size: 14px;
        flex-shrink: 0;
        display: none;
    }

    .nx-select-item.is-active .nx-select-check {
        display: inline-block;
    }

    .nx-select-empty {
        padding: 18px 12px;
        text-align: center;
        font-size: 12.5px;
        color: #71717A;
    }

    .nx-select-empty-btn {
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 8px;
        border: 1px solid #C7D2FE;
        background: #EEF2FF;
        color: var(--l-brand);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    .nx-select-empty-btn:hover {
        background: #E0E7FF;
    }

    .prospect-input-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .prospect-select-wrap {
        flex: 1;
        min-width: 240px;
    }

    .autofill-badge {
        display: none;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        font-weight: 600;
        color: #047857;
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        padding: 3px 10px;
        border-radius: 999px;
    }

    .autofill-badge.show {
        display: inline-flex;
    }

    /* Form Sections */
    .lead-section {
        padding: 20px 24px;
        border-bottom: 1px solid var(--l-hairline);
    }

    .lead-section:last-of-type {
        border-bottom: none;
    }

    .lead-sec-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 16px;
    }

    .lead-sec-title-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .lead-sec-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .lead-sec-icon.ic-blue { background: #EEF2FF; color: var(--l-brand); }
    .lead-sec-icon.ic-emerald { background: #ECFDF5; color: #059669; }
    .lead-sec-icon.ic-amber { background: #FFFBEB; color: #D97706; }

    .lead-sec-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--l-ink);
        margin: 0;
        line-height: 1.2;
    }

    .lead-sec-sub {
        font-size: 12px;
        color: var(--l-muted);
        margin: 2px 0 0;
    }

    /* Form Controls */
    .lead-page-wrap .form-label,
    .nexora-modal .form-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--l-ink-2);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .req-star {
        color: #EF4444;
        font-weight: 700;
    }

    .lead-page-wrap .form-control,
    .lead-page-wrap .form-select,
    .nexora-modal .form-control,
    .nexora-modal .form-select {
        height: 40px;
        padding: 8px 12px;
        font-size: 13.5px;
        color: var(--l-ink);
        background-color: #FFFFFF;
        border: 1px solid #D4D8E1;
        border-radius: 9px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    }

    .lead-page-wrap .form-select,
    .nexora-modal .form-select {
        padding-right: 32px;
    }

    .lead-page-wrap textarea.form-control {
        height: auto;
        min-height: 88px;
        padding: 10px 12px;
        resize: vertical;
    }

    .lead-page-wrap .form-control::placeholder,
    .nexora-modal .form-control::placeholder {
        color: #9CA3AF;
        font-size: 13px;
    }

    .lead-page-wrap .form-control:focus,
    .lead-page-wrap .form-select:focus,
    .nexora-modal .form-control:focus,
    .nexora-modal .form-select:focus {
        border-color: var(--l-brand);
        box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.12);
        outline: none;
    }

    .input-icon-wrap {
        position: relative;
    }

    .input-icon-wrap > i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        font-size: 14px;
        pointer-events: none;
    }

    .input-icon-wrap .form-control {
        padding-left: 36px;
    }

    /* Footer Bar */
    .lead-card-footer {
        background: var(--l-surface-soft);
        border-top: 1px solid var(--l-hairline);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .lead-footer-hint {
        font-size: 12px;
        color: var(--l-muted);
    }

    .lead-footer-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-nexora-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        height: 40px;
        padding: 0 22px;
        border-radius: 10px;
        border: none;
        background: var(--l-brand);
        color: #FFFFFF;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(67, 74, 250, 0.24);
        transition: all 0.15s ease;
    }

    .btn-nexora-primary:hover {
        background: var(--l-brand-hover);
        color: #FFFFFF;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(67, 74, 250, 0.32);
    }

    .btn-nexora-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    /* Modal Nexora Polish */
    .nexora-modal .modal-content {
        border: 1px solid var(--l-border);
        border-radius: 16px;
        box-shadow: 0 20px 50px -12px rgba(15, 23, 42, 0.22);
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .nexora-modal .modal-header {
        padding: 16px 22px;
        border-bottom: 1px solid var(--l-hairline);
        background: #FFFFFF;
    }

    .nexora-modal .modal-title {
        font-size: 15.5px;
        font-weight: 700;
        color: var(--l-ink);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nexora-modal .modal-body {
        padding: 20px 22px;
        background: #FFFFFF;
    }

    .nexora-modal .modal-footer {
        padding: 14px 22px;
        background: var(--l-surface-soft);
        border-top: 1px solid var(--l-hairline);
        gap: 8px;
    }

    @media (max-width: 767px) {
        .prospect-strip,
        .lead-section,
        .lead-card-footer {
            padding: 16px;
        }
        .lead-card-footer {
            flex-direction: column;
            align-items: stretch;
        }
        .lead-footer-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>
@endpush

@section('content')
<div class="lead-page-wrap">

    <!-- Page Top Header -->
    <div class="lead-top-bar">
        <div>
            <h2>Create New Lead</h2>
            <p>Select a prospect to auto-fill contact info, or add a new prospect and schedule follow-up.</p>
        </div>
        <div class="lead-top-actions">
            @if(Route::has('myleads'))
            <a href="{{ route('myleads') }}" class="btn-nexora-ghost">
                <i class="bi bi-list-ul"></i>
                <span>All Leads</span>
            </a>
            @endif
            <button type="button" class="btn-nexora-soft" data-bs-toggle="modal" data-bs-target="#addProspectusModal">
                <i class="bi bi-person-plus"></i>
                <span>Add New Prospect</span>
            </button>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="lead-card">
        <form method="POST" action="" id="leadForm">

            <!-- Step 1: Prospect Selector Strip -->
            <div class="prospect-strip">
                <div class="lead-sec-head" style="margin-bottom: 12px;">
                    <div class="lead-sec-title-wrap">
                        <span class="lead-sec-icon ic-blue"><i class="bi bi-building"></i></span>
                        <div>
                            <h3 class="lead-sec-title">Select Prospect</h3>
                            <p class="lead-sec-sub">Choosing an existing prospect automatically fills their contact & address details</p>
                        </div>
                    </div>
                    <span class="autofill-badge" id="prospectAutofillBadge">
                        <i class="bi bi-check-circle-fill"></i> Details auto-filled
                    </span>
                </div>

                <div class="prospect-input-row">
                    <div class="prospect-select-wrap">
                        <label for="prospectus" class="form-label">Prospect <span class="req-star">*</span></label>
                        <!-- Native select kept hidden for form compatibility -->
                        <select class="d-none" id="prospectus" name="prospectus" required>
                            <option value="">Select Prospect</option>
                        </select>

                        <!-- Custom Nexora Searchable Dropdown -->
                        <div class="nx-select" id="prospectNxSelect">
                            <button type="button" class="nx-select-trigger" id="prospectNxTrigger" aria-haspopup="listbox" aria-expanded="false">
                                <span class="nx-select-value is-placeholder" id="prospectNxLabel">Select Prospect</span>
                                <span class="nx-select-icons">
                                    <span class="nx-select-clear" id="prospectNxClear" title="Clear selection"><i class="bi bi-x"></i></span>
                                    <i class="bi bi-chevron-down nx-select-chev"></i>
                                </span>
                            </button>
                            <div class="nx-select-menu" id="prospectNxMenu" role="listbox">
                                <div class="nx-select-search-wrap">
                                    <i class="bi bi-search"></i>
                                    <input type="text" class="nx-select-search" id="prospectNxSearch" placeholder="Search prospect by name, contact, or phone..." autocomplete="off">
                                    <span class="nx-select-spinner" id="prospectNxSpinner">
                                        <span class="spinner-border spinner-border-sm" style="width:14px;height:14px;border-width:2px;"></span>
                                    </span>
                                </div>
                                <div class="nx-select-list" id="prospectNxList">
                                    <div class="nx-select-empty">Loading prospects...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="padding-top: 24px;">
                        <button type="button" class="btn-nexora-soft" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#addProspectusModal">
                            <i class="bi bi-plus-lg"></i>
                            <span>New Prospect</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Lead & Contact Information -->
            <div class="lead-section">
                <div class="lead-sec-head">
                    <div class="lead-sec-title-wrap">
                        <span class="lead-sec-icon ic-blue"><i class="bi bi-person-lines-fill"></i></span>
                        <div>
                            <h3 class="lead-sec-title">Lead & Contact Information</h3>
                            <p class="lead-sec-sub">Primary contact details for this lead</p>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="leadsName" class="form-label">Lead Name</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-briefcase"></i>
                            <input type="text" class="form-control" id="leadsName" name="leads_name" placeholder="Enter Lead / Company Name">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="contactPerson" class="form-label">Contact Person</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person"></i>
                            <input type="text" class="form-control" id="contactPerson" name="contact_person" placeholder="Enter Contact Person Name">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-telephone"></i>
                            <input type="tel" class="form-control" id="contactNumber" name="contact_number" placeholder="Enter Contact Number">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email ID</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@company.com">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="website_link" class="form-label">Website</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-globe"></i>
                            <input type="url" class="form-control" id="website_link" name="website_link" placeholder="https://company.com">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Location & Business Profile -->
            <div class="lead-section">
                <div class="lead-sec-head">
                    <div class="lead-sec-title-wrap">
                        <span class="lead-sec-icon ic-emerald"><i class="bi bi-geo-alt"></i></span>
                        <div>
                            <h3 class="lead-sec-title">Location & Business Profile</h3>
                            <p class="lead-sec-sub">Address, state, city and industry category</p>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-pin-map"></i>
                            <input type="text" class="form-control" id="address" name="address" placeholder="Enter Street / Office Address">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="state" class="form-label">State</label>
                        <select class="form-select" id="state" name="state">
                            <option value="">Select State</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="city" class="form-label">City</label>
                        <select class="form-select" id="city" name="city">
                            <option value="">Select City</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="business_type" class="form-label">Business Type</label>
                        <select class="form-select" id="business_type" name="business_type">
                            <option value="">Loading...</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="lead_source" class="form-label">Lead Source</label>
                        <select class="form-select" id="lead_source" name="lead_source">
                            <option value="">Loading...</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="product_type" class="form-label">Product Type</label>
                        <select class="form-select" id="product_type" name="product_type">
                            <option value="">Loading...</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 4: Status, Follow-up & Remark -->
            <div class="lead-section">
                <div class="lead-sec-head">
                    <div class="lead-sec-title-wrap">
                        <span class="lead-sec-icon ic-amber"><i class="bi bi-calendar-check"></i></span>
                        <div>
                            <h3 class="lead-sec-title">Status & Follow-up</h3>
                            <p class="lead-sec-sub">Set pipeline stage, next follow-up date and initial remark</p>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="sales_status" class="form-label">Status <span class="req-star">*</span></label>
                        <select class="form-select" id="sales_status" name="sales_status" required>
                            <option value="">Loading...</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="next_follow_up" class="form-label">Next Follow-up Date <span class="req-star">*</span></label>
                        <input type="date" class="form-control" id="next_follow_up" name="next_follow_up_date" required>
                    </div>

                    <div class="col-12">
                        <label for="remark" class="form-label">Remark <span class="req-star">*</span></label>
                        <textarea class="form-control" id="remark" name="remark" placeholder="Write key discussion points, client requirements, or next steps..." rows="3" required></textarea>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="lead-card-footer">
                <span class="lead-footer-hint">Fields marked with <span class="req-star">*</span> are required.</span>
                <div class="lead-footer-actions">
                    <button type="button" class="btn-nexora-ghost" id="resetLeadFormBtn">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset</span>
                    </button>
                    <button type="submit" id="submitLeadBtn" onclick="submitLead(event)" class="btn-nexora-primary">
                        <i class="bi bi-check2-circle"></i>
                        <span>Save Lead</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>

<!-- Add New Prospect Modal -->
<div class="modal fade nexora-modal" id="addProspectusModal" tabindex="-1" aria-labelledby="addProspectusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addProspectusModalLabel">
                    <span class="lead-sec-icon ic-blue"><i class="bi bi-person-plus"></i></span>
                    <span>Add New Prospect</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="addProspectusForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modalnewProspectusName" class="form-label">Prospect Name <span class="req-star">*</span></label>
                            <input type="text" class="form-control" id="modalnewProspectusName" name="modal_new_prospectus_name" placeholder="Enter Prospect Name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="modal_contact_person" class="form-label">Contact Person</label>
                            <input type="text" class="form-control" id="modal_contact_person" name="modal_contact_person" placeholder="Enter Contact Person">
                        </div>

                        <div class="col-md-6">
                            <label for="modal_contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="modal_contact_number" name="modal_contact_number" placeholder="Enter Contact Number">
                        </div>

                        <div class="col-md-6">
                            <label for="modal_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="modal_email" name="modal_email" placeholder="Enter Email">
                        </div>

                        <div class="col-md-6">
                            <label for="modal_address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="modal_address" name="modal_address" placeholder="Enter Address">
                        </div>

                        <div class="col-md-6">
                            <label for="modal_website_link" class="form-label">Website</label>
                            <input type="url" class="form-control" id="modal_website_link" name="modal_website_link" placeholder="Enter Website URL">
                        </div>

                        <div class="col-md-4">
                            <label for="modal_state" class="form-label">State</label>
                            <select class="form-select" id="modal_state" name="modal_state">
                                <option value="">Select State</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="modal_city" class="form-label">City</label>
                            <select class="form-select" id="modal_city" name="modal_city">
                                <option value="">Select City</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="modal_business_type" class="form-label">Business Type</label>
                            <select class="form-select" id="modal_business_type" name="modal_business_type">
                                <option value="">Loading...</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-nexora-ghost" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" id="submitProspectBtn" onclick="submitProspect(event)" class="btn-nexora-primary" form="addProspectusForm" style="height: 36px; padding: 0 18px;">
                    <i class="bi bi-check2"></i>
                    <span>Save Prospect</span>
                </button>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showAlert(type, message) {
    let colorClass = 'custom-alert-' + type;
    $('#alertBox').html(`
        <div class="custom-alert ${colorClass}">
            ${message}
            <button class="custom-alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    `);
    setTimeout(() => $('.custom-alert').fadeOut(500, function() { $(this).remove(); }), 2500);
}

// Prospect Searchable Dropdown State
let allProspectsCache = [];
let prospectSearchTimer = null;
let prospectSearchXhr = null;
let prospectFocusedIdx = -1;

function escNxHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function setProspectSelection(id, name, triggerChange = true) {
    const $sel = $('#prospectus');
    const $wrap = $('#prospectNxSelect');
    const $lbl = $('#prospectNxLabel');

    if (id) {
        if ($sel.find(`option[value="${id}"]`).length === 0) {
            $sel.append($('<option>', { value: id, text: name }));
        }
        $sel.val(id);
        $lbl.text(name).removeClass('is-placeholder');
        $wrap.addClass('has-value');
    } else {
        $sel.val('');
        $lbl.text('Select Prospect').addClass('is-placeholder');
        $wrap.removeClass('has-value');
        $('#prospectAutofillBadge').removeClass('show');
    }

    if (triggerChange) {
        $sel.trigger('change');
    }
}

function renderProspectList(items, queryText) {
    const $list = $('#prospectNxList');
    const selectedId = String($('#prospectus').val() || '');
    prospectFocusedIdx = -1;

    const cleanItems = (items || []).filter(it => it && it.prospectus_name && String(it.prospectus_name).trim() !== '');

    if (!cleanItems.length) {
        const qSafe = escNxHtml(queryText || '');
        $list.html(`
            <div class="nx-select-empty">
                <div>No matching prospect found${qSafe ? ' for "<strong>' + qSafe + '</strong>"' : ''}.</div>
                <button type="button" class="nx-select-empty-btn" id="nxQuickAddProspectBtn" data-prefill="${qSafe}">
                    <i class="bi bi-plus-lg"></i> Add as New Prospect
                </button>
            </div>
        `);
        return;
    }

    let html = '';
    cleanItems.slice(0, 80).forEach(item => {
        const name = String(item.prospectus_name).trim();
        const isActive = String(item.id) === selectedId;
        const metaParts = [];
        if (item.contact_person) metaParts.push(String(item.contact_person).trim());
        if (item.contact_number) metaParts.push(String(item.contact_number).trim());
        const metaStr = metaParts.join(' • ');

        html += `<button type="button" class="nx-select-item ${isActive ? 'is-active' : ''}" data-id="${item.id}" data-name="${escNxHtml(name)}">
            <div class="nx-select-item-main">
                <div class="nx-select-item-name">${escNxHtml(name)}</div>
                ${metaStr ? `<div class="nx-select-item-sub">${escNxHtml(metaStr)}</div>` : ''}
            </div>
            <i class="bi bi-check2 nx-select-check"></i>
        </button>`;
    });

    $list.html(html);
}

function openProspectDropdown() {
    const $wrap = $('#prospectNxSelect');
    $wrap.addClass('open');
    $('#prospectNxTrigger').attr('aria-expanded', 'true');
    setTimeout(() => {
        $('#prospectNxSearch').trigger('focus');
    }, 30);
}

function closeProspectDropdown() {
    const $wrap = $('#prospectNxSelect');
    $wrap.removeClass('open');
    $('#prospectNxTrigger').attr('aria-expanded', 'false');
}

function fetchProspectSearch(q) {
    if (prospectSearchXhr && prospectSearchXhr.readyState !== 4) {
        prospectSearchXhr.abort();
    }
    $('#prospectNxSpinner').show();
    prospectSearchXhr = $.ajax({
        url: "{{ route('getProspectus') }}",
        type: 'GET',
        data: q ? { q: q } : {},
        success: function (data) {
            $('#prospectNxSpinner').hide();
            if (!q) {
                allProspectsCache = data || [];
            }
            renderProspectList(data || [], q);
        },
        error: function (xhr, status) {
            $('#prospectNxSpinner').hide();
            if (status !== 'abort') {
                $('#prospectNxList').html('<div class="nx-select-empty">Could not load prospects.</div>');
            }
        }
    });
}

// Get prospects
$(document).ready(function () {
    prospect();

    $('#prospectNxTrigger').on('click', function (e) {
        if ($(e.target).closest('#prospectNxClear').length) return;
        if ($('#prospectNxSelect').hasClass('open')) {
            closeProspectDropdown();
        } else {
            openProspectDropdown();
        }
    });

    $('#prospectNxClear').on('click', function (e) {
        e.stopPropagation();
        setProspectSelection('', '', true);
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#prospectNxSelect').length) {
            closeProspectDropdown();
        }
    });

    $('#prospectNxSearch').on('input', function () {
        const q = $(this).val().trim();
        // Instant local filter preview while AJAX runs
        if (allProspectsCache.length) {
            const ql = q.toLowerCase();
            const localMatches = q
                ? allProspectsCache.filter(it =>
                    (it.prospectus_name && String(it.prospectus_name).toLowerCase().includes(ql)) ||
                    (it.contact_person && String(it.contact_person).toLowerCase().includes(ql)) ||
                    (it.contact_number && String(it.contact_number).toLowerCase().includes(ql))
                )
                : allProspectsCache;
            renderProspectList(localMatches, q);
        }

        clearTimeout(prospectSearchTimer);
        prospectSearchTimer = setTimeout(() => {
            fetchProspectSearch(q);
        }, 180);
    });

    $('#prospectNxSearch').on('keydown', function (e) {
        const $items = $('#prospectNxList .nx-select-item');
        if (e.key === 'Escape') {
            closeProspectDropdown();
            return;
        }
        if (!$items.length) return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            prospectFocusedIdx = Math.min(prospectFocusedIdx + 1, $items.length - 1);
            $items.removeClass('is-focused').eq(prospectFocusedIdx).addClass('is-focused')[0].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            prospectFocusedIdx = Math.max(prospectFocusedIdx - 1, 0);
            $items.removeClass('is-focused').eq(prospectFocusedIdx).addClass('is-focused')[0].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const targetIdx = prospectFocusedIdx >= 0 ? prospectFocusedIdx : 0;
            $items.eq(targetIdx).trigger('click');
        }
    });

    $(document).on('click', '#prospectNxList .nx-select-item', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        setProspectSelection(id, name, true);
        closeProspectDropdown();
        renderProspectList(allProspectsCache, '');
        $('#prospectNxSearch').val('');
    });

    $(document).on('click', '#nxQuickAddProspectBtn', function () {
        const prefill = $(this).attr('data-prefill') || '';
        closeProspectDropdown();
        $('#modalnewProspectusName').val(prefill);
        $('#addProspectusModal').modal('show');
    });

    $('#resetLeadFormBtn').on('click', function () {
        $('#leadForm')[0].reset();
        setProspectSelection('', '', false);
        $('#prospectNxSearch').val('');
        renderProspectList(allProspectsCache, '');
    });
});

function prospect(selectedId) {
    $.ajax({
        url: "{{ route('getProspectus') }}",
        type: 'GET',
        success: function (data) {
            allProspectsCache = data || [];
            $('#prospectus').empty().append('<option value="">Select Prospect</option>');
            let selectedName = '';
            $.each(allProspectsCache, function (key, item) {
                let name = (item && item.prospectus_name) ? String(item.prospectus_name).trim() : '';
                if (!name) return;
                $('#prospectus').append($('<option>', { value: item.id, text: name }));
                if (selectedId && String(item.id) === String(selectedId)) {
                    selectedName = name;
                }
            });
            renderProspectList(allProspectsCache, '');
            if (selectedId && selectedName) {
                setProspectSelection(selectedId, selectedName, true);
            }
        },
        error: function () {
            showAlert('danger', 'Failed to load prospects.');
        }
    });
}

// Get states
$(document).ready(function () {
    $.ajax({
        url: "{{ route('state') }}",
        type: "GET",
        dataType: "json",
        success: function (states) {
            let $stateDropdown = $('#state');
            $stateDropdown.empty().append('<option value="">Select State</option>');
            $.each(states, function (id, name) {
                $stateDropdown.append(`<option value="${id}">${name}</option>`);
            });
        },
        error: function () {
            showAlert('danger', 'Failed to load states.');
        }
    });
});

// Get cities on state change
$('#state').on('change', function () {
    var stateId = $(this).val();
    if (stateId) {
        $.ajax({
            url: "/city/" + stateId,
            type: "GET",
            dataType: "json",
            success: function (cities) {
                $('#city').empty().append('<option value="">Select City</option>');
                $.each(cities, function (id, name) {
                    $('#city').append(`<option value="${id}">${name}</option>`);
                });
            },
            error: function () {
                showAlert('danger', 'Could not fetch cities.');
            }
        });
    } else {
        $('#city').empty().append('<option value="">Select City</option>');
    }
});

// Get all cities
$.ajax({
    url: "{{ route('allcity') }}",
    type: "GET",
    success: function (data) {
        $('#city').empty().append('<option value="">Select City</option>');
        $.each(data, function (index, type) {
            $('#city').append(`<option value="${type.id}">${type.city_name}</option>`);
        });
    },
    error: function () {
        $('#city').html('<option value="">Unable to load cities</option>');
    }
});

// Get statuses
$.ajax({
    url: "{{ route('getStatuses') }}",
    type: 'GET',
    success: function (data) {
        $('#sales_status').empty().append('<option value="">Select Status</option>');
        $.each(data, function (key, status) {
            $('#sales_status').append(`<option value="${status.id}">${status.status_name}</option>`);
        });
    },
    error: function () {
        showAlert('danger', 'Failed to load sales statuses.');
    }
});

// Business types
$.ajax({
    url: "{{ route('getbusiness') }}",
    type: "GET",
    success: function (data) {
        $('#business_type').empty().append('<option value="">Select Business Type</option>');
        $.each(data, function (index, type) {
            $('#business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
        });
    },
    error: function () {
        $('#business_type').html('<option value="">Unable to load types</option>');
    }
});

// Lead sources
$.ajax({
    url: "{{ route('getsource') }}",
    type: "GET",
    success: function (data) {
        $('#lead_source').empty().append('<option value="">Select Source</option>');
        $.each(data, function (index, type) {
            $('#lead_source').append(`<option value="${type.id}">${type.source_name}</option>`);
        });
    },
    error: function () {
        $('#lead_source').html('<option value="">Unable to load sources</option>');
    }
});

// Product types
$.ajax({
    url: "{{ route('getproduct') }}",
    type: "GET",
    success: function (data) {
        $('#product_type').empty().append('<option value="">Select Product Type</option>');
        $.each(data, function (index, type) {
            $('#product_type').append(`<option value="${type.id}">${type.product_name}</option>`);
        });
    },
    error: function () {
        $('#product_type').html('<option value="">Unable to load products</option>');
    }
});

// Modal states
$(document).ready(function () {
    $.ajax({
        url: "{{ route('state') }}",
        type: "GET",
        dataType: "json",
        success: function (states) {
            let $stateDropdown = $('#modal_state');
            $stateDropdown.empty().append('<option value="">Select State</option>');
            $.each(states, function (id, name) {
                $stateDropdown.append(`<option value="${id}">${name}</option>`);
            });
        }
    });
});

// Modal cities
$('#modal_state').on('change', function () {
    var stateId = $(this).val();
    if (stateId) {
        $.ajax({
            url: "/city/" + stateId,
            type: "GET",
            dataType: "json",
            success: function (cities) {
                $('#modal_city').empty().append('<option value="">Select City</option>');
                $.each(cities, function (id, name) {
                    $('#modal_city').append(`<option value="${id}">${name}</option>`);
                });
            }
        });
    } else {
        $('#modal_city').empty().append('<option value="">Select City</option>');
    }
});

// Modal business types
$.ajax({
    url: "{{ route('getbusiness') }}",
    type: "GET",
    success: function (data) {
        $('#modal_business_type').empty().append('<option value="">Select Business Type</option>');
        $.each(data, function (index, type) {
            $('#modal_business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
        });
    },
    error: function () {
        $('#modal_business_type').html('<option value="">Unable to load types</option>');
    }
});

function submitProspect(e) {
    e.preventDefault();

    let newProspectusName = $('#modalnewProspectusName').val();
    if (!newProspectusName || !newProspectusName.trim()) {
        $('#modalnewProspectusName').focus();
        return;
    }

    let $btn = $('#submitProspectBtn');
    let origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span><span>Saving...</span>');

    $.ajax({
        url: '/prospectus',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            prospectus_name: newProspectusName,
            contact_person: $('#modal_contact_person').val(),
            contact_number: $('#modal_contact_number').val(),
            address: $('#modal_address').val(),
            state_id: $('#modal_state').val(),
            city_id: $('#modal_city').val(),
            email: $('#modal_email').val(),
            website_link: $('#modal_website_link').val(),
            business_type_id: $('#modal_business_type').val()
        },
        success: function (response) {
            $btn.prop('disabled', false).html(origHtml);
            $('#addProspectusModal').modal('hide');
            $('#addProspectusForm')[0].reset();
            showAlert('success', 'Prospect added successfully.');
            prospect(response && response.id ? response.id : null);
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html(origHtml);
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error === 'duplicate') {
                let p = xhr.responseJSON.prospect;
                let detailHtml = `
                    <div class="text-start mt-2 p-2 border rounded" style="background:#f8f9fa; font-size: 0.88rem; line-height: 1.6;">
                        <div><strong>Prospect Name:</strong> ${p.prospectus_name || '-'}</div>
                        <div><strong>Contact Person:</strong> ${p.contact_person || '-'}</div>
                        <div><strong>Contact Number:</strong> ${p.contact_number || '-'}</div>
                        ${p.email ? `<div><strong>Email:</strong> ${p.email}</div>` : ''}
                        ${p.address ? `<div><strong>Address:</strong> ${p.address}</div>` : ''}
                        ${p.city ? `<div><strong>City / State:</strong> ${p.city}${p.state ? ', ' + p.state : ''}</div>` : ''}
                        ${p.business_type ? `<div><strong>Business Type:</strong> ${p.business_type}</div>` : ''}
                    </div>
                `;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Prospect Already Exists!',
                        html: `<p class="mb-2 text-danger fw-semibold">${xhr.responseJSON.message}</p>${detailHtml}`,
                        confirmButtonText: 'Use This Existing Prospect',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#434afa',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#addProspectusModal').modal('hide');
                            $('#addProspectusForm')[0].reset();
                            prospect(p.id);
                        }
                    });
                } else {
                    alert(`${xhr.responseJSON.message}\nName: ${p.prospectus_name}\nPerson: ${p.contact_person}\nPhone: ${p.contact_number}`);
                }
            } else {
                showAlert('danger', 'Failed to add prospect. Please try again.');
                console.log(xhr.responseText);
            }
        }
    });
}

// Auto-fill fields on prospect selection
$(document).ready(function () {
    $('#prospectus').on('change', function () {
        let id = $(this).val();

        if (id !== "") {
            $.ajax({
                url: '/fillprospectus/' + id,
                type: 'GET',
                success: function (data) {
                    $('#contactPerson').val(data.contact_person);
                    $('#contactNumber').val(data.contact_number);
                    $('#address').val(data.address);
                    $('#leadsName').val(data.prospectus_name);
                    $('#email').val(data.email);
                    $('#website_link').val(data.website_link);
                    $('#business_type').val(data.business_type_id);
                    $('#state').val(data.state_id);
                    $('#city').val(data.city_id);
                    $('#prospectAutofillBadge').addClass('show');
                },
                error: function (xhr) {
                    showAlert('danger', 'Could not load prospect details.');
                    console.log(xhr.responseText);
                }
            });
        } else {
            $('#prospectAutofillBadge').removeClass('show');
        }
    });
});

function submitLead(e) {
    e.preventDefault();

    if (!$('#prospectus').val()) {
        openProspectDropdown();
        showAlert('danger', 'Please select a prospect first.');
        return;
    }

    let formEl = document.getElementById('leadForm');
    if (formEl && !formEl.checkValidity()) {
        formEl.reportValidity();
        return;
    }

    let $btn = $('#submitLeadBtn');
    let origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span><span>Saving...</span>');

    $.ajax({
        url: '/savelead',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            prospectus_id: $('#prospectus').val(),
            leads_name: $('#leadsName').val(),
            contact_person: $('#contactPerson').val(),
            contact_number: $('#contactNumber').val(),
            status_id: $('#sales_status').val(),
            address: $('#address').val(),
            state_id: $('#state').val(),
            city_id: $('#city').val(),
            email: $('#email').val(),
            website_link: $('#website_link').val(),
            next_follow_up_date: $('#next_follow_up').val(),
            business_type_id: $('#business_type').val(),
            remark: $('#remark').val(),
            lead_source_id: $('#lead_source').val(),
            products_id: $('#product_type').val()
        },
        success: function (response) {
            $btn.prop('disabled', false).html(origHtml);
            $('#leadForm')[0].reset();
            setProspectSelection('', '', false);
            $('#prospectNxSearch').val('');
            renderProspectList(allProspectsCache, '');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Lead Saved!',
                    text: 'Sales record submitted successfully.',
                    confirmButtonColor: '#434AFA',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                showAlert('success', 'Sales record submitted successfully!');
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html(origHtml);
            console.error(xhr.responseText);
            let msg = 'Failed to submit sales record!';
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                let firstKey = Object.keys(xhr.responseJSON.errors)[0];
                if (firstKey && xhr.responseJSON.errors[firstKey][0]) {
                    msg = xhr.responseJSON.errors[firstKey][0];
                }
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Could Not Save Lead',
                    text: msg,
                    confirmButtonColor: '#434AFA'
                });
            } else {
                alert(msg);
            }
        }
    });
}
</script>
@endpush
