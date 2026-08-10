@extends('layouts.app')

@section('content')
<div class="container mt-2">
     <div class="row margin-tb align-items-center">
        <div class="col-md-8">
            <h2 class="heading mb-0">Total Cordinators</h2>
        </div>
        <div class="col-md-4 text-md-right" style="display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;">
            <a class="btn btn-outline-primary" href="{{ route('coordinator.registrations') }}">Coordinator Registrations</a>
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
                    <th>Level</th>
                    <th>District</th>
                    <th>Total Trainers</th>
                    <th>Edit / Assign Schools</th>
                    <th>Data Upload</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($cordinator as $data)
            <tr class="data{{$data['id']}}">
                <td class="get-data">{{$data['id']}}</td>
                <td class="get-data">{{$data['instructor_name']}}</td>
                <td class="get-data">{{$data['email']}}</td>
                <td class="get-data">
                 @foreach($new_cordinator as $cordMaster)
                        @if($data['cordinator_id'] ==  $cordMaster['id'])
                            {{$cordMaster['cordinator_code']}}
                        @endif
                    @endforeach
                </td>
                <td class="get-data">{{$data['instructor_number']}}</td>
                <td class="get-data">
                    {{ (($data['coordinator_level'] ?? 'district') === 'state') ? 'State' : 'District' }}
                </td>
                <td class="get-data">
                    {{ (($data['coordinator_level'] ?? 'district') === 'state') ? 'All Districts' : ($data['district'] ?? '—') }}
                </td>
                <td class="get-data"><?php $tr = 0; ?> 
                    @foreach($trainers as $trainer)
                        @if($data['cordinator_id'] ==  $trainer['cordinator_id'])
                            <?php $tr++ ?>
                        @endif
                    @endforeach
                    {{$tr}}
                </td>
                <td style="text-align:center;">
                    <input class="permission-status school_assigned" data-id="{{$data['id']}}" type="checkbox" {{ (int) ($data['school_assigned_status'] ?? 0) === 1 ? 'checked' : '' }}>
                </td>
                <td style="text-align:center;">
                    <input class="permission-status data_upload" data-id="{{$data['id']}}" type="checkbox" {{ (int) ($data['data_upload_status'] ?? 0) === 1 ? 'checked' : '' }}>
                </td>
                <td style="text-align:center; white-space:nowrap;">
                    <button type="button" class="btn btn-sm btn-warning edit-cordinator" data-id="{{$data['id']}}">Edit</button>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- bootstrap coordinator modal -->
<div class="modal fade" id="Cordinator-modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="CordinatorModal"></h4>
                <button type="button" class="btn btn-primary" id="btn-save">Save</button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="CordinatorForm" name="CordinatorForm" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="cordinator_user_id" value="">
                    <h6 class="text-primary mb-2">Account</h6>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Coordinator Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="cordinator_name" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Father Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="father_name" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email (Login) <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label id="password_label">Password <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="password" id="cordinator_password" value="SOPL@1634" required>
                            <small id="password_hint" class="text-muted d-none">Leave blank to keep current password.</small>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Coordinator Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" id="cordinator_code" readonly required>
                            <small class="text-muted" id="code_hint">Auto: District SOPL_DC_### · State SOPL_SC_###</small>
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
                            <label>Coordinator Level <span class="text-danger">*</span></label>
                            <select name="coordinator_level" class="form-control" id="coordinator_level" required>
                                <option value="district">District</option>
                                <option value="state">State</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="district_field_wrap">
                            <label>District <span class="text-danger district-req">*</span></label>
                            <select name="district_name" class="form-control" id="check_distt" required>
                                <option value="">Select District</option>
                                @if(isset($district))
                                    @foreach($district as $d)
                                        <option value="{{ $d['district'] }}" data-id="{{ $d['id'] }}">{{ $d['district'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="block_field_wrap">
                            <label>Block <span class="text-danger district-req">*</span></label>
                            <select name="block" class="form-control" id="create_block" required>
                                <option value="">Select Block</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Expertise (Martial Art Type) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="martial_art_type" placeholder="e.g. Karate, Taekwondo" required>
                        </div>
                    </div>

                    <h6 class="text-primary mb-2 mt-2">Documents</h6>
                    <div id="existing_docs" class="mb-2 d-none small text-muted"></div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Aadhar Document <span class="text-danger doc-req">*</span></label>
                            <input type="file" class="form-control doc-input" name="aadhar_doc" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Qualification <span class="text-danger doc-req">*</span></label>
                            <input type="file" class="form-control doc-input" name="qualification_doc" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Martial Art Certificate <span class="text-danger doc-req">*</span></label>
                            <input type="file" class="form-control doc-input" name="martial_art_doc" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Photo <span class="text-danger doc-req">*</span></label>
                            <input type="file" class="form-control doc-input" name="photo" accept=".jpg,.jpeg,.png" required>
                        </div>
                    </div>
                </form>
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
    if ($(evt.target).closest('.edit-cordinator, .permission-status').length) {
        return;
    }
    let id = $(this).find("td:first").text();
    if($(evt.target).is(".get-data")){
        let newUrl = "cordinator_data/"+id;
        location.href= newUrl;
    }
});

function fetchNextCoordinatorCode() {
    if ($('#cordinator_user_id').val()) {
        return;
    }
    var level = $('#coordinator_level').val() || 'district';
    $.get('{{ url('next-coordinator-code') }}', { level: level }, function (res) {
        $('#cordinator_code').val(res.code || '');
    });
}

function setCreateMode() {
    $('#CordinatorForm').trigger("reset");
    $('#cordinator_user_id').val('');
    $('#CordinatorModal').html("Add Coordinator");
    $('#btn-save').text('Save');
    $('#cordinator_password').val('SOPL@1634').prop('required', true);
    $('#password_label').html('Password <span class="text-danger">*</span>');
    $('#password_hint').addClass('d-none');
    $('.doc-input').prop('required', true);
    $('.doc-req').removeClass('d-none');
    $('#existing_docs').addClass('d-none').empty();
    $('#create_block').html('<option value="">Select Block</option>');
    $('#coordinator_level').val('district');
    toggleCoordinatorLevelFields();
    fetchNextCoordinatorCode();
}

function toggleCoordinatorLevelFields() {
    var isState = $('#coordinator_level').val() === 'state';
    if (isState) {
        $('#district_field_wrap, #block_field_wrap').hide();
        $('#check_distt, #create_block').prop('required', false).val('');
        $('#create_block').html('<option value="">Select Block</option>');
    } else {
        $('#district_field_wrap, #block_field_wrap').show();
        $('#check_distt, #create_block').prop('required', true);
    }
}

function setEditMode() {
    $('#CordinatorModal').html("Edit Coordinator");
    $('#btn-save').text('Update');
    $('#cordinator_password').val('').prop('required', false);
    $('#password_label').html('Password');
    $('#password_hint').removeClass('d-none');
    $('.doc-input').prop('required', false);
    $('.doc-req').addClass('d-none');
}

function loadBlocks(distId, selectedBlock) {
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
                var selected = (selectedBlock && selectedBlock === value.block) ? ' selected' : '';
                $('#create_block').append('<option value="' + value.block + '"' + selected + '>' + value.block + '</option>');
            });
        }
    });
}

function add(){
    setCreateMode();
    $('#Cordinator-modal').modal('show');
}

function editCoordinator(id) {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type: 'GET',
        url: 'edit-cordinator/' + id,
        dataType: 'json',
        success: function (data) {
            setCreateMode();
            setEditMode();
            $('#cordinator_user_id').val(data.id);
            $('[name="cordinator_name"]').val(data.cordinator_name || '');
            $('[name="father_name"]').val(data.father_name || '');
            $('[name="email"]').val(data.email || '');
            $('[name="code"]').val(data.code || '');
            $('[name="number"]').val(data.number || '');
            $('[name="aadhar_number"]').val(data.aadhar_number || '');
            $('[name="blood_group"]').val(data.blood_group || '');
            $('[name="address"]').val(data.address || '');
            $('[name="martial_art_type"]').val(data.martial_art_type || '');
            $('[name="coordinator_level"]').val(data.coordinator_level || 'district');
            toggleCoordinatorLevelFields();
            if ((data.coordinator_level || 'district') === 'district') {
                $('[name="district_name"]').val(data.district_name || '');
                loadBlocks(data.district_id, data.block || '');
            }

            var docs = [];
            if (data.aadhar_doc) docs.push('Aadhar uploaded');
            if (data.qualification_doc) docs.push('Qualification uploaded');
            if (data.martial_art_doc) docs.push('Martial art cert uploaded');
            if (data.photo) docs.push('Photo uploaded');
            if (docs.length) {
                $('#existing_docs').removeClass('d-none').text('Existing: ' + docs.join(', ') + '. Upload only to replace.');
            }

            $('#Cordinator-modal').modal('show');
        },
        error: function (xhr) {
            let msg = 'Could not load coordinator';
            try {
                const obj = JSON.parse(xhr.responseText);
                if (obj.message) msg = obj.message;
            } catch (e) {}
            Swal.fire({ icon: 'error', title: msg });
        }
    });
}

$('#check_distt').on('change', function () {
    var distId = $(this).find('option:selected').data('id');
    loadBlocks(distId);
});

$('#coordinator_level').on('change', function () {
    toggleCoordinatorLevelFields();
    fetchNextCoordinatorCode();
});

jQuery(document).ready(function($){
    $(document).on('input', '#aadhar_number', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 12);
    });
    $(document).on('input', '#phone_number', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    $(document).on('click', '.edit-cordinator', function (e) {
        e.preventDefault();
        e.stopPropagation();
        editCoordinator($(this).data('id'));
    });

// CREATE / UPDATE
    $("#btn-save").click(function (e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        e.preventDefault();

        var formData = new FormData(document.getElementById('CordinatorForm'));
        var isEdit = !!$('#cordinator_user_id').val();
        $.ajax({
            type: 'POST',
            url: isEdit ? 'update-cordinator' : 'create-cordinator',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
                Swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: (data && data.message) ? data.message : (isEdit ? 'Coordinator updated' : 'Coordinator has been created'),
                  showConfirmButton: false,
                  timer: 2500
                }).then(function(isConfirm) {
                if (isConfirm) {
                    location.reload();
                  }
                });
            },
            error: function (data) {
                let msg = 'Something went wrong';
                try {
                    const obj = JSON.parse(data.responseText);
                    if (obj.errors) {
                        msg = Object.values(obj.errors)[0][0];
                    } else if (obj.message) {
                        msg = obj.message;
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
                  title: msg
                })
            }
        });
    });
});
function permissionToast(icon, title) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    Toast.fire({ icon: icon, title: title });
}

function updateCoordinatorPermission(url, checkbox) {
    const $cb = $(checkbox);
    const status = $cb.prop('checked') === true ? 1 : 0;
    const cordinator_id = $cb.data('id');

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') }
    });

    $.ajax({
        type: 'POST',
        dataType: 'json',
        cache: false,
        url: url,
        data: { status: status, cordinator_id: cordinator_id },
        success: function () {
            permissionToast('success', 'Permission status changed');
        },
        error: function (xhr) {
            // Revert UI if save failed
            $cb.prop('checked', !$cb.prop('checked'));
            let msg = 'Could not update permission';
            try {
                const obj = JSON.parse(xhr.responseText);
                if (obj.message) msg = obj.message;
                else if (obj.errors) msg = Object.values(obj.errors)[0][0];
            } catch (e) {}
            permissionToast('error', msg);
        }
    });
}

// Delegated so DataTables paging/search rows still work
$(document).on('change', '.school_assigned', function () {
    updateCoordinatorPermission('/school-assigned-status', this);
});

$(document).on('change', '.data_upload', function () {
    updateCoordinatorPermission('/data-upload-status', this);
});
</script>
@endsection
