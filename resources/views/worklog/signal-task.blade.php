@extends('layouts.app')
@section('title', 'Signal Tasks')
@section('page_title', 'Signal Tasks')

@push('styles')
<style>
  .data-table-card .custom-table thead th {  
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }
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

  .row-overdue,
  .row-overdue td {
      background: #f72323ff !important;
      box-shadow: inset 0 0 0 9999px #f02525ff !important;
      color: #fff !important;
  }
  
  .row-overdue td a {
      color: #fff !important;
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

  .summary-card::before,
  .status-card::before {
    display: none;
  }

  .summary-card:hover,
  .status-card:hover {
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
    width: 100%;
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

  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
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

  .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    background: transparent;
    font-size: 0.85rem;
    table-layout: auto;
    min-width: 100%;
  }

  .data-table-card .custom-table thead th {
    background: #fff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    font-weight: 700;
    padding: 0.4rem 0.5rem;
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
    padding: 0.25rem 0.5rem;
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

  /* Pagination */
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

  .dataTables_wrapper .dataTables_info {
      font-size: 0.75rem;
      color: #6b7280;
  }

  .btn-action-edit {
    color: white;
    background: #434AFA !important;
    border-radius: 4px;
    padding: 0.25rem 0.5rem;
    border: none;
    transition: all 0.2s ease;
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

  .action-btn {
    padding: 0.15rem 0.35rem;
    font-size: 0.7rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-right: 0.25rem;
    line-height: 1;
  }

  .action-btn.btn-primary {
    background: #434afa;
    color: white;
  }

  .action-btn.btn-primary:hover {
    background: #3538d4;
    transform: translateY(-1px);
  }

  .action-btn.btn-danger {
    background: #ef4444;
    color: white;
  }

  .action-btn.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
  }

  /* Poke button polish */
  .btn-poke { 
    background: linear-gradient(135deg,#ffc107,#ff9f1a);
    border: none;
    color: #212529;
    padding: 0.15rem 0.35rem;
    font-size: 0.7rem;
    border-radius: 4px;
    margin-right: 0.25rem;
    line-height: 1;
  }
  .btn-poke:hover { filter: brightness(0.95); }
  .poke-sent-badge {
    display:inline-block; margin-left:6px; padding:1px 6px; border-radius:10px; font-size:10px;
    background:#e7f1ff; color:#0d6efd; border:1px solid #cfe2ff;
  }

  .assign-users-grid {
    max-height: 180px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px;
    background: #fff;
  }
  .assign-users-grid .form-check {
    margin-bottom: 6px;
  }
  .assign-users-grid .form-check-input {
    width: 1rem;
    height: 1rem;
  }

  /* Compact modal forms */
  .form-compact .form-label { font-size: 0.85rem; margin-bottom: 0.2rem; }
  .form-compact .form-control,
  .form-compact .form-select { padding: 0.35rem 0.5rem; font-size: 0.875rem; }
  .form-compact .form-check-label { font-size: 0.875rem; }
  .form-compact .section-title { font-size: 0.8rem; color:#6c757d; margin: 0.2rem 0 0.4rem; font-weight: 600; }
  .form-compact .help-text { font-size: 0.75rem; color:#6c757d; }
  .modal-body.form-compact { padding-top: 0.75rem; }

  /* Slim colorful form accents */
  .form-accent {
    margin-bottom: 10px;
  }
  .chip-toggle {
    display:inline-flex; align-items:center; gap:8px; padding:4px 10px; 
     color:#1d4ed8; font-weight:600; font-size:12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
  }
  .chip-toggle .form-check-input { margin-left:8px; width:36px; height:18px; }
  .chip-row { display:flex; align-items:center; justify-content:space-between; gap:8px; }
  .chip-row .title { font-weight:700; letter-spacing:.2px; color: #0f172a; font-size:0.9rem; }

    .chip-title{
        border-bottom: none !important;
        font-weight: 700;
    }
  @media (max-width: 767px){
    .form-select-customer {
      width: 100% !important;
    }

    .filterBox {
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      padding: 1rem;
    }

    .filterBox .mb-2 {
      margin-bottom: 0 !important;
    }

    .container-fluid{
      padding-left: 0.5rem;
      padding-right: 0.5rem;
      margin-left: 0;
    }

    .form-compact .form-label { font-size: 12px; margin-bottom: 0.2rem; }
    .form-compact .form-select { padding: 0.35rem 0.5rem; font-size: 0.875rem; width: 100%; }
    
    .modal-header{
      flex-direction: row !important;
      flex-wrap: wrap;
      align-items: center !important;
      justify-content: space-between !important;
      gap: 10px;
      padding: 1rem;
    }

    .modal-subheader, .subHeader {
      width: auto !important;
      margin-top: 0;
      display: flex !important;
      align-items: center !important;
      gap: 10px;
    }

    .modal-footer-custom {
      display: flex;
      justify-content: center;
      width: 100%;
    }

    .modal-footer-custom .btn {
      width: 100% !important;
      margin: 0 !important;
    }

    .task-type-wrapper {
      width: 100%;
      display: flex;
    }

    .task-type-option {
      flex: 1;
      justify-content: center;
      padding: 6px 4px;
      font-size: 12px;
      display: flex;
      align-items: center;
    }

    .summary-cards {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 0.5rem;
    }

    .data-table-card .custom-table tbody td {
      font-size: 0.75rem
    }
    
    .table-search {
      flex-direction: row;
      gap: 0.5rem;
    }
    
    .table-search-field {
        width: 100%;
    }
    
    .table-search-btn {
      width: auto;
    }

  }

  .form-control{
        background: #DFDFDF;
        font-size: 14px;
        border-radius: 3px;
    }

    label{
        font-weight: 700;
    }

    .file-upload-box {
  border: 3px dashed #434AFA;
  border-radius: 12px;
  background-color: #f3f4f6;
  cursor: pointer;
  transition: 0.3s;
}

.file-upload-box:hover {
  border: 3px solid blue;
}

.upload-icon {
  font-size: 42px;
  color: #434AFA;
}

.task-type-wrapper {
  display: inline-flex;
  align-items: center;
  border: 1px solid #434AFA;
  border-radius: 3px;
  overflow: hidden;
  font-size: 14px;
}

.task-type-title {
  background: #434AFA;
  color: #fff;
  padding: 6px 14px;
  font-weight: 500;
  white-space: nowrap;
}

.task-type-option {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  cursor: pointer;
  border-left: 1px solid #434AFA;
  background: #fff;
  color: #000;
  white-space: nowrap;
}

/* hide default radio */
.task-type-option input {
  accent-color: #4c6fff;
  cursor: pointer;
}

/* active (selected) state */
.task-type-option:has(input:checked) {
  background: #eef2ff;
  font-weight: 500;
}

.subHeader{
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    width: auto !important;
}

.modal-header {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
}

.modal-title {
    margin: 0 !important;
}

.file-upload-box{
    padding: 3px !important;
}
.btn-close{
    /* margin-top: 2px !important; */
}

.select{
    background-color: #434AFA;
}


  .chip-row .title { font-weight:700; letter-spacing:.2px; color: #0f172a; font-size:0.9rem; }

    .chip-title{
        border-bottom: none !important;
        font-weight: 700;
    }
  @media (max-width: 767px){
    .form-select-customer {
      width: 100% !important;
    }

    .filterBox {
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      padding: 1rem;
    }

    .filterBox .mb-2 {
      margin-bottom: 0 !important;
    }

    .container-fluid{
      padding-left: 0.5rem;
      padding-right: 0.5rem;
      margin-left: 0;
    }

    .form-compact .form-label { font-size: 12px; margin-bottom: 0.2rem; }
    .form-compact .form-select { padding: 0.35rem 0.5rem; font-size: 0.875rem; width: 100%; }
    
    .modal-header{
      flex-direction: row !important;
      flex-wrap: wrap;
      align-items: center !important;
      justify-content: space-between !important;
      gap: 10px;
      padding: 1rem;
    }

    .modal-subheader, .subHeader {
      width: auto !important;
      margin-top: 0;
      display: flex !important;
      align-items: center !important;
      gap: 10px;
    }

    .modal-footer-custom {
      display: flex;
      justify-content: center;
      width: 100%;
    }

    .modal-footer-custom .btn {
      width: 100% !important;
      margin: 0 !important;
    }

    .task-type-wrapper {
      width: 100%;
      display: flex;
    }

    .task-type-option {
      flex: 1;
      justify-content: center;
      padding: 6px 4px;
      font-size: 12px;
      display: flex;
      align-items: center;
    }

    .summary-cards {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 0.5rem;
    }

    .data-table-card .custom-table tbody td {
      font-size: 0.75rem
    }
    
    .table-search {
      flex-direction: row;
      gap: 0.5rem;
    }
    
    .table-search-field {
        width: 100%;
    }
    
    .table-search-btn {
      width: auto;
    }

  }

  .form-control{
        background: #DFDFDF;
        font-size: 14px;
        border-radius: 3px;
    }

    label{
        font-weight: 700;
    }

    .file-upload-box {
  border: 3px dashed #434AFA;
  border-radius: 12px;
  background-color: #f3f4f6;
  cursor: pointer;
  transition: 0.3s;
}

.file-upload-box:hover {
  border: 3px solid blue;
}

.upload-icon {
  font-size: 42px;
  color: #434AFA;
}

.task-type-wrapper {
  display: inline-flex;
  align-items: center;
  border: 1px solid #434AFA;
  border-radius: 3px;
  overflow: hidden;
  font-size: 14px;
}

.task-type-title {
  background: #434AFA;
  color: #fff;
  padding: 6px 14px;
  font-weight: 500;
  white-space: nowrap;
}

.task-type-option {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  cursor: pointer;
  border-left: 1px solid #434AFA;
  background: #fff;
  color: #000;
  white-space: nowrap;
}

/* hide default radio */
.task-type-option input {
  accent-color: #4c6fff;
  cursor: pointer;
}

/* active (selected) state */
.task-type-option:has(input:checked) {
  background: #eef2ff;
  font-weight: 500;
}

.subHeader{
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    width: auto !important;
}

.modal-header {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
}

.modal-title {
    margin: 0 !important;
}

.file-upload-box{
    padding: 3px !important;
}
.btn-close{
    /* margin-top: 2px !important; */
}

.select{
    background-color: #434AFA;
}

.chat-container {
    display: flex;
    height: 700px;
    background: #f4f5f7;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.chat-sidebar {
    width: 300px;
    background: #fff;
    border-right: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
}

.chat-sidebar-header {
    padding: 15px;
    border-bottom: 1px solid #e5e7eb;
    background: #fff;
}

.chat-list {
    flex: 1;
    overflow-y: auto;
}

.chat-list-item {
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: background 0.2s;
}

.chat-list-item:hover {
    background: #f8f9fa;
}

.chat-list-item.active {
    background: #e7f1ff;
    border-left: 3px solid #434AFA;
}

.chat-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #d1d5db;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    color: #fff;
    font-weight: bold;
    flex-shrink: 0;
}

.chat-info {
    flex: 1;
    min-width: 0;
}

.chat-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
}

.chat-meta {
    font-size: 0.75rem;
    color: #6b7280;
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #efeae2;
}

.chat-main-header {
    padding: 15px;
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.message-bubble {
    background: #fff;
    border-radius: 8px;
    padding: 10px 12px;
    max-width: 80%;
    align-self: flex-start;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    position: relative;
    display: flex;
    flex-direction: column;
}

.message-sender {
    font-size: 0.75rem;
    font-weight: 600;
    color: #029688;
    margin-bottom: 4px;
}

.message-text {
    font-size: 0.9rem;
    color: #111827;
    margin-bottom: 15px;
    word-break: break-word;
    white-space: pre-wrap;
}

.message-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 4px;
}

.message-time {
    font-size: 0.65rem;
    color: #6b7280;
}

.btn-mark-task {
    background: transparent;
    border: 1px solid #e5e7eb;
    color: #4b5563;
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: background 0.2s;
}

.btn-mark-task:hover {
    background: #f3f4f6;
}

.btn-mark-task i.text-danger {
    color: #ef4444 !important;
}

.ai-tasks-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
    padding: 15px 0;
}

.ai-task-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
}

.ai-task-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 8px;
}

.ai-task-desc {
    font-size: 0.85rem;
    color: #4b5563;
    margin-bottom: 15px;
    flex: 1;
    white-space: pre-wrap;
}

.ai-task-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f3f4f6;
    padding-top: 10px;
}

@media (max-width: 768px) {
    .chat-container {
        flex-direction: column;
        height: auto;
    }
    .chat-sidebar {
        width: 100%;
        max-height: 250px;
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
    }
    .chat-main {
        height: 500px;
    }
}

/* Custom styles for active tabs and toggle buttons */
.nav-tabs .nav-link.active {
    background-color: #434AFA !important;
    color: #fff !important;
    border-color: #434AFA !important;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #434AFA !important;
    color: #fff !important;
    border-color: #434AFA !important;
}
</style>


@endpush

@section('content')
  <div class="container-fluid px-2">
    <ul class="nav nav-tabs mb-3 mt-3" id="signalTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages-pane" type="button" role="tab" aria-controls="messages-pane" aria-selected="true" style="color: #434afa; font-weight: bold;">Messages</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="ai-tasks-tab" data-bs-toggle="tab" data-bs-target="#ai-tasks-pane" type="button" role="tab" aria-controls="ai-tasks-pane" aria-selected="false" style="color: #434afa; font-weight: bold;">AI Tasks</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="immediate-tasks-tab" data-bs-toggle="tab" data-bs-target="#immediate-tasks-pane" type="button" role="tab" aria-controls="immediate-tasks-pane" aria-selected="false" style="color: #434afa; font-weight: bold;">Immediate Tasks</button>
      </li>
    </ul>

    <div class="tab-content" id="signalTabsContent">
      <div class="tab-pane fade show active" id="messages-pane" role="tabpanel" aria-labelledby="messages-tab" tabindex="0">
          <div class="chat-container">
            <div class="chat-sidebar">
                <div class="chat-sidebar-header">
                    <h6 class="mb-0">All numbers <br><small class="text-muted" style="font-size:10px;" id="connectedNumbersCount">0 number(s) connected</small></h6>
                </div>
                <div class="chat-list" id="chatList">
                    <div class="text-center p-3"><i class="bi bi-arrow-repeat spin"></i> Loading chats...</div>
                </div>
            </div>
            <div class="chat-main">
                <div class="chat-main-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0" id="chatMainHeader">All messages</h6>
                    <div>
                        <a href="{{ route('ai-task-log.index') }}" class="btn btn-sm btn-outline-secondary shadow-sm me-2" style="border-radius: 4px; font-weight: 600;">
                            <i class="bi bi-journal-text me-1"></i> Logs
                        </a>
                        <button id="processAiBtn" class="btn btn-sm text-white shadow-sm" style="background: #434AFA; border-radius: 4px; font-weight: 600;">
                            <i class="fas fa-robot me-1"></i> Process AI Tasks
                        </button>
                    </div>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <div class="text-center p-3"><i class="bi bi-arrow-repeat spin"></i> Loading messages...</div>
                </div>
            </div>
          </div>
      </div>
      <div class="tab-pane fade" id="ai-tasks-pane" role="tabpanel" aria-labelledby="ai-tasks-tab" tabindex="0">
          <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="btn-group" role="group">
                  <input type="radio" class="btn-check" name="aiTaskView" id="viewTableBtn" value="table" autocomplete="off" checked>
                  <label class="btn btn-outline-primary btn-sm" for="viewTableBtn" style="border-color:#434AFA; color:#434AFA;"><i class="bi bi-table"></i> Table</label>

                  <input type="radio" class="btn-check" name="aiTaskView" id="viewCardBtn" value="card" autocomplete="off">
                  <label class="btn btn-outline-primary btn-sm" for="viewCardBtn" style="border-color:#434AFA; color:#434AFA;"><i class="bi bi-grid"></i> Card</label>
              </div>
              <div>
                  <select id="statusFilter" class="form-select form-select-sm" style="border-radius: 6px; border-color: #e0e0e0; min-width: 140px;">
                      <option value="pending" selected>Pending</option>
                      <option value="converted">Converted</option>
                      <option value="all">All</option>
                  </select>
              </div>
          </div>
          
          <div id="aiTaskTableView" class="data-table-card">
              <div class="table-responsive">
                  <table class="table custom-table" id="aiTaskTable">
                      <thead>
                          <tr>
                              <th>Title</th>
                              <th>Description</th>
                              <th>Chat</th>
                              <th>Assigned To</th>
                              <th>Requested By</th>
                              <th>Created At</th>
                              <th>Status</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody id="aiTaskTableBody">
                          <tr>
                              <td colspan="8" class="text-center"><i class="bi bi-arrow-repeat spin"></i> Loading AI tasks...</td>
                          </tr>
                      </tbody>
                  </table>
              </div>
          </div>

          <div id="aiTaskCardView" class="ai-tasks-container" style="display: none;">
              <div class="text-center p-4 text-muted w-100"><i class="bi bi-arrow-repeat spin"></i> Loading AI tasks...</div>
          </div>
      </div>
      
      <div class="tab-pane fade" id="immediate-tasks-pane" role="tabpanel" aria-labelledby="immediate-tasks-tab" tabindex="0">
          <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
              <h5 class="mb-0" style="color:#434AFA; font-weight:bold; font-size:1.1rem;"></h5>
              <div>
                  <select id="immediateStatusFilter" class="form-select form-select-sm" style="border-radius: 6px; border-color: #e0e0e0; min-width: 140px; display:inline-block;">
                      <option value="pending" selected>Pending</option>
                      <option value="done">Done</option>
                      <option value="all">All</option>
                  </select>
              </div>
          </div>
          <div class="data-table-card">
              <div class="table-responsive">
                  <table class="table custom-table" id="immediateTaskTable">
                      <thead>
                          <tr>
                              <th>Title</th>
                              <th>Description</th>
                              <th>Status</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody id="immediateTaskTableBody">
                          <tr>
                              <td colspan="4" class="text-center"><i class="bi bi-arrow-repeat spin"></i> Loading immediate tasks...</td>
                          </tr>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
    </div>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"> 
                <h5 class="modal-title" id="createTaskModalLabel">Create New Task</h5>
                <div class="subHeader">
                    <div class="task-type-wrapper">
                        <span class="task-type-title">Select opt...</span>

                        <label class="task-type-option">
                        <input type="radio" name="task_type" value="task" checked>
                        Task
                        </label>

                        <label class="task-type-option">
                        <input type="radio" name="task_type" value="qc">
                        Qc
                        </label>

                        <label class="task-type-option">
                        <input type="radio" name="task_type" value="cp">
                        CP
                        </label>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form id="taskForm">
                <div class="modal-body form-compact">
                    @csrf
                    
                    <!-- Recurring (Top, colorful) -->
                    <div class="form-accent mb-2" id="recurrenceSection">
                        <div class="chip-row">
                            <div class="chip-title">Recurring</div>
                            <label class="chip-toggle">
                                Enable
                                <input class="form-check-input" type="checkbox" id="is_recurring">
                            </label>
                        </div>
                        <div id="recurrencePanel" class="mt-2" style="display:none;">
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <label class="form-label">Repeat</label>
                                    <select id="recurrence_type" class="form-select form-select-sm">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">Every</label>
                                    <input type="number" min="1" value="1" id="recurrence_interval" class="form-control form-control-sm" placeholder="Interval">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">End date</label>
                                    <input type="date" id="recurrence_end_date" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div id="recurrence_weekly" class="mt-2" style="display:none;">
                                <label class="form-label">On days</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="mon" id="dow_mon"><label class="form-check-label" for="dow_mon">Mon</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="tue" id="dow_tue"><label class="form-check-label" for="dow_tue">Tue</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="wed" id="dow_wed"><label class="form-check-label" for="dow_wed">Wed</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="thu" id="dow_thu"><label class="form-check-label" for="dow_thu">Thu</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="fri" id="dow_fri"><label class="form-check-label" for="dow_fri">Fri</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="sat" id="dow_sat"><label class="form-check-label" for="dow_sat">Sat</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="sun" id="dow_sun"><label class="form-check-label" for="dow_sun">Sun</label></div>
                                </div>
                            </div>
                            <div id="recurrence_monthly" class="mt-2" style="display:none;">
                                <label class="form-label">On day of month</label>
                                <input type="number" id="recurrence_day_of_month" class="form-control form-control-sm" min="1" max="31" placeholder="1-31">
                            </div>
                            <div id="recurrence_yearly" class="mt-2" style="display:none;">
                                <label class="form-label">In months</label>
                                <div class="row g-1">
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="m_1"><label class="form-check-label" for="m_1">Jan</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="2" id="m_2"><label class="form-check-label" for="m_2">Feb</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="3" id="m_3"><label class="form-check-label" for="m_3">Mar</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="4" id="m_4"><label class="form-check-label" for="m_4">Apr</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="5" id="m_5"><label class="form-check-label" for="m_5">May</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="6" id="m_6"><label class="form-check-label" for="m_6">Jun</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="7" id="m_7"><label class="form-check-label" for="m_7">Jul</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="8" id="m_8"><label class="form-check-label" for="m_8">Aug</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="9" id="m_9"><label class="form-check-label" for="m_9">Sep</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="10" id="m_10"><label class="form-check-label" for="m_10">Oct</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="11" id="m_11"><label class="form-check-label" for="m_11">Nov</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="12" id="m_12"><label class="form-check-label" for="m_12">Dec</label></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Customer Select -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label" id="label_customer">Clients</label>
                                <select name="customer_id" id="customer_id" class="form-select form-select-sm form-select-customer" required>
                                    <option value="" class="select">Select Customer</option>
                                </select>
                            </div>
                            <!-- Customer Project Select -->
                             <div class="mb-3">
                                <label for="customer_project_id" class="form-label">Project (Optional)</label>
                                <select name="customer_project_id" id="customer_project_id" class="form-select form-select-sm">
                                    <option value="">Select Project</option>
                                </select>
                            </div>
                        </div>

                        <!-- User Select -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" id="label_user">Assign To</label>
                                <div id="assignUsersContainer" class="assign-users-grid" data-input-name="user_ids[]"></div>
                                <small class="text-muted">Select one or more users to assign this task/QC.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Task Name -->
                    <div class="row">
                        <div class="mb-3 col-md-5">
                            <label for="task_name" class="form-label" id="label_task_name">Task Name</label>
                            <input type="text" name="task_name" id="task_name" class="form-control form-control-sm" required placeholder="Enter task name...">
                        </div>

                        <div class="mb-3 col-md-3">
                            <label for="estimated_efforts" class="form-label" id="label_estimated_efforts">Estimated Efforts</label>
                            <input type="text" name="estimated_efforts" id="estimated_efforts" class="form-control form-control-sm" placeholder="e.g. 2h, 3 days...">
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="due_date" class="form-label" id="label_due_date">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class = "row">
                        <!-- Task Description -->
                        <div class="mb-3 col-12 col-md-8">
                            <label for="task" class="form-label" id="label_task_desc">Description</label>
                            <textarea name="task" id="task" class="form-control form-control-sm" rows="4" required placeholder="Enter..."></textarea>
                            
                            <div class="row mt-3">
                                <!-- Task Status Select -->
                                <div class="mb-3 col-6">
                                    <label for="task_status_id" class="form-label" id="label_task_status">Status</label>
                                    <select name="task_status_id" id="task_status_id" class="form-select form-select-sm" required>
                                        <option value="">Select Status</option>
                                    </select>
                                </div>

                                <!-- Task Priority Select -->
                                <div class="mb-3 col-6">
                                    <label for="task_priority_id" class="form-label" id="label_task_priority">Priority</label>
                                    <select name="task_priority_id" id="task_priority_id" class="form-select form-select-sm">
                                        <option value="">Select Priority</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                            <!-- Image Upload with Add More -->
                        <div class="mb-3 col-12 col-md-4">
                            <div class="file-upload-box text-center p-4">

                                <!-- Upload Icon -->
                                <div class="upload-icon mb-3">
                                <i class="bi bi-cloud-arrow-up" style ="color: #434AFA;"></i>
                                </div>

                                <!-- File Input -->
                                <input
                                type="file" name="images[]" id="task_images" class="d-none" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.zip" style= "background: #DfDfDf;"
                                >

                                <!-- Browse Button -->
                                <button
                                type="button"
                                class="btn btn-sm btn-primary mb-2" style= "background: #434AFA;"
                                onclick="document.getElementById('task_images').click()"
                                >
                                Browse Files
                                </button>

                                <!-- Helper Text -->
                                <p style="color: black;" >Drop or Paste Here</p>

                            </div>

                            <!-- Preview -->
                            <div id="imagePreview" class="mt-2 d-flex gap-2 flex-wrap"></div>
                            <div id="selectedImagesList" class="mt-2"></div>
                        </div>
                    </div>
                </div>
                <div class="p-4 modal-footer-custom">
                    <button type="submit" class="btn btn-primary" style = "background: #434AFa;" id="createTaskSubmitBtn">
                        Submit
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    // Fallback for toastr using Swal if toastr is not defined
    if (typeof toastr === 'undefined') {
        window.toastr = {
            success: function(msg) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: msg, showConfirmButton: false, timer: 3000 });
                } else {
                    alert(msg);
                }
            },
            error: function(msg) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg, showConfirmButton: false, timer: 3000 });
                } else {
                    alert(msg);
                }
            }
        };
    }

$(document).ready(function() {
    let allMessages = [];
    let allAiTasks = [];
    let allImmediateTasks = [];
    let currentTab = 'messages'; // 'messages' or 'ai-tasks' or 'immediate-tasks'
    let currentImmediateStatusFilter = 'pending';

    fetchSignalTasks();
    fetchAiTasks();

    // Tab change listener to enable/disable Process AI button
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        currentTab = $(e.target).attr('id') === 'messages-tab' ? 'messages' : ($(e.target).attr('id') === 'ai-tasks-tab' ? 'ai-tasks' : 'immediate-tasks');
        if(currentTab === 'messages') {
            $('#processAiBtn').prop('disabled', false).show();
        } else {
            $('#processAiBtn').prop('disabled', true).hide();
        }
        
        if (currentTab === 'immediate-tasks') {
            loadImmediateTasks();
        }
        
        $('#searchInput').trigger('keyup'); // Re-trigger search for the active tab
    });

    function updateSummaryCards() {
        let totalMessages = allMessages.length;
        let aiTasks = allAiTasks.length;
        
        let pending = 0;
        let converted = 0;
        
        allAiTasks.forEach(task => {
            if ((task.status || 'pending') === 'converted') converted++;
            else pending++;
        });

        allMessages.forEach(msg => {
            if (msg.status === 'converted') converted++;
        });
        
        $('#cardTotalMessages').text(totalMessages);
        $('#cardAiTasks').text(aiTasks);
        $('#cardPending').text(pending);
        $('#cardConverted').text(converted);
    }

    function fetchSignalTasks() {
        $.ajax({
            url: "{{ route('signal-task.fetch') }}",
            type: "GET",
            success: function(response) {
                allMessages = response;
                renderMessages(allMessages);
                updateSummaryCards();
            },
            error: function(xhr) {
                console.error("Error fetching messages", xhr);
                $('#signalTaskTableBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load messages.</td></tr>');
            }
        });
    }

    function fetchAiTasks() {
        $.ajax({
            url: "{{ route('signal-task.fetch-ai-tasks') }}",
            type: "GET",
            success: function(response) {
                allAiTasks = response;
                renderAiTasks(allAiTasks);
                updateSummaryCards();
            },
            error: function(xhr) {
                console.error("Error fetching AI tasks", xhr);
                $('#aiTaskTableBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load AI tasks.</td></tr>');
            }
        });
    }

    function initDataTable(tableId) {
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }
        $(tableId).DataTable({
            dom: '<"top">rt<"bottom d-flex justify-content-between align-items-center mt-3"ip><"clear">',
            pageLength: 10,
            ordering: false,
            language: { 
                emptyTable: "No records found.",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
            }
        });
    }

    let currentStatusFilter = 'pending';

    $('#statusFilter').on('change', function() {
        currentStatusFilter = $(this).val();
        renderAiTasks(allAiTasks);
    });
    
    $('input[name="aiTaskView"]').on('change', function() {
        let view = $(this).val();
        if (view === 'table') {
            $('#aiTaskTableView').show();
            $('#aiTaskCardView').hide();
        } else {
            $('#aiTaskTableView').hide();
            $('#aiTaskCardView').show();
            $('#aiTaskCardView').css('display', 'grid');
        }
    });
    
    let currentSelectedChat = 'All chats';

    function renderMessages(tasks) {
        let chats = {};
        let totalMessages = 0;
        
        let filteredTasks = tasks; // We don't filter out converted messages in the Messages tab anymore
        
        filteredTasks.forEach(function(task) {
            let chatName = task.chat || 'Unknown';
            if (!chats[chatName]) {
                chats[chatName] = { name: chatName, count: 0, messages: [] };
            }
            chats[chatName].count++;
            chats[chatName].messages.push(task);
            totalMessages++;
        });

        let sidebarHtml = `
            <div class="chat-list-item ${currentSelectedChat === 'All chats' ? 'active' : ''}" onclick="selectChat('All chats')">
                <div class="chat-avatar" style="background:#e8f0fe; color:#1a73e8;"><i class="bi bi-chat-fill"></i></div>
                <div class="chat-info">
                    <div class="chat-name">All chats</div>
                    <div class="chat-meta">${totalMessages} messages</div>
                </div>
            </div>
        `;
        
        for (let chatName in chats) {
            sidebarHtml += `
                <div class="chat-list-item ${currentSelectedChat === chatName ? 'active' : ''}" onclick="selectChat('${chatName.replace(/'/g, "\\'")}')">
                    <div class="chat-avatar"><i class="bi bi-person-fill"></i></div>
                    <div class="chat-info">
                        <div class="chat-name">${chatName}</div>
                        <div class="chat-meta">${chats[chatName].count} messages</div>
                    </div>
                </div>
            `;
        }
        $('#chatList').html(sidebarHtml);
        
        let uniqueNumbers = Object.keys(chats).length;
        $('#connectedNumbersCount').text(uniqueNumbers + ' number(s) connected');
        
        renderChatMessages(currentSelectedChat === 'All chats' ? filteredTasks : (chats[currentSelectedChat] ? chats[currentSelectedChat].messages : []));
    }
    
    window.selectChat = function(chatName) {
        currentSelectedChat = chatName;
        renderMessages(allMessages);
    };

    function renderChatMessages(messagesToRender) {
        let html = '';
        $('#chatMainHeader').text(currentSelectedChat);
        
        if (messagesToRender.length === 0) {
            html = '<div class="text-center p-4 text-muted w-100">No messages found.</div>';
        } else {
            messagesToRender.forEach(function(msg) {
                let status = msg.status || 'pending';
                if (msg.ai_status === 'converted') {
                    status = 'converted';
                }
                
                let badge = msg.ai_task_id ? `<span class="badge bg-success ms-1" style="font-size:0.55rem; padding:0.2em 0.4em;">AI Detected</span>` : '';
                let displayTitle = msg.ai_title ? msg.ai_title : '';
                
                let title = msg.ai_title || msg.message_text || '';
                let fullText = msg.ai_description || msg.message_text || '';
                if (!title || title === 'null' || title === 'undefined' || title === 'N/A' || title.trim() === '') {
                    title = fullText;
                }
                if (!fullText || fullText === 'null' || fullText === 'undefined' || fullText === 'N/A' || fullText.trim() === '') {
                    fullText = title;
                }
                
                let actionBtn = msg.status === 'converted' ? 
                '<span class="text-success" style="font-weight: 500; font-size: 0.75rem;"><i class="bi bi-check-circle"></i> Converted</span>' : 
                `<button class="btn-mark-task convert-task-btn" data-id="${msg.id}" data-type="message" data-title="${title}" data-desc="${fullText.replace(/"/g, '&quot;')}">
                    <i class="bi bi-play-fill text-danger" style="font-size:1.1rem;"></i> Mark as task
                </button>`;
                
                let fullMessage = msg.message_text || 'N/A';
                
                html += `
                    <div class="message-bubble">
                        <div class="message-sender">${msg.sender || 'Unknown Sender'} - ${msg.chat || 'Unknown Chat'} ${badge}</div>
                        ${displayTitle ? `<div style="font-weight:600; font-size:0.85rem; margin-bottom:4px;">${displayTitle}</div>` : ''}
                        <div class="message-text">${fullMessage}</div>
                        <div class="message-footer">
                            <div class="message-time">${msg.created_at || ''}</div>
                            <div>${actionBtn}</div>
                        </div>
                    </div>
                `;
            });
        }
        $('#chatMessages').html(html);
    }

    function renderAiTasks(tasks) {
        let tableHtml = '';
        let cardHtml = '';
        
        let filteredTasks = tasks.filter(task => {
            let status = task.status || 'pending';
            if (currentStatusFilter !== 'all' && status !== currentStatusFilter) return false;
            return true;
        });

        if (filteredTasks.length === 0) {
            cardHtml = '<div class="text-center p-4 text-muted w-100">No AI tasks found.</div>';
        } else {
            filteredTasks.forEach(function(task) {
                let status = task.status || 'pending';
                
                let actionBtn = status === 'converted' ? 
                    '<span class="text-success" style="font-weight: 500; font-size: 0.75rem;"><i class="bi bi-check-circle"></i> Converted</span>' : 
                    `<button class="btn-mark-task convert-task-btn" data-id="${task.id}" data-type="ai_task" data-title="${task.title || ''}" data-desc="${(task.description || '').replace(/"/g, '&quot;')}">
                        <i class="bi bi-play-fill text-danger" style="font-size:1.1rem;"></i> Mark as task
                    </button>`;
                
                let fullDesc = task.description || 'N/A';
                
                let chatName = task.chat_name || 'N/A';
                let assignedTo = task.ai_assigned_to || 'Unassigned';
                let requestedBy = task.ai_requested_by || task.sender || 'Unknown';
                let createdAt = task.created_at ? new Date(task.created_at).toLocaleString('en-IN', { timeZone: 'Asia/Kolkata', dateStyle: 'medium', timeStyle: 'short' }) : 'N/A';

                let cardTitleHtml = `<a href="#" class="ai-task-detail-link" data-id="${task.id}" style="text-decoration:none; color:#000;" title="View Details">${task.title || 'N/A'} <i class="bi bi-info-circle ms-1 text-muted" style="font-size:0.8rem;"></i></a>`;

                cardHtml += `
                    <div class="ai-task-card">
                        <div class="ai-task-title">${cardTitleHtml}</div>
                        <div class="ai-task-desc" style="word-break: break-word;">${fullDesc}</div>
                        <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 10px;">
                            <div><strong>Chat:</strong> ${chatName}</div>
                            <div><strong>Assigned To:</strong> ${assignedTo}</div>
                            <div><strong>Requested By:</strong> ${requestedBy}</div>
                        </div>
                        <div class="ai-task-footer">
                            <span class="badge ${status === 'converted' ? 'bg-success' : 'bg-secondary'}">${status}</span>
                            ${actionBtn}
                        </div>
                    </div>
                `;
                
                let shortDesc = fullDesc.length > 30 ? fullDesc.substring(0, 30) + '...' : fullDesc;
                let descHtml = `<a href="#" class="ai-task-detail-link" data-id="${task.id}" style="font-size:0.85rem; text-decoration:none; color:#000;">${shortDesc}</a>`;
                let actionBtnTable = status === 'converted' ? 
                    '<span class="text-success" style="font-weight: 500; font-size: 0.8rem;"><i class="bi bi-check-circle"></i> Converted</span>' : 
                    `<button class="btn-action-edit convert-task-btn" data-id="${task.id}" data-type="ai_task" data-title="${task.title || ''}" data-desc="${(task.description || '').replace(/"/g, '&quot;')}">Convert to Task</button>`;

                let displayTitle = task.title || 'N/A';
                if (displayTitle.length > 20) displayTitle = displayTitle.substring(0, 20) + '...';
                
                let tableTitleHtml = `<a href="#" class="ai-task-detail-link" data-id="${task.id}" style="font-weight:500; text-decoration:none; color:#000;" title="View Details">${displayTitle}</a>`;

                tableHtml += `
                    <tr>
                        <td title="${task.title || ''}">${tableTitleHtml}</td>
                        <td>${descHtml}</td>
                        <td>${chatName}</td>
                        <td>${assignedTo}</td>
                        <td>${requestedBy}</td>
                        <td>${createdAt}</td>
                        <td>${status}</td>
                        <td>${actionBtnTable}</td>
                    </tr>
                `;
            });
        }
        
        $('#aiTaskCardView').html(cardHtml);
        
        if ($.fn.DataTable.isDataTable('#aiTaskTable')) {
            $('#aiTaskTable').DataTable().destroy();
        }
        $('#aiTaskTableBody').html(tableHtml);
        initDataTable('#aiTaskTable');
    }

    // Load initial dropdown data for the modal
    function loadModalDropdowns() {
        $.get("{{ route('task.users') }}", function(data) {
            let userHtml = '';
            if (data && data.length > 0) {
                $.each(data, function(i, user) {
                    userHtml += `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="user_ids[]" value="${user.id}" id="user_${user.id}">
                        <label class="form-check-label" for="user_${user.id}">${user.name}</label>
                    </div>`;
                });
            }
            $('#assignUsersContainer').html(userHtml);
        });

        $.get("{{ route('task.customers') }}", function(data) {
            let options = '<option value="">Select Customer</option>';
            if (data && data.length > 0) {
                $.each(data, function(i, customer) {
                    options += `<option value="${customer.id}">${customer.name}</option>`;
                });
            }
            $('#customer_id').html(options);
        });

        $.get("{{ route('task.statuses') }}", function(data) {
            let options = '<option value="">Select Status</option>';
            if (data && data.length > 0) {
                $.each(data, function(i, status) {
                    options += `<option value="${status.id}">${status.name}</option>`;
                });
            }
            $('#task_status_id').html(options);
        });

        $.get("{{ route('task.priorities') }}", function(data) {
            let options = '<option value="">Select Priority</option>';
            if (data && data.length > 0) {
                $.each(data, function(i, priority) {
                    options += `<option value="${priority.id}">${priority.name}</option>`;
                });
            }
            $('#task_priority_id').html(options);
        });
    }

    // Helper to load projects for a customer
    window.loadCustomerProjects = function(customerId, targetSelector, selectedProjectId = null) {
        const $target = $(targetSelector);
        $target.html('<option value="">Loading...</option>');
        
        if (!customerId) {
            $target.html('<option value="">Select Project</option>');
            return;
        }

        $.ajax({
            url: `/projects/fetch/${customerId}`,
            type: 'GET',
            success: function(projects) {
                let options = '<option value="">Select Project</option>';
                if (projects && projects.length > 0) {
                    projects.forEach(function(project) {
                        const selected = (selectedProjectId && String(project.id) === String(selectedProjectId)) ? 'selected' : '';
                        options += `<option value="${project.id}" ${selected}>${project.project_name}</option>`;
                    });
                } else {
                    options += '<option value="">No projects found</option>';
                }
                $target.html(options);
            },
            error: function() {
                $target.html('<option value="">Error loading projects</option>');
            }
        });
    }

    // Event listener for Create Task Customer change
    $('#customer_id').on('change', function() {
        loadCustomerProjects($(this).val(), '#customer_project_id');
    });

    loadModalDropdowns();

    // Open modal on click (intercepted for immediate task)
    $(document).on('click', '.convert-task-btn', function(e) {
        e.preventDefault();
        
        let id = $(this).data('id');
        let type = $(this).data('type');
        let title = $(this).data('title');
        let desc = $(this).data('desc');
        
        // If found nothing at the time of saving make description same as title and vice versa
        if (!title || title === 'null' || title === 'undefined' || title === 'N/A' || title.trim() === '') {
            title = desc;
        }
        if (!desc || desc === 'null' || desc === 'undefined' || desc === 'N/A' || desc.trim() === '') {
            desc = title;
        }

        Swal.fire({
            title: 'Convert Task',
            text: "Do you want to create an Immediate Task or a Regular Task?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#434AFA',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Immediate Task',
            cancelButtonText: 'Regular Task'
        }).then((result) => {
            if (result.isConfirmed) {
                // Immediate Task logic
                $.ajax({
                    url: '{{ route("signal-task.store-immediate") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        type: type,
                        title: title,
                        description: desc
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Immediate task created successfully!');
                            // Refresh data depending on active tab
                            if (currentTab === 'messages') {
                                fetchSignalTasks();
                            } else if (currentTab === 'ai-tasks') {
                                fetchAiTasks();
                            }
                            loadImmediateTasks();
                        } else {
                            toastr.error('Failed to create immediate task.');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error creating immediate task.');
                        console.error(xhr);
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Regular Task logic (old behavior)
                $('#taskForm')[0].reset();
                
                if (title) {
                    $('#task_name').val(title);
                }
                if (desc) {
                    $('#task').val(desc);
                }
                
                $('#taskForm').data('signal-id', id);
                $('#taskForm').data('signal-type', type);
                
                $('#createTaskModal').modal('show');
            }
        });
    });

    // Handle immediate task "Mark as Done"
    $(document).on('click', '.mark-immediate-done-btn', function(e) {
        e.preventDefault();
        let $btn = $(this);
        let id = $btn.data('id');
        let $row = $btn.closest('tr');
        
        $.ajax({
            url: '{{ route("signal-task.mark-immediate-done") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id
            },
            success: function(response) {
                if(response.success) {
                    toastr.success('Task marked as done!');
                    
                    let doneBadge = '<span class="badge bg-success">Done</span>';
                    
                    // Update task in global array
                    let taskIndex = allImmediateTasks.findIndex(t => t.id == id);
                    if (taskIndex !== -1) {
                        allImmediateTasks[taskIndex].status = 'done';
                    }
                    
                    if (currentImmediateStatusFilter === 'pending') {
                        // Remove row from DataTable
                        if ($.fn.DataTable.isDataTable('#immediateTaskTable')) {
                            $('#immediateTaskTable').DataTable().row($row).remove().draw(false);
                        } else {
                            $row.remove();
                        }
                    } else {
                        // Update DOM directly
                        $row.find('td:eq(2)').html(doneBadge);
                        $row.find('td:eq(3)').html('');
                        
                        // Update DataTable data if initialized so it persists on paginate/search
                        if ($.fn.DataTable.isDataTable('#immediateTaskTable')) {
                            let dt = $('#immediateTaskTable').DataTable();
                            dt.cell($row, 2).data(doneBadge);
                            dt.cell($row, 3).data('');
                        }
                    }
                } else {
                    toastr.error('Failed to update task.');
                }
            },
            error: function(xhr) {
                toastr.error('Error updating task.');
                console.error(xhr);
            }
        });
    });

    $('#immediateStatusFilter').on('change', function() {
        currentImmediateStatusFilter = $(this).val();
        renderImmediateTasks(allImmediateTasks);
    });

    // Load Immediate Tasks
    function loadImmediateTasks() {
        $.ajax({
            url: '{{ route("signal-task.fetch-immediate") }}',
            type: 'GET',
            success: function(tasks) {
                allImmediateTasks = tasks;
                renderImmediateTasks(allImmediateTasks);
            },
            error: function(xhr) {
                console.error("Error fetching immediate tasks:", xhr);
                if ($.fn.DataTable.isDataTable('#immediateTaskTable')) {
                    $('#immediateTaskTable').DataTable().destroy();
                }
                $('#immediateTaskTableBody').html('');
                initDataTable('#immediateTaskTable');
                toastr.error('Failed to load immediate tasks.');
            }
        });
    }

    function renderImmediateTasks(tasks) {
        let html = '';
        let filteredTasks = tasks;
        
        if (currentImmediateStatusFilter !== 'all') {
            filteredTasks = tasks.filter(t => (t.status || 'pending') === currentImmediateStatusFilter);
        }

        if (filteredTasks && filteredTasks.length > 0) {
            filteredTasks.forEach(task => {
                let statusBadge = task.status === 'done' ? 
                    '<span class="badge bg-success">Done</span>' : 
                    '<span class="badge bg-warning text-dark">Pending</span>';
                    
                let actionBtn = task.status === 'done' ? 
                    '' :
                    `<button class="btn btn-sm btn-outline-success mark-immediate-done-btn" style="border-radius:12px; padding:2px 8px; font-size:0.75rem;" data-id="${task.id}"><i class="bi bi-check2"></i></button>`;
                    
                let displayTitle = task.title || 'N/A';
                if (displayTitle.length > 20) displayTitle = displayTitle.substring(0, 20) + '...';
                
                let displayDesc = task.description || 'N/A';
                if (displayDesc.length > 20) displayDesc = displayDesc.substring(0, 20) + '...';
                    
                html += `
                    <tr>
                        <td title="${task.title || ''}">${displayTitle}</td>
                        <td title="${task.description || ''}">${displayDesc}</td>
                        <td>${statusBadge}</td>
                        <td>${actionBtn}</td>
                    </tr>
                `;
            });
        }
        
        if ($.fn.DataTable.isDataTable('#immediateTaskTable')) {
            $('#immediateTaskTable').DataTable().destroy();
        }
        $('#immediateTaskTableBody').html(html);
        initDataTable('#immediateTaskTable');
    }

    // Recurrence UI logic
    $('#is_recurring').on('change', function(){
        $('#recurrencePanel').toggle(this.checked);
    });
    $('#recurrence_type').on('change', function(){
        const t = $(this).val();
        $('#recurrence_weekly, #recurrence_monthly, #recurrence_yearly').hide();
        if (t === 'weekly') $('#recurrence_weekly').show();
        if (t === 'monthly') $('#recurrence_monthly').show();
        if (t === 'yearly') $('#recurrence_yearly').show();
    }).trigger('change');

    $(document).on('click', '.msg-text-link', function(e){
        e.preventDefault();
        const full = decodeURIComponent($(this).data('full') || '');
        showFullTextModal('Message Details', full);
    });

    function showFullTextModal(title, text) {
        let modalEl = document.getElementById('fullTaskTextModal');
        if (!modalEl) {
            const html = `
            <div class="modal fade" id="fullTaskTextModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="fullTaskTextModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <pre id="fullTaskTextBody" class="mb-0" style="white-space: pre-wrap; word-break: break-word;"></pre>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
            modalEl = document.getElementById('fullTaskTextModal');
        }
        $('#fullTaskTextModalLabel').text(title);
        $('#fullTaskTextBody').text(text);
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    $(document).on('click', '.ai-task-detail-link', function(e){
        e.preventDefault();
        const taskId = $(this).data('id');
        const task = allAiTasks.find(t => t.id == taskId);
        if(task) {
            showAiTaskDetailModal(task);
        }
    });

    function showAiTaskDetailModal(task) {
        let modalEl = document.getElementById('aiTaskDetailModal');
        if (!modalEl) {
            const html = `
            <div class="modal fade" id="aiTaskDetailModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">AI Task Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th style="width: 25%">Title</th><td id="detailTitle"></td></tr>
                            <tr><th>Chat</th><td id="detailChat"></td></tr>
                            <tr><th>Assigned To</th><td id="detailAssigned"></td></tr>
                            <tr><th>Requested By</th><td id="detailRequested"></td></tr>
                            <tr><th>Created At</th><td id="detailCreated"></td></tr>
                            <tr><th>Status</th><td id="detailStatus"></td></tr>
                            <tr><th>Description</th><td><pre id="detailDesc" class="mb-0" style="white-space: pre-wrap; word-break: break-word; font-family:inherit;"></pre></td></tr>
                        </tbody>
                    </table>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
            modalEl = document.getElementById('aiTaskDetailModal');
        }
        
        let chatName = task.chat_name || 'N/A';
        let assignedTo = task.ai_assigned_to || 'Unassigned';
        let requestedBy = task.ai_requested_by || task.sender || 'Unknown';
        let createdAt = task.created_at ? new Date(task.created_at).toLocaleString('en-IN', { timeZone: 'Asia/Kolkata', dateStyle: 'medium', timeStyle: 'short' }) : 'N/A';

        $('#detailTitle').text(task.title || 'N/A');
        $('#detailChat').text(chatName);
        $('#detailAssigned').text(assignedTo);
        $('#detailRequested').text(requestedBy);
        $('#detailCreated').text(createdAt);
        $('#detailStatus').html(`<span class="badge ${task.status === 'converted' ? 'bg-success' : 'bg-secondary'}">${task.status || 'pending'}</span>`);
        $('#detailDesc').text(task.description || 'N/A');
        
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    // Basic frontend search
    $('#searchInput').on('keyup', function() {
        let value = $(this).val();
        if (currentTab === 'messages') {
            if ($.fn.DataTable.isDataTable('#signalTaskTable')) {
                $('#signalTaskTable').DataTable().search(value).draw();
            }
        } else {
            if ($.fn.DataTable.isDataTable('#aiTaskTable')) {
                $('#aiTaskTable').DataTable().search(value).draw();
            }
        }
    });

    // Process AI tasks on demand
    $('#processAiBtn').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Starting...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('signal-task.process-ai-start') }}",
            type: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if(response.success) {
                    const chats = response.chats;
                    if (!chats || chats.length === 0) {
                        alert('No new messages to process.');
                        btn.html(originalText).prop('disabled', false);
                        return;
                    }
                    
                    let currentIndex = 0;
                    const totalChats = chats.length;
                    
                    const processNextChat = function() {
                        if (currentIndex >= totalChats) {
                            btn.html(`<i class="fas fa-spinner fa-spin me-1"></i> Processing... 100%`);
                            setTimeout(() => {
                                alert('AI Processing completed successfully!');
                                fetchSignalTasks();
                                fetchAiTasks();
                                btn.html(originalText).prop('disabled', false);
                            }, 500);
                            return;
                        }
                        
                        let progress = Math.round((currentIndex / totalChats) * 100);
                        btn.html(`<i class="fas fa-spinner fa-spin me-1"></i> Processing... ${progress}%`);
                        
                        $.ajax({
                            url: "{{ route('signal-task.process-ai-chat') }}",
                            type: 'POST',
                            data: { 
                                _token: "{{ csrf_token() }}",
                                chat: chats[currentIndex]
                            },
                            success: function() {
                                fetchAiTasks(); // refresh table dynamically
                                fetchSignalTasks(); // refresh signal table dynamically
                                currentIndex++;
                                processNextChat();
                            },
                            error: function(xhr) {
                                console.error('Error processing chat:', chats[currentIndex], xhr);
                                currentIndex++;
                                processNextChat();
                            }
                        });
                    };
                    
                    processNextChat();
                } else {
                    alert('Error starting AI tasks.');
                    btn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                alert('Error starting AI tasks. ' + (xhr.responseJSON?.message || ''));
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Handle form submission via AJAX
    $('#taskForm').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $('#createTaskSubmitBtn');
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Submitting...').prop('disabled', true);
        
        const formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Add task_type from radio button
        const taskType = $('input[name="task_type"]:checked').val();
        formData.set('task_type', taskType || 'task');

        // Recurrence fields
        const isRecurring = $('#is_recurring').is(':checked');
        formData.append('is_recurring', isRecurring ? 1 : 0);
        if (isRecurring) {
            formData.append('recurrence_type', $('#recurrence_type').val());
            formData.append('recurrence_interval', $('#recurrence_interval').val() || 1);
            const dows = [];
            ['mon','tue','wed','thu','fri','sat','sun'].forEach(function(k){
                if ($('#dow_'+k).is(':checked')) dows.push(k);
            });
            if (dows.length) { dows.forEach(v => formData.append('recurrence_days_of_week[]', v)); }
            const dom = $('#recurrence_day_of_month').val();
            if (dom) formData.append('recurrence_day_of_month', dom);
            const months = [];
            for (let i=1;i<=12;i++){ if ($('#m_'+i).is(':checked')) months.push(i); }
            if (months.length) { months.forEach(v => formData.append('recurrence_months[]', v)); }
            const endDate = $('#recurrence_end_date').val();
            if (endDate) formData.append('recurrence_end_date', endDate);
        }
        
        $.ajax({
            url: "{{ route('task.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                let signalId = $('#taskForm').data('signal-id');
                let signalType = $('#taskForm').data('signal-type');
                
                function finishSuccess() {
                    alert(response.message || 'Task created successfully!');
                    $('#createTaskModal').modal('hide');
                    $('#taskForm')[0].reset();
                    $('#imagePreview').empty();
                    $('#selectedImagesList').empty();
                    fetchSignalTasks(); 
                    fetchAiTasks(); 
                }

                if (signalId && (signalType === 'ai_task' || signalType === 'message')) {
                    $.post("{{ route('signal-task.mark-converted') }}", {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: signalId,
                        type: signalType
                    }, function() {
                        finishSuccess();
                    }).fail(function() {
                        finishSuccess();
                    });
                } else {
                    finishSuccess();
                }
            },
            error: function(xhr) {
                let errMsg = 'Failed to create task.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const firstError = Object.values(xhr.responseJSON.errors)[0];
                    errMsg = firstError[0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                alert(errMsg);
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
@endpush


