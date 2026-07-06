@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
<div class="container mt-2">
     <div class="row margin-tb">
        <div class="col-md-9">
            <h2 class="heading ">Advance Payments</h2>
        </div>
        <div class="col-md-3">
        <a class="btn btn-info" onClick="add()" href="javascript:void(0)" style="margin-left: 24%;">Add Advance Payment</a>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="card-body">
        <table class="table table-bordered" id="advancePayment">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>District</th>
                    <th>Payemnt</th>
                    <th>Payemnt Date</th>
                </tr>
            </thead>
            <tbody>
            @foreach($advance_payments as $data)
            <tr class="data{{$data['id']}}">
                <td>
                    @foreach($trainers as $d_data)
                        @if($d_data['id'] == $data['user_id'])
                            <strong class="trainers">{{$d_data['instructor_name']}} - {{$d_data['instructor_code']}}</strong>
                        @endif
                    @endforeach
                </td>
                <td>{{$data['role']}}</td>
                <td>@foreach($trainers as $d_data)
                    @if($d_data['id'] == $data['user_id'])
                        {{$d_data['district']}}
                    @endif
                @endforeach
                </td>
                <td>{{number_format($data['payment'], 2)}}</td>
                <td>{{$data['payment_date']}}</td>
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
                <form action="javascript:void(0)" id="advancePaymentForm" name="advancePayment" class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-row">
                         <div class="form-group col">
                          <label for="inputEmail4">User</label>
                          <select class="form-control " name="user">
                              <option value="">Select User</option>
                              @foreach($trainers as $user)
                               <option value="{{$user['id']}}">{{$user['instructor_name']}} - 
                               {{$user['instructor_code']}}</option>
                              @endforeach
                          </select>
                        </div>
                        <div class="form-group col">
                          <label for="inputPassword4">Role</label>
                          <select class="form-control " name="role">
                                <option value="">Select Role</option>
                                <option value="Trainer">Trainer</option>
                                <option value="Cordinator">Cordinator</option>
                          </select>
                        </div>
                        <div class="form-group col">
                          <label for="inputPassword4">Payment</label>
                          <input type="number" class="form-control " name="payment" >
                        </div> 
                         <div class="form-group col">
                          <label for="inputPassword4">Paid Date</label>
                          <input type="date" class="form-control" name="paid_date" >
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
    $('#advancePayment').DataTable( {
    ordering: false,
    dom: 'Bfrtip',
    pageLength : 25,
    buttons: [
        'excel'
    ]
} );

function add(){
    $('#advancePayment').trigger("reset");
    $('#trainerModal').html("Add Advance Payment");
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

        var formData =  $("#advancePaymentForm").serialize()
        var type = "POST";
        var ajaxurl = 'add-advance-payment';
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
                  title: 'Advance Payment has been created',
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


</script>
@endsection
