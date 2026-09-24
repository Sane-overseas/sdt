<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<style type="text/css">
    .attendance-table-wrap { width: 100%; overflow-x: auto; }
    div#trainerAttendances_wrapper .row:first-child { padding: 10px 10px 0; align-items: center; }
    div#trainerAttendances_wrapper .dt-buttons { margin-bottom: 0; }
    div#trainerAttendances_info { padding: 0; white-space: nowrap; }
    a.btn.deleteAttendance {
        padding: 1px 5px; background: #ff0707; color: #fff;
        width: 85px; margin-top: 6px; display: inline-block;
    }
    #trainerAttendances { width: 100% !important; font-size: 13px; }
    #trainerAttendances th.col-id, #trainerAttendances td.col-id {
        width: 42px; max-width: 48px; min-width: 36px;
        text-align: center; padding: 8px 4px !important; white-space: nowrap;
    }
    #trainerAttendances thead tr.filter-row th { padding: 4px 6px; background: #f8f9fa; }
    #trainerAttendances thead tr.filter-row input { width: 100%; min-width: 70px; font-size: 12px; padding: 4px 6px; height: auto; }
    #trainerAttendances thead tr.filter-row th:first-child input { min-width: 36px; padding: 4px 2px; }
    #trainerAttendances th, #trainerAttendances td { vertical-align: middle; padding: 8px 10px; }
    #trainerAttendances .trainer-line { font-weight: 600; line-height: 1.3; }
    #trainerAttendances .trainer-line small { color: #666; font-weight: 400; display: block; }
    #trainerAttendances .school-cell { white-space: normal; min-width: 140px; }
    #trainerAttendances .loc-line { line-height: 1.35; white-space: normal; min-width: 100px; }
    #trainerAttendances .loc-line small { color: #666; display: block; }
    #trainerAttendances .upload-cell { text-align: left; white-space: normal; min-width: 150px; }
    #trainerAttendances .file-stack { display: flex; flex-direction: column; gap: 5px; }
    #trainerAttendances .file-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 12px; }
    #trainerAttendances .file-side { display: inline-flex; align-items: center; gap: 6px; flex: 1; }
    #trainerAttendances .file-label { color: #444; white-space: nowrap; font-weight: 500; }
    #trainerAttendances a.file-link { color: #0b5cab; text-decoration: none; font-weight: 600; }
    #trainerAttendances a.file-link:hover { text-decoration: underline; }
    #trainerAttendances .file-status { flex-shrink: 0; font-size: 14px; }
    #trainerAttendances .date-cell { text-align: center; font-size: 12px; white-space: normal; min-width: 68px; }
    #trainerAttendances .dt-stack { line-height: 1.35; }
    #trainerAttendances .dt-stack small { display: block; color: #666; font-size: 11px; }
    #trainerAttendances .actions-cell { text-align: center; min-width: 90px; }
</style>
<body>
<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading">Trainer's Attendance Sheets</h2>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success"><p>{{ $message }}</p></div>
    @endif
    <div class="card-body attendance-table-wrap">
        <table class="table table-bordered" id="trainerAttendances">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>School Name</th>
                    <th>Location</th>
                    <th>Attendance Sheet</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Rejection Note</th>
                    <th>Approve Attendance</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($attendances ?? []) as $item)
                    @php
                        $trainerName = '';
                        $trainerCode = '';
                        $uploadedBy = '';
                        foreach ($user as $d_data) {
                            if ($d_data['id'] == $item['user_id']) {
                                $trainerName = $d_data['instructor_name'];
                                $trainerCode = $d_data['instructor_code'];
                            }
                            if ($d_data['id'] == $item['uploaded_user']) {
                                $uploadedBy = $d_data['instructor_name'];
                            }
                        }
                        $blockName = $item['block'] ?? $item['bloack'] ?? '—';
                        $files = [];
                        if (!empty($item['attendance_files'])) {
                            $files = is_array($item['attendance_files']) ? $item['attendance_files'] : json_decode($item['attendance_files'], true);
                        }
                        if (empty($files) && !empty($item['attendance_file'])) {
                            $files = [$item['attendance_file']];
                        }
                    @endphp
                    <tr>
                        <td class="col-id">{{ $item['id'] }}</td>
                        <td>
                            <div class="trainer-line">
                                {{ $trainerName ?: '—' }}@if($trainerCode) - {{ $trainerCode }}@endif
                            </div>
                        </td>
                        <td>{{ $uploadedBy ?: '—' }}</td>
                        <td class="school-cell">{{ $item['school_name'] }}</td>
                        <td>
                            <div class="loc-line">
                                {{ $item['district'] ?: '—' }}
                                <small>{{ $blockName }}</small>
                            </div>
                        </td>
                        <td class="upload-cell">
                            <div class="file-stack">
                                @if(!empty($files))
                                    @foreach($files as $idx => $attFile)
                                        <div class="file-row">
                                            <span class="file-side">
                                                <span class="file-label">Page/File {{ $idx + 1 }}:</span>
                                                <a href="{{ media_url('attendances', $attFile) }}" target="_blank" class="file-link complete-data">View</a>
                                            </span>
                                            <span class="file-status">
                                                <i class="bi-check-circle-fill nav-icn success-icon"></i>
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($item['status'] != 1)
                                        <div>
                                            <a href="javascript:void(0)" data-url="{{ route('delete-attendance', $item['id']) }}" class="btn deleteAttendance">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted"><i class="bi bi-x-circle-fill remove"></i> No File</span>
                                @endif
                            </div>
                        </td>
                        <td class="date-cell">
                            <div class="dt-stack">
                                @php $shownAt = $item['created_at'] ?? $item['created_date'] ?? $item['updated_at']; @endphp
                                {{ date('d/m/y', strtotime($shownAt)) }}
                                <small>{{ date('g:i A', strtotime($shownAt)) }}</small>
                            </div>
                        </td>
                        <td class="date-cell">{{ $item['route_date'] ?: '—' }}</td>
                        <td class="actions-cell">
                            <form action="{{ route('attendance-note') }}" method="post">
                                @csrf
                                <a href="" data-toggle="modal" data-target="#demoModalAttendance{{ $item['id'] }}" class="send_btn">Add</a>
                                <div class="modal fade note-model" id="demoModalAttendance{{ $item['id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" style="color: #fff;">Reason to Reject this Attendance</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="col-md-10">
                                                    <textarea rows="5" class="form-control" placeholder="Write here" name="attendance_note" required>{{ $item['attendance_note'] }}</textarea>
                                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
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
                            @if($item['attendance_note'] != null)
                                <br><span class="not-started">Rejected</span>
                            @endif
                        </td>
                        <td>
                            <div class="approval-cell-wrap text-center">
                                @if($item['status'] == 1)
                                    <span class="badge badge-success py-1 px-2" style="background:#28a745; color:#fff; font-size: 11px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;"><i class="bi bi-check-circle-fill"></i> Approved</span>
                                @elseif($item['attendance_note'] != null)
                                    <span class="badge badge-danger py-1 px-2" style="background:#dc3545; color:#fff; font-size: 11px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center" style="gap: 2px;">
                                        <span class="badge badge-warning py-1 px-2" style="background:#ffc107; color:#000; font-size: 10px; font-weight: 600; border-radius: 3px; white-space: nowrap;">Pending</span>
                                        <button type="button" class="btn btn-sm btn-success btn-approve-attendance" data-id="{{ $item['id'] }}" style="padding: 2px 8px; font-size: 11px; font-weight: 600; border-radius: 4px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; gap: 4px; line-height: 1.2;">
                                            <i class="bi bi-check-lg" style="font-size: 12px;"></i> <span>Approve</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="col-id">#</th>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>School Name</th>
                    <th>Location</th>
                    <th>Attendance Sheet</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Rejection Note</th>
                    <th>Approve Attendance</th>
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
<script src="{{ asset('js/datatables-excel-export.js') }}"></script>
<script type="text/javascript">
(function () {
    var $table = $('#trainerAttendances');
    var $filterRow = $table.find('tfoot tr').clone().addClass('filter-row');
    $filterRow.find('th').each(function () {
        $(this).html('<input type="text" class="form-control" placeholder="' + $(this).text() + '" />');
    });
    $table.find('thead').append($filterRow);
    $table.find('tfoot').remove();

    var trainerAttendances = $table.DataTable({
        ordering: false,
        orderCellsTop: true,
        dom: "<'row'<'col-sm-3'B><'col-sm-4'i><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'l><'col-sm-7'p>>",
        pageLength: 100,
        stateSave: true,
        autoWidth: false,
        buttons: [uploadedDataExcelButton($table, 'trainer-attendances')]
    });

    trainerAttendances.columns().every(function () {
        var that = this;
        $('input', $table.find('thead tr.filter-row th').eq(this.index())).on('keyup change clear', function () {
            if (that.search() !== this.value) that.search(this.value).draw();
        });
    });

    $(document).on('click', '.btn-approve-attendance', function () {
        let $btn = $(this);
        let attendance_id = $btn.data('id');
        let $cellWrap = $btn.closest('.approval-cell-wrap');

        $btn.prop('disabled', true).text('Approving...');

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/attendance-status',
            data: { attendance_status: 1, attendance_id: attendance_id },
            success: function () {
                $cellWrap.html('<span class="badge badge-success py-1 px-2" style="background:#28a745; color:#fff; font-size: 11px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;"><i class="bi bi-check-circle-fill"></i> Approved</span>');
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Attendance approved successfully!'
                });
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="bi bi-check-lg" style="font-size: 12px;"></i> <span>Approve</span>');
                let errMsg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Something went wrong. Please check!';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errMsg
                });
            }
        });
    });

    $('body').on('click', '.deleteAttendance', function () {
        var userURL = $(this).data('url');
        var trObj = $(this);
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: userURL,
                    type: 'GET',
                    dataType: 'json',
                    success: function () {
                        trObj.closest('tr').remove();
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Attendance was successfully deleted!',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    },
                    error: function(xhr) {
                        let errMsg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Could not delete attendance.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errMsg
                        });
                    }
                });
            }
        });
    });

    $('.complete-data').click(function () { $(this).addClass('visited'); });
})();
</script>
