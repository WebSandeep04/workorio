@extends('layouts.app')

@section('title', $department->name . ' - Expense History')
@section('page_title', $department->name . ' - Expense History')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .page-header {
      padding: 1rem 1.5rem;
      background: white;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
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

  .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
  }

  .custom-table thead th {
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

  .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
    vertical-align: middle;
  }

  .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }

  .badge-approved {
    background-color: #d1fae5;
    color: #065f46;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .badge-pending {
    background-color: #fef3c7;
    color: #92400e;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
  }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- <div class="page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('petty-cash.department-summary') }}" class="btn btn-light btn-sm rounded-circle">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="m-0 fw-bold">{{ $department->name }} - Expense History</h4>
        </div>
        <div>
            <span class="badge bg-primary fs-6">Total: ₹{{ number_format($expenses->sum('price'), 2) }}</span>
        </div>
    </div> -->

    <div class="px-3">
        <div class="data-table-card">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Expense Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->created_at->format('d M Y') }}</td>
                            <td>{{ $expense->expense ? $expense->expense->name : 'N/A' }}</td>
                            <td class="fw-bold">₹{{ number_format($expense->price, 2) }}</td>
                            <td>
                                @if($expense->is_approved)
                                    <span class="badge badge-approved">Approved</span>
                                @else
                                    <span class="badge badge-pending">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($expense->attachment)
                                    <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="text-primary">
                                        <i class="bi bi-paperclip fs-5"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No expenses found for this wallet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
