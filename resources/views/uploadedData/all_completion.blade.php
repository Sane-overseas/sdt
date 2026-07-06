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
    div#trainerCompletions_wrapper .row {
        padding: 10px;
    }
    div#trainerCompletions_info {
        padding: 0px;
    }  
</style>
 <body>
<div class="v-container mt-2">
     <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Trainer's Completions</h2>
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
        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="trainerCompletions">
            <thead>
                <tr>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>Completions</th>
                    <th>District</th>
                    <th>Block</th>
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
                    <tr>
                        <td>@foreach($user as $d_data)
                                @if($d_data['id'] == $data['user_id'])
                                     {{$d_data['instructor_name']}} - {{$d_data['instructor_code']}} 
                                @endif
                            @endforeach
                        </td>
                        <td>@foreach($user as $d_data)
                                @if($d_data['id'] == $data['uploaded_user'])
                                     {{$d_data['instructor_name']}}
                                @endif
                            @endforeach
                        </td>
                        <td>
                        @if($data['completion_file'])
                        <a href="{{asset('storage/completion/'.$data['completion_file'])}}" target="_blank"  class="complete-data">Check & Download</a>
                            @if($data['status'] == 0)
                            <a href="javascript:void(0)" data-url="{{ route('completion-remove' ,[$data['id'],$data['school_id']]) }}" class="btn completionVideo"><i class="bi bi-x-circle-fill remove"></i></a>
                            @endif
                        @else
                            <span class="pending-data">Pending</span>
                        @endif
                        </td> 
                        <td>{{$data['district']}}</td>
                        <td>{{$data['bloack']}}</td>
                        <td>{{$data['school_name']}}</td>
                        <td>
                            @foreach($schools as $school)
                                @if($school['id'] == $data['school_id'])
                                     {{$school['school_code']}} 
                                @endif
                            @endforeach</td>
                        <td style="width: 16%;">{{date('d/m/y - g:i A', strtotime($data['created_at']))}}</td>
                        <td style="width: 13%;">{{$data['route_date']}}</td>
                        <td style="width: 13%;">
                        @foreach($schools as $school)
                            @if($school['id'] == $data['school_id'])
                                @if($school['paid_status'] == 1)
                                    <span class="paid">Paid</span>
                                @else
                                    <span class="unpaid">Pending</span>
                                @endif
                            @endif
                        @endforeach
                        </td>
                        <td style="text-align:center;">
                            <form action="{{ route('completion-note', $data['id']) }}" method="post">
                                @csrf
                            <a href="" id="suspendd" data-toggle="modal"
                            data-target="#demoModal{{ $data['id'] }}"
                            class="send_btn">Add</a>
                             <!-- Modal Example Start-->
                                 <div class="modal fade note-model" id="demoModal{{ $data['id'] }}" value="{{$data['id']}}" tabindex="-1" role="dialog" aria- labelledby="demoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="demoModalLabel{{ $data['id'] }}" style="color: #fff ;">
                                                    Reason to Reject this completion</h5>
                                                <button type="button" class="close"
                                                    data-dismiss="modal" aria- label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                            <div class="col-md-10">
                                                <textarea rows="5" cols="15" class="form-control summernote" placeholder="Write here" name="completion_note"
                                                    value="completion_note" id="completion_note" required>{{ $data['completion_note']}}</textarea>
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
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                                <button class="send_btn" type="submit">Send</button>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- Modal Example End-->
                            </form>
                            @if($data['completion_note'] != null)
                            <br>
                                <span class="not-started mt-2">Rejected</span>
                            @endif
                        </td>
                        <td>  
                        <label class="container-ck12"> 
                            @if($data['completion_note'] != null && $data['emergency_approved'] == 0)
                                <a href="{{ url('getData/'.$data['user_id']) }}" target="_blank"><span class="pending-data">Upload Correct UC</span></a>
                            @elseif($data['emergency_approved'] == 1 && $data['completion_note'] != null )
                                <span class="approve">Emergency Approved</span>
                            @elseif($data['status'] == 1 && $data['emergency_approved'] == 0)
                             <span class="approve">Approved</span>
                            @else 
                            <span class="disapproved">Approval Pending</span>
                            @endif 
                            @if($data['completion_note'] == null)
                            <input class="completion-status approve-button" data-id="{{$data['id']}}"  type="checkbox" {{ $data['status'] == 1 ? 'checked' : '' }}>
                            <div class="checkmark"></div>
                            @endif
                        </label> 
                        </td>   
                    </tr>
                @endforeach  
            </tbody>
            <tfoot>
            <tr>
                 <th>Trainer</th>
                <th>Uploaded By</th>
                <th>Completions</th>
                <th>District</th>
                <th>Block</th>
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
<script type="text/javascript">

const trainerCompletions =  $('#trainerCompletions').DataTable( {
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
$('#trainerCompletions tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input type="text" class="form-control" placeholder="'+title+'" />' );
});

// Apply the search
trainerCompletions.columns().every( function () {
    var that = this;

    $( 'input', this.footer() ).on( 'keyup change', function () {
        if ( that.search() !== this.value ) {
            that
                .search( this.value )
                .draw();
        }
    } );
});

$('tfoot').each(function () {
    $(this).insertAfter($(this).siblings('thead'));
});

var tableC = $('#trainerCompletions').DataTable();
tableC.on( 'draw', function () {
    var body = $( tableC.table().body() );

    body.unhighlight();
    body.highlight( tableC.search() );  
});


 $(document).ready(function () {
    $(".paginate_button ").click(function () {
        history.go(0);
    });
});
   
$('.completion-status').change(function () {
    let status = $(this).prop('checked') === true ? 1 : 0;
    let completion_id = $(this).data('id');
    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/completion-status',
        data: {'completion_status': status, 'completion_id': completion_id},
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
              title: 'Approve Completion status are changed !'
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

$('body').on('click', '.completionVideo', function () {
  
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