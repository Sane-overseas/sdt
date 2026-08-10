@extends('layouts.app')

@section('content')
<style type="text/css">
    .dataTables_wrapper .dataTables_info {
        clear: none;
        margin-left: 10px;
        padding-top: 0;
    }
    .location-cell .district-name {
        font-weight: 600;
    }
    .location-cell .block-name {
        font-size: 12px;
        color: #6c757d;
    }
</style>
<div class="container mt-3">
    <div class="row margin-tb align-items-center mb-3">
        <div class="col-md-8">
            <h2 class="heading mb-1">School Requests</h2>
            <p class="text-muted mb-0 small">Trainer school requests — pending, approved, and rejected stay on this page.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light">
            <strong><i class="fas fa-filter"></i> Search & Filter</strong>
        </div>
        <div class="card-body py-3">
            <div class="row align-items-end">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold mb-1" for="districtFilter">District</label>
                    <select id="districtFilter" class="form-control">
                        <option value="">All Districts</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->district }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold mb-1" for="statusFilter">Status</label>
                    <select id="statusFilter" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-block">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered" id="schoolRequestsTable">
                <thead>
                    <tr>
                        <th>Trainer</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>School</th>
                        <th>Hrs</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th style="min-width:200px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $row)
                        @php
                            $school = $schools[$row->school_name] ?? null;
                            $status = $row->approval_status ?? 'approved';
                            $districtId = $row->district ?: ($school->district_id ?? '');
                            $districtName = optional($districts->get($row->district))->district
                                ?? optional($districts->get($school->district_id ?? null))->district
                                ?? '—';
                            $blockName = $row->block ?: '—';
                        @endphp
                        <tr data-district="{{ $districtId }}" data-status="{{ ucfirst($status) }}">
                            <td>{{ $row->user->instructor_name ?? '—' }}</td>
                            <td>{{ $row->user->email ?? '—' }}</td>
                            <td class="location-cell">
                                <div class="district-name">{{ $districtName }}</div>
                                <div class="block-name">Block: {{ $blockName }}</div>
                            </td>
                            <td>{{ $school->school_name ?? ('#'.$row->school_name) }}</td>
                            <td>
                                {{ $school && $school->training_hours !== null ? number_format((float)$school->training_hours, 1).' total' : '—' }}
                                @if($school && $school->daily_training_hours !== null)
                                    / {{ number_format((float)$school->daily_training_hours, 1) }}/day
                                @endif
                            </td>
                            <td>{{ $row->created_at?->format('d-m-Y H:i') }}</td>
                            <td>
                                @if($status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                    @if($row->approval_note)
                                        <div class="small text-muted mt-1">{{ $row->approval_note }}</div>
                                    @endif
                                @else
                                    <span class="badge badge-success">Approved</span>
                                    @if($row->approved_at)
                                        <div class="small text-muted mt-1">{{ $row->approved_at->format('d-m-Y H:i') }}</div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($status === 'pending')
                                    <form method="POST" action="{{ route('admin.school-requests.approve', $row->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.school-requests.reject', $row->id) }}" class="d-inline reject-form">
                                        @csrf
                                        <input type="hidden" name="note" class="reject-note" value="">
                                        <button type="button" class="btn btn-sm btn-danger btn-reject">Reject</button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted">No school requests yet.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th>Trainer</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>School</th>
                        <th>Hrs</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script>
$(function () {
    $(document).on('click', '.btn-reject', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $form = $(this).closest('form.reject-form');
        if (!$form.length) {
            $form = $(this).closest('form');
        }

        function submitReject(note) {
            $form.find('.reject-note').val(note || '');
            $form.trigger('submit');
        }

        if (typeof Swal !== 'undefined' && Swal.fire) {
            Swal.fire({
                title: 'Reject this request?',
                input: 'text',
                inputPlaceholder: 'Optional note for trainer',
                showCancelButton: true,
                confirmButtonText: 'Reject',
                confirmButtonColor: '#d33'
            }).then(function (result) {
                if (result.isConfirmed) {
                    submitReject(result.value || '');
                }
            });
            return;
        }

        if (window.confirm('Reject this school request?')) {
            var note = window.prompt('Optional note for trainer:', '') || '';
            submitReject(note);
        }
    });

    if (!$('#schoolRequestsTable').length || !$.fn.DataTable) {
        return;
    }

    var $empty = $('#schoolRequestsTable tbody td[colspan]');
    if ($empty.length) {
        $empty.closest('tr').remove();
    }

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'schoolRequestsTable') {
            return true;
        }
        var row = settings.aoData[dataIndex].nTr;
        var district = $('#districtFilter').val();
        var status = $('#statusFilter').val();
        if (district && String($(row).data('district')) !== String(district)) {
            return false;
        }
        if (status && String($(row).data('status')) !== String(status)) {
            return false;
        }
        return true;
    });

    var table = $('#schoolRequestsTable').DataTable({
        ordering: false,
        pageLength: 25,
        order: [],
        stateSave: false,
        dom: "<'row'<'col-sm-1'B><'col-sm-5'i><'col-sm-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'><'col-sm-7'p>>",
        buttons: [
            {
                extend: 'excel',
                text: 'Download'
            }
        ]
    });

    $('#schoolRequestsTable tfoot th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Filter ' + title + '" />');
    });

    $('#schoolRequestsTable tfoot').insertAfter($('#schoolRequestsTable thead'));

    table.columns().every(function () {
        var that = this;
        $('input', this.footer()).on('keyup change', function () {
            if (that.search() !== this.value) {
                that.search(this.value).draw();
            }
        });
    });

    $('#districtFilter, #statusFilter').on('change', function () {
        table.draw();
    });

    $('#clearFilters').on('click', function () {
        $('#districtFilter').val('');
        $('#statusFilter').val('');
        table.search('');
        table.columns().every(function () {
            this.search('');
            $('input', this.footer()).val('');
        });
        table.draw();
    });
});
</script>
@endsection
