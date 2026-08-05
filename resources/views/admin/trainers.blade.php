@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
<div class="container mt-2">
     <div class="row margin-tb">
        <div class="col-md-8">
            <h2 class="heading ">Total Trainers</h2>
        </div>
        <div class="col-md-4 text-right">
        <a class="btn btn-secondary mb-1" href="{{ route('trainer.registrations') }}">Registrations</a>
        <a class="btn btn-info" onClick="add()" href="javascript:void(0)">Add Trainer</a>
        </div>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    @if(isset($currentState) && $currentState)
    <div class="alert alert-info py-2">
        Showing trainers for state: <strong>{{ $currentState->name }}</strong> ({{ $currentState->code }})
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
                    <th>Phone Number</th>
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
<!-- bootstrap trainer modal -->
<div class="modal fade" id="trainer-modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="trainerModal"></h4>
                <button type="button" class="btn btn-primary" id="btn-save">Save</button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="trainerForm" name="trainerForm" class="form-horizontal" enctype="multipart/form-data">
                    <h6 class="text-primary mb-2">Account</h6>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Trainer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="trainer_name" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Father Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="father_name" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="password" value="SOPL@1634" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Trainer Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Amount / School</label>
                            <input type="number" class="form-control" name="amount">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Incentive</label>
                            <input type="number" class="form-control" name="extra_amount">
                        </div>
                    </div>

                    <h6 class="text-primary mb-2 mt-2">Personal Details</h6>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="number" id="phone_number"
                                   inputmode="numeric" maxlength="10" placeholder="10 digit mobile" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Aadhar Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="aadhar_number" id="aadhar_number"
                                   inputmode="numeric" maxlength="12" placeholder="12 digit Aadhar" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Blood Group <span class="text-danger">*</span></label>
                            <select name="blood_group" class="form-control" required>
                                <option value="">Select</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}">{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" rows="2" required></textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label>District <span class="text-danger">*</span></label>
                            <select name="district_name" class="form-control" id="check_distt" required>
                                <option value="">Select District</option>
                                @if(isset($district))
                                    @foreach($district as $key => $data)
                                        <option value="{{$data['district']}}" data-id="{{ $data['id'] }}">{{$data['district']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Block <span class="text-danger">*</span></label>
                            <select name="block" class="form-control" id="create_block" required>
                                <option value="">Select Block</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Coordinator <span class="text-danger">*</span></label>
                            <select name="cordinator" class="form-control" required>
                                <option value="">Select Coordinator</option>
                                @if(isset($cordinator))
                                    @foreach($cordinator as $key => $data)
                                        <option value="{{$data['id']}}">{{$data['cordinator_name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Expertise (Martial Art Type) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="martial_art_type" placeholder="e.g. Karate, Taekwondo" required>
                        </div>
                    </div>

                    <h6 class="text-primary mb-2 mt-2">Documents</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Aadhar Document <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="aadhar_doc" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Qualification <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="qualification_doc" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Martial Art Certificate <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="martial_art_doc" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Photo <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png" required>
                        </div>
                    </div>
                </form>
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
    $('#create_block').html('<option value="">Select Block</option>');
}

$('#check_distt').on('change', function () {
    var distId = $(this).find('option:selected').data('id');
    $('#create_block').html('<option value="">Select Block</option>');
    if (!distId) return;
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type: 'POST',
        url: '/blockdata',
        data: { id: distId },
        success: function (result) {
            $('#create_block').html('<option value="">Select Block</option>');
            $.each(result.block || [], function (key, value) {
                $('#create_block').append('<option value="' + value.block + '">' + value.block + '</option>');
            });
        }
    });
});

jQuery(document).ready(function($){
    $(document).on('input', '#aadhar_number', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 12);
    });
    $(document).on('input', '#phone_number', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

// CREATE
    $("#btn-save").click(function (e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        e.preventDefault();

        var formData = new FormData(document.getElementById('trainerForm'));
        $.ajax({
            type: 'POST',
            url: 'create-trainer',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
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
                let title = 'Something went wrong';
                try {
                    const obj = JSON.parse(data.responseText);
                    if (obj.errors) {
                        title = Object.values(obj.errors)[0][0];
                    } else if (obj.message) {
                        title = obj.message;
                    }
                } catch (e) {}
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
                  title: title
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
