@extends('layouts.app')

@section('title', 'Wallet Summary')
@section('page_title', 'Wallet Summary')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Grid for the 3 separate cards per department */
  .summary-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 1rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
    min-height: 80px;
  }
  
  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 12px 0px #0000000A;
  }

  .summary-card-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon i {
    font-size: 1.25rem;
    color: white;
  }

  .summary-card-content {
      display: flex;
      flex-direction: column;
      flex-grow: 1;
  }

  .summary-card-label {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      color: #64748b;
      font-family: Montserrat;
      margin-bottom: 0.25rem;
  }

  .summary-card-value {
      font-size: 1.1rem;
      font-weight: 800;
      color: #0f172a;
      font-family: Montserrat;
      line-height: 1;
  }

  .dept-section {
    background: #fff;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .dept-title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #f1f5f9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .icon-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }
  .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }

  .text-danger { color: #ef4444 !important; }
  .text-success { color: #10b981 !important; }
  .text-dark { color: #0f172a !important; }
  .text-primary { color: #434afa !important; }

  .page-header {
      padding: 1rem 1.5rem;
      background: white;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
  }

  .metric-arrow {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
    margin-left: auto; /* Push to right */
  }

  .metric-arrow:hover {
    background: #5b59f7;
    color: #fff;
  }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- <div class="page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('petty-cash.index') }}" class="btn btn-light btn-sm rounded-circle">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="m-0 fw-bold">Wallet Summary</h4>
        </div>
    </div> -->

    <div class="px-3">
        @foreach($summary as $dept)
        <div class="dept-section">
            <div class="dept-title">
                <i class="bi bi-building text-primary"></i>
                {{ $dept['department_name'] }}
            </div>
            
            <div class="summary-cards-grid">
                <!-- Opening Balance Card -->
                <div class="summary-card">
                    <div class="summary-card-icon icon-violet">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Opening Balance</div>
                        <div class="summary-card-value text-dark">₹{{ number_format($dept['opening_balance'], 2) }}</div>
                    </div>
                </div>

                <!-- Expense Card -->
                <div class="summary-card">
                    <div class="summary-card-icon icon-rose">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Total Expense</div>
                        <div class="summary-card-value text-danger">₹{{ number_format($dept['total_expense'], 2) }}</div>
                    </div>
                    <a href="{{ route('petty-cash.department.expenses', $dept['department_id']) }}" class="metric-arrow">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <!-- Remaining Card -->
                <div class="summary-card">
                    <div class="summary-card-icon icon-emerald">
                        <i class="bi bi-piggy-bank"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Remaining</div>
                        <div class="summary-card-value text-success">₹{{ number_format($dept['remaining'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
