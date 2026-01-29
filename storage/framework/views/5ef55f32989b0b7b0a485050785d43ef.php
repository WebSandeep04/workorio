<!-- from the new laptop2 -->


<?php $__env->startSection('title', 'Sales Product'); ?>
<?php $__env->startSection('page_title', 'Sales Product'); ?>

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
      grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
      gap: 0.5rem;
      margin-bottom: 1rem;
    }

    .summary-card,
    .status-card {
      background: #fff;
      border-radius: 10px;
      border: 1px solid #eceef3;
      padding: 0.4rem;
      box-shadow: 0px 4px 4px 0px #0000000A;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      width: 100%;
      min-height: 55px;
      height: 55px;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .metric-arrow {
      width: 24px;
      height: 24px;
      border-radius: 6px;
      color: #000;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: all 0.2s ease;
      position: absolute;
      right: 6px;
      bottom: 6px;
      font-size: 0.8rem;
    }

    .metric-arrow:hover {
      background: #5b59f7;
      color: #fff;
    }

    .summary-card-icon {
      width: 32px;
      height: 32px;
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .summary-card-icon img {
      width: 20px;
      height: 20px;
      object-fit: contain;
    }

    .icon-sunrise {
      background: linear-gradient(135deg, #f97316, #fb923c);
    }

    .icon-amber {
      background: linear-gradient(135deg, #f59e0b, #fbbf24);
    }

    .icon-emerald {
      background: linear-gradient(135deg, #34d399, #10b981);
    }

    .icon-rose {
      background: linear-gradient(135deg, #fb7185, #f43f5e);
    }

    .icon-sky {
      background: linear-gradient(135deg, #3b82f6, #60a5fa);
    }

    .icon-violet {
      background: linear-gradient(135deg, #8b5cf6, #a78bfa);
    }

    .summary-card-content {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      flex-grow: 1;
      min-width: 0;
    }

    .summary-card::before,
    .status-card::before {
      display: none;
    }

    .summary-card:hover,
    .status-card:hover {
      transform: translateY(-2px);
      box-shadow: 0px 8px 8px 0px #0000000A;
    }

    .summary-card.card-1 {
      background: #fff;
    }

    .summary-card.card-2 {
      background: #fff;
    }

    .summary-card.card-3 {
      background: #fff;
    }

    .summary-card.card-4 {
      background: #fff;
    }

    .summary-card.card-5 {
      background: #fff;
    }

    /* Status cards - all white background like dashboard */
    .status-card:nth-child(6n+1),
    .status-card:nth-child(6n+2),
    .status-card:nth-child(6n+3),
    .status-card:nth-child(6n+4),
    .status-card:nth-child(6n+5),
    .status-card:nth-child(6n+6),
    .status-card:nth-child(6n+7),
    .status-card:nth-child(6n+8),
    .status-card:nth-child(6n+9),
    .status-card:nth-child(6n+10),
    .status-card:nth-child(6n+11),
    .status-card:nth-child(6n+12) {
      background: #fff;
    }

    .summary-card-label,
    .status-card-label {
      font-size: 8px;
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: 0.15rem;
      color: #000;
      flex-shrink: 0;
      line-height: 1.1;
      font-family: Montserrat;
    }

    .summary-card-value,
    .status-card-value {
      font-size: 0.9rem;
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
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .filterBox {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 0.5rem;
      background: #434AFA;
      padding: 0.75rem;
      color: #fff;
      border-radius: 5px;
      flex-wrap: wrap;
      box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
      margin-bottom: 0.5rem;
      border: 1px solid #434AFA;
      font-family: Montserrat, sans-serif;
    }

    .filterBox .form-label-modern {
      color: #fff;
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
      border-radius: 2px;
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
      background: #434afa;
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

    .custom-table {
      border-collapse: separate;
      border-spacing: 0;
      width: 100%;
      background: white;
      border-radius: 0px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .custom-table th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-weight: 600;
      font-size: 9px;
      padding: 0;
      text-align: center;
      border: none;
      position: sticky;
      top: 0;
      z-index: 10;
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .custom-table td {
      font-size: 9px;
      padding: 0;
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

    .bulk-assignment-controls {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 0.5rem;
      margin: 0.5rem 0;
    }

    .bulk-assignment-controls .form-select {
      border: 2px solid #667eea;
      border-radius: 6px;
      padding: 0.25rem 0.5rem;
      font-size: 10px;
    }

    .bulk-assignment-controls .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 6px;
      padding: 0.25rem 0.75rem;
      font-size: 10px;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
      transition: all 0.3s ease;
    }

    .bulk-assignment-controls .btn-primary:hover {
      background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(102, 126, 234, 0.5);
    }

    .lead-checkbox {
      width: 14px;
      height: 14px;
      cursor: pointer;
      accent-color: #667eea;
    }

    .status-badge {
      display: inline-block;
      color: #000;
      font-size: 0.85rem;
      font-weight: normal;
      font-family: Montserrat, sans-serif;
    }

    .custom-table .badge {
      font-size: 9px;
      padding: 2px 6px;
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
      border-radius: 18px;
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
      font-family: Montserrat;
    }

    .data-table-card .custom-table thead th,
    .data-table-card .custom-table tbody td {
      white-space: nowrap;
    }

    .data-table-card .custom-table tbody td {
      font-size: 0.85rem;
      padding: 0.65rem 0.75rem;
      color: #000;
      border-bottom: 1px solid #f4f4f6;
      text-align: left;
      background: transparent;
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

    .data-table-card .custom-table tbody td:nth-child(1) {
      min-width: 100px;
    }

    .data-table-card .custom-table tbody td:nth-child(2) {
      min-width: 120px;
    }

    .data-table-card .custom-table tbody td:nth-child(3) {
      min-width: 120px;
    }

    .data-table-card .custom-table tbody td:nth-child(4) {
      min-width: 140px;
    }

    .data-table-card .custom-table tbody td:nth-child(5) {
      min-width: 110px;
    }

    .data-table-card .custom-table tbody td:nth-child(6) {
      min-width: 120px;
    }

    .data-table-card .custom-table tbody td:nth-child(7) {
      min-width: 120px;
    }

    .data-table-card .custom-table tbody td:nth-child(8) {
      min-width: 120px;
    }

    .data-table-card .custom-table tbody td:nth-child(9) {
      min-width: 150px;
    }

    .data-table-card .custom-table tbody td:nth-child(10) {
      min-width: 130px;
    }

    .data-table-card .custom-table tbody td:nth-child(11) {
      min-width: 130px;
    }

    .data-table-card .custom-table tbody td:nth-child(12) {
      min-width: 130px;
    }

    .data-table-card .custom-table tbody td:nth-child(13) {
      min-width: 110px;
    }

    .data-table-card .custom-table tbody td:nth-child(14) {
      min-width: 140px;
    }

    .data-table-card .custom-table tbody tr:last-child td {
      border-bottom: none;
    }

    .data-table-card .custom-table tbody td .text-danger,
    .data-table-card .custom-table tbody td .priority-high,
    .data-table-card .custom-table tbody td .highlight-high {
      color: #ef4444;
      font-weight: 600;
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
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(360deg);
      }
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

    @media (max-width: 767px) {
      .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .summary-cards,
      .status-cards {
        grid-template-columns: repeat(2, 1fr);
      }

      .data-table-card .custom-table tbody td {
        font-size: 0.75rem
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
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
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

    .form-control-modern,
    .form-select-modern {
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      padding: 0.4rem 0.6rem;
      transition: all 0.3s ease;
      font-size: 0.8rem;
      font-family: Montserrat, sans-serif;
    }

    .form-control-modern:focus,
    .form-select-modern:focus {
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
        <a href="<?php echo e(route('alldata.today-followups')); ?>" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="summary-card card-2">
        <div class="summary-card-icon icon-amber">
          <img src="<?php echo e(asset('img/icons/underprocess.png')); ?>" alt="Under Process">
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Under Process</div>
          <div class="summary-card-value" id="underProcess">0</div>
        </div>
        <a href="<?php echo e(route('alldata.under-process')); ?>" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="summary-card card-3">
        <div class="summary-card-icon icon-emerald">
          <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Completed">
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Today Completed</div>
          <div class="summary-card-value" id="todayCompleted">0</div>
        </div>
        <a href="<?php echo e(route('alldata.today-completed')); ?>" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="summary-card card-4">
        <div class="summary-card-icon icon-rose">
          <img src="<?php echo e(asset('img/icons/pending.png')); ?>" alt="Pending">
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Today Pending</div>
          <div class="summary-card-value" id="todayPending">0</div>
        </div>
        <a href="<?php echo e(route('alldata.today-pending')); ?>" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="summary-card card-5">
        <div class="summary-card-icon icon-cyan">
          <img src="<?php echo e(asset('img/icons/new.png')); ?>" alt="New">
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Today's New</div>
          <div class="summary-card-value" id="todayNew">0</div>
        </div>
        <a href="<?php echo e(route('alldata.today-new')); ?>" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
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
    <?php if (isset($component)) { $__componentOriginalf3f7946f558699cf27352737986448eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3f7946f558699cf27352737986448eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-panel','data' => ['showSearch' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show-search' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf3f7946f558699cf27352737986448eb)): ?>
<?php $attributes = $__attributesOriginalf3f7946f558699cf27352737986448eb; ?>
<?php unset($__attributesOriginalf3f7946f558699cf27352737986448eb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf3f7946f558699cf27352737986448eb)): ?>
<?php $component = $__componentOriginalf3f7946f558699cf27352737986448eb; ?>
<?php unset($__componentOriginalf3f7946f558699cf27352737986448eb); ?>
<?php endif; ?>

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
          <table class="table custom-table" id="alldatatable">
            <thead>
              <tr>
                <th>Status</th>
                <th>Prospect</th>
                <th>Remark</th>
                <th>Lead</th>
                <th>Contact Person</th>
                <th>Contact No.</th>
                <th>Next Follow</th>
                <th>State</th>
                <th>City</th>
                <th>Email</th>
                <th>Business</th>
                <th>Source</th>
                <th>Product</th>
                <th>Ticket</th>
                <?php if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1)): ?>
                  <th>Assign To</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td
                  colspan="<?php echo e(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '15' : '14'); ?>"
                  class="loading-state">
                  <i class="bi bi-arrow-repeat"></i>
                  <p class="mt-2 mb-0">Loading sales records...</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="table-range-meta" id="alldataRangeInfo">
      Showing 0-0 from 0 data
    </div>

    <!-- Bulk Assignment Controls -->
    <?php if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1)): ?>
      <div class="bulk-assignment-controls" id="bulkAssignmentControls" style="display: none;">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-md-4">
                <span class="text-muted fw-bold" style="font-size: 10px;">
                  <i class="bi bi-check-circle"></i>
                  <span id="selectedCount">0</span> leads selected
                </span>
              </div>
              <div class="col-md-4">
                <select class="form-select" id="bulkAssignUser" style="font-size: 10px; padding: 0.25rem 0.5rem;">
                  <option value="">Select User to Assign</option>
                </select>
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-primary btn-sm" id="bulkAssignBtn" disabled>
                  <i class="bi bi-people"></i>
                  Assign Selected
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="clearSelectionBtn"
                  style="font-size: 10px; padding: 0.25rem 0.75rem;">
                  <i class="bi bi-x-circle"></i>
                  Clear
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
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
  <div class="modal fade" id="addLeadModal" tabindex="-1" aria-labelledby="addLeadModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
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
                  <input type="text" class="form-control form-control-modern" id="add_lead_leadsName" name="leads_name"
                    placeholder="Enter Lead Name">
                </div>

                <div class="mb-2">
                  <label for="add_lead_contactPerson" class="form-label-modern">Contact Person</label>
                  <input type="text" class="form-control form-control-modern" id="add_lead_contactPerson"
                    name="contact_person" placeholder="Enter Contact Person Name">
                </div>

                <div class="mb-2">
                  <label for="add_lead_contactNumber" class="form-label-modern">Contact Number</label>
                  <input type="tel" class="form-control form-control-modern" id="add_lead_contactNumber"
                    name="contact_number" placeholder="Enter Contact Number">
                </div>

                <div class="mb-2">
                  <label for="add_lead_sales_status" class="form-label-modern">Status</label>
                  <select class="form-select form-select-modern" id="add_lead_sales_status" name="sales_status" required>
                    <option value="">Loading...</option>
                  </select>
                </div>

                <div class="mb-2">
                  <label for="add_lead_address" class="form-label-modern">Address</label>
                  <input type="text" class="form-control form-control-modern" id="add_lead_address" name="address"
                    placeholder="Enter Address">
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
                  <input type="email" class="form-control form-control-modern" id="add_lead_email" name="email"
                    placeholder="Enter Email ID">
                </div>

                <div class="mb-2">
                  <label for="add_lead_website_link" class="form-label-modern">Website</label>
                  <input type="url" class="form-control form-control-modern" id="add_lead_website_link"
                    name="website_link" placeholder="Enter Website URL">
                </div>

                <div class="mb-2">
                  <label for="add_lead_next_follow_up" class="form-label-modern">Next Follow-up Date</label>
                  <input type="date" class="form-control form-control-modern" id="add_lead_next_follow_up"
                    name="next_follow_up_date" required>
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
                  <textarea class="form-control form-control-modern" id="add_lead_remark" name="remark"
                    placeholder="Enter Remark" rows="2" required></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="submit" onclick="submitLead(event)"
            class="btn-modern btn-modern-primary w-100 justify-content-center" form="addLeadForm">
            <i class="bi bi-check-circle"></i> Submit Lead
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Prospectus Modal -->
  <div class="modal fade" id="addProspectusModal" tabindex="-1" aria-labelledby="addProspectusModalLabel"
    aria-hidden="true" data-bs-backdrop="static">
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
                <input type="text" class="form-control form-control-modern" id="modalnewProspectusName"
                  name="modal_new_prospectus_name" placeholder="Enter Prospectus Name" required>
              </div>
              <div class="col-md-6">
                <label for="modal_contact_person" class="form-label-modern">Contact Person</label>
                <input type="text" class="form-control form-control-modern" id="modal_contact_person"
                  name="modal_contact_person" placeholder="Enter Contact Person" required>
              </div>
              <div class="col-md-6">
                <label for="modal_contact_number" class="form-label-modern">Contact Number</label>
                <input type="text" class="form-control form-control-modern" id="modal_contact_number"
                  name="modal_contact_number" placeholder="Enter Contact Number" required>
              </div>
              <div class="col-md-6">
                <label for="modal_address" class="form-label-modern">Address</label>
                <input type="text" class="form-control form-control-modern" id="modal_address" name="modal_address"
                  placeholder="Enter Address" required>
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
                <input type="email" class="form-control form-control-modern" id="modal_email" name="modal_email"
                  placeholder="Enter Email" required>
              </div>
              <div class="col-md-6">
                <label for="modal_website_link" class="form-label-modern">Website</label>
                <input type="url" class="form-control form-control-modern" id="modal_website_link"
                  name="modal_website_link" placeholder="Enter Website URL">
              </div>
              <div class="col-md-6">
                <label for="modal_business_type" class="form-label-modern">Business Type</label>
                <select class="form-select form-select-modern" id="modal_business_type" name="modal_business_type"
                  required>
                  <option value="">Loading...</option>
                </select>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="submit" onclick="submitProspect(event)"
            class="btn-modern btn-modern-primary w-100 justify-content-center" form="addProspectusForm">
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

    // Load summary stats
    function loadSummaryStats() {
      $.get("<?php echo e(route('alldata.summary-stats')); ?>")
        .done(function (data) {
          $('#todayFollowups').text(data.today_followups || 0);
          $('#underProcess').text(data.under_process || 0);
          $('#todayCompleted').text(data.today_completed || 0);
          $('#todayPending').text(data.today_pending || 0);
          $('#todayNew').text(data.today_new || 0);
        })
        .fail(function () {
          console.error('Failed to load summary stats');
        });
    }

    // Load status counts
    function loadStatusCounts() {
      $.get("<?php echo e(route('alldata.status-counts')); ?>")
        .done(function (data) {
          if (!data || data.length === 0) {
            $('#statusCardsContainer').html('<div class="status-card"><div class="status-card-label">No Statuses</div><div class="status-card-value">0</div></div>');
            return;
          }

          let html = '';
          data.forEach(function (status) {
            html += `
                <div class="status-card">
                  <div class="status-card-label">${status.status_name || 'N/A'}</div>
                  <div class="status-card-value">${status.count || 0}</div>
                </div>
              `;
          });
          $('#statusCardsContainer').html(html);
        })
        .fail(function () {
          console.error('Failed to load status counts');
          $('#statusCardsContainer').html('<div class="status-card"><div class="status-card-label">Error Loading</div><div class="status-card-value">-</div></div>');
        });
    }

    function loadSalesRecords(page = 1) {
      $('#alldatatable tbody').html(`
          <tr>
            <td colspan="<?php echo e(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '15' : '14'); ?>" class="loading-state">
              <i class="bi bi-arrow-repeat"></i>
              <p class="mt-2 mb-0">Loading sales records...</p>
            </td>
          </tr>
        `);

      $.ajax({
        url: '<?php echo e(route("fetchalldata")); ?>?page=' + page,
        type: 'GET',
        success: function (data) {
          let html = '';

          if (data.data.length === 0) {
            html = `<tr>
                      <td colspan="<?php echo e(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '15' : '14'); ?>" class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>No Records Found</h5>
                        <p>No sales records available at the moment.</p>
                      </td>
                    </tr>`;
          } else {
            data.data.forEach(function (record, index) {
              let remark = '-';
              if (record.latest_remark) {
                const fullRemark = record.latest_remark || '';
                const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
              }

              let assignToColumn = '';
              <?php if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1)): ?>
                let dropdownOptions = '<option value="">Select Member</option>';
                if (window.teamMembers && window.teamMembers.length > 0) {
                  window.teamMembers.forEach(function (member) {
                    dropdownOptions += `<option value="${member.id}">${member.name}</option>`;
                  });
                }
                assignToColumn = `
                                <td>
                                    <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                        ${dropdownOptions}
                                    </select>
                                </td>
                            `;
              <?php endif; ?>

              html += `
                            <tr>
                                <td><span class="status-badge">${record.status_name ?? 'N/A'}</span></td>
                                <td>${record.prospectus_name ?? 'N/A'}</td>
                                <td>${remark}</td>
                                <td>${record.leads_name ?? ''}</td>
                                <td>${record.contact_person ?? ''}</td>
                                <td>${record.contact_number ?? ''}</td>
                                <td>${record.next_follow_up_date ?? 'N/A'}</td>
                                <td>${record.state_name ?? 'N/A'}</td>
                                <td>${record.city_name ?? 'N/A'}</td>
                                <td>${record.email ?? ''}</td>
                                <td>${record.business_name ?? 'N/A'}</td>
                                <td>${record.source_name ?? 'N/A'}</td>
                                <td>${record.product_name ?? 'N/A'}</td>
                                <td>${record.ticket_value ?? '0'}</td>
                                ${assignToColumn}
                            </tr>
                        `;
            });
          }

          $('#alldatatable tbody').html(html);
          renderPagination(data);
          updateRangeInfo(data.from, data.to, data.total);
        },
        error: function (xhr) {
          console.error("Error:", xhr.responseText);
          $('#alldatatable tbody').html(`
                  <tr>
                    <td colspan="<?php echo e(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '15' : '14'); ?>" class="text-danger text-center py-4">
                      <i class="bi bi-exclamation-triangle"></i>
                      <p class="mt-2">Failed to load records. Please try again.</p>
                    </td>
                  </tr>
                `);
        }
      });
    }

    function renderPagination(data) {
      let pagination = $('#paginationLinks');
      pagination.empty();

      const current = data.current_page;
      const last = data.last_page;

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
      const $info = $('#alldataRangeInfo');
      if (!$info.length) return;

      const totalValue = Number(total);
      const safeTotal = Number.isFinite(totalValue) && totalValue >= 0 ? totalValue : 0;

      const startValue = Number(from);
      const safeStart = safeTotal === 0 ? 0 : (Number.isFinite(startValue) && startValue > 0 ? startValue : 1);

      const endValue = Number(to);
      let safeEnd = safeTotal === 0 ? 0 : (Number.isFinite(endValue) && endValue >= safeStart ? endValue : safeStart);

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
        loadSalesRecords(page);
      }
    });

    $(document).ready(function () {
      loadSummaryStats();
      loadStatusCounts();
      loadSalesRecords();
      setInterval(loadSummaryStats, 60000); // Refresh stats every minute
      setInterval(loadStatusCounts, 60000); // Refresh status counts every minute
    });

    // search 

    function searchSalesTable(page = 1) {
      let search = $("#search").val();

      $('#alldatatable tbody').html(`
          <tr>
            <td colspan="<?php echo e(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '15' : '14'); ?>" class="loading-state">
              <i class="bi bi-arrow-repeat"></i>
              <p class="mt-2 mb-0">Searching...</p>
            </td>
          </tr>
        `);

      $.ajax({
        url: '<?php echo e(route("alldatasearch")); ?>?page=' + page,
        type: 'GET',
        data: { search: search },
        success: function (response) {
          let data = response.data;
          let html = '';

          if (data.length === 0) {
            html = `<tr>
                      <td colspan="<?php echo e(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '15' : '14'); ?>" class="empty-state">
                        <i class="bi bi-search"></i>
                        <h5>No Results Found</h5>
                        <p>Try adjusting your search criteria.</p>
                      </td>
                    </tr>`;
          } else {
            data.forEach(function (record, index) {
              const fullRemark = record.last_remark || '';
              const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;

              let assignToColumn = '';
              <?php if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1)): ?>
                let dropdownOptions = '<option value="">Select Member</option>';
                if (window.teamMembers && window.teamMembers.length > 0) {
                  window.teamMembers.forEach(function (member) {
                    dropdownOptions += `<option value="${member.id}">${member.name}</option>`;
                  });
                }
                assignToColumn = `
                                <td>
                                    <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                        ${dropdownOptions}
                                    </select>
                                </td>
                            `;
              <?php endif; ?>

              html += `
                            <tr>
                                <td><span class="status-badge">${record.status_name ?? 'N/A'}</span></td>
                                <td>${record.prospectus_name ?? 'N/A'}</td>
                                <td>${fullRemark ? `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>` : '-'}</td>
                                <td>${record.leads_name ?? 'N/A'}</td>
                                <td>${record.contact_person ?? 'N/A'}</td>
                                <td>${record.contact_number ?? 'N/A'}</td>
                                <td>${record.next_follow_up_date ?? 'N/A'}</td>
                                <td>${record.state_name ?? 'N/A'}</td>
                                <td>${record.city_name ?? 'N/A'}</td>
                                <td>${record.email ?? 'N/A'}</td>
                                <td>${record.business_name ?? 'N/A'}</td>
                                <td>${record.source_name ?? 'N/A'}</td>
                                <td>${record.product_name ?? 'N/A'}</td>
                                <td>${record.ticket_value ?? '0'}</td>
                                ${assignToColumn}
                            </tr>
                        `;
            });
          }

          $('#alldatatable tbody').html(html);

          let links = '';
          response.links.forEach(link => {
            if (link.url !== null) {
              links += `<li class="page-item ${link.active ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${link.url.split('page=')[1]}">${link.label}</a>
                        </li>`;
            } else {
              links += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            }
          });

          $('#paginationsearchLinks').html(links);
          updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
          console.error("Error:", xhr.responseText);
        }
      });
    }

    $("#search").on("keyup", function () {
      $('#paginationLinks').hide();
      $('#paginationfilterLinks').hide();
      searchSalesTable(1);
    });

    $(document).on('click', '#paginationsearchLinks .page-link', function (e) {
      e.preventDefault();
      let page = $(this).data('page');
      if (page) {
        $('#paginationLinks').hide();
        $('#paginationfilterLinks').hide();
        searchSalesTable(page);
      }
    });

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
    $(document).ready(function () {
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

    // get tenant sales users for dropdown
    $.ajax({
      url: "<?php echo e(route('user.sales-users')); ?>",
      type: "GET",
      success: function (data) {
        $('#user_id').empty().append('<option value="">All Sales Users</option>');
        $.each(data, function (index, user) {
          $('#user_id').append(`<option value="${user.id}">${user.name}</option>`);
        });
      },
      error: function () {
        $('#user_id').html('<option value="">Unable to load users</option>');
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
    $('#state').on('change', function () {
      const stateId = $(this).val();
      if (stateId) {
        $.ajax({
          url: `/city/${stateId}`,
          type: 'GET',
          success: function (response) {
            let cityOptions = '<option value="">Select City</option>';
            $.each(response, function (id, name) {
              cityOptions += `<option value="${id}">${name}</option>`;
            });
            $('#city').html(cityOptions);
          },
          error: function () {
            $('#city').html('<option value="">Unable to load cities</option>');
          }
        });
      } else {
        $('#city').html('<option value="">Select City</option>');
      }
    });

    // filter
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    function loadFilteredTable(page = 1) {
      $('#alldatatable tbody').html(`
          <tr>
            <td colspan="<?php echo e(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '15' : '14'); ?>" class="loading-state">
              <i class="bi bi-arrow-repeat"></i>
              <p class="mt-2 mb-0">Filtering records...</p>
            </td>
          </tr>
        `);

      $.ajax({
        url: '<?php echo e(route("alldatafilter")); ?>?page=' + page,
        type: 'POST',
        data: {
          status: $('#sales_status').val(),
          city: $('#city').val(),
          state: $('#state').val(),
          business: $('#business_type').val(),
          source: $('#lead_source').val(),
          product: $('#product_type').val(),
          user_id: $('#user_id').val(),
        },
        success: function (response) {
          let data = response.data;
          let html = '';

          if (data.length === 0) {
            html = `<tr>
                      <td colspan="<?php echo e(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '15' : '14'); ?>" class="empty-state">
                        <i class="bi bi-funnel"></i>
                        <h5>No Records Found</h5>
                        <p>Try adjusting your filter criteria.</p>
                      </td>
                    </tr>`;
          } else {
            data.forEach(function (record, index) {
              let remark = '-';
              if (record.last_remark) {
                const fullRemark = record.last_remark || '';
                const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
              }

              let assignToColumn = '';
              <?php if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1)): ?>
                let dropdownOptions = '<option value="">Select Member</option>';
                if (window.teamMembers && window.teamMembers.length > 0) {
                  window.teamMembers.forEach(function (member) {
                    dropdownOptions += `<option value="${member.id}">${member.name}</option>`;
                  });
                }
                assignToColumn = `
                                <td>
                                    <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                        ${dropdownOptions}
                                    </select>
                                </td>
                            `;
              <?php endif; ?>

              html += `
                            <tr>
                                <td><span class="status-badge">${record.status_name ?? 'N/A'}</span></td>
                                <td>${record.prospectus_name ?? 'N/A'}</td>
                                <td>${remark}</td>
                                <td>${record.leads_name ?? ''}</td>
                                <td>${record.contact_person ?? ''}</td>
                                <td>${record.contact_number ?? ''}</td>
                                <td>${record.next_follow_up_date ?? 'N/A'}</td>
                                <td>${record.state_name ?? 'N/A'}</td>
                                <td>${record.city_name ?? 'N/A'}</td>
                                <td>${record.email ?? ''}</td>
                                <td>${record.business_name ?? 'N/A'}</td>
                                <td>${record.source_name ?? 'N/A'}</td>
                                <td>${record.product_name ?? 'N/A'}</td>
                                <td>${record.ticket_value ?? '0'}</td>
                                ${assignToColumn}
                            </tr>
                        `;
            });
          }

          $('#alldatatable tbody').html(html);

          let links = '';
          response.links.forEach(link => {
            if (link.url !== null) {
              links += `<li class="page-item ${link.active ? 'active' : ''}">
                            <a href="#" class="page-link" data-page="${link.url.split('page=')[1]}">${link.label}</a>
                        </li>`;
            } else {
              links += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            }
          });

          $('#paginationfilterLinks').html(links);
          updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
          console.error("Error:", xhr.responseText);
        }
      });
    }

    $(document).on('click', '#paginationfilterLinks .page-link', function (e) {
      e.preventDefault();
      let page = $(this).data('page');
      if (page) {
        $('#paginationLinks').hide();
        loadFilteredTable(page);
      }
    });

    $(document).on('change', '#sales_status, #city, #state, #business_type, #lead_source, #product_type, #user_id', function () {
      $('#paginationLinks').hide();
      loadFilteredTable(1);
    });

    // date filter
    function loadDateFilteredTable(from_date = '', to_date = '', page = 1) {
      $('#alldatatable tbody').html(`
          <tr>
            <td colspan="<?php echo e(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '15' : '14'); ?>" class="loading-state">
              <i class="bi bi-arrow-repeat"></i>
              <p class="mt-2 mb-0">Filtering by date...</p>
            </td>
          </tr>
        `);

      $.ajax({
        url: '<?php echo e(route("alldatafilterdate")); ?>?page=' + page,
        type: 'POST',
        data: {
          _token: '<?php echo e(csrf_token()); ?>',
          from_date: from_date,
          to_date: to_date
        },
        success: function (response) {
          let data = response.data;
          let html = '';

          if (data.length === 0) {
            html = `<tr>
                      <td colspan="<?php echo e(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '15' : '14'); ?>" class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <h5>No Records Found</h5>
                        <p>No records found for the selected date range.</p>
                      </td>
                    </tr>`;
          } else {
            data.forEach(function (record, index) {
              let remark = '-';
              if (record.last_remark) {
                const fullRemark = record.last_remark || '';
                const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
              }

              let assignToColumn = '';
              <?php if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1)): ?>
                let dropdownOptions = '<option value="">Select Member</option>';
                if (window.teamMembers && window.teamMembers.length > 0) {
                  window.teamMembers.forEach(function (member) {
                    dropdownOptions += `<option value="${member.id}">${member.name}</option>`;
                  });
                }
                assignToColumn = `
                                <td>
                                    <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                        ${dropdownOptions}
                                    </select>
                                </td>
                            `;
              <?php endif; ?>

              html += `
                            <tr>
                                <td><span class="status-badge">${record.status_name ?? 'N/A'}</span></td>
                                <td>${record.prospectus_name ?? 'N/A'}</td>
                                <td>${remark}</td>
                                <td>${record.leads_name ?? ''}</td>
                                <td>${record.contact_person ?? ''}</td>
                                <td>${record.contact_number ?? ''}</td>
                                <td>${record.next_follow_up_date ?? 'N/A'}</td>
                                <td>${record.state_name ?? 'N/A'}</td>
                                <td>${record.city_name ?? 'N/A'}</td>
                                <td>${record.email ?? ''}</td>
                                <td>${record.business_name ?? 'N/A'}</td>
                                <td>${record.source_name ?? 'N/A'}</td>
                                <td>${record.product_name ?? 'N/A'}</td>
                                <td>${record.ticket_value ?? '0'}</td>
                                ${assignToColumn}
                            </tr>
                        `;
            });
          }

          $('#alldatatable tbody').html(html);

          let links = '';
          response.links.forEach(link => {
            if (link.url !== null) {
              links += `<li class="page-item ${link.active ? 'active' : ''}">
                            <a href="#" class="page-link" data-page="${link.url.split('page=')[1]}">${link.label}</a>
                        </li>`;
            } else {
              links += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            }
          });
          $('#paginationdateLinks').html(links);
          updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
          console.error("Error:", xhr.responseText);
        }
      });
    }

    $(document).on('change', '#from_date, #to_date', function () {
      $('#paginationLinks').hide();
      $('#paginationfilterLinks').hide();
      $('#paginationsearchLinks').hide();
      let from_date = $('#from_date').val();
      let to_date = $('#to_date').val();
      loadDateFilteredTable(from_date, to_date, 1);
    });

    $(document).on('click', '#paginationdateLinks .page-link', function (e) {
      e.preventDefault();
      let page = $(this).data('page');
      let from_date = $('#from_date').val();
      let to_date = $('#to_date').val();
      if (page) {
        $('#paginationLinks').hide();
        $('#paginationfilterLinks').hide();
        $('#paginationsearchLinks').hide();
        loadDateFilteredTable(from_date, to_date, page);
      }
    });

    // Load team members for reassignment dropdowns
    function loadTeamMembers() {
      var teamMembersUrl = '<?php echo e(route("alldata.team-members")); ?>';

      return $.ajax({
        url: teamMembersUrl,
        type: 'GET',
        headers: {
          'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        success: function (response) {
          window.teamMembers = response;
          if (window.teamMembers && window.teamMembers.length > 0) {
            loadSalesRecords();
          }
        },
        error: function (xhr) {
          console.error('Error loading team members:', xhr.responseText);
        }
      });
    }

    function reassignLead(leadId, newUserId) {
      if (!newUserId) return;

      $.ajax({
        url: '<?php echo e(route("alldata.reassign")); ?>',
        type: 'POST',
        data: {
          lead_id: leadId,
          new_user_id: newUserId,
          _token: '<?php echo e(csrf_token()); ?>'
        },
        success: function (response) {
          if (response.success) {
            showAlert('success', 'Lead reassigned successfully!');
            loadSalesRecords();
          } else {
            showAlert('error', 'Error: ' + response.message);
          }
        },
        error: function (xhr, status, error) {
          console.error('Error reassigning lead:', xhr.responseText);
          showAlert('error', 'Error reassigning lead. Please try again.');
        }
      });
    }

    $(document).ready(function () {
      <?php if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1)): ?>
        loadTeamMembers();
        setupBulkAssignment();
      <?php else: ?>
        loadSalesRecords();
      <?php endif; ?>
      });

    function setupBulkAssignment() {
      if (window.teamMembers && window.teamMembers.length > 0) {
        let bulkDropdown = $('#bulkAssignUser');
        bulkDropdown.empty().append('<option value="">Select User to Assign</option>');
        window.teamMembers.forEach(function (member) {
          bulkDropdown.append(`<option value="${member.id}">${member.name}</option>`);
        });
      }

      $('#selectAll').on('change', function () {
        let isChecked = $(this).is(':checked');
        $('.lead-checkbox').prop('checked', isChecked);
        updateBulkAssignmentControls();
      });

      $(document).on('change', '.lead-checkbox', function () {
        updateBulkAssignmentControls();
        updateSelectAllCheckbox();
      });

      $('#bulkAssignBtn').on('click', function () {
        let selectedUser = $('#bulkAssignUser').val();
        if (!selectedUser) {
          showAlert('error', 'Please select a user to assign leads to.');
          return;
        }

        let selectedLeads = $('.lead-checkbox:checked').map(function () {
          return $(this).val();
        }).get();

        if (selectedLeads.length === 0) {
          showAlert('error', 'Please select at least one lead to assign.');
          return;
        }

        if (confirm(`Are you sure you want to assign ${selectedLeads.length} leads to the selected user?`)) {
          bulkAssignLeads(selectedLeads, selectedUser);
        }
      });

      $('#clearSelectionBtn').on('click', function () {
        $('.lead-checkbox').prop('checked', false);
        $('#selectAll').prop('checked', false);
        updateBulkAssignmentControls();
      });
    }

    function updateBulkAssignmentControls() {
      let checkedCount = $('.lead-checkbox:checked').length;
      let bulkControls = $('#bulkAssignmentControls');
      let selectedCount = $('#selectedCount');
      let bulkAssignBtn = $('#bulkAssignBtn');

      if (checkedCount > 0) {
        bulkControls.show();
        selectedCount.text(checkedCount);
        bulkAssignBtn.prop('disabled', false);
      } else {
        bulkControls.hide();
        selectedCount.text('0');
        bulkAssignBtn.prop('disabled', true);
      }
    }

    function updateSelectAllCheckbox() {
      let totalCheckboxes = $('.lead-checkbox').length;
      let checkedCheckboxes = $('.lead-checkbox:checked').length;

      if (checkedCheckboxes === 0) {
        $('#selectAll').prop('indeterminate', false).prop('checked', false);
      } else if (checkedCheckboxes === totalCheckboxes) {
        $('#selectAll').prop('indeterminate', false).prop('checked', true);
      } else {
        $('#selectAll').prop('indeterminate', true);
      }
    }

    function bulkAssignLeads(leadIds, userId) {
      let promises = leadIds.map(function (leadId) {
        return new Promise(function (resolve, reject) {
          $.ajax({
            url: '<?php echo e(route("alldata.reassign")); ?>',
            type: 'POST',
            data: {
              lead_id: leadId,
              new_user_id: userId,
              _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function (response) {
              resolve(response);
            },
            error: function (xhr) {
              reject(xhr);
            }
          });
        });
      });

      Promise.all(promises)
        .then(function (results) {
          let successCount = results.filter(r => r.success).length;
          let failCount = results.length - successCount;

          if (failCount === 0) {
            showAlert('success', `Successfully assigned ${successCount} leads!`);
          } else {
            showAlert('error', `Assigned ${successCount} leads successfully. ${failCount} leads failed to assign.`);
          }

          $('.lead-checkbox').prop('checked', false);
          $('#selectAll').prop('checked', false);
          updateBulkAssignmentControls();

          if (window.currentView === 'search') {
            searchSalesTable(1);
          } else if (window.currentView === 'filter') {
            loadFilteredTable(1);
          } else if (window.currentView === 'date') {
            loadDateFilteredTable($('#from_date').val(), $('#to_date').val(), 1);
          } else {
            loadSalesRecords(1);
          }
        })
        .catch(function (error) {
          console.error('Bulk assignment error:', error);
          showAlert('error', 'An error occurred during bulk assignment. Please try again.');
        });
    }

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

  <script>
    // --- Add Lead Modal Logic ---
    $(document).ready(function () {
      // Initial fetch for Add Lead modal dropdowns
      fetchAddLeadOptions();
    });

    function fetchAddLeadOptions() {
      // Prospectus
      $.get("<?php echo e(route('getProspectus')); ?>", function (data) {
        let opts = '<option value="">Select Prospectus</option>';
        $.each(data, function (key, val) { opts += `<option value="${val.id}">${val.prospectus_name}</option>`; });
        $('#add_lead_prospectus').html(opts);
      });

      // States (Add Lead)
      $.get("<?php echo e(route('state')); ?>", function (data) {
        let opts = '<option value="">Select State</option>';
        $.each(data, function (id, name) { opts += `<option value="${id}">${name}</option>`; });
        $('#add_lead_state').html(opts);
      });

      // States (Prospect Modal)
      $.get("<?php echo e(route('state')); ?>", function (data) {
        let opts = '<option value="">Select State</option>';
        $.each(data, function (id, name) { opts += `<option value="${id}">${name}</option>`; });
        $('#modal_state').html(opts);
      });

      // Status
      $.get("<?php echo e(route('getStatuses')); ?>", function (data) {
        let opts = '<option value="">Select Status</option>';
        $.each(data, function (key, val) { opts += `<option value="${val.id}">${val.status_name}</option>`; });
        $('#add_lead_sales_status').html(opts);
      });

      // Business Type (Add Lead)
      $.get("<?php echo e(route('getbusiness')); ?>", function (data) {
        let opts = '<option value="">Select Business Type</option>';
        $.each(data, function (idx, val) { opts += `<option value="${val.id}">${val.business_name}</option>`; });
        $('#add_lead_business_type').html(opts);
      });

      // Business Type (Prospect Modal)
      $.get("<?php echo e(route('getbusiness')); ?>", function (data) {
        let opts = '<option value="">Select Business Type</option>';
        $.each(data, function (idx, val) { opts += `<option value="${val.id}">${val.business_name}</option>`; });
        $('#modal_business_type').html(opts);
      });

      // Lead Source
      $.get("<?php echo e(route('getsource')); ?>", function (data) {
        let opts = '<option value="">Select Source</option>';
        $.each(data, function (idx, val) { opts += `<option value="${val.id}">${val.source_name}</option>`; });
        $('#add_lead_lead_source').html(opts);
      });

      // Product Type
      $.get("<?php echo e(route('getproduct')); ?>", function (data) {
        let opts = '<option value="">Select Product Type</option>';
        $.each(data, function (idx, val) { opts += `<option value="${val.id}">${val.product_name}</option>`; });
        $('#add_lead_product_type').html(opts);
      });
    }

    // Add Lead State Change -> Cities
    $(document).on('change', '#add_lead_state', function () {
      let stateId = $(this).val();
      if (stateId) {
        $.get("/city/" + stateId, function (data) {
          let opts = '<option value="">Select City</option>';
          $.each(data, function (id, name) { opts += `<option value="${id}">${name}</option>`; });
          $('#add_lead_city').html(opts);
        });
      } else {
        $('#add_lead_city').html('<option value="">Select City</option>');
      }
    });

    // Prospect Modal State Change -> Cities
    $(document).on('change', '#modal_state', function () {
      let stateId = $(this).val();
      if (stateId) {
        $.get("/city/" + stateId, function (data) {
          let opts = '<option value="">Select City</option>';
          $.each(data, function (id, name) { opts += `<option value="${id}">${name}</option>`; });
          $('#modal_city').html(opts);
        });
      } else {
        $('#modal_city').html('<option value="">Select City</option>');
      }
    });

    // Prospectus Change -> Fill Data
    $(document).on('change', '#add_lead_prospectus', function () {
      let id = $(this).val();
      if (id) {
        $.get('/fillprospectus/' + id, function (data) {
          $('#add_lead_contactPerson').val(data.contact_person);
          $('#add_lead_contactNumber').val(data.contact_number);
          $('#add_lead_address').val(data.address);
          $('#add_lead_leadsName').val(data.prospectus_name);
          $('#add_lead_email').val(data.email);
          $('#add_lead_website_link').val(data.website_link);
          $('#add_lead_business_type').val(data.business_type_id);
          $('#add_lead_state').val(data.state_id);

          if (data.state_id) {
            $.get("/city/" + data.state_id, function (cities) {
              let opts = '<option value="">Select City</option>';
              $.each(cities, function (id, name) { opts += `<option value="${id}">${name}</option>`; });
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
        .done(function (response) {
          showAlert('success', 'Sales record submitted successfully!');
          $('#addLeadForm')[0].reset();
          $('#addLeadModal').modal('hide');
          loadSalesRecords();
          loadSummaryStats();
          loadStatusCounts();
        })
        .fail(function (xhr) {
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
        .done(function (response) {
          $('#addProspectusModal').modal('hide');
          $('#addProspectusForm')[0].reset();
          showAlert('success', 'Prospect added successfully.');
          // Refresh Prospectus Dropdown in Add Lead Modal
          $.get("<?php echo e(route('getProspectus')); ?>", function (data) {
            let opts = '<option value="">Select Prospectus</option>';
            $.each(data, function (key, val) { opts += `<option value="${val.id}">${val.prospectus_name}</option>`; });
            $('#add_lead_prospectus').html(opts);
          });
        })
        .fail(function (xhr) {
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

  </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Don't Delete\laravel\leadmanagement (akrati ui work)\resources\views/alldata.blade.php ENDPATH**/ ?>