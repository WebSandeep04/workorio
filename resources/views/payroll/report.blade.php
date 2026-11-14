@extends('layouts.app')
@section('title', 'Salary Report')

@section('content')
<style>
  .modern-card {
      background: #fff;
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      margin-bottom: 1.5rem;
  }
  .modern-card .card-header {
      background: #fff;
      border-bottom: 1px solid #f1f5f9;
      padding: 1.25rem 1.5rem;
      border-radius: 12px 12px 0 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
  }
  .modern-card .card-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #1e293b;
      margin: 0;
  }
  .custom-table th {
      background: #f8fafc;
      color: #475569;
      font-weight: 600;
      font-size: 0.82rem;
      letter-spacing: 0.3px;
      padding: 0.75rem 0.85rem;
      border-bottom: 2px solid #e2e8f0;
      white-space: nowrap;
  }
  .custom-table td {
      padding: 0.75rem 0.85rem;
      color: #334155;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
      font-size: 0.85rem;
      white-space: nowrap;
  }
  .custom-table tfoot th {
      background: #f1f5f9;
      font-weight: 700;
      color: #0f172a;
      padding: 0.85rem;
      border-top: 2px solid #cbd5e1;
      white-space: nowrap;
  }
  .accordion-item {
      border: 1px solid #e2e8f0;
      border-radius: 10px !important;
      margin-bottom: 1rem;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  }
  .accordion-button {
      background: #ffffff;
      padding: 1rem 1.25rem;
      font-size: 0.95rem;
      font-weight: 600;
      color: #1e293b;
      box-shadow: none !important;
  }
  .accordion-button:not(.collapsed) {
      background: #f8fafc;
      color: #434afa;
      border-bottom: 1px solid #e2e8f0;
  }
  .accordion-button::after {
      background-size: 1rem;
  }
  .badge-payroll {
      font-size: 0.75rem;
      padding: 0.35rem 0.65rem;
      font-weight: 600;
      border-radius: 6px;
  }
  .stat-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      background: #f1f5f9;
      color: #334155;
  }
</style>

<div class="container-fluid px-2 mt-2">
    <!-- Filter Card: Select Year Only -->
    <div class="modern-card">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-4 col-sm-6">
                    <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.85rem;"><i class="bi bi-calendar-range me-1 text-primary"></i> Select Year</label>
                    <select name="year" id="year" class="form-select" style="height: 40px; font-size: 0.95rem;">
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Accordion Container for Generated Months -->
    <div id="payrollAccordionContainer" class="accordion" style="display: none;">
        <!-- Month Accordion Items Injected by AJAX -->
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="modern-card text-center p-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted fw-semibold">Loading generated payrolls for the selected year...</p>
    </div>

    <!-- Empty State -->
    <div id="noDataMessage" class="modern-card text-center p-5" style="display: none;">
        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
        <h5 class="mt-3 text-dark fw-bold">No Generated Payrolls Found</h5>
        <p class="text-muted">No finalized or generated payroll data exists for <span id="emptyYearSpan"></span>.</p>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Export button click delegator
    $(document).on('click', '.exportBtn', function(e) {
        e.preventDefault();
        const month = $(this).data('month');
        const year = $('#year').val();
        const penalty = $(this).data('penalty');
        window.location.href = "{{ route('payroll.report.export') }}?month=" + month + "&year=" + year + "&with_penalty=" + penalty;
    });

    $(document).on('click', '.exportPdfBtn', function(e) {
        e.preventDefault();
        const month = $(this).data('month');
        const year = $('#year').val();
        const penalty = $(this).data('penalty');
        window.location.href = "{{ route('payroll.report.export-pdf') }}?month=" + month + "&year=" + year + "&with_penalty=" + penalty;
    });

    // Auto-generate report on page load for current selected year
    fetchYearlyReport();

    $('#year').on('change', function() {
        fetchYearlyReport();
    });

    function fetchYearlyReport() {
        const year = $('#year').val();
        const selectYear = $('#year');
        selectYear.prop('disabled', true);

        $('#payrollAccordionContainer').hide().empty();
        $('#noDataMessage').hide();
        $('#loadingState').show();

        $.ajax({
            url: "{{ route('payroll.report.fetch-yearly') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                year: year
            },
            success: function(response) {
                selectYear.prop('disabled', false);
                $('#loadingState').hide();

                if (response.success && response.payrolls && response.payrolls.length > 0) {
                    renderYearlyAccordions(response.payrolls, year);
                    $('#payrollAccordionContainer').show();
                } else {
                    $('#emptyYearSpan').text(year);
                    $('#noDataMessage').show();
                }
            },
            error: function(xhr) {
                selectYear.prop('disabled', false);
                $('#loadingState').hide();
                $('#emptyYearSpan').text(year);
                $('#noDataMessage').show();
                console.error("Yearly Payroll Fetch Error:", xhr);
            }
        });
    }

    function renderYearlyAccordions(payrolls, year) {
        const container = $('#payrollAccordionContainer');
        container.empty();

        payrolls.forEach(function(item, index) {
            const isFirst = index === 0; // expand the latest generated month by default
            const collapseId = `collapseMonth_${item.month}`;
            const headingId = `headingMonth_${item.month}`;
            const showPf = item.show_pf ?? true;
            const showEsi = item.show_esi ?? true;
            const showEmployerCont = item.show_employer_cont ?? (showPf || showEsi);
            const cols = item.columns || [];
            const totals = item.totals || {};
            const rows = item.data || [];

            const statusBadgeClass = (item.payroll_status === 'Finalized') ? 'bg-success' : 'bg-warning text-dark';
            const formattedTotalNet = Number(totals.paid_salary || 0).toLocaleString('en-IN');
            const formattedTotalGross = Number(totals.gross_salary || 0).toLocaleString('en-IN');

            let accordionHtml = `
            <div class="accordion-item mb-3">
                <h2 class="accordion-header" id="${headingId}">
                    <button class="accordion-button ${isFirst ? '' : 'collapsed'} d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${isFirst ? 'true' : 'false'}" aria-controls="${collapseId}">
                        <div class="d-flex align-items-center flex-wrap gap-3 w-100 pe-3">
                            <span class="fs-6 fw-bold text-dark"><i class="bi bi-calendar3 me-2 text-primary"></i>${item.month_name} ${year}</span>
                            <span class="badge ${statusBadgeClass} badge-payroll">${item.payroll_status}</span>
                            
                            <div class="ms-auto d-flex align-items-center flex-wrap gap-2 text-muted fw-normal" style="font-size: 0.85rem;">
                                <span class="stat-pill"><i class="bi bi-people-fill text-primary"></i>${item.employee_count} Employees</span>
                                <span class="stat-pill"><i class="bi bi-cash-stack text-success"></i>Gross: ₹${formattedTotalGross}</span>
                                <span class="stat-pill bg-primary text-white"><i class="bi bi-wallet2 text-white"></i>Net: ₹${formattedTotalNet}</span>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="${collapseId}" class="accordion-collapse collapse ${isFirst ? 'show' : ''}" aria-labelledby="${headingId}">
                    <div class="accordion-body p-0">
                        <!-- Toolbar for this Month (Exports) -->
                        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <span class="fw-semibold text-secondary" style="font-size: 0.9rem;">
                                    <i class="bi bi-table me-1"></i> Full Breakdown & Detailed Summary (${item.month_name} ${year})
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-danger dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item exportPdfBtn" href="#" data-month="${item.month}" data-penalty="1">With Penalty</a></li>
                                        <li><a class="dropdown-item exportPdfBtn" href="#" data-month="${item.month}" data-penalty="0">Without Penalty</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-success dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item exportBtn" href="#" data-month="${item.month}" data-penalty="1">With Penalty</a></li>
                                        <li><a class="dropdown-item exportBtn" href="#" data-month="${item.month}" data-penalty="0">Without Penalty</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table for this Month -->
                        <div class="table-responsive" style="max-height: 480px; overflow-y: auto; overflow-x: auto;">
                            <table class="table custom-table mb-0">
                                <thead style="position: sticky; top: 0; z-index: 2;">
                                    <tr>
                                        <th style="min-width: 110px;">Emp Code</th>
                                        <th style="min-width: 140px;">Employee Name</th>
                                        <th style="min-width: 130px;">Employment Type</th>
                                        <th style="min-width: 110px;">Branch</th>
                                        <th style="min-width: 110px;">Department</th>`;
            
            cols.forEach(c => {
                accordionHtml += `<th class="text-end" style="min-width: 100px;">${c}</th>`;
            });

            accordionHtml += `
                                        <th class="text-end fw-bold" style="min-width: 110px;">Gross Salary</th>
                                        <th class="text-center" style="min-width: 90px;">Working Days</th>
                                        <th class="text-center" style="min-width: 90px;">Total Present</th>
                                        <th class="text-center" style="min-width: 80px;">Full Day</th>
                                        <th class="text-center" style="min-width: 80px;">Half Day</th>
                                        <th class="text-center text-danger" style="min-width: 105px;">Unpaid Half Day</th>
                                        <th class="text-center" style="min-width: 95px;">Sunday Work</th>
                                        <th class="text-center" style="min-width: 95px;">Holiday Work</th>
                                        <th class="text-center" style="min-width: 75px;">Leave</th>
                                        <th class="text-center" style="min-width: 95px;">Unpaid Leave</th>
                                        <th class="text-center" style="min-width: 75px;">Absent</th>
                                        <th class="text-center" style="min-width: 110px;">Total Weekly Off</th>
                                        <th class="text-center" style="min-width: 95px;">Total Holidays</th>
                                        <th class="text-center" style="min-width: 130px;">Total Deduction Days</th>
                                        <th class="text-end" style="min-width: 130px;">Days Deduction</th>
                                        <th class="text-end" style="min-width: 130px;">Advance Deduction</th>
                                        <th class="text-end" style="min-width: 130px;">Loan Deduction</th>`;

            cols.forEach(c => {
                accordionHtml += `<th class="text-end" style="min-width: 105px;">Earned ${c}</th>`;
            });
            accordionHtml += `<th class="text-end fw-bold" style="min-width: 115px;">Earned Gross</th>`;

            if (showPf) {
                accordionHtml += `<th class="text-end" style="min-width: 120px;">PF Deduction</th>`;
            }
            if (showEsi) {
                accordionHtml += `<th class="text-end" style="min-width: 120px;">ESI Deduction</th>`;
            }

            accordionHtml += `
                                        <th class="text-end" style="min-width: 130px;">Total Deduction</th>
                                        <th class="text-end" style="min-width: 110px;">Salary</th>
                                        <th class="text-center" style="min-width: 100px;">Total Lates</th>
                                        <th class="text-center" style="min-width: 110px;">Exempted Lates</th>
                                        <th class="text-center" style="min-width: 100px;">Eligible Lates</th>
                                        <th class="text-center" style="min-width: 90px;">Penalty Days</th>
                                        <th class="text-end" style="min-width: 120px;">Penalty Amount</th>
                                        <th class="text-end" style="min-width: 110px;">Final Salary</th>`;

            if (showPf) {
                accordionHtml += `<th class="text-end" style="min-width: 110px;">Employer PF</th>`;
            }
            if (showEsi) {
                accordionHtml += `<th class="text-end" style="min-width: 110px;">Employer ESI</th>`;
            }
            if (showEmployerCont) {
                accordionHtml += `<th class="text-end" style="min-width: 140px;">Total Employer Cont.</th>`;
            }

            accordionHtml += `
                                    </tr>
                                </thead>
                                <tbody>`;

            // Employee Rows
            rows.forEach(function(row) {
                const salaryClass = row.paid_salary === 'Not Generated' ? 'text-muted' : 'fw-bold text-success';
                const salaryText = row.paid_salary === 'Not Generated' ? 'Not Generated' : '₹' + Number(row.paid_salary).toLocaleString('en-IN');
                const salaryBeforeClass = row.salary_before_penalty === 'Not Generated' ? 'text-muted' : 'fw-bold text-success';
                const salaryBeforeText = row.salary_before_penalty === 'Not Generated' ? 'Not Generated' : '₹' + Number(row.salary_before_penalty).toLocaleString('en-IN');

                accordionHtml += `
                    <tr>
                        <td><span class="fw-medium">${row.employee_code || '-'}</span></td>
                        <td>${row.employee_name || 'Unknown'}</td>
                        <td>${row.employment_type || '-'}</td>
                        <td>${row.branch_name || '-'}</td>
                        <td>${row.department_name || '-'}</td>`;

                cols.forEach(c => {
                    let val = row.components[c] !== undefined ? row.components[c] : 0;
                    accordionHtml += `<td class="text-end text-muted">₹${Number(val).toLocaleString('en-IN')}</td>`;
                });

                accordionHtml += `
                        <td class="text-end fw-bold text-dark">₹${Number(row.gross_salary || 0).toLocaleString('en-IN')}</td>
                        <td class="text-center">${row.total_working_days || 0}</td>
                        <td class="text-center">${row.total_present_combined || 0}</td>
                        <td class="text-center">${row.total_present || 0}</td>
                        <td class="text-center">${row.total_halfday || 0}</td>
                        <td class="text-center text-danger">${row.total_unpaid_halfday || 0}</td>
                        <td class="text-center">${row.sunday_work || 0}</td>
                        <td class="text-center">${row.holiday_work || 0}</td>
                        <td class="text-center">${row.leave || 0}</td>
                        <td class="text-center">${row.unpaid_leave || 0}</td>
                        <td class="text-center text-danger">${row.absent || 0}</td>
                        <td class="text-center">${row.total_weekly_off || 0}</td>
                        <td class="text-center">${row.total_holidays || 0}</td>
                        <td class="text-center text-danger">${row.total_deduction_days || 0}</td>
                        <td class="text-end text-danger">₹${Number(row.lop_deduction || 0).toLocaleString('en-IN')}</td>
                        <td class="text-end text-danger">₹${Number(row.advance_deduction || 0).toLocaleString('en-IN')}</td>
                        <td class="text-end text-danger">₹${Number(row.loan_deduction || 0).toLocaleString('en-IN')}</td>`;

                cols.forEach(c => {
                    let earnedVal = (row.earned_components && row.earned_components[c] !== undefined) 
                        ? row.earned_components[c] 
                        : (row.components[c] !== undefined ? row.components[c] : 0);
                    accordionHtml += `<td class="text-end text-muted">₹${Number(earnedVal).toLocaleString('en-IN')}</td>`;
                });
                accordionHtml += `<td class="text-end fw-bold text-dark">₹${Number(row.earned_gross_salary || 0).toLocaleString('en-IN')}</td>`;

                if (showPf) {
                    accordionHtml += `<td class="text-end text-danger">₹${Number(row.pf_deduction || 0).toLocaleString('en-IN')}</td>`;
                }
                if (showEsi) {
                    accordionHtml += `<td class="text-end text-danger">₹${Number(row.esi_deduction || 0).toLocaleString('en-IN')}</td>`;
                }

                accordionHtml += `
                        <td class="text-end text-danger">₹${Number(row.deduction_amount || 0).toLocaleString('en-IN')}</td>
                        <td class="text-end ${salaryBeforeClass}">${salaryBeforeText}</td>
                        <td class="text-center text-danger">${row.total_late_occurrences || 0}</td>
                        <td class="text-center text-success">${row.exempted_late_occurrences || 0}</td>
                        <td class="text-center text-danger">${row.penalty_eligible_lates || 0}</td>
                        <td class="text-center text-danger">${row.penalty_days || 0}</td>
                        <td class="text-end text-danger">₹${Number(row.penalty_amount || 0).toLocaleString('en-IN')}</td>
                        <td class="text-end ${salaryClass}">${salaryText}</td>`;

                if (showPf) {
                    accordionHtml += `<td class="text-end text-primary">₹${Number(row.employer_pf || 0).toLocaleString('en-IN')}</td>`;
                }
                if (showEsi) {
                    accordionHtml += `<td class="text-end text-primary">₹${Number(row.employer_esi || 0).toLocaleString('en-IN')}</td>`;
                }
                if (showEmployerCont) {
                    accordionHtml += `<td class="text-end fw-bold text-primary">₹${Number(row.total_employer_contribution || 0).toLocaleString('en-IN')}</td>`;
                }

                accordionHtml += `</tr>`;
            });

            accordionHtml += `</tbody>`;

            // Table Footer with Totals for every column
            accordionHtml += `
                                <tfoot style="position: sticky; bottom: 0; z-index: 2;">
                                    <tr>
                                        <th colspan="5" class="text-end fw-bold">Total:</th>`;

            cols.forEach(c => {
                let compTotal = totals.components && totals.components[c] !== undefined ? totals.components[c] : 0;
                accordionHtml += `<th class="text-end">₹${Number(compTotal).toLocaleString('en-IN')}</th>`;
            });

            accordionHtml += `
                                        <th class="text-end fw-bold">₹${Number(totals.gross_salary || 0).toLocaleString('en-IN')}</th>
                                        <th class="text-center">${totals.total_working_days || 0}</th>
                                        <th class="text-center">${totals.total_present_combined || 0}</th>
                                        <th class="text-center">${totals.total_present || 0}</th>
                                        <th class="text-center">${totals.total_halfday || 0}</th>
                                        <th class="text-center">${totals.sunday_work || 0}</th>
                                        <th class="text-center">${totals.holiday_work || 0}</th>
                                        <th class="text-center">${totals.leave || 0}</th>
                                        <th class="text-center">${totals.unpaid_leave || 0}</th>
                                        <th class="text-center text-danger">${totals.absent || 0}</th>
                                        <th class="text-center">${totals.total_weekly_off || 0}</th>
                                        <th class="text-center">${totals.total_holidays || 0}</th>
                                        <th class="text-center text-danger">${totals.total_deduction_days || 0}</th>
                                        <th class="text-end text-danger">₹${Number(totals.lop_deduction || 0).toLocaleString('en-IN')}</th>
                                        <th class="text-end text-danger">₹${Number(totals.advance_deduction || 0).toLocaleString('en-IN')}</th>
                                        <th class="text-end text-danger">₹${Number(totals.loan_deduction || 0).toLocaleString('en-IN')}</th>`;

            cols.forEach(c => {
                let compEarnedTotal = totals.earned_components && totals.earned_components[c] !== undefined ? totals.earned_components[c] : 0;
                accordionHtml += `<th class="text-end">₹${Number(compEarnedTotal).toLocaleString('en-IN')}</th>`;
            });
            accordionHtml += `<th class="text-end fw-bold">₹${Number(totals.earned_gross_salary || 0).toLocaleString('en-IN')}</th>`;

            if (showPf) {
                accordionHtml += `<th class="text-end text-danger">₹${Number(totals.pf_deduction || 0).toLocaleString('en-IN')}</th>`;
            }
            if (showEsi) {
                accordionHtml += `<th class="text-end text-danger">₹${Number(totals.esi_deduction || 0).toLocaleString('en-IN')}</th>`;
            }

            accordionHtml += `
                                        <th class="text-end text-danger">₹${Number(totals.deduction_amount || 0).toLocaleString('en-IN')}</th>
                                        <th class="text-end text-success fw-bold">₹${Number(totals.salary_before_penalty || 0).toLocaleString('en-IN')}</th>
                                        <th class="text-center">${totals.total_late_occurrences || 0}</th>
                                        <th class="text-center text-success">${totals.exempted_late_occurrences || 0}</th>
                                        <th class="text-center text-danger">${totals.penalty_eligible_lates || 0}</th>
                                        <th class="text-center text-danger">${totals.penalty_days || 0}</th>
                                        <th class="text-end text-danger">₹${Number(totals.penalty_amount || 0).toLocaleString('en-IN')}</th>
                                        <th class="text-end text-success fw-bold">₹${Number(totals.paid_salary || 0).toLocaleString('en-IN')}</th>`;

            if (showPf) {
                accordionHtml += `<th class="text-end text-primary">₹${Number(totals.employer_pf || 0).toLocaleString('en-IN')}</th>`;
            }
            if (showEsi) {
                accordionHtml += `<th class="text-end text-primary">₹${Number(totals.employer_esi || 0).toLocaleString('en-IN')}</th>`;
            }
            if (showEmployerCont) {
                accordionHtml += `<th class="text-end fw-bold text-primary">₹${Number(totals.total_employer_contribution || 0).toLocaleString('en-IN')}</th>`;
            }

            accordionHtml += `
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>`;

            container.append(accordionHtml);
        });
    }
});
</script>
@endpush
@endsection
