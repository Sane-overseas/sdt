 <!DOCTYPE html>
 <html>
 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
    @section('title', 'Uploaded Data')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
 </head>
 <style type="text/css">
    .images-table-wrap {
        width: 100%;
        overflow-x: auto;
    }
    div#trainerImages_wrapper .row:first-child {
        padding: 10px 10px 0;
        align-items: center;
    }
    div#trainerImages_wrapper .dt-buttons {
        margin-bottom: 0;
    }
    div#trainerImages_info {
        padding: 0;
        white-space: nowrap;
    }
    a.btn.deleteAllImages {
        padding: 1px 5px;
        background: #ff0707;
        color: #fff;
        width: 90px;
        margin-top: 8px;
        display: inline-block;
    }
    #trainerImages { width: 100% !important; font-size: 13px; table-layout: auto; }
    #trainerImages th.col-id,
    #trainerImages td.col-id {
        width: 42px;
        max-width: 48px;
        min-width: 36px;
        text-align: center;
        padding: 8px 4px !important;
        white-space: nowrap;
    }
    #trainerImages thead tr.filter-row th:first-child input {
        min-width: 36px;
        padding: 4px 2px;
    }
    #trainerImages thead tr.filter-row th {
        padding: 4px 6px;
        background: #f8f9fa;
    }
    #trainerImages thead tr.filter-row input {
        width: 100%;
        min-width: 70px;
        font-size: 12px;
        padding: 4px 6px;
        height: auto;
    }
    #trainerImages th, #trainerImages td {
        vertical-align: middle;
        padding: 8px 10px;
    }
    #trainerImages .trainer-line { font-weight: 600; line-height: 1.3; }
    #trainerImages .trainer-line small { color: #666; font-weight: 400; display: block; }
    #trainerImages .school-cell { white-space: normal; min-width: 140px; }
    #trainerImages .loc-line { line-height: 1.35; white-space: normal; min-width: 100px; }
    #trainerImages .loc-line small { color: #666; display: block; }
    #trainerImages .upload-cell {
        text-align: left;
        white-space: normal;
        min-width: 170px;
    }
    #trainerImages .img-stack {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    #trainerImages .img-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: 12px;
        line-height: 1.3;
    }
    #trainerImages .img-row .img-side {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        flex: 1;
        min-width: 0;
    }
    #trainerImages .img-row .img-label {
        color: #444;
        white-space: nowrap;
        min-width: 88px;
    }
    #trainerImages .img-row a.img-link {
        color: #0b5cab;
        text-decoration: none;
        font-weight: 600;
    }
    #trainerImages .img-row a.img-link:hover { text-decoration: underline; }
    #trainerImages .img-row .img-status {
        flex-shrink: 0;
        font-size: 14px;
        line-height: 1;
    }
    #trainerImages .img-row .deleteImage {
        padding: 0;
        margin: 0;
        line-height: 1;
    }
    #trainerImages .date-cell { text-align: center; font-size: 12px; white-space: normal; min-width: 68px; }
    #trainerImages .dt-stack { line-height: 1.35; }
    #trainerImages .dt-stack small { display: block; color: #666; font-size: 11px; }
    #trainerImages .actions-cell { text-align: center; min-width: 100px; }
</style>
 <body>
<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Trainer's Images</h2>
        </div>
        <div class="col-md-2">

        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="card-body images-table-wrap">
        <table class="table table-bordered" id="trainerImages">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>School Name</th>
                    <th>Location</th>
                    <th>Images</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Rejection Note</th>
                    <th>Approve Images</th>
                </tr>
            </thead>
            <tbody>
                @foreach($images as $image)
                    @php
                        $trainerName = '';
                        $trainerCode = '';
                        $uploadedBy = '';
                        foreach ($user as $d_data) {
                            if ($d_data['id'] == $image['user_id']) {
                                $trainerName = $d_data['instructor_name'];
                                $trainerCode = $d_data['instructor_code'];
                            }
                            if ($d_data['id'] == $image['uploaded_user']) {
                                $uploadedBy = $d_data['instructor_name'];
                            }
                        }
                        $blockName = $image['block'] ?? $image['bloack'] ?? '—';
                        $imageSlots = [
                            ['key' => 'ifsb_image', 'label' => 'School Board', 'type' => 1],
                            ['key' => 'group_image', 'label' => 'Group', 'type' => 2],
                            ['key' => 'fst_aimage', 'label' => '1st Activity', 'type' => 3],
                            ['key' => 'snd_aimage', 'label' => '2nd Activity', 'type' => 4],
                            ['key' => 'trd_aimage', 'label' => '3rd Activity', 'type' => 5],
                        ];
                    @endphp
                    <tr>
                        <td class="col-id">{{ $image['id'] }}</td>
                        <td>
                            <div class="trainer-line">
                                {{ $trainerName ?: '—' }}@if($trainerCode) - {{ $trainerCode }}@endif
                            </div>
                        </td>
                        <td>{{ $uploadedBy ?: '—' }}</td>
                        <td class="school-cell">{{ $image['school_name'] }}</td>
                        <td>
                            <div class="loc-line">
                                {{ $image['district'] ?: '—' }}
                                <small>{{ $blockName }}</small>
                            </div>
                        </td>
                        <td class="upload-cell">
                            <div class="img-stack">
                                @foreach($imageSlots as $slot)
                                    <div class="img-row">
                                        <span class="img-side">
                                            <span class="img-label">{{ $slot['label'] }}</span>
                                            @if(!empty($image[$slot['key']]))
                                                <a href="{{ asset('storage/images/'.$image[$slot['key']]) }}" target="_blank" class="img-link complete-data">View</a>
                                                @if(($image['status'] ?? 0) != 1)
                                                <a href="javascript:void(0)" data-url="{{ route('images', [$image['id'], $slot['type']]) }}" class="btn deleteImage" title="Delete {{ $slot['label'] }}"><i class="bi bi-x-circle-fill remove"></i></a>
                                                @endif
                                            @endif
                                        </span>
                                        <span class="img-status">
                                            @if(!empty($image[$slot['key']]))
                                                <i class="bi-check-circle-fill nav-icn success-icon"></i>
                                            @else
                                                <i class="bi bi-x-circle-fill remove"></i>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="date-cell">
                            <div class="dt-stack">
                                @php $shownAt = $image['updated_at'] ?? $image['created_at']; @endphp
                                {{ date('d/m/y', strtotime($shownAt)) }}
                                <small>{{ date('g:i A', strtotime($shownAt)) }}</small>
                            </div>
                        </td>
                        <td class="date-cell">{{ $image['route_date'] ?: '—' }}</td>
                        <td class="actions-cell">
                            <a href="" data-toggle="modal" data-target="#demoModal{{ $image['id'] }}" class="send_btn">Add</a>
                            <form action="{{ route('image-note', $image['id']) }}" method="post">
                                @csrf
                                <div class="modal fade note-model" id="demoModal{{ $image['id'] }}" value="{{ $image['id'] }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="demoModalLabel{{ $image['id'] }}" style="color: #fff;">
                                                    Reason to Reject this image</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="col-md-10">
                                                    <textarea rows="5" cols="15" class="form-control summernote" placeholder="Write here" name="image_note" required>{{ $image['image_note'] }}</textarea>
                                                    <input type="hidden" name="id" value="{{ $image['id'] }}">
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
                            @if($image['image_note'] != null)
                                <br><span class="not-started">Rejected</span>
                            @endif
                            @if(($image['status'] ?? 0) != 1)
                            <a href="javascript:void(0)" data-url="{{ route('delete-images', [$image['id'], $image['school_id']]) }}" class="btn deleteAllImages">Delete All</a>
                            @endif
                        </td>
                        <td>
                            <label class="container-ck12">
                                @if(($image['status'] ?? 0) == 1)
                                    <span class="approve">Approved</span>
                                @elseif($image['image_note'] != null)
                                    <span class="not-started">Rejected</span>
                                @else
                                    <span class="disapproved">Pending</span>
                                    <input class="image-status approve-button" data-id="{{ $image['id'] }}" type="checkbox">
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
                    <th>Images</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Rejection Note</th>
                    <th>Approve Images</th>
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
    var $table = $('#trainerImages');

    var $filterRow = $table.find('tfoot tr').clone().addClass('filter-row');
    $filterRow.find('th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="' + title + '" />');
    });
    $table.find('thead').append($filterRow);
    $table.find('tfoot').remove();

    var trainerImages = $table.DataTable({
        ordering: false,
        dom: "<'row'<'col-sm-3'B><'col-sm-4'i><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'l><'col-sm-7'p>>",
        pageLength: 100,
        stateSave: true,
        autoWidth: false,
        buttons: [
            {
                extend: 'excel',
                text: 'Download'
            }
        ]
    });

    trainerImages.columns().every(function () {
        var that = this;
        $('input', $table.find('thead tr.filter-row th').eq(this.index())).on('keyup change clear', function () {
            if (that.search() !== this.value) {
                that.search(this.value).draw();
            }
        });
    });

    $('.image-status').change(function () {
        let status = $(this).prop('checked') === true ? 1 : 0;
        let image_id = $(this).data('id');
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/image-status',
            data: {'image_status': status, 'image_id': image_id},
            success: function () {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Approve Images status are changed !',
                    showConfirmButton: false,
                    timer: 2000
                });
            },
            error: function () {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Somethig Wrong Please Check!',
                    showConfirmButton: false,
                    timer: 4000
                });
            },
        });
    });

    $('body').on('click', '.deleteImage', function () {
        var userURL = $(this).data('url');
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
                    success: function () {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'It was succesfully deleted! !',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function () {
                            location.reload();
                        });
                    },
                });
            }
        });
    });

    $('body').on('click', '.deleteAllImages', function () {
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
                    success: function () {
                        trObj.parents("tr").remove();
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'It was succesfully deleted! !',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    },
                });
            }
        });
    });

    $('.complete-data').click(function () {
        $(this).addClass("visited");
    });
})();
</script>
