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
    div#trainerVideos_wrapper .row {
        padding: 10px;
    }
    div#trainerVideos_info {
        padding: 0px;
    }
    a.btn.deleteAllvideos {
        padding: 1px 5px;
        background: #ff0707;
        color: #fff;
        width: 90px;
        margin-top: 13px;
    }
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
    <div class="card-body">
        <table class="table table-bordered" id="trainerVideos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>1st Activity Video</th>
                    <th>2nd Activity Video</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>School Name</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Rejection Note</th>
                    <th>Approve Videos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($videos as $video)
                    <tr>
                        <td>{{$video['id']}}</td>
                        <td>@foreach($user as $d_data)
                                @if($d_data['id'] == $video['user_id'])
                                      {{$d_data['instructor_name']}} - {{$d_data['instructor_code']}}
                                @endif
                            @endforeach
                        </td>
                        <td>@foreach($user as $d_data)
                                @if($d_data['id'] == $video['uploaded_user'])
                                     {{$d_data['instructor_name']}}
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @if($video['fst_video'])
                            <a href="{{asset('storage/videos/'.$video['fst_video'])}}" target="_blank"  class="complete-data">Check & Download</a>
                            <a href="javascript:void(0)" data-url="{{ route('1stvideo' ,$video['id']) }}" class="btn deleteVideo"><i class="bi bi-x-circle-fill remove"></i></a>@else<span class="pending-data">Pending</span> @endif
                        </td>
                        <td>
                            @if($video['snd_video'])
                             <a href="{{asset('storage/videos/'.$video['snd_video'])}}" target="_blank"  class="complete-data">Check & Download</a>
                             <a href="javascript:void(0)" data-url="{{ route('2ndvideo' ,$video['id']) }}" class="btn deleteVideo"><i class="bi bi-x-circle-fill remove"></i></a>@else<span class="pending-data">Pending</span> @endif
                        </td>
                        <td>{{$video['district']}}</td>
                        <td>{{$video['bloack']}}</td>
                        <td>{{$video['school_name']}}</td>
                        <td style="width: 16%;">{{date('d/m/y - g:i A', strtotime($video['created_at']))}}</td>
                        <td>{{$video['route_date']}}
                        </td>
                        <td style="text-align:center;">
                            <a href="" id="suspendd" data-toggle="modal"
                            data-target="#demoModal{{ $video['id'] }}"
                            class="send_btn">Add</a>
                            <form action="{{  route('video-note', $video['id']) }}" method="post">
                                @csrf
                             <!-- Modal Example Start-->
                                 <div class="modal fade note-model" id="demoModal{{ $video['id'] }}" value="{{$video['id']}}" tabindex="-1" role="dialog" aria- labelledby="demoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="demoModalLabel{{ $video['id'] }}" style="color: #fff ;">
                                                    Reason to Reject this Video</h5>
                                                <button type="button" class="close"
                                                    data-dismiss="modal" aria- label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                            <div class="col-md-10">
                                                <textarea rows="5" cols="15" class="form-control summernote" placeholder="Write here" name="video_note"
                                                    value="video_note" id="video_note" required>{{ $video['video_note']}}</textarea>
                                                <input type="hidden" name="id" value="{{ $video['id'] }}">
                                            </div>
                                        </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                                <button class="send_btn" type="submit">Send</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- Modal Example End-->
                            </form>
                            @if($video['video_note'] != null)
                            <br>
                                <span class="not-started mt-2">Rejected</span>
                            @endif
                             <a href="javascript:void(0)" data-url="{{ route('delete-videos' ,[$video['id'] , $video['school_id']]) }}" class="btn deleteAllvideos">Delete All</a>
                        </td>
                        <td >
                        <label class="container-ck12">
                          @if($video['status'] == 1)
                            <span class="approve">Approved</span>
                          @else
                            <span class="disapproved">Approval Pending</span>
                          @endif
                          <input class="video-status approve-button" data-id="{{$video['id']}}"  type="checkbox" {{ $video['status'] == 1 ? 'checked' : '' }}>
                          <div class="checkmark"></div>
                        </label>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>#</th>
                <th>Trainer</th>
                <th>Uploaded By</th>
                <th>1st Activity Video</th>
                <th>2nd Activity Video</th>
                <th>District</th>
                <th>Block</th>
                <th>School Name</th>
                <th>Create Date</th>
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
<script type="text/javascript">

const trainerVideos = $('#trainerVideos').DataTable( {
    ordering: false,
    dom: "<'row'<'col-sm-1'B><'col-sm-5'i><'col-sm-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'><'col-sm-7'p>>",
    pageLength : 100,
    stateSave: true,
    buttons: [
       {
          extend: 'excel',
          text: 'Download'
       }
    ]
});
 // Setup - add a text input to each footer cell
$('#trainerVideos tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input type="text" class="form-control" placeholder="'+title+'" />' );
} );

$('tfoot').each(function () {
    $(this).insertAfter($(this).siblings('thead'));
});

// Apply the search
trainerVideos.columns().every( function () {
    var that = this;

    $( 'input', this.footer() ).on( 'keyup change', function () {
        if ( that.search() !== this.value ) {
            that
                .search( this.value )
                .draw();
        }
    } );
});

$('.video-status').change(function () {
    let status = $(this).prop('checked') === true ? 1 : 0;
    let video_id = $(this).data('id');
    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/video-status',
        data: {'video_status': status, 'video_id': video_id},
        success: function (data) {
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
              title: 'Approve video status are changed !'
            })
        },
        error: function (data){
            const Toast = Swal.mixin({
                 toast: true,
                 position: 'top-end',
                 showConfirmButton: false,
                 timer: 4000,
                 timerProgressBar: true,
                 didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                 }
            })

            Toast.fire({
              icon: 'error',
              title: 'Somethig Wrong Please Check!'
            })
        },
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

</script>
