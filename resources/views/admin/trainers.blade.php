@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
<div class="container mt-2">
     <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Total Trainers</h2>
        </div>
        <div class="col-md-2">
        <a class="btn btn-info" onClick="add()" href="javascript:void(0)">Add Trainer</a>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="card-body">
        <table class="table table-bordered" id="trainerTable">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Trainer code</th>
                    <th>Trainer Number</th>
                    <th>Cordinator</th>
                    <th>District</th>
                    <th>Get Data</th>
                    <th>Active Status</th>
                </tr>
            </thead>
            <tbody>
            @foreach($trainers as $data)
            <tr class="data{{$data['id']}}">
                <td>{{$data['id']}}</td>
                <td>{{$data['instructor_name']}}</td>
                <td>{{$data['email']}}</td>
                <td>{{$data['instructor_code']}}</td>
                <td>{{$data['instructor_number']}}</td>
                <td>
                    @foreach($cordinator as $c_data)
                        @if($c_data['id'] == $data['cordinator_id'])
                             {{$c_data['cordinator_name']}}
                        @endif
                    @endforeach
                </td>
                <td>{{$data['district']}}</td>
                <td style="text-align: center;">
                    <a href="{{ url('getData/'.$data['id']) }}" class="btn saveStopitems" id="{{$data['id']}}"><i class="bi bi-caret-down-square save-icon"></i></a>
                </td>
                <td style="text-align: center;"><input type="checkbox" data-id="{{$data['id']}}" name="status" class="active-status" {{ $data['active_status'] == 1 ? 'checked' : '' }}></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- boostrap trainer model -->
<div class="modal fade" id="trainer-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="trainerModal"></h4>
                <button type="submit" class="btn btn-primary float-right" id="btn-save">Save</button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="trainerForm" name="trainerForm" class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-row">
                         <div class="form-group col">
                          <label for="inputEmail4">Trainer Name</label>
                          <input type="text" class="form-control " name="trainer_name" >
                        </div>
                        <div class="form-group col">
                          <label for="inputPassword4">Email</label>
                          <input type="email" class="form-control " name="email" >
                        </div>
                         <div class="form-group col">
                          <label for="inputPassword4">Password</label>
                          <input type="text" class="form-control " name="password" value="Sopl@1634" >
                        </div>
                        <div class="form-group col">
                          <label for="inputEmail4">Amount per School</label>
                          <input type="number" class="form-control" name="amount" >
                        </div>
                        <div class="form-group col">
                          <label for="inputEmail4">Incentive Amount</label>
                          <input type="number" class="form-control" name="extra_amount" >
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col">
                          <label for="inputPassword4">Trainer code</label>
                          <input type="text" class="form-control " name="code" >
                        </div>
                         <div class="form-group col">
                          <label for="inputEmail4">Number</label>
                          <input type="number" class="form-control" name="number" >
                        </div>
                        <div class="form-group col">
                          <label for="inputPassword4">Cordinator</label>
                          <select name="cordinator" class="form-control">
                               <option value="" selected>Select Cordinator</option>
                                @if(isset($cordinator))
                                    @foreach($cordinator as $key => $data)
                                        <option value="{{$data['id']}}">{{$data['cordinator_name']}}</option>
                                    @endforeach
                                @endif
                              </select>
                        </div>
                        <div class="form-group col">
                            <label>District Name</label>
                            <select name="district_name" class="form-control" id="check_distt">
                                <option value="">Select District</option>
                                @if(isset($district))
                                    @foreach($district as $key => $data)
                                        <option value="{{$data['district']}}">{{$data['district']}}</option>
                                    @endforeach
                                @endif
                            </select>
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
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttns/2.4.2/js/buttons.print.min.js"></script>
<script type="text/javascript">
    $('#trainerTable').DataTable( {
    dom: 'Bfrtip',
    pageLength : 25,
    buttons: [
        'excel'
    ]
} );

function add(){
    $('#trainerForm').trigger("reset");
    $('#trainerModal').html("Add Trainer");
    $('#trainer-modal').modal('show');
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

        var formData =  $("#trainerForm").serialize()
        var type = "POST";
        var ajaxurl = 'create-trainer';
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
                  title: 'Trainer has been created',
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

let trainer = Array.prototype.slice.call(document.querySelectorAll('.active-status'));

trainer.forEach(function(html) {
    let switchery = new Switchery(html,  { size: 'small' });
});

$(document).ready(function(){
    $('.active-status').change(function () {
        let status = $(this).prop('checked') === true ? 1 : 0;
        let trainer_id = $(this).data('id');
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/trainer-status',
            data: {'active_status': status, 'trainer_id': trainer_id},
            success: function (data) {
                const Toast = Swal.mixin({
                     toast: true,
                     position: 'top-end',
                     showConfirmButton: false,
                     timer: 1500,
                     timerProgressBar: true,
                     didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                     }
                })

                Toast.fire({
                  icon: 'success',
                  title: 'Active status are changed !'
                })
            }
        });
    });
});
</script>
@endsection
