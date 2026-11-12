@extends('layouts.app')

@section('title', $title)
@section('page_title', $title)

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
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

  .summary-header-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.9rem 1.25rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    margin-bottom: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
  }

  .summary-header-title {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    font-weight: 700;
    font-size: 1.05rem;
    color: #0f172a;
    font-family: Montserrat;
  }

  .summary-header-badge {
    background: #434AFA;
    color: #fff;
    font-weight: 700;
    font-size: 0.95rem;
    padding: 0.4rem 1rem;
    border-radius: 6px;
    font-family: Montserrat;
  }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="px-3">
        <!-- Header Info Card -->
        <div class="summary-header-card">
            <div class="summary-header-title">
                <a href="{{ route('petty-cash.department-summary') }}" class="btn btn-sm btn-light border rounded-circle d-inline-flex align-items-center justify-content-center me-1" style="width: 32px; height: 32px;" title="Back to Wallet Summary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <i class="bi bi-wallet2 text-primary fs-5"></i>
                <span>{{ $title }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fw-semibold" style="font-size: 0.85rem;">Total Opening Balance:</span>
                <span class="summary-header-badge">₹{{ number_format($balances->sum('amount'), 2) }}</span>
            </div>
        </div>

        <!-- Data Table Card matching department-expenses.blade.php -->
        <div class="data-table-card">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Date & Time</th>
                            <th>Wallet / Department</th>
                            <th class="text-end">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($balances as $index => $item)
                        <tr>
                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                {{ $item->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-building me-1 text-primary"></i>
                                    {{ $item->department ? $item->department->name : '-' }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark" style="font-size: 0.95rem;">
                                ₹{{ number_format($item->amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="bi bi-wallet2 fs-2 d-block text-secondary mb-2"></i>
                                No opening balance records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

