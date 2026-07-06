@extends('layouts.app')
@section('content')
<div class="container">
		<div class="row">
	        <div class="col-lg-10 margin-tb">
                <h2 class="heading">{{$trainer_data['instructor_name']}} - {{$trainer_data['instructor_code']}}</h2>
            </div>
	        <div class="col-lg-2">
	           	<a href="{{ route('dashboard') }}" class="back-btn">Back</a>
	        </div>
	    </div>
		<div class="card-body">
			<div class="row mb-2">
				<div class="col-md td-div total-div">
					<span class="trainer-as-hed total-text">Total Schools</span>
					<span class="trainer-as-amt total-text">{{count($trainer_data['asigned_schools'])}}</span>
				</div>
				<div class="col-md td-div compete-div">
					<span class="trainer-as-hed complete-text">UC Received</span>
					<span class="trainer-as-amt complete-text">
						<?php $number = 0; ?>
						@foreach( $trainer_data['asigned_schools'] as $data)					
                    		@if($data['uc_submitted'] == 1)
                    			 <?php $number++ ?> 
                    		@endif
						@endforeach
						{{ $number }}	
				</span>
				</div>
				<div class="col-md td-div pending-div">
					<span class="trainer-as-hed pending-text">UC Pending</span>
					<span class="trainer-as-amt pending-text">
						<?php $number = 0; ?>
						@foreach( $trainer_data['asigned_schools'] as $data)					
                    		@if($data['uc_submitted'] == 0 && $data['route_date'] != null)
                    			 <?php $number++ ?> 
                    		@endif
						@endforeach	
						{{ $number }}	
					</span>
				</div>
				<div class="col-md not-started-dev td-div">
					<span class="trainer-as-hed pending-text">Not Started Schools</span>
					<span class="trainer-as-amt pending-text">
						<?php $number = 0; ?>
						@foreach( $trainer_data['asigned_schools'] as $data)					
                    		@if($data['route_date'] == null)
                    			 <?php $number++ ?> 
                    		@endif
						@endforeach	
						{{ $number }}
					</span>
				</div>
			</div>
	        <table class="table table-bordered" id="trainerDetail">
	            <thead>
	                <tr>
	                    <th>Assigned Schools</th>
	                    <th>District</th>
	                    <th>Block</th>
	                    <th>Route Plan</th>
	                    <th>UC Status</th>
	                    <th>Remarks</th>
	                </tr>
	            </thead>
	            <tbody>
	                @foreach( $trainer_data['asigned_schools'] as $data)
	                    <tr>
	                        <td style="width: 15%;">
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
	                        <td style="width: 32%;">
	                        	@if($data['route_date'] != null)
	                            	<i class="bi bi-calendar-date-fill"></i> {{$data['route_date']}}  <i class="bi bi-clock-fill"></i>  {{date('H:i', strtotime($data['start_route_plan']))}} - {{date('H:i', strtotime($data['end_route_plan']))}}
	                            @endif	
	                        </td>
	                        <td style="text-align: center;">
                        		@if($data['uc_submitted'] == 1)
                        			<span class="compete">UC Submitted</span>
                        		@elseif($data['route_date'] == null)
                        			<span class="not-started">Not Started</span>
                        		@else
                        			<span class="pending">UC Pending</span>	
                        		@endif     		
							</td>
							<td class="{{$data['id']}}"> 
		                            	<a href="{{ route('remark', $data['id']) }}" id="suspendd" data-toggle="modal"
			                            data-target="#demoModal{{ $data['id'] }}"
			                            class="send_btn ml-3">Add Remark</i></a>
			                            <form id="uplodeForm" action="{{ route('remark', $data['id']) }}" method="POST">
		                            	@csrf
		                             <!-- Modal Example Start-->
		                                 <div class="modal fade note-model" id="demoModal{{ $data['id'] }}" value="{{$data['id']}}" tabindex="-1" role="dialog" aria- labelledby="demoModalLabel" aria-hidden="true">
		                                    <div class="modal-dialog" role="document">
		                                        <div class="modal-content">
		                                            <div class="modal-header">
		                                                <h5 class="modal-title" id="demoModalLabel{{ $data['id'] }}" style="color: #fff ;">
		                                                    Remark</h5>
		                                                <button type="button" class="close"
		                                                    data-dismiss="modal" aria- label="Close">
		                                                    <span aria-hidden="true">&times;</span>
		                                                </button>
		                                            </div>
		                                            <div class="modal-body">
		                                            <div class="row remark-div">
		                                            	@if(isset($data['remark']))
		                                            		<strong class="premark-had">Previous Remarks</strong>
			                                            	<?php
			                                            		$remarks = (explode("OR", $data['remark']));
			                                            	?>
			                                            	@foreach($remarks as $key => $remark)
			                                            		<span class="remark">{{$remark}}</span>
			                                            	@endforeach
		                                            	@endif
					                                    <textarea rows="5" cols="15" class="form-control summernote" placeholder="Write here" name="remark"
                                                    value="remark" id="remark" required></textarea> 
					                                        <input type="hidden" id="id" name="id" class="form-control " value="{{$data['id']}}" >
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
		                            <!-- Modal Example End-->
		                            </form>
		                        </td> 
	                    </tr>
	                @endforeach  
	            </tbody>
	        </table>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">

$('#trainerDetail').DataTable( {
    ordering: false,
    order: [[3, 'desc']]
} );

</script>