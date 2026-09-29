@extends('layouts.app')
@section('title', 'Trainer Detailed & Bank Report')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .report-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
    }
    .report-header {
        padding: 18px 24px;
        background: #004857;
        color: #fff;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .report-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #fff;
    }
    .filter-box {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
    }
    .table-container {
        width: 100%;
        overflow-x: auto;
    }
    .detailed-table {
        width: 100%;
        margin-bottom: 0;
        font-size: 13px;
        border-collapse: collapse;
    }
    .detailed-table th {
        background: #003640;
        color: #fff;
        font-weight: 600;
        padding: 10px 12px;
        vertical-align: middle;
        white-space: nowrap;
        border: 1px solid #002b33;
        font-size: 12.5px;
    }
    .detailed-table td {
        padding: 10px 12px;
        vertical-align: middle;
        border: 1px solid #cbd5e1;
        background: #fff;
    }
    .trainer-merged-cell {
        background: #ffffff !important;
        vertical-align: middle !important;
        border-right: 2px solid #cbd5e1 !important;
    }
    .detailed-table tbody tr.trainer-group-odd td:not(.trainer-merged-cell) {
        background-color: #f8fafc;
    }
    .detailed-table tbody tr.trainer-group-even td:not(.trainer-merged-cell) {
        background-color: #ffffff;
    }
    .detailed-table tbody tr:hover td:not(.trainer-merged-cell) {
        background-color: #f1f5f9;
    }
    tr.trainer-row-first td {
        border-top: 2px solid #94a3b8;
    }
    .trainer-info-cell {
        min-width: 180px;
    }
    .trainer-title {
        font-weight: 700;
        color: #004857;
        font-size: 13.5px;
        line-height: 1.3;
    }
    .trainer-code-badge {
        display: inline-block;
        background: #e2e8f0;
        color: #334155;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
        margin-top: 3px;
    }
    .contact-info {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
        line-height: 1.4;
    }
    .contact-info i {
        width: 14px;
        color: #004857;
    }
    .bank-info-cell {
        min-width: 200px;
        font-size: 12px;
        line-height: 1.45;
    }
    .bank-item {
        margin-bottom: 2px;
    }
    .bank-label {
        color: #64748b;
        font-weight: 500;
    }
    .bank-val {
        font-weight: 600;
        color: #1e293b;
    }
    .school-info-cell {
        min-width: 170px;
    }
    .school-title {
        font-weight: 600;
        color: #0f172a;
        line-height: 1.35;
    }
    .school-code-sub {
        font-size: 11px;
        color: #64748b;
    }
    .loc-sub {
        font-size: 11.5px;
        color: #475569;
        margin-top: 3px;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-complete {
        background: #dcfce7;
        color: #166534;
    }
    .status-ongoing {
        background: #fef9c3;
        color: #854d0e;
    }
    .status-pending {
        background: #fee2e2;
        color: #991b1b;
    }
    .claim-badge-claimed {
        background: #e0e7ff;
        color: #3730a3;
    }
    .claim-badge-unclaimed {
        background: #f1f5f9;
        color: #64748b;
    }
    .claim-badge-paid {
        background: #dcfce7;
        color: #166534;
    }
    .date-text {
        font-size: 11px;
        color: #64748b;
        margin-top: 3px;
        display: block;
    }
    .remark-cell {
        min-width: 160px;
        max-width: 240px;
    }
    .remark-text-box {
        font-size: 11.5px;
        color: #334155;
        background: #f8fafc;
        padding: 6px 8px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
        max-height: 70px;
        overflow-y: auto;
        line-height: 1.35;
        white-space: pre-line;
    }
    .btn-edit-remark {
        margin-top: 5px;
        padding: 2px 7px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
        background: #004857;
        color: #fff !important;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-edit-remark:hover {
        background: #003640;
    }
    .btn-excel {
        background: #107c41;
        color: #fff !important;
        font-weight: 600;
        font-size: 13px;
        padding: 7px 14px;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        text-decoration: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        transition: background 0.2s;
    }
    .btn-excel:hover {
        background: #0b5a2f;
        color: #fff !important;
    }
    .pagination {
        margin-bottom: 0;
        gap: 3px;
        display: flex;
        flex-wrap: wrap;
    }
    .pagination .page-item .page-link {
        color: #004857;
        border-radius: 4px;
        font-size: 13px;
        padding: 5px 10px;
        border: 1px solid #cbd5e1;
    }
    .pagination .page-item.active .page-link {
        background-color: #004857;
        border-color: #004857;
        color: #fff;
    }
    .pagination .page-item.disabled .page-link {
        color: #94a3b8;
        background-color: #f8fafc;
    }
    .pagination svg {
        display: none !important;
    }
</style>

<div class="container-fluid px-4 py-3">
    <div class="report-card">
        <div class="report-header">
            <div>
                <h3><i class="bi bi-file-earmark-spreadsheet mr-2"></i> Trainer Detailed &amp; Bank Report</h3>
                <small class="text-white-50">Combined report from Trainers Profile (Bank/KYC) and Assigned Schools</small>
            </div>
            <div>
                <a href="{{ route('trainers-reporting.detailed-report.export', request()->query()) }}" class="btn-excel">
                    <i class="bi bi-file-earmark-excel-fill"></i> Export to Excel (.xlsx)
                </a>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="filter-box">
            <form action="{{ route('trainers-reporting.detailed-report') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="font-weight-bold text-muted small mb-1">District</label>
                        <select name="district_id" class="form-control form-control-sm">
                            <option value="">All Districts</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}" {{ (string)$districtFilter === (string)$d->id ? 'selected' : '' }}>
                                    {{ $d->district }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="font-weight-bold text-muted small mb-1">Trainer</label>
                        <select name="trainer_id" class="form-control form-control-sm">
                            <option value="">All Trainers</option>
                            @foreach($trainers as $t)
                                <option value="{{ $t->id }}" {{ (string)$trainerFilter === (string)$t->id ? 'selected' : '' }}>
                                    {{ $t->instructor_name }} {{ $t->instructor_code ? '('.$t->instructor_code.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="font-weight-bold text-muted small mb-1">Training Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Statuses</option>
                            <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Complete</option>
                            <option value="ongoing" {{ $statusFilter === 'ongoing' ? 'selected' : '' }}>In Progress / Ongoing</option>
                            <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending / Not Started</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="font-weight-bold text-muted small mb-1">Claim Status</label>
                        <select name="claim_status" class="form-control form-control-sm">
                            <option value="">All Claim Status</option>
                            <option value="claimed" {{ $claimFilter === 'claimed' ? 'selected' : '' }}>Claimed</option>
                            <option value="unclaimed" {{ $claimFilter === 'unclaimed' ? 'selected' : '' }}>Not Claimed</option>
                            <option value="paid" {{ $claimFilter === 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="font-weight-bold text-muted small mb-1">Payment Status</label>
                        <select name="payment_status" class="form-control form-control-sm">
                            <option value="">All Payment</option>
                            <option value="paid" {{ $paymentFilter === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ $paymentFilter === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="font-weight-bold text-muted small mb-1">Search Keyword</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Trainer, School, A/C, PAN..." value="{{ $search }}">
                    </div>

                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="font-weight-bold text-muted small mb-1">Per Page</label>
                        <select name="per_page" class="form-control form-control-sm">
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per page</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 per page</option>
                            <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200 per page</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-sm-6 mb-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary px-3 mr-2" style="background:#004857; border-color:#004857;">
                            <i class="bi bi-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('trainers-reporting.detailed-report') }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Content --}}
        <div class="table-container">
            <table class="table detailed-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">#</th>
                        <th>Name (Trainer / ID)</th>
                        <th>Email &amp; Phone Number</th>
                        <th>Bank &amp; KYC Details</th>
                        <th>District / Block</th>
                        <th>School Name</th>
                        <th style="text-align: center;">Training Status</th>
                        <th style="text-align: center;">Claim Status</th>
                        <th style="text-align: center;">Payment Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedTrainers = $rows->getCollection()->groupBy('user_id');
                    @endphp
                    @forelse($groupedTrainers as $userId => $trainerSchools)
                        @php
                            $firstRow = $trainerSchools->first();
                            $schoolCount = count($trainerSchools);
                            $districtName = $firstRow->school_district_name ?: ($firstRow->assignment_district_name ?: '—');
                            $blockName = $firstRow->assignment_block ?: ($firstRow->school_block ?: '—');
                            $isEvenGroup = ($loop->iteration % 2 === 0);
                            $groupClass = $isEvenGroup ? 'trainer-group-even' : 'trainer-group-odd';
                        @endphp

                        @foreach($trainerSchools as $sIndex => $row)
                            @php
                                $trainingStatus = 'Pending';
                                $statusClass = 'status-pending';
                                $statusIcon = 'bi-clock';
                                if ((int)$row->assignment_status === 1 || (int)($row->uc_submitted ?? 0) === 1) {
                                    $trainingStatus = 'Complete';
                                    $statusClass = 'status-complete';
                                    $statusIcon = 'bi-check-circle-fill';
                                } elseif (!empty($row->assignment_end_date)) {
                                    $trainingStatus = 'In Progress';
                                    $statusClass = 'status-ongoing';
                                    $statusIcon = 'bi-arrow-repeat';
                                }

                                $claimStatus = 'Not Claimed';
                                $claimClass = 'claim-badge-unclaimed';
                                if ((int)$row->assignment_claim_status === 1) {
                                    $claimStatus = 'Claimed';
                                    $claimClass = 'claim-badge-claimed';
                                } elseif ((int)$row->assignment_claim_status === 2 || (int)$row->assignment_paid_status === 1) {
                                    $claimStatus = 'Paid';
                                    $claimClass = 'claim-badge-paid';
                                }

                                $isPaid = ((int)$row->assignment_paid_status === 1);
                            @endphp
                            <tr class="{{ $groupClass }} {{ $sIndex === 0 ? 'trainer-row-first' : '' }}">
                                @if($sIndex === 0)
                                    <td rowspan="{{ $schoolCount }}" class="trainer-merged-cell" style="text-align: center; font-weight: 700; color: #475569; width: 45px;">
                                        {{ $loop->parent->iteration }}
                                    </td>
                                    <td rowspan="{{ $schoolCount }}" class="trainer-merged-cell trainer-info-cell">
                                        <div class="trainer-title">{{ $firstRow->trainer_name ?: '—' }}</div>
                                        @if($firstRow->trainer_code)
                                            <span class="trainer-code-badge">{{ $firstRow->trainer_code }}</span>
                                        @endif
                                    </td>
                                    <td rowspan="{{ $schoolCount }}" class="trainer-merged-cell">
                                        <div class="contact-info">
                                            <div><i class="bi bi-envelope"></i> {{ $firstRow->trainer_email ?: '—' }}</div>
                                            <div class="mt-1"><i class="bi bi-telephone"></i> <strong>{{ $firstRow->trainer_phone ?: '—' }}</strong></div>
                                        </div>
                                    </td>
                                    <td rowspan="{{ $schoolCount }}" class="trainer-merged-cell bank-info-cell">
                                        <div class="bank-item">
                                            <span class="bank-label">Holder:</span>
                                            <span class="bank-val">{{ $firstRow->account_holder_name ?: '—' }}</span>
                                        </div>
                                        <div class="bank-item">
                                            <span class="bank-label">Bank:</span>
                                            <span class="bank-val">{{ $firstRow->bank_name ?: '—' }}</span>
                                        </div>
                                        <div class="bank-item">
                                            <span class="bank-label">A/C No:</span>
                                            <span class="bank-val">{{ $firstRow->account_number ?: '—' }}</span>
                                        </div>
                                        <div class="bank-item">
                                            <span class="bank-label">IFSC:</span>
                                            <span class="bank-val text-uppercase">{{ $firstRow->ifsc_code ?: '—' }}</span>
                                        </div>
                                        <div class="bank-item">
                                            <span class="bank-label">PAN:</span>
                                            <span class="bank-val text-uppercase">{{ $firstRow->pan_number ?: '—' }}</span>
                                        </div>
                                    </td>
                                    <td rowspan="{{ $schoolCount }}" class="trainer-merged-cell">
                                        <div style="font-weight: 600; color: #1e293b;">{{ $districtName }}</div>
                                        <div class="loc-sub"><i class="bi bi-geo-alt"></i> {{ $blockName }}</div>
                                    </td>
                                @endif

                                <td class="school-info-cell">
                                    <div class="school-title">{{ $row->school_title }}</div>
                                    @if($row->school_code_value)
                                        <span class="school-code-sub">Code: {{ $row->school_code_value }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-badge {{ $statusClass }}">
                                        <i class="bi {{ $statusIcon }}"></i> {{ $trainingStatus }}
                                    </span>
                                    @if(!empty($row->assignment_route_date))
                                        <span class="date-text">{{ $row->assignment_route_date }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-badge {{ $claimClass }}">
                                        {{ $claimStatus }}
                                    </span>
                                    @if($row->assignment_claimed_at)
                                        <span class="date-text">Claimed: {{ date('d/m/Y', strtotime($row->assignment_claimed_at)) }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($isPaid)
                                        <span class="status-badge status-complete">
                                            <i class="bi bi-check2-circle"></i> Paid
                                        </span>
                                        @if($row->paid_date)
                                            <span class="date-text">Date: {{ date('d/m/Y', strtotime($row->paid_date)) }}</span>
                                        @endif
                                    @else
                                        <span class="status-badge status-pending">
                                            <i class="bi bi-hourglass-split"></i> Unpaid
                                        </span>
                                    @endif
                                </td>
                                <td class="remark-cell">
                                    <div class="remark-text-box" id="remark-text-{{ $row->assignment_id }}">{{ $row->assignment_remark ?: 'No remarks added' }}</div>
                                    <button type="button" class="btn-edit-remark" 
                                        onclick="openRemarkModal({{ $row->assignment_id }}, '{{ addslashes($row->school_title) }}', '{{ addslashes($row->trainer_name) }}')">
                                        <i class="bi bi-pencil-square"></i> Add / Edit Remark
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 28px; display: block; margin-bottom: 6px;"></i>
                                No trainer assignment records found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rows->hasPages())
            <div class="p-3 d-flex justify-content-between align-items-center border-top">
                <div class="text-muted small">
                    Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ $rows->total() }} records
                </div>
                <div>
                    {{ $rows->onEachSide(1)->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Remark Modal --}}
<div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
            <div class="modal-header" style="background: #004857; color: #fff;">
                <h5 class="modal-title font-weight-bold" style="font-size: 16px;">
                    <i class="bi bi-chat-left-text mr-1"></i> Update Remark
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="saveRemarkForm" onsubmit="submitRemarkForm(event)">
                @csrf
                <input type="hidden" id="modal_assignment_id" name="assignment_id">
                <div class="modal-body p-4">
                    <div class="mb-3 p-2 bg-light rounded" style="font-size: 12.5px; border: 1px solid #e2e8f0;">
                        <div><strong>Trainer:</strong> <span id="modal_trainer_name">—</span></div>
                        <div><strong>School:</strong> <span id="modal_school_name">—</span></div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Remark Text</label>
                        <textarea rows="4" id="modal_remark_text" name="remark" class="form-control" placeholder="Enter remarks here..." required></textarea>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="append" value="1" id="modal_append_check">
                        <label class="form-check-label text-muted small" for="modal_append_check">
                            Append to previous remarks (with today's date) instead of overwriting
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnSaveRemark" style="background: #004857; border-color: #004857;">
                        <i class="bi bi-check-lg"></i> Save Remark
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRemarkModal(assignmentId, schoolName, trainerName) {
    $('#modal_assignment_id').val(assignmentId);
    $('#modal_trainer_name').text(trainerName);
    $('#modal_school_name').text(schoolName);
    
    var currentRemark = $('#remark-text-' + assignmentId).text();
    if (currentRemark === 'No remarks added') {
        currentRemark = '';
    }
    $('#modal_remark_text').val(currentRemark);
    $('#modal_append_check').prop('checked', false);
    
    $('#remarkModal').modal('show');
}

function submitRemarkForm(e) {
    e.preventDefault();
    var assignmentId = $('#modal_assignment_id').val();
    var remarkText = $('#modal_remark_text').val();
    var append = $('#modal_append_check').is(':checked') ? 1 : 0;
    var $btn = $('#btnSaveRemark');
    
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    
    $.ajax({
        url: '/trainers-reporting/save-remark/' + assignmentId,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            remark: remarkText,
            append: append
        },
        dataType: 'json',
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Save Remark');
            $('#remarkModal').modal('hide');
            
            var displayRemark = res.remark ? res.remark : 'No remarks added';
            $('#remark-text-' + assignmentId).text(displayRemark);
            
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Remark saved on assignment successfully!',
                showConfirmButton: false,
                timer: 2000
            });
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Save Remark');
            var errMsg = 'Failed to save remark. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errMsg
            });
        }
    });
}
</script>
@endsection
