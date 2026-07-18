<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @section('title', 'Uploaded Data')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<style type="text/css">
    .testimonials-table-wrap { width: 100%; overflow-x: auto; }
    div#trainerTestimonials_wrapper .row:first-child { padding: 10px 10px 0; align-items: center; }
    div#trainerTestimonials_wrapper .dt-buttons { margin-bottom: 0; }
    div#trainerTestimonials_info { padding: 0; white-space: nowrap; }
    a.btn.deleteTestimonial {
        padding: 1px 5px; background: #ff0707; color: #fff;
        width: 90px; margin-top: 8px; display: inline-block;
    }
    #trainerTestimonials { width: 100% !important; font-size: 13px; }
    #trainerTestimonials th.col-id, #trainerTestimonials td.col-id {
        width: 42px; max-width: 48px; min-width: 36px;
        text-align: center; padding: 8px 4px !important; white-space: nowrap;
    }
    #trainerTestimonials thead tr.filter-row th { padding: 4px 6px; background: #f8f9fa; }
    #trainerTestimonials thead tr.filter-row input { width: 100%; min-width: 70px; font-size: 12px; padding: 4px 6px; height: auto; }
    #trainerTestimonials thead tr.filter-row th:first-child input { min-width: 36px; padding: 4px 2px; }
    #trainerTestimonials th, #trainerTestimonials td { vertical-align: middle; padding: 8px 10px; }
    #trainerTestimonials .trainer-line { font-weight: 600; line-height: 1.3; }
    #trainerTestimonials .trainer-line small { color: #666; font-weight: 400; display: block; }
    #trainerTestimonials .school-cell { white-space: normal; min-width: 140px; }
    #trainerTestimonials .loc-line { line-height: 1.35; white-space: normal; min-width: 100px; }
    #trainerTestimonials .loc-line small { color: #666; display: block; }
    #trainerTestimonials .upload-cell { text-align: left; white-space: normal; min-width: 140px; }
    #trainerTestimonials .file-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 12px; }
    #trainerTestimonials .file-row .file-side { display: inline-flex; align-items: center; gap: 4px; flex: 1; }
    #trainerTestimonials .file-row .file-label { color: #444; white-space: nowrap; min-width: 72px; }
    #trainerTestimonials .file-row a.file-link { color: #0b5cab; text-decoration: none; font-weight: 600; }
    #trainerTestimonials .file-row a.file-link:hover { text-decoration: underline; }
    #trainerTestimonials .file-row .file-status { flex-shrink: 0; font-size: 14px; }
    #trainerTestimonials .date-cell { text-align: center; font-size: 12px; white-space: normal; min-width: 68px; }
    #trainerTestimonials .dt-stack { line-height: 1.35; }
    #trainerTestimonials .dt-stack small { display: block; color: #666; font-size: 11px; }
    #trainerTestimonials .actions-cell { text-align: center; min-width: 90px; }
</style>
<body>
<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Trainer's Testimonials</h2>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success"><p>{{ $message }}</p></div>
    @endif
    <div class="card-body testimonials-table-wrap">
        <table class="table table-bordered" id="trainerTestimonials">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>School Name</th>
                    <th>Location</th>
                    <th>Testimonial</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Rejection Note</th>
                    <th>Approve</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($testimonials ?? []) as $item)
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
                            <div class="file-row">
                                <span class="file-side">
                                    <span class="file-label">Video</span>
                                    @if(!empty($item['testimonial_video']))
                                        <a href="{{ asset('storage/testimonials/'.$item['testimonial_video']) }}" target="_blank" class="file-link complete-data">View</a>
                                    @endif
                                </span>
                                <span class="file-status">
                                    @if(!empty($item['testimonial_video']))
                                        <i class="bi-check-circle-fill nav-icn success-icon"></i>
                                    @else
                                        <i class="bi bi-x-circle-fill remove"></i>
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td class="date-cell">
                            <div class="dt-stack">
                                @php $shownAt = $item['updated_at'] ?? $item['created_at']; @endphp
                                {{ date('d/m/y', strtotime($shownAt)) }}
                                <small>{{ date('g:i A', strtotime($shownAt)) }}</small>
                            </div>
                        </td>
                        <td class="date-cell">{{ $item['route_date'] ?: '—' }}</td>
                        <td class="actions-cell">
                            <form action="{{ route('testimonial-note') }}" method="post">
                                @csrf
                                <a href="" data-toggle="modal" data-target="#testimonialNoteModal{{ $item['id'] }}" class="send_btn">Add</a>
                                <div class="modal fade note-model" id="testimonialNoteModal{{ $item['id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" style="color: #fff;">Reason to Reject this Testimonial</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="col-md-10">
                                                    <textarea rows="5" class="form-control" placeholder="Write here" name="testimonial_note" required>{{ $item['testimonial_note'] }}</textarea>
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
                            @if(!empty($item['testimonial_note']))
                                <br><span class="not-started">Rejected</span>
                            @endif
                            @if($item['status'] != 1)
                            <a href="javascript:void(0)" data-url="{{ route('delete-testimonial', $item['id']) }}" class="btn deleteTestimonial">Delete</a>
                            @endif
                        </td>
                        <td>
                            <label class="container-ck12">
                                @if($item['status'] == 1)
                                    <span class="approve">Approved</span>
                                @elseif(!empty($item['testimonial_note']))
                                    <span class="not-started">Rejected</span>
                                @else
                                    <span class="disapproved">Pending</span>
                                    <input class="testimonial-status approve-button" data-id="{{ $item['id'] }}" type="checkbox">
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
                    <th>School Name</th>
                    <th>Location</th>
                    <th>Testimonial</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Rejection Note</th>
                    <th>Approve</th>
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
    var $table = $('#trainerTestimonials');
    var $filterRow = $table.find('tfoot tr').clone().addClass('filter-row');
    $filterRow.find('th').each(function () {
        $(this).html('<input type="text" class="form-control" placeholder="' + $(this).text() + '" />');
    });
    $table.find('thead').append($filterRow);
    $table.find('tfoot').remove();

    var trainerTestimonials = $table.DataTable({
        ordering: false,
        dom: "<'row'<'col-sm-3'B><'col-sm-4'i><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'l><'col-sm-7'p>>",
        pageLength: 100,
        stateSave: true,
        autoWidth: false,
        buttons: [{ extend: 'excel', text: 'Download' }]
    });

    trainerTestimonials.columns().every(function () {
        var that = this;
        $('input', $table.find('thead tr.filter-row th').eq(this.index())).on('keyup change clear', function () {
            if (that.search() !== this.value) that.search(this.value).draw();
        });
    });

    $('.testimonial-status').change(function () {
        $.ajax({
            type: 'GET', dataType: 'json', url: '/testimonial-status',
            data: { testimonial_status: $(this).prop('checked') ? 1 : 0, testimonial_id: $(this).data('id') },
            success: function () {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Testimonial status updated!', showConfirmButton: false, timer: 2000 });
            },
            error: function () {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Something went wrong. Please check!', showConfirmButton: false, timer: 4000 });
            }
        });
    });

    $('body').on('click', '.deleteTestimonial', function () {
        var userURL = $(this).data('url');
        var trObj = $(this);
        Swal.fire({ title: 'Are you sure?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete it!' })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({ url: userURL, type: 'GET', dataType: 'json', success: function () {
                        trObj.parents('tr').remove();
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Testimonial has been deleted.', showConfirmButton: false, timer: 2000 });
                    }});
                }
            });
    });

    $('.complete-data').click(function () { $(this).addClass('visited'); });
})();
</script>
