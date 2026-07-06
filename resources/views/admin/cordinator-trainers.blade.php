@extends('layouts.app')

@section('content')
<div class="container mt-2">
    <div class="row m-2">
        <div class="col-md total-div td-div">
            <span class="trainer-as-hed total-text">Total Schools</span>
            <span class="trainer-as-amt total-text">{{$totalScholls}}</span>
        </div>
        <div class="col-md compete-div td-div">
            <span class="trainer-as-hed complete-text">Complete Schools</span>
            <span class="trainer-as-amt complete-text">{{$completeSchools}}</span>
        </div>
        <div class="col-md pending-div td-div">
            <span class="trainer-as-hed pending-text">Pending Schools</span>
            <span class="trainer-as-amt pending-text">{{ $pendingSchools }}</span>
        </div>
        <div class="col-md not-started-dev td-div">
            <span class="trainer-as-hed pending-text">Not Started Schools</span>
            <span class="trainer-as-amt pending-text">{{ $notstartedSchools }}</span>
        </div>
    <div>
     <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Total Trainers</h2>
        </div>
        @if(Auth::user()->role == 1)
        <div class="col-md-2">
        <a class="btn btn-info" onClick="add()" href="javascript:void(0)">Add Trainer</a>
        </div>
        @endif
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
                    <th>Total Schools</th>
                    <th>Completed Schools</th>
                    <th>Pending Schools</th>
                    <th>Not Started Schools</th>
                    <th>Get Data</th>
                </tr>
            </thead>
            <tbody>
            @foreach($cordinator_trainers as $data)
            <tr class="data{{$data['id']}}">
                <td>{{$data['id']}}</td>
                <td><strong>{{$data['instructor_name']}}</strong></td>
                <td>{{$data['email']}}</td>
                <td>{{$data['instructor_code']}}</td>
                <td>{{$data['instructor_number']}}</td>             
                <td>
                    <?php $totalSchools = 0; ?>
                    @foreach($data['asigned_schools'] as $scholls)
                        <?php $totalSchools++ ?> 
                    @endforeach
                    <span class="total">{{$totalSchools}}</span>
                </td>
                <td>
                    <?php 
                        $completeSchools = 0; 
                        $pendingSchools = 0;
                        $notstartedSchools = 0;
                    ?>
                    @foreach($data['asigned_schools'] as $scholls)
                        @if($scholls['status'] == 1)
                            <?php $completeSchools++ ?> 
                        @endif
                        @if($scholls['status'] == 0 && $scholls['route_date'] != null)
                            <?php $pendingSchools++ ?> 
                        @endif
                        @if($scholls['route_date'] == null)
                            <?php $notstartedSchools++ ?> 
                        @endif
                    @endforeach
                   <span class="compete">{{$completeSchools}}</span>
                </td>
                <td><span class="pending">{{$pendingSchools}}</span></td>
                <td><span class="not-started">{{ $notstartedSchools}}</span></td>
                <td style="text-align: center;"> 
                    <a href="{{ url('getData/'.$data['id']) }}" class="btn saveStopitems" id="{{$data['id']}}"><i class="bi bi-caret-down-square save-icon"></i></a>
                </td>
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
<script type="text/javascript">
$('#trainerTable').DataTable( {
  
    
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
</script>
@endsection
