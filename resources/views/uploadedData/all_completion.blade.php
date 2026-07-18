<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @section('title', 'Uploaded Data')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<style type="text/css">
    .uc-table-wrap { width: 100%; overflow-x: auto; }
    div#trainerCompletions_wrapper .row:first-child { padding: 10px 10px 0; align-items: center; }
    div#trainerCompletions_wrapper .dt-buttons { margin-bottom: 0; }
    div#trainerCompletions_info { padding: 0; white-space: nowrap; }
    #trainerCompletions { width: 100% !important; font-size: 13px; }
    #trainerCompletions th.col-id, #trainerCompletions td.col-id {
        width: 42px; max-width: 48px; min-width: 36px;
        text-align: center; padding: 8px 4px !important; white-space: nowrap;
    }
    #trainerCompletions thead tr.filter-row th { padding: 4px 6px; background: #f8f9fa; }
    #trainerCompletions thead tr.filter-row input { width: 100%; min-width: 70px; font-size: 12px; padding: 4px 6px; height: auto; }
    #trainerCompletions thead tr.filter-row th:first-child input { min-width: 36px; padding: 4px 2px; }
    #trainerCompletions th, #trainerCompletions td { vertical-align: middle; padding: 8px 10px; }
    #trainerCompletions .trainer-line { font-weight: 600; line-height: 1.3; }
    #trainerCompletions .trainer-line small { color: #666; font-weight: 400; display: block; }
    #trainerCompletions .school-cell { white-space: normal; min-width: 140px; }
    #trainerCompletions .school-cell small { color: #666; display: block; }
    #trainerCompletions .loc-line { line-height: 1.35; white-space: normal; min-width: 100px; }
    #trainerCompletions .loc-line small { color: #666; display: block; }
    #trainerCompletions .upload-cell { text-align: left; white-space: normal; min-width: 130px; }
    #trainerCompletions .file-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 12px; }
    #trainerCompletions .file-row .file-side { display: inline-flex; align-items: center; gap: 4px; flex: 1; }
    #trainerCompletions .file-row .file-label { color: #444; white-space: nowrap; min-width: 58px; }
    #trainerCompletions .file-row a.file-link { color: #0b5cab; text-decoration: none; font-weight: 600; }
    #trainerCompletions .file-row a.file-link:hover { text-decoration: underline; }
    #trainerCompletions .file-row .file-status { flex-shrink: 0; font-size: 14px; }
    #trainerCompletions .date-cell { text-align: center; font-size: 12px; white-space: normal; min-width: 68px; }
    #trainerCompletions .dt-stack { line-height: 1.35; }
    #trainerCompletions .dt-stack small { display: block; color: #666; font-size: 11px; }
    #trainerCompletions .actions-cell { text-align: center; min-width: 90px; }
    #trainerCompletions .paid-cell { text-align: center; white-space: nowrap; }
</style>
<body>
<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Trainer's Completions</h2>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success"><p>{{ $message }}</p></div>
    @endif
    <div class="card-body uc-table-wrap">
        <table class="table table-bordered" id="trainerCompletions">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>Completions</th>
                    <th>Location</th>
                    <th>School Name</th>
                    <th>School Code</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Paid Status</th>
                    <th>Rejection Note</th>
                    <th>Approve Completion</th>
                </tr>
            </thead>
            <tbody>
                @foreach($completion as $data)
                    @php
                        $trainerName = '';
                        $trainerCode = '';
                        $uploadedBy = '';
                        $schoolCode = '';
                        $paidStatus = null;
                        foreach ($user as $d_data) {
                            if ($d_data['id'] == $data['user_id']) {
                                $trainerName = $d_data['instructor_name'];
                                $trainerCode = $d_data['instructor_code'];
                            }
                            if ($d_data['id'] == $data['uploaded_user']) {
                                $uploadedBy = $d_data['instructor_name'];
                            }
                        }
                        foreach ($schools as $school) {
                            if ($school['id'] == $data['school_id']) {
                                $schoolCode = $school['school_code'] ?? '';
                                $paidStatus = $school['paid_status'] ?? 0;
                            }
                        }
                        $blockName = $data['block'] ?? $data['bloack'] ?? '—';
                    @endphp
                    <tr>
                        <td class="col-id">{{ $data['id'] }}</td>
                        <td>
                            <div class="trainer-line">
                                {{ $trainerName ?: '—' }}@if($trainerCode) - {{ $trainerCode }}@endif
                            </div>
                        </td>
                        <td>{{ $uploadedBy ?: '—' }}</td>
                        <td class="upload-cell">
                            <div class="file-row">
                                <span class="file-side">
                                    <span class="file-label">Completions</span>
                                    @if($data['completion_file'])
                                        <a href="{{ asset('storage/completion/'.$data['completion_file']) }}" target="_blank" class="file-link complete-data">View</a>
                                        @if($data['status'] == 0)
                                        <a href="javascript:void(0)" data-url="{{ route('completion-remove', [$data['id'], $data['school_id']]) }}" class="btn completionVideo"><i class="bi bi-x-circle-fill remove"></i></a>
                                        @endif
                                    @endif
                                </span>
                                <span class="file-status">
                                    @if($data['completion_file'])
                                        <i class="bi-check-circle-fill nav-icn success-icon"></i>
                                    @else
                                        <i class="bi bi-x-circle-fill remove"></i>
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="loc-line">
                                {{ $data['district'] ?: '—' }}
                                <small>{{ $blockName }}</small>
                            </div>
                        </td>
                        <td class="school-cell">{{ $data['school_name'] }}</td>
                        <td>{{ $schoolCode ?: '—' }}</td>
                        <td class="date-cell">
                            <div class="dt-stack">
                                @php $shownAt = $data['updated_at'] ?? $data['created_at']; @endphp
                                {{ date('d/m/y', strtotime($shownAt)) }}
                                <small>{{ date('g:i A', strtotime($shownAt)) }}</small>
                            </div>
                        </td>
                        <td class="date-cell">{{ $data['route_date'] ?: '—' }}</td>
                        <td class="paid-cell">
                            @if($paidStatus == 1)
                                <span class="paid">Paid</span>
                            @else
                                <span class="unpaid">Pending</span>
                            @endif
                        </td>
                        <td class="actions-cell">
                            <form action="{{ route('completion-note', $data['id']) }}" method="post">
                                @csrf
                                <a href="" data-toggle="modal" data-target="#demoModal{{ $data['id'] }}" class="send_btn">Add</a>
                                <div class="modal fade note-model" id="demoModal{{ $data['id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" style="color: #fff;">Reason to Reject this completion</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="col-md-10">
                                                    <textarea rows="5" class="form-control" placeholder="Write here" name="completion_note" required>{{ $data['completion_note'] }}</textarea>
                                                    <input type="hidden" name="id" value="{{ $data['id'] }}">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col">
                                                    <input class="ml-2 checkExtraAmount" name="emergency_approved" type="checkbox" {{ $data['emergency_approved'] == 1 ? 'checked' : '' }}>
                                                    <label for="inputEmail4">Emergency Approved</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button class="send_btn" type="submit">Send</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @if($data['completion_note'] != null)
                                <br><span class="not-started">Rejected</span>
                            @endif
                        </td>
                        <td>
                            <label class="container-ck12">
                                @if($data['completion_note'] != null && $data['emergency_approved'] == 0)
                                    <a href="{{ url('getData/'.$data['user_id']) }}" target="_blank"><span class="pending-data">Upload Correct UC</span></a>
                                @elseif($data['emergency_approved'] == 1 && $data['completion_note'] != null)
                                    <span class="approve">Emergency Approved</span>
                                @elseif($data['status'] == 1 && $data['emergency_approved'] == 0)
                                    <span class="approve">Approved</span>
                                @else
                                    <span class="disapproved">Pending</span>
                                @endif
                                @if($data['completion_note'] == null)
                                    <input class="completion-status approve-button" data-id="{{ $data['id'] }}" type="checkbox" {{ $data['status'] == 1 ? 'checked' : '' }}>
                                    <div class="checkmark"></div>
                                @endif
                            </label>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="col-id">#</th>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>Completions</th>
                    <th>Location</th>
                    <th>School Name</th>
                    <th>School Code</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Paid Status</th>
                    <th>Rejection Note</th>
                    <th>Approve Completion</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript">
(function () {
    var $table = $('#trainerCompletions');
    var $filterRow = $table.find('tfoot tr').clone().addClass('filter-row');
    $filterRow.find('th').each(function () {
        $(this).html('<input type="text" class="form-control" placeholder="' + $(this).text() + '" />');
    });
    $table.find('thead').append($filterRow);
    $table.find('tfoot').remove();

    var trainerCompletions = $table.DataTable({
        ordering: false,
        dom: "<'row'<'col-sm-3'B><'col-sm-4'i><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'l><'col-sm-7'p>>",
        pageLength: 100,
        stateSave: true,
        autoWidth: false,
        buttons: [{ extend: 'excel', text: 'Download' }]
    });

    trainerCompletions.columns().every(function () {
        var that = this;
        $('input', $table.find('thead tr.filter-row th').eq(this.index())).on('keyup change clear', function () {
            if (that.search() !== this.value) that.search(this.value).draw();
        });
    });

    $('.completion-status').change(function () {
        $.ajax({
            type: 'GET', dataType: 'json', url: '/completion-status',
            data: { completion_status: $(this).prop('checked') ? 1 : 0, completion_id: $(this).data('id') },
            success: function () {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Approve Completion status are changed !', showConfirmButton: false, timer: 2000 });
            },
            error: function () {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Somethig Wrong Please Check!', showConfirmButton: false, timer: 4000 });
            }
        });
    });

    $('body').on('click', '.completionVideo', function () {
        var userURL = $(this).data('url');
        var trObj = $(this);
        Swal.fire({ title: 'Are you sure?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete it!' })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({ url: userURL, type: 'GET', dataType: 'json', success: function () {
                        trObj.parents('tr').remove();
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'It was succesfully deleted! !', showConfirmButton: false, timer: 2000 });
                    }});
                }
            });
    });

    $('.complete-data').click(function () { $(this).addClass('visited'); });
})();
</script>
