 <!DOCTYPE html>
 <html>
 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title></title>
      <meta name="csrf-token" content="{{ csrf_token() }}" />
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
     <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
   
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
 </head>
<style type="text/css">
     div#trainerDistributions_wrapper .row {
        padding: 10px;
    }
    div#trainerDistributions_info {
        padding: 0px;
    }
</style>
 <body> 
<div class="v-container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Trainer's Distributions</h2>
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
        <table class="table table-bordered" id="trainerDistributions">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Trainer</th>
                    <th>Uploaded By</th>
                    <th>Distributions</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>School Name</th>
                    <th>Date & Time</th>
                    <th>Route Date</th>
                    <th>Rejection Note</th>
                    <th>Distributed Certificats</th>
                    <th>Approve Distributions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($distributions as $data)
                    <tr>
                        <td>{{$data['id']}}</td>
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
                        @if($data['distribution_file'])
                        <a href="{{asset('storage/distribution/'.$data['distribution_file'])}}" target="_blank" class="complete-data">Check & Download</a>
                        <a href="javascript:void(0)" data-url="{{ route('distribution-remove' ,$data['id']) }}" class="btn distributionVideo"><i class="bi bi-x-circle-fill remove"></i></a>@else<span class="pending-data">Pending</span> @endif
                        </td> 
                        <td>{{$data['district']}}</td>
                        <td>{{$data['bloack']}}</td>
                        <td>{{$data['school_name']}}</td>
                         <td style="width: 16%;">{{date('d/m/y - g:i A', strtotime($data['created_at']))}}</td>
                        <td>{{$data['route_date']}}</td>
                        <td style="text-align:center;"> 
                            <a href="" id="suspendd" data-toggle="modal"
                            data-target="#demoModal{{ $data['id'] }}"
                            class="send_btn">Add</a>
                             <form action="{{ route('distribution-note', $data['id'])  }}" method="post">
                                @csrf
                             <!-- Modal Example Start-->
                                 <div class="modal fade note-model" id="demoModal{{ $data['id'] }}" value="{{$data['id']}}" tabindex="-1" role="dialog" aria- labelledby="demoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="demoModalLabel{{ $data['id'] }}" style="color: #fff ;">
                                                    Reason to Reject this distribution</h5>
                                                <button type="button" class="close"
                                                    data-dismiss="modal" aria- label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                            <div class="col-md-10">
                                                <textarea rows="5" cols="15" class="form-control summernote" placeholder="Write here" name="distribution_note"
                                                    value="distribution_note" id="distribution_note" required>{{ $data['distribution_note']}}</textarea>
                                                <input type="hidden" name="id" value="{{ $data['id'] }}">    
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
                            @if($data['distribution_note'] != null)
                            <br>
                                <span class="not-started mt-2">Rejected</span>
                            @endif
                        </td>
                         <td>{{$data['complete_students']}}</td>
                        <td>
                        <label class="container-ck12">
                            @if($data['status'] == 1) 
                                <span class="approve">Approved</span>
                            @else
                                <span class="disapproved">Approval Pending</span>
                            @endif 
                          <input class="distributions-status approve-button" data-id="{{$data['id']}}"  type="checkbox" {{ $data['status'] == 1 ? 'checked' : '' }}>
                          <div class="checkmark"></div>
                        </label>
                        </td>  
                    </tr>
                @endforeach  
            </tbody>
            <tfoot>
            <tr>
                <th>#</th>
                <th>Trainer</th>
                <th>Uploaded By</th>
                <th>Distributions</th>
                <th>District</th>
                <th>Block</th>
                <th>School Name</th>
                <th>Date & Time</th>
                <th>Route Date</th>
                <th>Rejection Note</th>
                <th>Distributed Certificats</th>
                <th>Approve Distributions</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script type="text/javascript">

const trainerDistributions =  $('#trainerDistributions').DataTable( {
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
$('#trainerDistributions tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input type="text" class="form-control" placeholder="'+title+'" />' );
} );

// Apply the search
trainerDistributions.columns().every( function () {
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

$('.distributions-status').change(function () {
    let status = $(this).prop('checked') === true ? 1 : 0;
    let distributions_id = $(this).data('id');
    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/distributions-status',
        data: {'distributions_status': status, 'distributions_id': distributions_id},
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
              title: 'Approve Distributions status are changed !'
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

$('body').on('click', '.distributionVideo', function () {
  
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