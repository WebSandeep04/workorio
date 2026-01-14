{{-- resources/views/components/filter-panel.blade.php --}}
@props(['showSearch' => true])

<div class="filterBox mb-2">
    <div class="mb-2">
        <label for="sales_status" class="form-label-modern">
            <i class="bi bi-tag"></i> Status
        </label>
        <select class="form-control form-control-modern" id="sales_status" name="sales_status" required>
            <option value="">Loading...</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="state" class="form-label-modern">
            <i class="bi bi-geo-alt"></i> State
        </label>
        <select class="form-control form-control-modern" id="state" name="state" required>
            <option value="">Loading...</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="city" class="form-label-modern">
            <i class="bi bi-building"></i> City
        </label>
        <select class="form-control form-control-modern" id="city" name="city">
            <option value="">Loading...</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="business_type" class="form-label-modern">
            <i class="bi bi-briefcase"></i> Business Type
        </label>
        <select class="form-control form-control-modern" id="business_type" name="business_type" required>
            <option value="">Loading...</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="lead_source" class="form-label-modern">
            <i class="bi bi-funnel"></i> Lead Sources
        </label>
        <select class="form-control form-control-modern" id="lead_source" name="lead_source" required>
            <option value="">Loading...</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="product_type" class="form-label-modern">
            <i class="bi bi-box-seam"></i> Product Type
        </label>
        <select class="form-control form-control-modern" id="product_type" name="product_type" required>
            <option value="">Loading...</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="user_id" class="form-label-modern">
            <i class="bi bi-person"></i> Sales User
        </label>
        <select class="form-control form-control-modern" id="user_id" name="user_id">
            <option value="">All Sales Users</option>
        </select>
    </div>

    @if ($showSearch)
        <div class="mb-2">
            <label for="search" class="form-label-modern">
                <i class="bi bi-search"></i> Search
            </label>
            <input type="text" class="form-control form-control-modern" id="search" placeholder="🔍 Search anything...">
        </div>
    @endif

    <div class="mb-2">
        <label for="from_date" class="form-label-modern">
            <i class="bi bi-calendar-event"></i> From Date
        </label>
        <input type="date" class="form-control form-control-modern" id="from_date" name="from_date">
    </div>

    <div class="mb-2">
        <label for="to_date" class="form-label-modern">
            <i class="bi bi-calendar-check"></i> To Date
        </label>
        <input type="date" class="form-control form-control-modern" id="to_date" name="to_date">
    </div>
</div>
