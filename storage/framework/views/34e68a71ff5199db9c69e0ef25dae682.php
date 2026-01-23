

<?php $__env->startSection('title', 'My Leads'); ?>
<?php $__env->startSection('page_title', 'My Leads'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .summary-cards,
  .status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.5rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 70px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .metric-arrow {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    color: #000;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
    position: absolute;
    right: 8px;
    bottom: 8px;
    font-size: 0.9rem;
  }

  .metric-arrow:hover {
    background: #5b59f7;
    color: #fff;
  }

  .summary-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon img {
    width: 24px;
    height: 24px;
    object-fit: contain;
  }

  .icon-sunrise { background: linear-gradient(135deg, #f97316, #fb923c); }
  .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
  .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }
  .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .icon-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card::before {
    display: none;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }

  .summary-card.card-1,
  .summary-card.card-2,
  .summary-card.card-3,
  .summary-card.card-4,
  .summary-card.card-5 {
    background: #fff;
  }

  /* Dark gradients for status cards - cycling through dark colors */
  .status-card:nth-child(6n+1) {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  }
  .status-card:nth-child(6n+2) {
    background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);
  }
  .status-card:nth-child(6n+3) {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  }
  .status-card:nth-child(6n+4) {
    background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
  }
  .status-card:nth-child(6n+5) {
    background: linear-gradient(135deg, #16a085 0%, #27ae60 100%);
  }
  .status-card:nth-child(6n+6) {
    background: linear-gradient(135deg, #d35400 0%, #e67e22 100%);
  }
  
  /* Additional dark gradients for more status cards */
  .status-card:nth-child(6n+7) {
    background: linear-gradient(135deg, #2c2c54 0%, #40407a 100%);
  }
  .status-card:nth-child(6n+8) {
    background: linear-gradient(135deg, #0c2461 0%, #1e3799 100%);
  }
  .status-card:nth-child(6n+9) {
    background: linear-gradient(135deg, #6a1b9a 0%, #8e24aa 100%);
  }
  .status-card:nth-child(6n+10) {
    background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
  }
  .status-card:nth-child(6n+11) {
    background: linear-gradient(135deg, #b71c1c 0%, #c62828 100%);
  }
  .status-card:nth-child(6n+12) {
    background: linear-gradient(135deg, #004d40 0%, #00695c 100%);
  }

  .summary-card-label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.2;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #101828;
    font-family: Montserrat;
  }

  .status-card {
    border-radius: 8px;
    padding: 0.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 70px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .status-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.1;
    background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent);
  }

  .status-card:hover {
    transform: translateY(-2px) scale(1.01);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
  }

  .status-card-label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    opacity: 0.95;
    flex-shrink: 0;
    line-height: 1.2;
  }

  .status-card-value {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
  }

  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: white;
    border-radius: 5px;
    flex-wrap: wrap;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
    margin-bottom: 0.5rem;
    border: 1px solid #434AFA;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-label-modern {
    color: white;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 6px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    transition: all 0.3s ease;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern option {
    color: #000;
    background: #fff;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern:focus {
    outline: none;
    border-color: #fff;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
    color: #000;
  }

  .filterBox .form-control-modern:hover {
    border-color: rgba(255, 255, 255, 0.6);
    background: #fff;
  }

  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }

  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .table-search-field {
    flex: 1;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f4f5f7;
    border: 1px solid #e5e7eb;
    border-radius: 2px;
    padding: 0.35rem 0.9rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
  }

  .table-search-btn {
    padding: 0.35rem 1rem;
    background: #434AFA;
    color: white;
    border: none;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  .table-search-btn:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
    color: white;
    text-decoration: none;
  }

  .table-search-btn:active {
    transform: translateY(0);
    background: #2d30b8;
  }

  .table-search-field i {
    color: #9ca3af;
    font-size: 0.85rem;
  }

  .table-search-field input {
    border: none;
    background: transparent;
    font-size: 0.85rem;
    width: 100%;
    outline: none;
    color: #111827;
  }

  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .modern-card-body {
    padding: 0.5rem;
  }

  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  .data-table-card .modern-card-body {
    padding: 0;
  }

  .data-table-card .table-responsive {
    border-radius: 5px;
    border: none;
    box-shadow: none;
    padding: 0.5rem 0.75rem 1rem;
    overflow-x: auto;
    background: transparent;
  }

  .data-table-card .table-responsive::-webkit-scrollbar {
    height: 8px;
  }

  .data-table-card .table-responsive::-webkit-scrollbar-track {
    background: #e4e7ec;
    border-radius: 999px;
  }

  .data-table-card .table-responsive::-webkit-scrollbar-thumb {
    background: #434AFA;
    border-radius: 999px;
  }

  .data-table-card .table-responsive {
    scrollbar-color: #434AFA #e4e7ec;
  }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
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
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }

  .data-table-card .custom-table tbody tr:last-child td {
    border-bottom: none;
  }

  .data-table-card .custom-table tbody td:nth-child(1) { min-width: 100px; }
  .data-table-card .custom-table tbody td:nth-child(2) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(3) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(4) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(5) { min-width: 110px; }
  .data-table-card .custom-table tbody td:nth-child(6) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(7) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(8) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(9) { min-width: 150px; }
  .data-table-card .custom-table tbody td:nth-child(10) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(11) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(12) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(13) { min-width: 110px; }
  .data-table-card .custom-table tbody td:nth-child(14) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(15) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(16) { min-width: 180px; }

  .custom-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 10px;
    padding: 0.25rem 0.35rem;
    text-align: center;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  }

  .custom-table td {
    font-size: 10px;
    padding: 0.25rem 0.35rem;
    vertical-align: middle;
    text-align: center;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
  }

  .custom-table tbody tr {
    transition: all 0.3s ease;
  }

  .custom-table tbody tr:hover {
    background: rgba(102, 126, 234, 0.08);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
  }

  .custom-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
  }

  .assign-select {
    font-size: 9px;
    padding: 2px 4px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    background: white;
    width: 100%;
    max-width: 120px;
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .assign-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.3);
  }

  .status-badge {
    display: inline-block;
    color: #000;
    font-size: 0.85rem;
    font-weight: normal;
    font-family: Montserrat, sans-serif;
  }

  .pagination .page-link {
    color: #434afa;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
  }

  .pagination .page-item.active .page-link {
    background: #434afa;
    border-color: #434afa;
    color: white;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }

  .pagination .page-link:hover {
    background: rgba(67, 74, 250, 0.15);
    border-color: #434afa;
    transform: translateY(-1px);
  }

  .table-responsive {
    border-radius: 5px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  }

  .remark-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .remark-link:hover {
    color: #764ba2;
    text-decoration: underline;
  }

  .loading-state {
    text-align: center;
    padding: 1rem;
    color: #667eea;
    font-size: 10px;
  }

  .loading-state i {
    font-size: 1rem;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .empty-state {
    text-align: center;
    padding: 1rem;
    color: #6c757d;
    font-size: 10px;
  }

  .empty-state i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
  }

   @media (max-width: 767px){
    .container-fluid{
      padding-left: 0.5rem;
      padding-right: 0.5rem;
      margin-right: 0;
    }
    
    .summary-cards,
    .status-cards {
      grid-template-columns: repeat(2, 1fr);
    }

    .table-search {
      flex-direction: row;
      gap: 0.5rem;
    }
    
    .table-search-btn {
      width: auto;
      padding: 0.35rem 0.75rem;
    }

    .table-search-field {
        width: 100%;
    }
  }

  /* Modal Styles */
  .modal-content {
      border-radius: 0px !important;
      border: none;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      overflow: hidden;
  }
  
  .modal-header {
      border-radius: 0px !important;
      background: #434AFA !important;
      color: white;
      border-bottom: none;
      padding: 0.6rem 1rem;
  }
  
  .modal-footer {
      border-top: 1px solid #f0f0f0;
      padding: 0.6rem 1rem;
      background: #fff;
  }

  .form-label-modern {
      color: #434AFA;
      font-weight: 600;
      margin-bottom: 0.2rem;
      display: flex;
      align-items: center;
      gap: 0.25rem;
      font-size: 0.75rem;
      font-family: Montserrat, sans-serif;
  }
  
  .form-control-modern, .form-select-modern {
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      padding: 0.4rem 0.6rem;
      transition: all 0.3s ease;
      font-size: 0.8rem;
      font-family: Montserrat, sans-serif;
  }
  
  .form-control-modern:focus, .form-select-modern:focus {
      border-color: #434AFA;
      box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.1);
      outline: none;
  }
  
  .btn-modern {
      padding: 0.4rem 1.2rem;
      border-radius: 4px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.8rem;
      font-family: Montserrat, sans-serif;
  }
  
  .btn-modern-primary {
      background: #434AFA;
      color: white;
  }
  
  .btn-modern-primary:hover {
      background: #3538d4;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2);
      color: white;
  }

  .blur-active {
    filter: blur(2px);
    transition: filter 0.3s ease;
    pointer-events: none;
  }

  #addProspectusModal .modal-content {
    filter: none !important;
    pointer-events: auto;
  }

  .modal-backdrop.show {
    background-color: rgba(15, 23, 42, 0.55);
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <!-- Summary Cards -->
  <div class="summary-cards">
    <div class="summary-card card-1">
      <div class="summary-card-icon icon-sunrise">
        <img src="<?php echo e(asset('img/icons/call.png')); ?>" alt="Calls">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Today's Follow Ups</div>
        <div class="summary-card-value" id="todayFollowups">0</div>
      </div>
      <a href="<?php echo e(route('todayfollowupstable')); ?>" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-amber">
        <img src="<?php echo e(asset('img/icons/underprocess.png')); ?>" alt="Under Process">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Under Process</div>
        <div class="summary-card-value" id="underProcess">0</div>
      </div>
      <a href="<?php echo e(route('underprocesstable')); ?>" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-3">
      <div class="summary-card-icon icon-emerald">
        <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Completed">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Today Completed</div>
        <div class="summary-card-value" id="todayCompleted">0</div>
      </div>
      <a href="<?php echo e(route('todaycompletedtable')); ?>" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-4">
      <div class="summary-card-icon icon-rose">
        <img src="<?php echo e(asset('img/icons/pending.png')); ?>" alt="Pending">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Today Pending</div>
        <div class="summary-card-value" id="todayPending">0</div>
      </div>
      <a href="<?php echo e(route('todaypendingtable')); ?>" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-5">
      <div class="summary-card-icon icon-sky">
        <img src="<?php echo e(asset('img/icons/new.png')); ?>" alt="New">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Today New</div>
        <div class="summary-card-value" id="todayNew">0</div>
      </div>
      <a href="<?php echo e(route('todaynewtable')); ?>" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
  </div>

  <!-- Status Cards -->
  <div class="status-cards" id="statusCardsContainer">
    <div class="status-card">
      <div class="status-card-label">Loading...</div>
      <div class="status-card-value">0</div>
    </div>
  </div>

  <!-- Filters - Always Visible -->
  <div class="filterBox mb-2">
    <div class="mb-2">
        <label for="sales_status" class="form-label-modern">
            <i class="bi bi-tag"></i> Status
        </label>
        <select class="form-control form-control-modern" id="sales_status" name="sales_status">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="state" class="form-label-modern">
            <i class="bi bi-geo-alt"></i> State
        </label>
        <select class="form-control form-control-modern" id="state" name="state">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="city" class="form-label-modern">
            <i class="bi bi-building"></i> City
        </label>
        <select class="form-control form-control-modern" id="city" name="city">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="business_type" class="form-label-modern">
            <i class="bi bi-briefcase"></i> Business Type
        </label>
        <select class="form-control form-control-modern" id="business_type" name="business_type">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="lead_source" class="form-label-modern">
            <i class="bi bi-funnel"></i> Lead Sources
        </label>
        <select class="form-control form-control-modern" id="lead_source" name="lead_source">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="product_type" class="form-label-modern">
            <i class="bi bi-box-seam"></i> Product Type
        </label>
        <select class="form-control form-control-modern" id="product_type" name="product_type">
            <option value="">Select</option>
        </select>
    </div>

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

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search leads, contacts, emails..." />
    </div>
    <button type="button" class="table-search-btn" id="addBtn" data-bs-toggle="modal" data-bs-target="#addLeadModal">
      <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="sales_table">
          <thead>
            <tr>
              <th>Status</th>
              <th>Prospect</th>
              <th>Lead</th>
              <th>Contact Person</th>
              <th>Contact No.</th>
              <th>Next Follow</th>
              <th>Remark</th>
              <th>Address</th>
              <th>State</th>
              <th>City</th>
              <th>Email</th>
              <th>Business</th>
              <th>Source</th>
              <th>Product</th>
              <th>Ticket</th>
              <th>Assign To</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="16" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading leads...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="table-range-meta" id="myleadsRangeInfo">
    Showing 0-0 from 0 data
  </div>
</div>

<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationfilterLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationsearchLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationdateLinks"></ul>
</div>

<?php echo $__env->make('partials.remarks-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Add Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1" aria-labelledby="addLeadModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content blur-target">
      <div class="modal-header">
        <h5 class="modal-title" id="addLeadModalLabel" style="font-size: 1rem;">
            <i class="bi bi-person-plus-fill text-white me-2"></i>Add New Lead
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-2">
        <form method="POST" action="" id="addLeadForm">
          <div class="row g-2">
            <!-- Column 1 -->
            <div class="col-md-6">
              <div class="mb-2">
                <label for="add_lead_prospectus" class="form-label-modern">Prospectus</label>
                <div class="input-group input-group-sm">
                    <select class="form-select form-select-modern" id="add_lead_prospectus" name="prospectus" required>
                      <option value="">Select Prospectus</option>
                    </select>
                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="openProspectusModal()">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
              </div>

              <div class="mb-2">
                <label for="add_lead_leadsName" class="form-label-modern">Lead Name</label>
                <input type="text" class="form-control form-control-modern" id="add_lead_leadsName" name="leads_name" placeholder="Enter Lead Name">
              </div>

              <div class="mb-2">
                <label for="add_lead_contactPerson" class="form-label-modern">Contact Person</label>
                <input type="text" class="form-control form-control-modern" id="add_lead_contactPerson" name="contact_person" placeholder="Enter Contact Person Name">
              </div>

              <div class="mb-2">
                <label for="add_lead_contactNumber" class="form-label-modern">Contact Number</label>
                <input type="tel" class="form-control form-control-modern" id="add_lead_contactNumber" name="contact_number" placeholder="Enter Contact Number">
              </div>

              <div class="mb-2">
                <label for="add_lead_sales_status" class="form-label-modern">Status</label>
                <select class="form-select form-select-modern" id="add_lead_sales_status" name="sales_status" required>
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="add_lead_address" class="form-label-modern">Address</label>
                <input type="text" class="form-control form-control-modern" id="add_lead_address" name="address" placeholder="Enter Address">
              </div>

              <div class="mb-2">
                <label for="add_lead_state" class="form-label-modern">State</label>
                <select class="form-select form-select-modern" id="add_lead_state" name="state">
                  <option value="">Select State</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="add_lead_city" class="form-label-modern">City</label>
                <select class="form-select form-select-modern" id="add_lead_city" name="city">
                  <option value="">Select City</option>
                </select>
              </div>
            </div>
            
            <!-- Column 2 -->
            <div class="col-md-6">
              <div class="mb-2">
                <label for="add_lead_email" class="form-label-modern">Email ID</label>
                <input type="email" class="form-control form-control-modern" id="add_lead_email" name="email" placeholder="Enter Email ID">
              </div>

              <div class="mb-2">
                <label for="add_lead_website_link" class="form-label-modern">Website</label>
                <input type="url" class="form-control form-control-modern" id="add_lead_website_link" name="website_link" placeholder="Enter Website URL">
              </div>
              
              <div class="mb-2">
                <label for="add_lead_next_follow_up" class="form-label-modern">Next Follow-up Date</label>
                <input type="date" class="form-control form-control-modern" id="add_lead_next_follow_up" name="next_follow_up_date" required>
              </div>

              <div class="mb-2">
                <label for="add_lead_business_type" class="form-label-modern">Business Type</label>
                <select class="form-select form-select-modern" id="add_lead_business_type" name="business_type">
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="add_lead_lead_source" class="form-label-modern">Lead Sources</label>
                <select class="form-select form-select-modern" id="add_lead_lead_source" name="lead_source">
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="add_lead_product_type" class="form-label-modern">Product Type</label>
                <select class="form-select form-select-modern" id="add_lead_product_type" name="product_type">
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="add_lead_remark" class="form-label-modern">Remark</label>
                <textarea class="form-control form-control-modern" id="add_lead_remark" name="remark" placeholder="Enter Remark" rows="2" required></textarea>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
         <button type="submit" onclick="submitLead(event)" class="btn-modern btn-modern-primary w-100 justify-content-center" form="addLeadForm">
            <i class="bi bi-check-circle"></i> Submit Lead
         </button>
      </div>
    </div>
  </div>
</div>

<!-- Add Prospectus Modal -->
<div class="modal fade" id="addProspectusModal" tabindex="-1" aria-labelledby="addProspectusModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProspectusModalLabel" style="font-size: 1rem;">
            <i class="bi bi-building-add text-white me-2"></i>Add New Prospectus
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body py-2">
        <form id="addProspectusForm">
          <div class="row g-2">
            <div class="col-md-6">
              <label for="modalnewProspectusName" class="form-label-modern">Prospect Name</label>
              <input type="text" class="form-control form-control-modern" id="modalnewProspectusName" name="modal_new_prospectus_name" placeholder="Enter Prospectus Name" required>
            </div>
            <div class="col-md-6">
              <label for="modal_contact_person" class="form-label-modern">Contact Person</label>
              <input type="text" class="form-control form-control-modern" id="modal_contact_person" name="modal_contact_person" placeholder="Enter Contact Person" required>
            </div>
            <div class="col-md-6">
              <label for="modal_contact_number" class="form-label-modern">Contact Number</label>
              <input type="text" class="form-control form-control-modern" id="modal_contact_number" name="modal_contact_number" placeholder="Enter Contact Number" required>
            </div>
            <div class="col-md-6">
              <label for="modal_address" class="form-label-modern">Address</label>
              <input type="text" class="form-control form-control-modern" id="modal_address" name="modal_address" placeholder="Enter Address" required>
            </div>
            <div class="col-md-6">
              <label for="modal_state" class="form-label-modern">State</label>
              <select class="form-select form-select-modern" id="modal_state" name="modal_state" required>
                <option value="">Select State</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="modal_city" class="form-label-modern">City</label>
              <select class="form-select form-select-modern" id="modal_city" name="modal_city" required>
                <option value="">Select City</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="modal_email" class="form-label-modern">Email</label>
              <input type="email" class="form-control form-control-modern" id="modal_email" name="modal_email" placeholder="Enter Email" required>
            </div>
            <div class="col-md-6">
              <label for="modal_website_link" class="form-label-modern">Website</label>
              <input type="url" class="form-control form-control-modern" id="modal_website_link" name="modal_website_link" placeholder="Enter Website URL">
            </div>
            <div class="col-md-6">
              <label for="modal_business_type" class="form-label-modern">Business Type</label>
              <select class="form-select form-select-modern" id="modal_business_type" name="modal_business_type" required>
                <option value="">Loading...</option>
              </select>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="submit" onclick="submitProspect(event)" class="btn-modern btn-modern-primary w-100 justify-content-center" form="addProspectusForm">
            <i class="bi bi-check-circle"></i> Save Prospect
        </button>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script>

let currentPage = 1;

// Show only date part like YYYY-MM-DD for any date-like input
function formatDateOnly(value) {
    if (!value) return 'N/A';
    const str = String(value);
    const t = str.indexOf('T');
    if (t > 0) return str.slice(0, t);
    // Fallback: try to parse and format
    const d = new Date(str);
    if (!isNaN(d.getTime())) {
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }
    return str.length >= 10 ? str.slice(0, 10) : str;
}

// Build compact pagination: "Previous [current / last] Next"
function buildSimplePagination($container, current, last) {
    $container.empty();
    // Prev
    $container.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">
              <i class="bi bi-chevron-left"></i> Previous
            </a>
        </li>
    `);
    // Current (disabled as display only)
    $container.append(`
        <li class="page-item active">
            <span class="page-link">${current} / ${last}</span>
        </li>
    `);
    // Next
    $container.append(`
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">
              Next <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    `);
}

// Load summary stats
function loadSummaryStats() {
    $.ajax({
        url: '<?php echo e(route("myleads.summary-stats")); ?>',
        type: 'GET',
        success: function(data) {
            $('#todayFollowups').text(data.today_followups || 0);
            $('#underProcess').text(data.under_process || 0);
            $('#todayCompleted').text(data.today_completed || 0);
            $('#todayPending').text(data.today_pending || 0);
            $('#todayNew').text(data.today_new || 0);
        },
        error: function(xhr) {
            console.error("Error loading summary stats:", xhr.responseText);
        }
    });
}

// Load status counts
function loadStatusCounts() {
    $.ajax({
        url: '<?php echo e(route("myleads.status-counts")); ?>',
        type: 'GET',
        success: function(data) {
            const $container = $('#statusCardsContainer');
            $container.empty();
            
            if (data.length === 0) {
                $container.append(`
                    <div class="status-card">
                        <div class="status-card-label">No Status Data</div>
                        <div class="status-card-value">0</div>
                    </div>
                `);
            } else {
                data.forEach(function(status) {
                    if (status.count > 0) {
                        $container.append(`
                            <div class="status-card">
                                <div class="status-card-label">${status.status_name}</div>
                                <div class="status-card-value">${status.count}</div>
                            </div>
                        `);
                    }
                });
            }
        },
        error: function(xhr) {
            console.error("Error loading status counts:", xhr.responseText);
        }
    });
}

function loadMyLeads(page = 1) {
    $.ajax({
        url: '<?php echo e(route("myleads.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            per_page: 10
        },
        success: function (data) {
            let html = '';

            if (data.data.length === 0) {
                html = '<tr><td colspan="16" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
            } else {
                data.data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        const fullRemark = record.latest_remark.remark || '';
                        const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                        remark = `<a href="/remark?sales_record_id=${record.id}" title="${fullRemark.replace(/"/g, '&quot;')}" class="remark-link">${shortRemark}</a>`;
                    }

                    // Create dropdown options for team members (if user is manager)
                    let dropdownOptions = '<option value="">Select User</option>';
                    if (window.allUsers && window.allUsers.length > 0) {
                        window.allUsers.forEach(function (u) {
                            dropdownOptions += `<option value="${u.id}">${u.name}</option>`;
                        });
                    }
                    let assignToColumn = `
                        <td>
                            <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                ${dropdownOptions}
                            </select>
                        </td>
                    `;

                    const statusName = record.status?.status_name ?? 'N/A';
                    const statusBadge = `<span class="status-badge">${statusName}</span>`;

                    html += `
                        <tr>
                            <td>${statusBadge}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${formatDateOnly(record.next_follow_up_date)}</td>
                            <td>${remark}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? '0'}</td>
                            ${assignToColumn}
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);
            renderPagination(data);
            updateRangeInfo(data.from, data.to, data.total);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            $('#sales_table tbody').html('<tr><td colspan="16" class="text-center empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">Error loading data. Please try again.</p></td></tr>');
        }
    });
}

function renderPagination(data) {
    const $pagination = $('#paginationLinks');
    const current = data.current_page;
    const last = data.last_page;
    buildSimplePagination($pagination, current, last);
    $('#paginationfilterLinks').hide();
    $('#paginationsearchLinks').hide();
    $('#paginationdateLinks').hide();
    $pagination.show();
}

function updateRangeInfo(from, to, total) {
    const $info = $('#myleadsRangeInfo');
    if (!$info.length) return;

    const totalValue = Number(total);
    const safeTotal = Number.isFinite(totalValue) && totalValue >= 0 ? totalValue : 0;

    const startValue = Number(from);
    const safeStart = safeTotal === 0 ? 0 : (Number.isFinite(startValue) && startValue > 0 ? startValue : 1);

    const endValue = Number(to);
    const safeEnd = safeTotal === 0 ? 0 : (Number.isFinite(endValue) && endValue >= safeStart ? endValue : safeStart);

    const formattedStart = safeStart.toLocaleString('en-IN');
    const formattedEnd = safeEnd.toLocaleString('en-IN');
    const formattedTotal = safeTotal.toLocaleString('en-IN');

    $info.text(`Showing ${formattedStart}-${formattedEnd} from ${formattedTotal} data`);
}

$(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page && page !== currentPage) {
        currentPage = page;
        loadMyLeads(page);
    }
});

$(document).ready(function () {
    // Load summary stats and status counts
    loadSummaryStats();
    loadStatusCounts();
    
    // Load team members first, then load leads
    <?php if($hasSubordinates): ?>
            console.log('Loading all users...');
            loadAllUsers().then(function(){ loadMyLeads(); });
    <?php else: ?>
            console.log('Loading all users...');
            loadAllUsers().then(function(){ loadMyLeads(); });
    <?php endif; ?>
});

// search functionality
function searchMyLeads(page = 1) {
    let search = $("#search").val();

    $.ajax({
        url: '<?php echo e(route("myleads.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            search: search,
            per_page: 10
        },
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="16" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
            } else {
                data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        const fullRemark = record.latest_remark.remark || '';
                        const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                        remark = `<a href="/remark?sales_record_id=${record.id}" title="${fullRemark.replace(/"/g, '&quot;')}" class="remark-link">${shortRemark}</a>`;
                    }

                    // Create dropdown options for team members (if user is manager)
                    let dropdownOptions = '<option value="">Select User</option>';
                    if (window.allUsers && window.allUsers.length > 0) {
                        window.allUsers.forEach(function (u) {
                            dropdownOptions += `<option value="${u.id}">${u.name}</option>`;
                        });
                    }
                    let assignToColumn = `
                        <td>
                            <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                ${dropdownOptions}
                            </select>
                        </td>
                    `;

                    const statusName = record.status?.status_name ?? 'N/A';
                    const statusBadge = `<span class="status-badge">${statusName}</span>`;
                    
                    html += `
                        <tr>
                            <td>${statusBadge}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? 'N/A'}</td>
                            <td>${record.contact_person ?? 'N/A'}</td>
                            <td>${record.contact_number ?? 'N/A'}</td>
                            <td>${formatDateOnly(record.next_follow_up_date)}</td>
                            <td>${remark}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? 'N/A'}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? '0'}</td>
                            ${assignToColumn}
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);

            // compact pagination
            buildSimplePagination($('#paginationsearchLinks'), response.current_page, response.last_page);
            $('#paginationLinks').hide();
            $('#paginationfilterLinks').hide();
            $('#paginationdateLinks').hide();
            $('#paginationsearchLinks').show();
            updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
        }
    });
}

// Trigger on keyup
$("#search").on("keyup", function () {
    searchMyLeads(1); 
});

// Handle pagination click
$(document).on('click', '#paginationsearchLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        searchMyLeads(page);
    }
});

// Load filter options
$(document).ready(function() {
    // get business type 
    $.ajax({
        url: "<?php echo e(route('getbusiness')); ?>",
        type: "GET",
        success: function (data) {
            $('#business_type').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
            });
        },
        error: function () {
            $('#business_type').html('<option value="">Unable to load types</option>');
        }
    });

    // get status
    $.ajax({
        url: "<?php echo e(route('getStatuses')); ?>",
        type: 'GET',
        success: function (data) {
            $('#sales_status').empty().append('<option value="">Select</option>');
            $.each(data, function (key, status) {
                $('#sales_status').append(`<option value="${status.id}">${status.status_name}</option>`);
            });
        },
        error: function () {
            alert('Failed to load sales statuses.');
        }
    });

    // get state
    $.ajax({
        url: "<?php echo e(route('state')); ?>",
        type: "GET",
        dataType: "json",
        success: function (states) {
            let $stateDropdown = $('#state');
            $stateDropdown.empty();
            $stateDropdown.append('<option value="">Select</option>');
            
            $.each(states, function (id, name) {
                $stateDropdown.append(`<option value="${id}">${name}</option>`);
            });
        },
        error: function () {
            alert("Failed to load states.");
        }
    });

    // get sources
    $.ajax({
        url: "<?php echo e(route('getsource')); ?>",
        type: "GET",
        success: function (data) {
            $('#lead_source').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#lead_source').append(`<option value="${type.id}">${type.source_name}</option>`);
            });
        },
        error: function () {
            $('#lead_source').html('<option value="">Unable to load types</option>');
        }
    });

    // get product
    $.ajax({
        url: "<?php echo e(route('getproduct')); ?>",
        type: "GET",
        success: function (data) {
            $('#product_type').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#product_type').append(`<option value="${type.id}">${type.product_name}</option>`);
            });
        },
        error: function () {
            $('#product_type').html('<option value="">Unable to load types</option>');
        }
    });

    // get all cities
    $.ajax({
        url: "<?php echo e(route('allcity')); ?>",
        type: "GET",
        success: function (data) {
            $('#city').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#city').append(`<option value="${type.id}">${type.city_name}</option>`);
            });
        },
        error: function () {
            $('#city').html('<option value="">Unable to load types</option>');
        }
    });

    // State change - load cities for selected state
    $('#state').on('change', function() {
        const stateId = $(this).val();
        if (stateId) {
            $.ajax({
                url: `/myleads/cities/${stateId}`,
                type: 'GET',
                success: function(response) {
                    let cityOptions = '<option value="">Select City</option>';
                    response.forEach(function(city) {
                        cityOptions += `<option value="${city.id}">${city.city_name}</option>`;
                    });
                    $('#city').html(cityOptions);
                },
                error: function() {
                    $('#city').html('<option value="">Unable to load cities</option>');
                }
            });
        } else {
            $('#city').html('<option value="">Select City</option>');
        }
    });
});

// filter functionality
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function loadFilteredMyLeads(page = 1) {
    $.ajax({
        url: '<?php echo e(route("myleads.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            status_id: $('#sales_status').val(),
            city_id: $('#city').val(),
            state_id: $('#state').val(),
            business_type_id: $('#business_type').val(),
            lead_source_id: $('#lead_source').val(),
            products_id: $('#product_type').val(),
            per_page: 10
        },
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="16" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
            } else {
                data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        const fullRemark = record.latest_remark.remark || '';
                        const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                        remark = `<a href="/remark?sales_record_id=${record.id}" title="${fullRemark.replace(/"/g, '&quot;')}" class="remark-link">${shortRemark}</a>`;
                    }

                    // Create dropdown options for team members (if user is manager)
                    let dropdownOptions = '<option value="">Select User</option>';
                    if (window.allUsers && window.allUsers.length > 0) {
                        window.allUsers.forEach(function (u) {
                            dropdownOptions += `<option value="${u.id}">${u.name}</option>`;
                        });
                    }
                    let assignToColumn = `
                        <td>
                            <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                ${dropdownOptions}
                            </select>
                        </td>
                    `;

                    const statusName = record.status?.status_name ?? 'N/A';
                    const statusBadge = `<span class="status-badge">${statusName}</span>`;

                    html += `
                        <tr>
                            <td>${statusBadge}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${formatDateOnly(record.next_follow_up_date)}</td>
                            <td>${remark}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? '0'}</td>
                            ${assignToColumn}
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);

            // compact pagination
            buildSimplePagination($('#paginationfilterLinks'), response.current_page, response.last_page);
            $('#paginationLinks').hide();
            $('#paginationsearchLinks').hide();
            $('#paginationdateLinks').hide();
            $('#paginationfilterLinks').show();
            updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            alert("Server error occurred. Check the console.");
        }
    });
}

$(document).on('click', '#paginationfilterLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        loadFilteredMyLeads(page);
    }
});

$(document).on('change', '#sales_status, #city, #state, #business_type, #lead_source, #product_type', function () {
    loadFilteredMyLeads(1);
});

// date filter functionality
function loadDateFilteredMyLeads(from_date = '', to_date = '', page = 1) {
    $.ajax({
        url: '<?php echo e(route("myleads.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            date_from: from_date,
            date_to: to_date,
            per_page: 10
        },
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="16" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
            } else {
                data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        const fullRemark = record.latest_remark.remark || '';
                        const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                        remark = `<a href="/remark?sales_record_id=${record.id}" title="${fullRemark.replace(/"/g, '&quot;')}" class="remark-link">${shortRemark}</a>`;
                    }

                    // Create dropdown options for team members (if user is manager)
                    let dropdownOptions = '<option value="">Select User</option>';
                    if (window.allUsers && window.allUsers.length > 0) {
                        window.allUsers.forEach(function (u) {
                            dropdownOptions += `<option value="${u.id}">${u.name}</option>`;
                        });
                    }
                    let assignToColumn = `
                        <td>
                            <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                ${dropdownOptions}
                            </select>
                        </td>
                    `;

                    const statusName = record.status?.status_name ?? 'N/A';
                    const statusBadge = `<span class="status-badge">${statusName}</span>`;

                    html += `
                        <tr>
                            <td>${statusBadge}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${formatDateOnly(record.next_follow_up_date)}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? '0'}</td>
                            ${assignToColumn}
                            <td>${remark}</td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);

            // compact pagination
            buildSimplePagination($('#paginationdateLinks'), response.current_page, response.last_page);
            $('#paginationLinks').hide();
            $('#paginationfilterLinks').hide();
            $('#paginationsearchLinks').hide();
            $('#paginationdateLinks').show();
            updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            alert("Server error occurred. Check the console.");
        }
    });
}

$(document).on('change', '#from_date, #to_date', function () {
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    loadDateFilteredMyLeads(from_date, to_date, 1);
});

$(document).on('click', '#paginationdateLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    if (page) {
        loadDateFilteredMyLeads(from_date, to_date, page);
    }
});

// Load all users for reassignment dropdowns
function loadAllUsers() {
    return $.ajax({
        url: '<?php echo e(route("fetchUsersForManager")); ?>',
        type: 'GET',
        success: function(response) {
            window.allUsers = response || [];
        }
    });
}

// Handle lead reassignment
function reassignLead(leadId, newUserId) {
    if (!newUserId) return;
    
    $.ajax({
        url: '<?php echo e(route("myleads.reassign")); ?>',
        type: 'POST',
        data: {
            lead_id: leadId,
            new_user_id: newUserId,
            _token: '<?php echo e(csrf_token()); ?>'
        },
        success: function(response) {
            if (response.success) {
                // Show success message
                alert('Lead reassigned successfully!');
                // Reload the table to reflect changes
                loadMyLeads();
                // Reload summary stats and status counts
                loadSummaryStats();
                loadStatusCounts();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error reassigning lead:', xhr.responseText);
            alert('Error reassigning lead. Please try again.');
        }
    });
}

</script>
<script>
// --- Add Lead Modal Logic ---
$(document).ready(function() {
    // Initial fetch for Add Lead modal dropdowns
    fetchAddLeadOptions();
});

function fetchAddLeadOptions() {
    // Prospectus
    $.get("<?php echo e(route('getProspectus')); ?>", function(data) {
        let opts = '<option value="">Select Prospectus</option>';
        $.each(data, function(key, val) { opts += `<option value="${val.id}">${val.prospectus_name}</option>`; });
        $('#add_lead_prospectus').html(opts);
    });

    // States (Add Lead)
    $.get("<?php echo e(route('state')); ?>", function(data) {
        let opts = '<option value="">Select State</option>';
        $.each(data, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
        $('#add_lead_state').html(opts);
    });

    // States (Prospect Modal)
    $.get("<?php echo e(route('state')); ?>", function(data) {
        let opts = '<option value="">Select State</option>';
        $.each(data, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
        $('#modal_state').html(opts);
    });
    
    // Status
    $.get("<?php echo e(route('getStatuses')); ?>", function(data) {
         let opts = '<option value="">Select Status</option>';
         $.each(data, function(key, val) { opts += `<option value="${val.id}">${val.status_name}</option>`; });
         $('#add_lead_sales_status').html(opts);
    });
    
    // Business Type (Add Lead)
    $.get("<?php echo e(route('getbusiness')); ?>", function(data) {
         let opts = '<option value="">Select Business Type</option>';
         $.each(data, function(idx, val) { opts += `<option value="${val.id}">${val.business_name}</option>`; });
         $('#add_lead_business_type').html(opts);
    });

    // Business Type (Prospect Modal)
    $.get("<?php echo e(route('getbusiness')); ?>", function(data) {
         let opts = '<option value="">Select Business Type</option>';
         $.each(data, function(idx, val) { opts += `<option value="${val.id}">${val.business_name}</option>`; });
         $('#modal_business_type').html(opts);
    });
    
    // Lead Source
    $.get("<?php echo e(route('getsource')); ?>", function(data) {
         let opts = '<option value="">Select Source</option>';
         $.each(data, function(idx, val) { opts += `<option value="${val.id}">${val.source_name}</option>`; });
         $('#add_lead_lead_source').html(opts);
    });
    
    // Product Type
    $.get("<?php echo e(route('getproduct')); ?>", function(data) {
         let opts = '<option value="">Select Product Type</option>';
         $.each(data, function(idx, val) { opts += `<option value="${val.id}">${val.product_name}</option>`; });
         $('#add_lead_product_type').html(opts);
    });
}

// Add Lead State Change -> Cities
$(document).on('change', '#add_lead_state', function() {
    let stateId = $(this).val();
    if(stateId) {
        $.get("/city/" + stateId, function(data) {
             let opts = '<option value="">Select City</option>';
             $.each(data, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
             $('#add_lead_city').html(opts);
        });
    } else {
        $('#add_lead_city').html('<option value="">Select City</option>');
    }
});

// Prospect Modal State Change -> Cities
$(document).on('change', '#modal_state', function() {
    let stateId = $(this).val();
    if(stateId) {
        $.get("/city/" + stateId, function(data) {
             let opts = '<option value="">Select City</option>';
             $.each(data, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
             $('#modal_city').html(opts);
        });
    } else {
        $('#modal_city').html('<option value="">Select City</option>');
    }
});

// Prospectus Change -> Fill Data
$(document).on('change', '#add_lead_prospectus', function() {
    let id = $(this).val();
    if(id) {
        $.get('/fillprospectus/' + id, function(data) {
            $('#add_lead_contactPerson').val(data.contact_person);
            $('#add_lead_contactNumber').val(data.contact_number);
            $('#add_lead_address').val(data.address);
            $('#add_lead_leadsName').val(data.prospectus_name);
            $('#add_lead_email').val(data.email);
            $('#add_lead_website_link').val(data.website_link);
            $('#add_lead_business_type').val(data.business_type_id);
            $('#add_lead_state').val(data.state_id); 
            
             if(data.state_id){
                 $.get("/city/" + data.state_id, function(cities) {
                     let opts = '<option value="">Select City</option>';
                     $.each(cities, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
                     $('#add_lead_city').html(opts).val(data.city_id);
                 });
             }
        });
    }
});

function submitLead(e) {
    e.preventDefault();
    let data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        prospectus_id: $('#add_lead_prospectus').val(),
        leads_name: $('#add_lead_leadsName').val(),
        contact_person: $('#add_lead_contactPerson').val(),
        contact_number: $('#add_lead_contactNumber').val(),
        status_id: $('#add_lead_sales_status').val(),
        address: $('#add_lead_address').val(),
        state_id: $('#add_lead_state').val(),
        city_id: $('#add_lead_city').val(),
        email: $('#add_lead_email').val(),
        website_link: $('#add_lead_website_link').val(),
        next_follow_up_date: $('#add_lead_next_follow_up').val(),
        business_type_id: $('#add_lead_business_type').val(),
        remark: $('#add_lead_remark').val(),
        lead_source_id: $('#add_lead_lead_source').val(),
        products_id: $('#add_lead_product_type').val()
    };
    
    $.post('/savelead', data)
     .done(function(response) {
         showAlert('success', 'Sales record submitted successfully!');
         $('#addLeadForm')[0].reset();
         $('#addLeadModal').modal('hide');
         loadMyLeads(); 
         loadSummaryStats(); 
         loadStatusCounts();
     })
     .fail(function(xhr) {
         console.error(xhr.responseText);
         showAlert('error', 'Failed to submit sales record!');
     });
}

function submitProspect(e) {
    e.preventDefault();
    let data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        prospectus_name: $('#modalnewProspectusName').val(),
        contact_person: $('#modal_contact_person').val(),
        contact_number: $('#modal_contact_number').val(),
        address: $('#modal_address').val(),
        state_id: $('#modal_state').val(),
        city_id: $('#modal_city').val(),
        email: $('#modal_email').val(),
        website_link: $('#modal_website_link').val(),
        business_type_id: $('#modal_business_type').val()
    };

    $.post('/prospectus', data)
    .done(function(response) {
        $('#addProspectusModal').modal('hide');
        $('#addProspectusForm')[0].reset();
        showAlert('success', 'Prospect added successfully.');
        // Refresh Prospectus Dropdown in Add Lead Modal
         $.get("<?php echo e(route('getProspectus')); ?>", function(data) {
            let opts = '<option value="">Select Prospectus</option>';
            $.each(data, function(key, val) { opts += `<option value="${val.id}">${val.prospectus_name}</option>`; });
            $('#add_lead_prospectus').html(opts);
        });
    })
    .fail(function(xhr) {
        showAlert('error', 'Something went wrong while adding prospect!');
        console.log(xhr.responseText);
    });
}
function openProspectusModal() {
   $('#addLeadModal .blur-target').addClass('blur-active');
    $('#addProspectusModal').modal('show');

}

$(document).ready(function () {

    $('#addProspectusModal').on('hidden.bs.modal', function () {
        $('#addLeadModal .blur-target').removeClass('blur-active');
    });

});

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    const alertHtml = `
      <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <i class="bi ${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    $('body').append(alertHtml);
    setTimeout(() => $('.alert').fadeOut(), 3000);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/myleads.blade.php ENDPATH**/ ?>