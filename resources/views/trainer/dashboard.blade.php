@extends('layouts.app')

@section('content')
@php
    $isStateCoordinator = !empty($isStateCoordinator);
@endphp
<style type="text/css">
.claim_btn {
    border-radius: 5px;
    background-color: #000;
    color: #fff;
    padding: 5px 16px;
    float: right;
}
.claim_btn:hover {
    color: #ffffff;
}
@media only screen and (max-width: 600px){
	.claim_btn {
	    margin-top: -33px;
	}
}
.district-school-block {
    margin-bottom: 1.5rem;
}
.district-school-block h3 {
    font-size: 1.15rem;
    margin-bottom: 0.5rem;
    color: #004857;
}
</style>

@if($isStateCoordinator)
{{-- State coordinator dashboard: all schools by district, no Paid card --}}
<div class="mt-4">
	<div class="row m-2">
		<div class="col-md total-div td-div">
			<span class="trainer-as-hed total-text">Total Schools</span>
			<span class="trainer-as-amt total-text">{{ $stateSchoolSummary['total'] ?? 0 }}</span>
		</div>
		<div class="col-md compete-div td-div">
			<span class="trainer-as-hed complete-text">Complete Schools</span>
			<span class="trainer-as-amt complete-text">{{ $stateSchoolSummary['complete'] ?? 0 }}</span>
		</div>
		<div class="col-md pending-div td-div">
			<span class="trainer-as-hed pending-text">Assigned Schools</span>
			<span class="trainer-as-amt pending-text">{{ $stateSchoolSummary['assigned'] ?? 0 }}</span>
		</div>
		<div class="col-md not-started-dev td-div">
			<span class="trainer-as-hed pending-text">Not Assigned</span>
			<span class="trainer-as-amt pending-text">{{ $stateSchoolSummary['not_assigned'] ?? 0 }}</span>
		</div>
	</div>
</div>
<div class="container mt-2">
	<div class="row margin-tb mb-2 mt-2">
        <div class="col-md-12">
            <h2 class="heading">All Schools (District-wise)</h2>
        </div>
    </div>

	@foreach(($schoolsByDistrict ?? []) as $block)
	<div class="district-school-block">
		<h3>{{ $block['district'] }} <span class="text-muted">({{ $block['count'] }})</span></h3>
		<table class="table table-bordered state-district-schools-table">
			<thead>
				<tr>
					<th>School Name</th>
					<th>Block</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			@forelse($block['schools'] as $school)
				<tr>
					<td><strong>{{ $school['school_name'] }}</strong></td>
					<td>{{ $school['block'] ?: '—' }}</td>
					<td>
						@if($school['status_label'] === 'Complete')
							<span class="compete">{{ $school['status_label'] }}</span>
						@elseif($school['status_label'] === 'Pending')
							<span class="pending">{{ $school['status_label'] }}</span>
						@elseif($school['status_label'] === 'Not Assigned')
							<span class="not-started">{{ $school['status_label'] }}</span>
						@else
							<span class="pending">{{ $school['status_label'] }}</span>
						@endif
					</td>
				</tr>
			@empty
				<tr>
					<td class="text-muted">No schools in this district.</td>
					<td></td>
					<td></td>
				</tr>
			@endforelse
			</tbody>
		</table>
	</div>
	@endforeach
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script type="text/javascript">
$('.state-district-schools-table').each(function () {
	if ($(this).find('tbody tr td').length >= 3) {
		$(this).DataTable({
			ordering: false,
			pageLength: 10,
			lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
		});
	}
});
</script>

@else
{{-- District coordinator / trainer dashboard --}}
<div class="mt-4">
	<div class="row m-2">
		<div class="col-md total-div td-div">
				<span class="trainer-as-hed total-text">Total Schools</span>
				<span class="trainer-as-amt total-text">{{count($user['asigned_schools'])}}</span>
			</div>
			<div class="col-md compete-div td-div">
				<span class="trainer-as-hed complete-text">Complete Schools</span>
				<span class="trainer-as-amt complete-text">
				<?php $complete = 0; ?>
				@foreach($user['asigned_schools'] as $key => $data)
					@foreach($schools as $school)
	                    @if($data['school_name'] == $school['id'])
	                    	@if($school['status'] == 1 && $data['paid_status'] == 0)
	                    		<?php $complete++ ?>
	                    	@endif
	                    @endif
	                @endforeach
	            @endforeach
	            {{ $complete }}
			</span>
			</div>
			<div class="col-md pending-div td-div">
				<span class="trainer-as-hed pending-text">Pending Schools</span>
				<span class="trainer-as-amt pending-text">
				<?php $number = 0; ?>
				@foreach($user['asigned_schools'] as $key => $data)
					@foreach($schools as $school)
	                    @if($data['school_name'] == $school['id'])
	                    	@if($school['status'] == 0 && $data['route_date'] !== null)
	                    		<?php $number++ ?>
	                    	@endif
	                    @endif
	                @endforeach
	            @endforeach
	            {{ $number }}
				</span>
			</div>
			<div class="col-md not-started-dev td-div">
				<span class="trainer-as-hed pending-text">Not Started Schools</span>
				<span class="trainer-as-amt pending-text">
				<?php $number = 0; ?>
				@foreach($user['asigned_schools'] as $key => $data)
					@foreach($schools as $school)
	                    @if($data['school_name'] == $school['id'])
	                    	@if($data['route_date'] == null)
	                    		<?php $number++ ?>
	                    	@endif
	                    @endif
	                @endforeach
	            @endforeach
	            {{ $number }}
				</span>
			</div>
			<div class="col-md paid-dev td-div">
				<span class="trainer-as-hed pending-text">Paid Schools</span>
				<span class="trainer-as-amt pending-text">
				<?php $paid = 0; ?>
				@foreach($user['asigned_schools'] as $key => $data)
					@foreach($schools as $school)
	                    @if($data['school_name'] == $school['id'])
	                    	@if($data['paid_status'] == 1)
	                    		<?php $paid++ ?>
	                    	@endif
	                    @endif
	                @endforeach
	            @endforeach
	            {{ $paid }}
				</span>
			</div>
		</div>
	<div>
</div>
@php
    $completedSchoolsForClaim = [];
    foreach ($user['asigned_schools'] as $data) {
        foreach ($schools as $school) {
            if ($data['school_name'] == $school['id']) {
                $isComplete = (($school['status'] ?? 0) == 1 || ($data['status'] ?? 0) == 1) && ($data['paid_status'] ?? 0) == 0;
                if ($isComplete) {
                    $completedSchoolsForClaim[] = [
                        'asigned_id' => $data['id'],
                        'school_id' => $school['id'],
                        'school_name' => $school['school_name'],
                        'block' => $data['block'] ?? $school['block'] ?? '—',
                        'claim_status' => $data['claim_status'] ?? 0,
                    ];
                }
            }
        }
    }
@endphp
<div class="container mt-2">
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
        {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
        {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
	<div class="row margin-tb mb-2 mt-2">
        <div class="col-md-10">
            <h2 class="heading ">Trainer Performance</h2>
        </div>
        <div class="col-md-2">
			<a href="#" id="suspendd" data-toggle="modal" data-target="#demoModal" class="claim_btn">Claim</a>
			<form action="{{ route('claim-note')}}" method="post" id="claimSubmitForm" enctype="multipart/form-data">
            @csrf
			<div class="modal fade note-model" id="demoModal" value="1" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="demoModalLabel" style="color: #fff ;">
                                Claim Completed Schools</h5>
                            <button type="button" class="close"
                                data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="{{$user['id']}}">
                            
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size: 15px; color: #333;">Select Completed Schools for Claim:</label>
                                @if(count($completedSchoolsForClaim) > 0)
                                    <div class="table-responsive" style="max-height: 240px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                                        <table class="table table-sm table-hover table-bordered mb-0">
                                            <thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">
                                                <tr>
                                                    <th style="width: 45px; text-align: center;">
                                                        <input type="checkbox" id="selectAllClaimSchools" checked title="Select / Deselect All">
                                                    </th>
                                                    <th>School Name</th>
                                                    <th>Block</th>
                                                    <th style="text-align: center; width: 140px;">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($completedSchoolsForClaim as $item)
                                                    <tr>
                                                        <td style="text-align: center; vertical-align: middle;">
                                                            <input type="checkbox" 
                                                                   name="claim_schools[]" 
                                                                   value="{{ $item['asigned_id'] }}" 
                                                                   class="claim-school-checkbox" 
                                                                   id="claim_sch_{{ $item['asigned_id'] }}"
                                                                   checked>
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            <label for="claim_sch_{{ $item['asigned_id'] }}" class="mb-0 font-weight-bold" style="cursor: pointer;">
                                                                {{ $item['school_name'] }}
                                                            </label>
                                                        </td>
                                                        <td style="vertical-align: middle;">{{ $item['block'] }}</td>
                                                        <td style="text-align: center; vertical-align: middle;">
                                                            @if(($item['claim_status'] ?? 0) == 1)
                                                                <span class="badge badge-warning" style="background-color: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px;">Claim Requested</span>
                                                            @else
                                                                <span class="badge badge-success" style="background-color: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px;">Completed</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <small class="text-muted d-block mt-1">Tick the checkbox for each school you want to claim.</small>
                                @else
                                    <div class="alert alert-warning py-2 mb-0">
                                        <i class="bi bi-info-circle-fill mr-1"></i> No completed schools found to claim.
                                    </div>
                                @endif
                            </div>

                            @php
                                $hasSavedBankDetails = !empty($user['account_number']) 
                                    && !empty($user['ifsc_code']) 
                                    && !empty($user['pan_doc']) 
                                    && !empty($user['passbook_doc']);
                            @endphp

                            @if(!$hasSavedBankDetails)
                                <div class="form-group mt-3 mb-2">
                                    <label class="font-weight-bold" style="font-size: 15px; color: #333;">
                                        <i class="bi bi-bank2 mr-1 text-primary"></i> Bank & KYC Details for Payment Transfer:
                                    </label>

                                    <div class="alert alert-info py-2 px-3 mb-2" style="font-size: 13px;">
                                        <i class="bi bi-info-circle-fill mr-1"></i>
                                     Please enter your bank account and upload your PAN & Passbook/Cheque documents for payment.
                                    </div>

                                    <div id="bankDetailsFieldsContainer" style="background: #fdfdfd; padding: 12px; border: 1px solid #e0e0e0; border-radius: 6px;" class="mb-2">
                                        <div class="row">
                                            <div class="col-md-6 form-group mb-2">
                                                <label class="form-label font-weight-bold mb-1" style="font-size: 13px;">Bank Name <span class="text-danger">*</span></label>
                                                <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="e.g. State Bank of India" value="{{ old('bank_name', $user['bank_name'] ?? '') }}" required>
                                            </div>
                                            <div class="col-md-6 form-group mb-2">
                                                <label class="form-label font-weight-bold mb-1" style="font-size: 13px;">Name in Bank (Account Holder) <span class="text-danger">*</span></label>
                                                <input type="text" name="account_holder_name" class="form-control form-control-sm" placeholder="e.g. Full Name as in Passbook" value="{{ old('account_holder_name', $user['account_holder_name'] ?? ($user['instructor_name'] ?? '')) }}" required>
                                            </div>
                                            <div class="col-md-6 form-group mb-2">
                                                <label class="form-label font-weight-bold mb-1" style="font-size: 13px;">Bank Account Number <span class="text-danger">*</span></label>
                                                <input type="text" name="account_number" class="form-control form-control-sm" placeholder="Enter Bank Account Number" value="{{ old('account_number', $user['account_number'] ?? '') }}" required>
                                            </div>
                                            <div class="col-md-6 form-group mb-2">
                                                <label class="form-label font-weight-bold mb-1" style="font-size: 13px;">IFSC Code <span class="text-danger">*</span></label>
                                                <input type="text" name="ifsc_code" class="form-control form-control-sm text-uppercase" placeholder="e.g. SBIN0001234" value="{{ old('ifsc_code', $user['ifsc_code'] ?? '') }}" maxlength="20" style="text-transform: uppercase;" required>
                                            </div>
                                            <div class="col-md-12 form-group mb-2">
                                                <label class="form-label font-weight-bold mb-1" style="font-size: 13px;">PAN Card Number <span class="text-danger">*</span></label>
                                                <input type="text" name="pan_number" class="form-control form-control-sm text-uppercase" placeholder="e.g. ABCDE1234F" value="{{ old('pan_number', $user['pan_number'] ?? '') }}" maxlength="20" style="text-transform: uppercase;" required>
                                            </div>
                                            <div class="col-md-6 form-group mb-2">
                                                <label class="form-label font-weight-bold mb-1" style="font-size: 13px;">
                                                    PAN Card Photo / PDF <span class="text-danger">*</span>
                                                </label>
                                                <input type="file" name="pan_doc" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf" required style="font-size: 12px;">
                                                <small class="text-muted d-block mt-1">Accepted: JPG, PNG, PDF (Max 5MB)</small>
                                            </div>
                                            <div class="col-md-6 form-group mb-2">
                                                <label class="form-label font-weight-bold mb-1" style="font-size: 13px;">
                                                    Passbook / Cheque Photo / PDF <span class="text-danger">*</span>
                                                </label>
                                                <input type="file" name="passbook_doc" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf" required style="font-size: 12px;">
                                                <small class="text-muted d-block mt-1">Accepted: JPG, PNG, PDF (Max 5MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group mb-0 mt-2">
                                <label for="image_note" class="font-weight-bold" style="font-size: 15px; color: #333;">Claim Message / Note:</label>
                                <textarea rows="2" class="form-control" placeholder="Write your claim message / note here..." name="claim_note" id="image_note">{{ $user['claim_note'] ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="close-btn"
                                data-dismiss="modal">Close</button>
                            <button type="submit" class="up-save">Submit Claim</button>
                        </div>
                    </div>
                </div>
            </div>
        	</form>
        </div>
    </div>
    <div>
			<table class="table table-bordered" id="dashboardTable">
				<thead>
					<tr>
						<th>School Name</th>
						<th>Video</th>
						<th>Images</th>
						<th>Completion</th>
						<th>Distribution</th>
						<th>School Status</th>
					</tr>
				</thead>
				<tbody>
				@foreach($user['asigned_schools'] as $key => $data)
					<tr>
					    <td>
					    	@foreach($schools as $school)
					    		@if($data['school_name'] == $school['id'])
					    			<strong>{{$school['school_name']}}</strong>
					    		@endif
					    	@endforeach
					    </td>
					    <td>
					    	@foreach($schools as $school)
					    		@if($data['school_name'] == $school['id'])
					    			@if(isset($videos))
					    				@foreach($videos as $video)
							    			@if($school['school_name'] ==  $video['school_name'])
							    				<?php
							    				$cunt = array($video['fst_video'] ,$video['snd_video']);
	       										$value = count(array_filter($cunt));
							    				?>
							    				@if($value > 0){{$value}}/2 @endif
							    				@if($video['status'] == 0)
							    				<i class="bi bi-info-circle-fill dash-pending"></i>
							    				@else
							    				<i class="bi-check-circle-fill nav-icn success-icon"></i>
							    				@endif
							    			@endif
							    		@endforeach
						    		@else
						    			0/2
						    		@endif
					    		@endif
					    	@endforeach
					    </td>
					    <td>
					    	@foreach($schools as $school)
					    		@if($data['school_name'] == $school['id'])
					    			@if(isset($images))
					    				@foreach($images as $image)
							    			@if($school['school_name'] == $image['school_name'])
							    				<?php
							    				$cunt = array($image['ifsb_image'] ,$image['group_image'] ,$image['fst_aimage'] ,$image['snd_aimage'],$image['trd_aimage']);
	       										$value = count(array_filter($cunt));
							    				?>
							    				@if($value > 0){{$value}}/5 @endif
                                                @if (($image['status'] ?? null) == 0)
							    					<i class="bi bi-info-circle-fill dash-pending"></i>
							    				@else
							    					<i class="bi-check-circle-fill nav-icn success-icon"></i>
							    				@endif
							    			@endif
						    			@endforeach
						    		@else
						    			0/5
						    		@endif
					    		@endif
					    	@endforeach
					    </td>
					    <td>
					    	@foreach($schools as $school)
					    		@if($data['school_name'] == $school['id'])
					    			@if(isset($completion))
					    				@foreach($completion as $c_data)
							    			@if($school['school_name'] == $c_data['school_name'])
							    				@if($c_data['status'] == 1)
							    					Complete <i class="bi-check-circle-fill nav-icn success-icon"></i>
							    				@else
								    				Approval Pending <i class="bi bi-info-circle-fill dash-pending"></i>
								    			@endif
							    			@endif
							    		@endforeach
						    		@else
						    			Pending
						    		@endif
					    		@endif
					    	@endforeach
					    </td>
	                    <td>
	                    	@foreach($schools as $school)
					    		@if($data['school_name'] == $school['id'])
					    			@if(isset($distribution))
					    				@foreach($distribution as $d_data)
							    			@if($school['school_name'] == $d_data['school_name'])
							    				@if($d_data['status'] == 1)
							    					Complete <i class="bi-check-circle-fill nav-icn success-icon"></i>
							    				@else
								    				Approval Pending <i class="bi bi-info-circle-fill dash-pending"></i>
								    			@endif
							    			@endif
							    		@endforeach
						    		@else
						    			Pending
						    		@endif
					    		@endif
					    	@endforeach
	                    </td>
	                    <td style="text-align: center;">
	                    @foreach($schools as $school)
	                        @if($data['school_name'] == $school['id'])
	                        	@if($data['route_date'] == null)
	                        		<span class="not-started">Not Started</span>
	                        	 @elseif($data['paid_status'] == 1)
                            		<span class="paid">Paid</span>
	                        	@elseif($school['status'] == 0)
	                        		<span class="pending">Pending</span>
	                        	@else
	                        		<span class="compete">Completed</span>
	                        	@endif
	                        @endif
	                    @endforeach
	                </td>
					</tr>
				@endforeach
		    	</tbody>
			</table>
		</div>
	</div>
	</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script type="text/javascript">
$('#dashboardTable').DataTable( {
    ordering: false,
    info:     false,
    responsive: true,
    	lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, 'All']
    ]
});

$(document).ready(function() {
    $('#selectAllClaimSchools').on('change', function () {
        $('.claim-school-checkbox').prop('checked', $(this).is(':checked'));
    });

    $('.claim-school-checkbox').on('change', function () {
        const total = $('.claim-school-checkbox').length;
        const checked = $('.claim-school-checkbox:checked').length;
        $('#selectAllClaimSchools').prop('checked', total === checked);
    });

    $('#claimSubmitForm').on('submit', function (e) {
        const total = $('.claim-school-checkbox').length;
        const checked = $('.claim-school-checkbox:checked').length;
        if (total > 0 && checked === 0) {
            e.preventDefault();
            alert('Please select at least one completed school to claim.');
            return false;
        }
    });
});
</script>
@endif
@endsection
