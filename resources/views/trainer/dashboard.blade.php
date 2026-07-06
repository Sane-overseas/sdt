@extends('layouts.app')

@section('content')
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
</style>
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
<div class="container mt-2">
	<div class="row margin-tb mb-2 mt-2">
        <div class="col-md-10">
            <h2 class="heading ">Trainer Performance</h2>
        </div>
        <div class="col-md-2">
			<a href="#" id="suspendd" data-toggle="modal" data-target="#demoModal" class="claim_btn">Claim</a>
			<form action="{{ route('claim-note')}}" method="post">
            @csrf
			<div class="modal fade note-model" id="demoModal" value="1" tabindex="-1" role="dialog" aria- labelledby="demoModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="demoModalLabel" style="color: #fff ;">
                                Send your Claim Message</h5>
                            <button type="button" class="close"
                                data-dismiss="modal" aria- label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <div class="row ">
                        	<input type="hidden" name="id" value="{{$user['id']}}">
                            <textarea rows="5" cols="15" class="form-control summernote" placeholder="Write here" name="claim_note" value="image_note" id="image_note" required></textarea>
                        </div>
                    </div>
                        <div class="modal-footer">
                            <button type="button" class="close-btn"
                                data-dismiss="modal">Close</button>
                            <button  type="submit" class="up-save" >Save</button>
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
							    				{{-- @if($image['status'] == 0) --}}
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
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$('#dashboardTable').DataTable( {
    ordering: false,
    info:     false,
    lengthMenu:     "Show _MENU_ entries",
    responsive: true,
    	lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, 'All']
    ]
});
</script>
@endsection
