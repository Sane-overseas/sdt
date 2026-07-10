@extends('layouts.app')

@section('content')
<div class="container mt-2">
     <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Total Cordinators</h2>
        </div>
        <div class="col-md-2">
        <a class="btn btn-info" onClick="add()" href="javascript:void(0)">Add Cordinator</a>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    @if(isset($currentState) && $currentState)
    <div class="alert alert-info py-2">
        Showing coordinators for state: <strong>{{ $currentState->name }}</strong> ({{ $currentState->code }})
    </div>
    @endif
    <div class="card-body">
        <table class="table table-bordered" id="cordinatorTable">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Cordinator code</th>
                    <th>Cordinator Number</th>
                    <th>District</th>
                    <th>Total Trainers</th>
                    <th>Schools Assigned Permission</th>
                    <th>Data upload Permission</th>
                </tr>
            </thead>
            <tbody>
            @foreach($cordinator as $data)
            <tr class="data{{$data['id']}}">
                <td class="get-data">{{$data['id']}}</td>
                <td class="get-data">{{$data['instructor_name']}}</td>
                <td class="get-data">{{$data['email']}}</td>
                <td class="get-data">
                 @foreach($new_cordinator as $cordinator)
                        @if($data['cordinator_id'] ==  $cordinator['id'])
                            {{$cordinator['cordinator_code']}}
                        @endif
                    @endforeach
                </td>
                <td class="get-data">{{$data['instructor_number']}}</td>
                <td class="get-data">{{$data['district']}}</td>
                <td class="get-data"><?php $tr = 0; ?> 
                    @foreach($trainers as $trainer)
                        @if($data['cordinator_id'] ==  $trainer['cordinator_id'])
                            <?php $tr++ ?>
                        @endif
                    @endforeach
                    {{$tr}}
                </td>
                <td style="text-align:center;">
                    <input class="permission-status school_assigned" data-id="{{$data['id']}}"  type="checkbox" {{ $data['school_assigned_status'] == 1 ? 'checked' : '' }}>
                </td>
                <td style="text-align:center;">
                    <input class="permission-status data_upload" data-id="{{$data['id']}}"  type="checkbox" {{ $data['data_upload_status'] == 1 ? 'checked' : '' }}>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- boostrap trainer model -->
<div class="modal fade" id="Cordinator-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="CordinatorModal"></h4>
                <button type="submit" class="btn btn-primary float-right" id="btn-save">Save</button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="CordinatorForm" name="CordinatorForm" class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-row">
                         <div class="form-group col">
                          <label for="inputEmail4">Cordinator Name</label>
                          <input type="text" class="form-control " name="cordinator_name" >
                        </div>
                        <div class="form-group col">
                          <label for="inputPassword4">Cordinator code</label>
                          <input type="text" class="form-control " name="code" >
                        </div>
                    </div>                   
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">


let  cordinatorTable = $('#cordinatorTable').DataTable( {
    ordering: false,
});

cordinatorTable.on('click', 'tbody tr', function (evt) {
    let data = cordinatorTable.row(this).data();
    let id = $(this).find("td:first").text();
    if($(evt.target).is(".get-data")){
        let newUrl = "cordinator_data/"+id;
        location.href= newUrl;
    }
});

function add(){
    $('#CordinatorForm').trigger("reset");
    $('#CordinatorModal').html("Add Cordinator");
    $('#Cordinator-modal').modal('show');
    $('#id').val('');
}  

jQuery(document).ready(function($){
// CREATE
    $("#btn-save").click(function (e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        e.preventDefault();

        var formData =  $("#CordinatorForm").serialize()
        var type = "POST";
        var ajaxurl = 'create-cordinator';
        $.ajax({
            type: type,
            url: ajaxurl,
            data: formData,
            dataType: 'json',
            success: function (data) {
                var allInputs = document.querySelectorAll(['input','select']);
                allInputs.forEach(singleInput => singleInput.value = '');
                Swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: 'Cordinator has been created',
                  showConfirmButton: false,
                  timer: 2000
                }).then(function(isConfirm) {
                if (isConfirm) {
                    location.reload();
                  } 
                });
            },
            error: function (data) {
                const obj = JSON.parse(data.responseText);
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                  icon: 'error',
                  title: obj.message
                })
            }
        });
    });
});
$('.school_assigned').change(function () {
    let status = $(this).prop('checked') === true ? 1 : 0;
    let cordinator_id = $(this).data('id');
    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/school-assigned-status',
        data: {'status': status, 'cordinator_id': cordinator_id},
        success: function (data) {
            const Toast = Swal.mixin({
                 toast: true,
                 position: 'top-end',
                 showConfirmButton: false,
                 // timer: 2000,
                 // timerProgressBar: true,
                 didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                 }
            })

            Toast.fire({
              icon: 'success',
              title: 'Permission status are changed !'
            })
        }
    });
});

$('.data_upload').change(function () {
    let status = $(this).prop('checked') === true ? 1 : 0;
    let cordinator_id = $(this).data('id');

    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/data-upload-status',
        data: {'status': status, 'cordinator_id': cordinator_id},
        success: function (data) {
            const Toast = Swal.mixin({
                 toast: true,
                 position: 'top-end',
                 showConfirmButton: false,
                 // timer: 2000,
                 // timerProgressBar: true,
                 didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                 }
            })

            Toast.fire({
              icon: 'success',
              title: 'Permission status are changed !'
            })
        }
    });
});
</script>
@endsection
