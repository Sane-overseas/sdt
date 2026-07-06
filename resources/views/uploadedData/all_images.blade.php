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
    div#trainerImages_wrapper .row {
        padding: 10px;
    }
    div#trainerImages_info {
        padding: 0px;
    }
    a.btn.deleteAllImages{
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
    <div class="card-body">
        <table class="table table-bordered" id="trainerImages">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>In front of School Board Image</th>
                    <th>Group Image</th>
                    <th>1st Activity Image</th>
                    <th>2nd Activity Image</th>
                    <th>3rd Activity Image</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>School Name</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Rejection Note</th>
                    <th>Approve Images</th>
                </tr>
            </thead>
            <tbody>
                @foreach($images as $image)
                    <tr>
                         <td>{{$image['id']}}</td>
                        <td>@foreach($user as $d_data)
                                @if($d_data['id'] == $image['user_id'])
                                     {{$d_data['instructor_name']}} - {{$d_data['instructor_code']}}
                                @endif
                            @endforeach
                        </td>
                        <td>@foreach($user as $d_data)
                                @if($d_data['id'] == $image['uploaded_user'])
                                     {{$d_data['instructor_name']}}
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @if($image['ifsb_image'])
                            <a href="{{asset('storage/images/'.$image['ifsb_image'])}}" target="_blank"  class="complete-data" >Check & Download</a>
                            <a href="javascript:void(0)" data-url="{{ route('images' , [$image['id'] ,1]) }}" class="btn deleteImage"><i class="bi bi-x-circle-fill remove"></i></a>@else<span class="pending-data">Pending</span> @endif
                        </td>
                        <td>
                            @if($image['group_image'])
                            <a href="{{asset('storage/images/'.$image['group_image'])}}" target="_blank"  class="complete-data" >Check & Download</a>
                             <a href="javascript:void(0)" data-url="{{ route('images' , [$image['id'] ,2]) }}" class="btn deleteImage"><i class="bi bi-x-circle-fill remove"></i></a>@else<span class="pending-data">Pending</span> @endif
                            </td>
                        <td>
                             @if($image['fst_aimage'])
                             <a href="{{asset('storage/images/'.$image['fst_aimage'])}}" target="_blank"  class="complete-data" >Check & Download</a>
                             <a href="javascript:void(0)" data-url="{{ route('images' , [$image['id'] ,3]) }}" class="btn deleteImage"><i class="bi bi-x-circle-fill remove"></i></a>@else<span class="pending-data">Pending</span> @endif
                            </td>
                        <td>
                            @if($image['snd_aimage'])
                             <a href="{{asset('storage/images/'.$image['snd_aimage'])}}" target="_blank"  class="complete-data" >Check & Download</a>
                            <a href="javascript:void(0)" data-url="{{ route('images' , [$image['id'] ,4]) }}" class="btn deleteImage"><i class="bi bi-x-circle-fill remove"></i></a>@else<span class="pending-data">Pending</span> @endif
                        </td>
                        <td>
                            @if($image['trd_aimage'])
                             <a href="{{asset('storage/images/'.$image['trd_aimage'])}}" target="_blank"  class="complete-data" >Check & Download</a>
                            <a href="javascript:void(0)" data-url="{{ route('images' , [$image['id'] ,5]) }}" class="btn deleteImage"><i class="bi bi-x-circle-fill remove"></i></a>@else<span class="pending-data">Pending</span> @endif
                        </td>
                        <td>{{$image['district']}}</td>
                        <td>{{$image['bloack']}}</td>
                        <td>{{$image['school_name']}}</td>
                         <td style="width: 16%;">{{date('d/m/y - g:i A', strtotime($image['created_at']))}}</td>
                        <td>{{$image['route_date']}}
                        <td style="text-align:center;">
                            <a href="" id="suspendd" data-toggle="modal"
                            data-target="#demoModal{{ $image['id'] }}"
                            class="send_btn">Add</a>
                            <form action="{{ route('image-note', $image['id']) }}" method="post">
                                @csrf
                             <!-- Modal Example Start-->
                                 <div class="modal fade note-model" id="demoModal{{ $image['id'] }}" value="{{$image['id']}}" tabindex="-1" role="dialog" aria- labelledby="demoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="demoModalLabel{{ $image['id'] }}" style="color: #fff ;">
                                                    Reason to Reject this image</h5>
                                                <button type="button" class="close"
                                                    data-dismiss="modal" aria- label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                            <div class="col-md-10">
                                                <textarea rows="5" cols="15" class="form-control summernote" placeholder="Write here" name="image_note"
                                                    value="image_note" id="image_note" required>{{ $image['image_note']}}</textarea>
                                                <input type="hidden" name="id" value="{{ $image['id'] }}">
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
                            @if($image['image_note'] != null)
                            <br>
                                <span class="not-started mt-2">Rejected</span>
                            @endif
                            <a href="javascript:void(0)" data-url="{{ route('delete-images' ,[$image['id'] , $image['school_id']]) }}" class="btn deleteAllImages">Delete All</a>
                        </td>
                        <td>
                        <label class="container-ck12">
                          {{-- @if($image['status'] == 1)  --}}
                          @if (($image['status'] ?? null) == 0)
                            <span class="approve">Approved</span>
                          @else
                            <span class="disapproved">Approval Pending</span>
                          @endif
                          <input class="image-status approve-button" data-id="{{$image['id']}}"  type="checkbox" {{ ($image['status'] ?? null) == 1 ? 'checked' : '' }}>
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
                <th>In front of School Board Image</th>
                <th>Group Image</th>
                <th>1st Activity Image</th>
                <th>2nd Activity Image</th>
                <th>3rd Activity Image</th>
                <th>District</th>
                <th>Block</th>
                <th>School Name</th>
                <th>Create Date</th>
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
<script type="text/javascript">

const table = $('#trainerImages').DataTable( {
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
$('#trainerImages tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input type="text" class="form-control" placeholder="'+title+'" />' );
} );

$('tfoot').each(function () {
    $(this).insertAfter($(this).siblings('thead'));
});

// Apply the search
table.columns().every( function () {
    var that = this;

    $( 'input', this.footer() ).on( 'keyup change', function () {
        if ( that.search() !== this.value ) {
            that
                .search( this.value )
                .draw();
        }
    } );
} );

$(document).ready(function () {
    $(".paginate_button ").click(function () {
        history.go(0);
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
              title: 'Approve Images status are changed !'
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

$('body').on('click', '.deleteImage', function () {

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
