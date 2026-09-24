<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
	@section('title', 'Edit Trainer')
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">
	@vite(['resources/sass/app.scss', 'resources/js/app.js'])
	<title></title>
</head>
<style type="text/css">
	.back-button {
		font-size: 20px;
        font-weight: 600;
        color: #000;
        background: #fff;
    }
    .save-trainer {
	    padding: 16px;
	}
    .school-tools {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    .school-search {
        max-width: 320px;
    }
    .school-checklist {
        border: 1px solid #d8dde3;
        border-radius: 8px;
        background: #fff;
        padding: 10px;
        max-height: 280px;
        overflow-y: auto;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        align-content: start;
    }
    .school-check-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin: 0;
        padding: 8px 10px;
        border-radius: 6px;
        font-size: 13px;
        border: 1px solid #e8ecef;
        background: #fafbfc;
        cursor: pointer;
        min-height: 40px;
    }
    .school-check-item input {
        flex-shrink: 0;
        margin-top: 2px;
    }
    .school-check-item span {
        line-height: 1.3;
        word-break: break-word;
    }
    .school-check-item:hover {
        background: #f0f6fa;
        border-color: #c5d9e8;
    }
    .school-empty {
        color: #6c757d;
        font-size: 13px;
        padding: 10px 8px;
        grid-column: 1 / -1;
    }
    @media (max-width: 992px) {
        .school-checklist {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 768px) {
        .school-tools {
            display: block;
        }
        .school-search {
            margin-top: 8px;
            max-width: 100%;
        }
        .school-checklist {
            grid-template-columns: 1fr;
            max-height: 220px;
        }
        .school-check-item {
            font-size: 13px;
            padding: 8px;
        }
    }
</style>
<body>
	<div class="container">
		<div class="row margin-tb">
	        <div class="col-6">
	            <h2 class="heading ">{{$trainer_data['instructor_name']}} - {{$trainer_data['instructor_code']}}</h2>
	        </div>
	        <div class="col-6">
	        	<button class="float-right back-button col-2" onclick="history.back()">BACK</button>
	        	@if(Auth::user()->role == 1)
	        	<a href=" {{ route('schools-reporting') }}" class="float-right back-button " style="text-decoration:none ; margin-right: 10px;" target="_blank">Check Assigned School</a>
	        	@endif
	        </div>
	    </div>
		<form action="javascript:void(0)" id="trainerForm" name="trainerForm" class="form-horizontal" enctype="multipart/form-data">
			@csrf
			<div class="md-container">
				<div class="modal-header">
				<h4 class="modal-title">Edit Trainer</h4>
				</div>
				<?php
					$role = Auth::user()->role;
					$canEditTrainer = $canEditTrainer ?? ((int) $role === 1);
					$isAdmin = $isAdmin ?? ((int) $role === 1);
					$lockProfile = !$canEditTrainer;
					$lockPay = !$isAdmin;
				?>
				<div class="form-row">
				     <div class="form-group col-md">
				      <label>Trainer Name <span class="text-danger">*</span></label>
				      <input type="text" class="form-control " name="trainer_name" value="{{$trainer_data['instructor_name']}}"  @if($lockProfile) readonly @endif>
				      <input type="hidden" class="form-control " name="id" value="{{$trainer_data['id']}}">
				    </div>
				    <div class="form-group col-md">
				      <label>Father Name <span class="text-danger">*</span></label>
				      <input type="text" class="form-control" name="father_name" value="{{ $trainer_data['father_name'] ?? '' }}" @if($lockProfile) readonly @endif required>
				    </div>
				    <div class="form-group col-md">
				      <label>Email <span class="text-danger">*</span></label>
				      <input type="email" class="form-control " name="email"  value="{{$trainer_data['email']}}"  @if($lockProfile) readonly @endif>
				    </div>
				    <div class="form-group col-md">
				      <label>Amount per School</label>
				      <input type="number" class="form-control" name="amount" value="{{$trainer_data['amount']}}"  @if($lockPay) readonly @endif>
				    </div>
                    <div class="form-group col-md">
                        <label>Incentive Amount</label>
                        <input type="number" class="form-control" name="extra_amount" value="{{$trainer_data['extra_amount']}}"  @if($lockPay) readonly @endif>
                      </div>
				</div>
				<div class="form-row">
					<div class="form-group col-md">
				      <label>Trainer code <span class="text-danger">*</span></label>
				      <input type="text" class="form-control " name="code" value="{{$trainer_data['instructor_code']}}" @if($lockProfile) readonly @endif>
				    </div>
				     <div class="form-group col-md">
				      <label>Phone Number <span class="text-danger">*</span></label>
				      <input type="text" class="form-control" name="number" id="phone_number" value="{{$trainer_data['instructor_number']}}" maxlength="10" @if($lockProfile) readonly @endif>
				    </div>
				     <div class="form-group col-md">
				      <label>Aadhar Number <span class="text-danger">*</span></label>
				      <input type="text" class="form-control" name="aadhar_number" id="aadhar_number"
				             value="{{ $trainer_data['aadhar_number'] ?? '' }}"
				             inputmode="numeric" maxlength="12"
				             placeholder="12 digit Aadhar" @if($lockProfile) readonly @endif required>
				    </div>
				    <div class="form-group col-md">
				      <label>Blood Group <span class="text-danger">*</span></label>
				      <select name="blood_group" class="form-control" @if($lockProfile) disabled @endif required>
				        <option value="">Select</option>
				        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
				            <option value="{{ $bg }}" {{ ($trainer_data['blood_group'] ?? '') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
				        @endforeach
				      </select>
				      @if($lockProfile)
				        <input type="hidden" name="blood_group" value="{{ $trainer_data['blood_group'] ?? '' }}">
				      @endif
				    </div>
				    <div class="form-group col-md">
				      <label>Coordinator <span class="text-danger">*</span></label>
				      <select name="cordinator" class="form-control" @if(!$isAdmin) disabled @endif>
					       <option value="" selected>Select Coordinator</option>
					        @if(isset($cordinator))
								@foreach($cordinator as $key => $data)
					        		<option value="{{$data['id']}}" {{ $trainer_data['cordinator_id'] == $data['id'] ? 'selected' : '' }}>{{$data['cordinator_name']}}</option>
					        	@endforeach
					        @endif
					      </select>
					      @if(!$isAdmin)
					        <input type="hidden" name="cordinator" value="{{ $trainer_data['cordinator_id'] }}">
					      @endif
				    </div>
				     <div class="form-group col-md">
                        <label>District Name <span class="text-danger">*</span></label>
                        <select name="district_name" class="form-control"  @if($lockProfile) disabled @endif>
                            <option value="">Select District</option>
                            @if(isset($district))
                                @foreach($district as $key => $data)
                                    <option value="{{$data['district']}}" {{ $trainer_data['district'] == $data['district'] ? 'selected' : '' }}>{{$data['district']}}</option>
                                @endforeach
                            @endif
                        </select>
                        @if($lockProfile)
                          <input type="hidden" name="district_name" value="{{ $trainer_data['district'] ?? '' }}">
                        @endif
                    </div>
				</div>
				<div class="form-row">
				    <div class="form-group col-md-12">
				        <label>Address <span class="text-danger">*</span></label>
				        <textarea class="form-control" name="address" rows="2" @if($lockProfile) readonly @endif required>{{ $trainer_data['address'] ?? '' }}</textarea>
				    </div>
				    <div class="form-group col-md-6">
				        <label>Expertise (Martial Art Type) <span class="text-danger">*</span></label>
				        <input type="text" class="form-control" name="martial_art_type" value="{{ $trainer_data['martial_art_type'] ?? '' }}" @if($lockProfile) readonly @endif required>
				    </div>
				</div>
				<div class="form-row">
				    <div class="form-group col-md-6">
				        <label>Aadhar Document</label>
				        <input type="file" class="form-control" name="aadhar_doc" accept=".jpg,.jpeg,.png,.pdf" @if($lockProfile) disabled @endif>
				        @if(!empty($trainer_data['aadhar_doc']))
				            <small><a href="{{ media_url('trainer_data', basename($trainer_data['aadhar_doc'])) ?: url('/m/r/'.basename($trainer_data['aadhar_doc'])) }}" target="_blank">View current Aadhar</a></small>
				        @endif
				    </div>
				    <div class="form-group col-md-6">
				        <label>Qualification</label>
				        <input type="file" class="form-control" name="qualification_doc" accept=".jpg,.jpeg,.png,.pdf" @if($lockProfile) disabled @endif>
				        @if(!empty($trainer_data['qualification_doc']))
				            <small><a href="{{ media_url('trainer_data', basename($trainer_data['qualification_doc'])) ?: url('/m/r/'.basename($trainer_data['qualification_doc'])) }}" target="_blank">View current Qualification</a></small>
				        @endif
				    </div>
				    <div class="form-group col-md-6">
				        <label>Martial Art Certificate</label>
				        <input type="file" class="form-control" name="martial_art_doc" accept=".jpg,.jpeg,.png,.pdf" @if($lockProfile) disabled @endif>
				        @if(!empty($trainer_data['martial_art_doc']))
				            <small><a href="{{ media_url('trainer_data', basename($trainer_data['martial_art_doc'])) ?: url('/m/r/'.basename($trainer_data['martial_art_doc'])) }}" target="_blank">View current Certificate</a></small>
				        @endif
				    </div>
				    <div class="form-group col-md-6">
				        <label>Photo</label>
				        <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png" @if($lockProfile) disabled @endif>
				        @if(!empty($trainer_data['photo']))
				            <small><a href="{{ media_url('trainer_data', basename($trainer_data['photo'])) ?: url('/m/r/'.basename($trainer_data['photo'])) }}" target="_blank">View current Photo</a></small>
				        @endif
				    </div>
				</div>

				<div class="mt-4 mb-3" style="border-top: 1px solid #e2e8f0; padding-top: 16px;">
				    <div class="d-flex justify-content-between align-items-center mb-3">
				        <h5 class="font-weight-bold text-primary mb-0">
				            <i class="bi bi-bank2 mr-1"></i> Bank &amp; KYC Details
				        </h5>
				        @if($canEditTrainer)
				        <button type="button" class="btn btn-sm btn-outline-primary" id="toggleBankEditBtn" onclick="toggleBankKycEdit()">
				            <i class="bi bi-pencil-square mr-1"></i> <span id="bankEditBtnText">Edit Bank &amp; KYC</span>
				        </button>
				        @endif
				    </div>
				    @php
				        $hasBankDetails = !empty($trainer_data['bank_name']) 
				            || !empty($trainer_data['account_number']) 
				            || !empty($trainer_data['ifsc_code']) 
				            || !empty($trainer_data['pan_number']) 
				            || !empty($trainer_data['pan_doc']) 
				            || !empty($trainer_data['passbook_doc']);
				    @endphp

				    {{-- View-Only Mode --}}
				    <div id="bankDetailsView">
				        @if($hasBankDetails)
				            <div class="table-responsive" style="max-width: 100%;">
				                <table class="table table-sm table-bordered mb-0" style="background: #fafafa; font-size: 13px;">
				                    <tbody>
				                        <tr>
				                            <th style="width: 25%; background: #f1f5f9;">Bank Name</th>
				                            <td style="width: 25%;"><strong>{{ $trainer_data['bank_name'] ?? '—' }}</strong></td>
				                            <th style="width: 25%; background: #f1f5f9;">Account Holder Name</th>
				                            <td style="width: 25%;"><strong>{{ $trainer_data['account_holder_name'] ?? ($trainer_data['instructor_name'] ?? '—') }}</strong></td>
				                        </tr>
				                        <tr>
				                            <th style="background: #f1f5f9;">Account Number</th>
				                            <td><strong>{{ $trainer_data['account_number'] ?? '—' }}</strong></td>
				                            <th style="background: #f1f5f9;">IFSC Code</th>
				                            <td><strong class="text-uppercase">{{ $trainer_data['ifsc_code'] ?? '—' }}</strong></td>
				                        </tr>
				                        <tr>
				                            <th style="background: #f1f5f9;">PAN Number</th>
				                            <td colspan="3"><strong class="text-uppercase">{{ $trainer_data['pan_number'] ?? '—' }}</strong></td>
				                        </tr>
				                        <tr>
				                            <th style="background: #f1f5f9;">Uploaded KYC Documents</th>
				                            <td colspan="3">
				                                <div class="d-flex flex-wrap gap-2">
				                                    @if(!empty($trainer_data['pan_doc']))
				                                        <a href="{{ media_url('trainer_data', basename($trainer_data['pan_doc'])) ?: url('/m/r/'.basename($trainer_data['pan_doc'])) }}" target="_blank" class="btn btn-sm btn-info text-white mr-2" style="font-size: 11px; padding: 4px 8px;">
				                                            <i class="bi bi-file-earmark-image"></i> View PAN Card
				                                        </a>
				                                    @endif
				                                    @if(!empty($trainer_data['passbook_doc']))
				                                        <a href="{{ media_url('trainer_data', basename($trainer_data['passbook_doc'])) ?: url('/m/r/'.basename($trainer_data['passbook_doc'])) }}" target="_blank" class="btn btn-sm btn-info text-white" style="font-size: 11px; padding: 4px 8px;">
				                                            <i class="bi bi-file-earmark-image"></i> View Passbook / Cheque
				                                        </a>
				                                    @endif
				                                    @if(empty($trainer_data['pan_doc']) && empty($trainer_data['passbook_doc']))
				                                        <span class="text-muted">No documents uploaded</span>
				                                    @endif
				                                </div>
				                            </td>
				                        </tr>
				                    </tbody>
				                </table>
				            </div>
				        @else
				            <div class="alert alert-light border py-2 mb-0" style="font-size: 13px;">
				                <i class="bi bi-info-circle mr-1 text-muted"></i> No bank details uploaded by this trainer yet. Click <strong>Edit Bank &amp; KYC</strong> above to add bank details.
				            </div>
				        @endif
				    </div>

				    {{-- Edit Mode --}}
				    @if($canEditTrainer)
				    <div id="bankDetailsEdit" style="display: none; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px;">
				        <div class="form-row">
				            <div class="form-group col-md-6">
				                <label>Bank Name</label>
				                <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="e.g. State Bank of India" value="{{ $trainer_data['bank_name'] ?? '' }}">
				            </div>
				            <div class="form-group col-md-6">
				                <label>Account Holder Name</label>
				                <input type="text" class="form-control" name="account_holder_name" id="account_holder_name" placeholder="Name as per bank records" value="{{ $trainer_data['account_holder_name'] ?? ($trainer_data['instructor_name'] ?? '') }}">
				            </div>
				        </div>
				        <div class="form-row">
				            <div class="form-group col-md-6">
				                <label>Account Number</label>
				                <input type="text" class="form-control" name="account_number" id="account_number" placeholder="Enter bank account number" value="{{ $trainer_data['account_number'] ?? '' }}">
				            </div>
				            <div class="form-group col-md-6">
				                <label>IFSC Code</label>
				                <input type="text" class="form-control text-uppercase" name="ifsc_code" id="ifsc_code" placeholder="e.g. SBIN0001234" maxlength="11" value="{{ $trainer_data['ifsc_code'] ?? '' }}" oninput="this.value = this.value.toUpperCase()">
				            </div>
				        </div>
				        <div class="form-row">
				            <div class="form-group col-md-4">
				                <label>PAN Number</label>
				                <input type="text" class="form-control text-uppercase" name="pan_number" id="pan_number" placeholder="e.g. ABCDE1234F" maxlength="10" value="{{ $trainer_data['pan_number'] ?? '' }}" oninput="this.value = this.value.toUpperCase()">
				            </div>
				            <div class="form-group col-md-4">
				                <label>PAN Card Document</label>
				                <input type="file" class="form-control" name="pan_doc" accept=".jpg,.jpeg,.png,.pdf">
				                @if(!empty($trainer_data['pan_doc']))
				                    <small class="d-block mt-1"><a href="{{ media_url('trainer_data', basename($trainer_data['pan_doc'])) ?: url('/m/r/'.basename($trainer_data['pan_doc'])) }}" target="_blank"><i class="bi bi-file-earmark-check"></i> View current PAN Document</a></small>
				                @endif
				            </div>
				            <div class="form-group col-md-4">
				                <label>Passbook / Cancelled Cheque</label>
				                <input type="file" class="form-control" name="passbook_doc" accept=".jpg,.jpeg,.png,.pdf">
				                @if(!empty($trainer_data['passbook_doc']))
				                    <small class="d-block mt-1"><a href="{{ media_url('trainer_data', basename($trainer_data['passbook_doc'])) ?: url('/m/r/'.basename($trainer_data['passbook_doc'])) }}" target="_blank"><i class="bi bi-file-earmark-check"></i> View current Passbook</a></small>
				                @endif
				            </div>
				        </div>
				        <div class="d-flex justify-content-between align-items-center mt-3 pt-2" style="border-top: 1px dashed #cbd5e1;">
				            <button type="button" class="btn btn-sm btn-secondary" onclick="toggleBankKycEdit()">
				                <i class="bi bi-x-circle mr-1"></i> Cancel
				            </button>
				            <button type="button" class="btn btn-sm btn-success px-3" onclick="$('#btn-save').click();">
				                <i class="bi bi-check2-circle mr-1"></i> Save Bank &amp; KYC Details
				            </button>
				        </div>
				    </div>
				    @endif
				</div>
				@if($canEditTrainer)
				<div class="row">
                    <div class="form-group col-md">
                        <label>District <small class="text-muted">(for school assign)</small></label>
                        <select name="district" class="form-control" id="check_distt">
                            <option value="">Select District</option>
                            @if(isset($district))
                                @foreach($district as $key => $data)
                                    <option value="{{$data['id']}}"
                                        {{ isset($trainer_data['district']) && $trainer_data['district'] == $data['district'] ? 'selected' : '' }}>
                                        {{$data['district']}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                	</div>
                    <div class="form-group col-md">
                        <label>Block Name</label>
                        <select name="block" class="form-control" id="check_block" >
                            <option value="">Select Block</option>
                        </select>
                    </div>
				</div>
				<div class="row">
                    <div class="form-group col-12">
                        <label>School Name</label>
                        <div class="school-tools">
                            <label class="mb-0">
                                <input class="selectAll" type="checkbox" id="selectAll"> Select All Schools for Selected Block
                            </label>
                            <input type="text" id="schoolSearch" class="form-control school-search" placeholder="Search schools...">
                        </div>
                        <div id="schoolChecklist" class="school-checklist">
                            <div class="school-empty">Select district and block to load schools.</div>
                        </div>
                    </div>
				</div>
				<div class="save-trainer">
					<button type="submit" class="btn btn-primary float-right update-btn" id="btn-save">Save</button>
				</div>
				@endif
			</div>
		</form>
		<div class="card-body">
	        <table class="table table-bordered" id="trainerAssScholls">
	            <thead>
	                <tr>
	                    <th>Assigned Schools ( {{count($trainer_data['asigned_schools'])}} )</th>
	                    <th>District</th>
	                    <th>Block</th>
	                    <th>Assigned Date</th>
	                    <th>Assigned by</th>
	                    <th>Remove School</th>
	                </tr>
	            </thead>
	            <tbody>
	                @foreach($trainer_data['asigned_schools'] as $data)
	                    <tr>
	                        <td>
	                        	@foreach($school as $s_name)
	                        		@if($data['school_name'] == $s_name['id'])
	                        			<strong>{{$s_name['school_name']}}</strong>
	                        		@endif
	                        	@endforeach
	                        </td>
	                        <td>
	                        	@foreach($district as $s_name)
	                        		@if($data['district'] == $s_name['id'])
	                        			{{$s_name['district']}}
	                        		@endif
	                        	@endforeach
	                        </td>
	                        <td>{{$data['block']}}</td>
	                        <td>{{date('d/m/y', strtotime($data['created_at']))}}</td>
	                        <td>
	                        	@foreach($user as $us_data )
	                        		@if($data['asigned_by'] == $us_data['id'])
	                        			{{$us_data['instructor_name']}}
	                        		@endif
	                        	@endforeach
	                        </td>
	                        <td>
	                        	@if($canEditTrainer && $data['uc_submitted'] == 0)
	                            <a href="javascript:void(0)" data-url="{{ route('a-school' ,[$data['id'] ,$data['school_name']]) }}" class="btn" id="asignedSchoolDelete"><i class="bi bi-x-circle-fill remove"></i> </a>
	                            @elseif($data['uc_submitted'] != 0)
	                            	<span class="compete">UC Received</span>
	                            @else
	                            	—
	                            @endif
							</td>
	                    </tr>
	                @endforeach
	            </tbody>
	        </table>
    	</div>
	</div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript">

	const trainerAssScholls =  $('#trainerAssScholls').DataTable( {
	    ordering: false,
	    dom: 'Bfrtip',
	    pageLength : 30,
	    stateSave: true,
	    searchHighlight: true,
	});

    const trainerSavedBlock = @json($trainer_data['block'] ?? null);

    function loadBlocks(districtId, selectedBlock, thenLoadSchools) {
        $("#check_block").html('<option value="">Select Block</option>');
        $("#schoolChecklist").html('<div class="school-empty">Select block to load schools.</div>');
        $("#selectAll").prop('checked', false);
        $("#schoolSearch").val('');
        if (!districtId) return;

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            type: "POST",
            url: '/blockdata',
            data: { id: districtId },
            success: function (result) {
                $('#check_block').html('<option value="">Select Block</option>');
                $.each(result.block || [], function (key, value) {
                    var sel = selectedBlock && selectedBlock === value.block ? 'selected' : '';
                    $("#check_block").append('<option value="' + value.block + '" ' + sel + '>' + value.block + '</option>');
                });
                if (selectedBlock && thenLoadSchools) {
                    $('#check_block').trigger('change');
                }
            }
        });
    }

    // Profile district → assignment district sync
    $('select[name="district_name"]').on('change', function () {
        var name = $(this).val();
        var matched = '';
        $('#check_distt option').each(function () {
            if ($(this).text().trim() === String(name).trim()) {
                matched = $(this).val();
                return false;
            }
        });
        if (matched) {
            $('#check_distt').val(matched).trigger('change');
        }
    });

    $(function () {
        var distId = $('#check_distt').val();
        if (distId) {
            loadBlocks(distId, trainerSavedBlock, false);
        }
    });

    function renderSchoolChecklist(list) {
        var $checklist = $("#schoolChecklist");
        $checklist.html('');

        if (!list || !list.length) {
            $checklist.html('<div class="school-empty">No schools available for this block.</div>');
            return;
        }

        $.each(list, function (key, value) {
            var item = '' +
                '<label class="school-check-item" data-name="' + String(value.school_name).toLowerCase() + '">' +
                    '<input type="checkbox" name="school_name[]" value="' + value.id + '">' +
                    '<span>' + value.school_name + '</span>' +
                '</label>';
            $checklist.append(item);
        });
    }

    $("#selectAll").on('change', function () {
        var checked = $(this).is(':checked');
        $('#schoolChecklist input[type="checkbox"]').prop('checked', checked);
    });

    $("#schoolSearch").on('input', function () {
        var query = String($(this).val() || '').toLowerCase().trim();
        $("#schoolChecklist .school-check-item").each(function () {
            var name = $(this).data('name');
            $(this).toggle(!query || String(name).indexOf(query) !== -1);
        });
    });

	$(document).on('input', '#aadhar_number', function () {
		this.value = this.value.replace(/\D/g, '').slice(0, 12);
	});
	$(document).on('input', '#phone_number', function () {
		this.value = this.value.replace(/\D/g, '').slice(0, 10);
	});

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
            url: '/update-trainer',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
            	Swal.fire({
					position: 'center',
					icon: 'success',
					title: 'Successfully Data  Updated!',
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

    $('#check_distt').change(function () {
        loadBlocks(this.value, null, false);
    });

    $('#check_block').change(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var blockValue = this.value;
        $("#selectAll").prop('checked', false);
        $("#schoolSearch").val('');
        if (!blockValue) {
            $("#schoolChecklist").html('<div class="school-empty">Select block to load schools.</div>');
            return;
        }
        $("#schoolChecklist").html('<div class="school-empty">Loading schools...</div>');
        $.ajax({
            type: "POST",
            url: '/schooldata',
            data: { value: blockValue },
            success: function (result) {
                renderSchoolChecklist(result.school || []);
            }
        })
    });

	$('body').on('click', '#asignedSchoolDelete', function () {

      var userURL = $(this).data('url');
      var trObj = $(this);

      	$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });

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
                type: 'DELETE',
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
					  title: 'Some data add in this School Please Check!'
					})
	            },
            });
		  }
		})
   });

   function toggleBankKycEdit() {
       var viewDiv = document.getElementById('bankDetailsView');
       var editDiv = document.getElementById('bankDetailsEdit');
       var btnText = document.getElementById('bankEditBtnText');
       var btn = document.getElementById('toggleBankEditBtn');

       if (!editDiv) return;

       if (editDiv.style.display === 'none' || editDiv.style.display === '') {
           editDiv.style.display = 'block';
           if (viewDiv) viewDiv.style.display = 'none';
           if (btnText) btnText.innerText = 'Close Bank & KYC Edit';
           if (btn) {
               btn.classList.remove('btn-outline-primary');
               btn.classList.add('btn-outline-secondary');
           }
       } else {
           editDiv.style.display = 'none';
           if (viewDiv) viewDiv.style.display = 'block';
           if (btnText) btnText.innerText = 'Edit Bank & KYC';
           if (btn) {
               btn.classList.remove('btn-outline-secondary');
               btn.classList.add('btn-outline-primary');
           }
       }
   }
</script>
