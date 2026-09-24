 <!DOCTYPE html>
 <html>
 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta name="csrf-token" content="{{ csrf_token() }}" />
    @section('title', 'Uploaded Data')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}"></script>
 </head>
  <style type="text/css">
    .videos-table-wrap {
        width: 100%;
        overflow-x: auto;
    }
    div#trainerVideos_wrapper .row:first-child {
        padding: 10px 10px 0;
        align-items: center;
    }
    div#trainerVideos_wrapper .dt-buttons {
        margin-bottom: 0;
    }
    div#trainerVideos_info {
        padding: 0;
        white-space: nowrap;
    }
    a.btn.deleteAllvideos {
        padding: 1px 5px;
        background: #ff0707;
        color: #fff;
        width: 90px;
        margin-top: 8px;
        display: inline-block;
    }
    #trainerVideos { width: 100% !important; font-size: 13px; table-layout: auto; }
    #trainerVideos th.col-id,
    #trainerVideos td.col-id {
        width: 42px;
        max-width: 48px;
        min-width: 36px;
        text-align: center;
        padding: 8px 4px !important;
        white-space: nowrap;
    }
    #trainerVideos thead tr.filter-row th:first-child input {
        min-width: 36px;
        padding: 4px 2px;
    }
    #trainerVideos thead tr.filter-row th {
        padding: 4px 6px;
        background: #f8f9fa;
    }
    #trainerVideos thead tr.filter-row input {
        width: 100%;
        min-width: 70px;
        font-size: 12px;
        padding: 4px 6px;
        height: auto;
    }
    #trainerVideos .trainer-line { font-weight: 600; line-height: 1.3; }
    #trainerVideos .trainer-line small { color: #666; font-weight: 400; display: block; }
    #trainerVideos .school-cell { white-space: normal; min-width: 140px; }
    #trainerVideos .loc-line { line-height: 1.35; white-space: normal; min-width: 100px; }
    #trainerVideos .loc-line small { color: #666; display: block; }
    #trainerVideos th, #trainerVideos td {
        vertical-align: middle;
        padding: 8px 10px;
    }
    #trainerVideos .upload-cell {
        text-align: left;
        white-space: normal;
        min-width: 130px;
    }
    #trainerVideos .video-stack {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    #trainerVideos .video-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: 12px;
        line-height: 1.3;
    }
    #trainerVideos .video-row .vid-side {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        flex: 1;
        min-width: 0;
    }
    #trainerVideos .video-row .vid-label {
        color: #444;
        white-space: nowrap;
    }
    #trainerVideos .video-row a.vid-link {
        color: #0b5cab;
        text-decoration: none;
        font-weight: 600;
    }
    #trainerVideos .video-row a.vid-link:hover { text-decoration: underline; }
    #trainerVideos .video-row .vid-status {
        flex-shrink: 0;
        font-size: 14px;
        line-height: 1;
    }
    #trainerVideos .video-row .deleteVideo {
        padding: 0;
        margin: 0;
        line-height: 1;
    }
    #trainerVideos .date-cell { text-align: center; font-size: 12px; white-space: normal; min-width: 68px; }
    #trainerVideos .dt-stack { line-height: 1.35; }
    #trainerVideos .dt-stack small { display: block; color: #666; font-size: 11px; }
    #trainerVideos .actions-cell { text-align: center; min-width: 100px; }
</style>
 <body>
<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Trainer's Videos</h2>
        </div>
        <div class="col-md-2">

        </div>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="card-body videos-table-wrap">
        <table class="table table-bordered" id="trainerVideos">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>School Name</th>
                    <th>Location</th>
                    <th>Videos</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Rejection Note</th>
                    <th>Approve Videos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($videos as $video)
                    @php
                        $trainerName = '';
                        $trainerCode = '';
                        $uploadedBy = '';
                        foreach ($user as $d_data) {
                            if ($d_data['id'] == $video['user_id']) {
                                $trainerName = $d_data['instructor_name'];
                                $trainerCode = $d_data['instructor_code'];
                            }
                            if ($d_data['id'] == $video['uploaded_user']) {
                                $uploadedBy = $d_data['instructor_name'];
                            }
                        }
                        $blockName = $video['block'] ?? $video['bloack'] ?? '—';
                    @endphp
                    <tr>
                        <td class="col-id">{{ $video['id'] }}</td>
                        <td>
                            <div class="trainer-line">
                                {{ $trainerName ?: '—' }}@if($trainerCode) - {{ $trainerCode }}@endif
                            </div>
                        </td>
                        <td>{{ $uploadedBy ?: '—' }}</td>
                        <td class="school-cell">{{ $video['school_name'] }}</td>
                        <td>
                            <div class="loc-line">
                                {{ $video['district'] ?: '—' }}
                                <small>{{ $blockName }}</small>
                            </div>
                        </td>
                        <td class="upload-cell">
                            <div class="video-stack">
                                <div class="video-row">
                                    <span class="vid-side">
                                        <span class="vid-label">1st Video</span>
                                        @if($video['fst_video'])
                                            <a href="{{ media_url('videos', $video['fst_video']) }}" target="_blank" class="vid-link complete-data">View</a>
                                            @if($video['status'] != 1)
                                            <a href="javascript:void(0)" data-url="{{ route('1stvideo', $video['id']) }}" class="btn deleteVideo" title="Delete 1st video"><i class="bi bi-x-circle-fill remove"></i></a>
                                            @endif
                                        @endif
                                    </span>
                                    <span class="vid-status">
                                        @if($video['fst_video'])
                                            <i class="bi-check-circle-fill nav-icn success-icon"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill remove"></i>
                                        @endif
                                    </span>
                                </div>
                                <div class="video-row">
                                    <span class="vid-side">
                                        <span class="vid-label">2nd Video</span>
                                        @if($video['snd_video'])
                                            <a href="{{ media_url('videos', $video['snd_video']) }}" target="_blank" class="vid-link complete-data">View</a>
                                            @if($video['status'] != 1)
                                            <a href="javascript:void(0)" data-url="{{ route('2ndvideo', $video['id']) }}" class="btn deleteVideo" title="Delete 2nd video"><i class="bi bi-x-circle-fill remove"></i></a>
                                            @endif
                                        @endif
                                    </span>
                                    <span class="vid-status">
                                        @if($video['snd_video'])
                                            <i class="bi-check-circle-fill nav-icn success-icon"></i>
                                        @else
                                            <i class="bi bi-x-circle-fill remove"></i>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="date-cell">
                            <div class="dt-stack">
                                @php $shownAt = $video['created_at'] ?? $video['created_date'] ?? $video['updated_at']; @endphp
                                {{ date('d/m/y', strtotime($shownAt)) }}
                                <small>{{ date('g:i A', strtotime($shownAt)) }}</small>
                            </div>
                        </td>
                        <td class="date-cell">{{ $video['route_date'] ?: '—' }}</td>
                        <td class="actions-cell">
                            <a href="" data-toggle="modal" data-target="#demoModal{{ $video['id'] }}" class="send_btn">Add</a>
                            <form action="{{ route('video-note', $video['id']) }}" method="post">
                                @csrf
                                <div class="modal fade note-model" id="demoModal{{ $video['id'] }}" value="{{ $video['id'] }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="demoModalLabel{{ $video['id'] }}" style="color: #fff;">
                                                    Reason to Reject this Video</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="col-md-10">
                                                    <textarea rows="5" cols="15" class="form-control summernote" placeholder="Write here" name="video_note" required>{{ $video['video_note'] }}</textarea>
                                                    <input type="hidden" name="id" value="{{ $video['id'] }}">
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
                            @if($video['video_note'] != null)
                                <br><span class="not-started">Rejected</span>
                            @endif
                            @if($video['status'] != 1)
                            <a href="javascript:void(0)" data-url="{{ route('delete-videos', [$video['id'], $video['school_id']]) }}" class="btn deleteAllvideos">Delete All</a>
                            @endif
                        </td>
                        <td>
                            <div class="approval-cell-wrap text-center">
                                @if($video['status'] == 1)
                                    <span class="badge badge-success py-1 px-2" style="background:#28a745; color:#fff; font-size: 11px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;"><i class="bi bi-check-circle-fill"></i> Approved</span>
                                @elseif($video['video_note'] != null)
                                    <span class="badge badge-danger py-1 px-2" style="background:#dc3545; color:#fff; font-size: 11px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center" style="gap: 2px;">
                                        <span class="badge badge-warning py-1 px-2" style="background:#ffc107; color:#000; font-size: 10px; font-weight: 600; border-radius: 3px; white-space: nowrap;">Pending</span>
                                        <button type="button" class="btn btn-sm btn-success btn-approve-video" data-id="{{ $video['id'] }}" style="padding: 2px 8px; font-size: 11px; font-weight: 600; border-radius: 4px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; gap: 4px; line-height: 1.2;">
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
                <th>Videos</th>
                <th>Date & Time</th>
                <th>Route Date</th>
                <th>Rejection Note</th>
                <th>Approve Videos</th>
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
    var $table = $('#trainerVideos');

    // Build per-column filters in thead BEFORE DataTables init (avoids column misalignment)
    var $filterRow = $table.find('tfoot tr').clone().addClass('filter-row');
    $filterRow.find('th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="' + title + '" />');
    });
    $table.find('thead').append($filterRow);
    $table.find('tfoot').remove();

    var trainerVideos = $table.DataTable({
        ordering: false,
        orderCellsTop: true,
        dom: "<'row'<'col-sm-3'B><'col-sm-4'i><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'l><'col-sm-7'p>>",
        pageLength: 100,
        stateSave: true,
        autoWidth: false,
        buttons: [uploadedDataExcelButton($table, 'trainer-videos')]
    });

    trainerVideos.columns().every(function () {
        var that = this;
        $('input', $table.find('thead tr.filter-row th').eq(this.index())).on('keyup change clear', function () {
            if (that.search() !== this.value) {
                that.search(this.value).draw();
            }
        });
    });

$(document).on('click', '.btn-approve-video', function () {
    let $btn = $(this);
    let video_id = $btn.data('id');
    let $cellWrap = $btn.closest('.approval-cell-wrap');

    $btn.prop('disabled', true).text('Approving...');

    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/video-status',
        data: {'video_status': 1, 'video_id': video_id},
        success: function (data) {
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
              title: 'Video approved successfully!'
            });
        },
        error: function (xhr){
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

$('body').on('click', '.deleteVideo', function () {

  var userURL = $(this).data('url');
  var trObj = $(this);

  Swal.fire({
      title: 'Are you sure?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
         $.ajax({
            url: userURL,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                // trObj.parents("tr").remove();
                const Toast = Swal.mixin({
                     toast: true,
                     position: 'top-end',
                     showConfirmButton: false,
                     timer: 2000,
                     timerProgressBar: true,
                     didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                     }
                })

                Toast.fire({
                  icon: 'success',
                  title: 'It was succesfully deleted! !'
                }).then(function(isConfirm) {
                if (isConfirm) {
                    location.reload();
                  }
                });
            },
        });
      }
    })
});

$('body').on('click', '.deleteAllvideos', function () {

      var userURL = $(this).data('url');
      var trObj = $(this);

      Swal.fire({
          title: 'Are you sure?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
             $.ajax({
                url: userURL,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    trObj.parents("tr").remove();
                     const Toast = Swal.mixin({
                         toast: true,
                         position: 'top-end',
                         showConfirmButton: false,
                         timer: 2000,
                         timerProgressBar: true,
                         didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                         }
                    })

                    Toast.fire({
                      icon: 'success',
                      title: 'It was succesfully deleted! !'
                    })
                },
            });
          }
        })
});


$('.complete-data').click(function(){
    $(this).addClass("visited");
});
})();
</script>
