<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
	<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
	<title></title>
</head>
<body>
	 @include('layouts.navigation')
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
	<div class="container">
		<div class="row">
	        <div class="col-lg-10 margin-tb">
                <h2 class="heading">{{$trainer_data['instructor_name']}} - {{$trainer_data['instructor_number']}}</h2>
            </div>
	        <div class="col-lg-2">
	           	<a href="#" class="back-btn" onclick="history.back()">Back</a>
	        </div>
	    </div>
		<div class="card-body">
			<div class="row mb-2">
				<div class="col-md td-div total-div">
					<span class="trainer-as-hed total-text">Total Schools</span></br>
					<span class="trainer-as-amt total-text">{{count($trainer_data['asigned_schools'])}}</span>
				</div>
				<div class="col-md td-div compete-div">
					<span class="trainer-as-hed complete-text">Complete Schools</span>
					<span class="trainer-as-amt complete-text">
						<?php $number = 0; ?>
						@foreach( $trainer_data['asigned_schools'] as $data)
							@foreach($schools as $school)
		                        @if($data['school_name'] == $school['id'])
		                        	@if($school['status'] == 1 && $data['paid_status'] == 0)
		                        		<?php $number++ ?>
		                        	@endif
		                        @endif
		                    @endforeach
						@endforeach
						{{ $number }}
				</span>
				</div>
				<div class="col-md td-div pending-div">
					<span class="trainer-as-hed pending-text">Pending Schools</span></br>
					<span class="trainer-as-amt pending-text">
						<?php $number = 0; ?>
						@foreach($trainer_data['asigned_schools'] as $key => $data)
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
					@foreach($trainer_data['asigned_schools'] as $key => $data)
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
					<span class="trainer-as-hed pending-text">Paid Schools</span></br>
					<span class="trainer-as-amt pending-text">
					<?php $paid = 0; ?>
					@foreach($trainer_data['asigned_schools'] as $key => $data)
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
			<table class="table table-bordered">
				<thead>
					<tr>
						<td><strong>Months</strong></td>
						@foreach($userArr as $moths)
							<th>{{$moths['month']}}</th>
						@endforeach
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong>Total Schools</strong></td>
						@foreach($userArr as $moths)
							<td>{{$moths['count']}}</td>
						@endforeach
					</tr>

				</tbody>
			</table>
	        <table class="table table-bordered" id="trainerDetail">
	            <thead>
	                <tr>
	                    <th>Assigned Schools</th>
	                    <th>District</th>
	                    <th>Block</th>
	                    <th>Complete Date</th>
	                    <th>Month</th>
	                    <th>Videos</th>
	                    <th>Images</th>
	                    <th>Completion</th>
	                    <th>Distribution</th>
	                    <th>Schools Status</th>
	                </tr>
	            </thead>
	            <tbody>
	                @foreach($trainer_data['asigned_schools'] as $data)
	                    <tr>
	                        <td style="width: 15%;">
	                        	@foreach($schools as $s_name)
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
	                        <td>{{$data['end_date']}}</td>
	                        <td>
	                        @if($data['end_date'] != null)
	                        	{{date('M', strtotime($data['end_date']))}}
	                        @endif
	                        </td>
	                        <td style="width: 32%;">
	                        	@foreach($schools as $school)
		                            @if($data['school_name'] == $school['id'])
		                                @if($trainer_data['videos'] != null)
		                                    @foreach($trainer_data['videos'] as $video)
		                                    	@if($school['school_name'] == $video['school_name'])
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
	                         <td style="width: 32%;">
	                        	@foreach($schools as $school)
		                            @if($data['school_name'] == $school['id'])
		                                @if($trainer_data['images'] != null)
		                                    @foreach($trainer_data['images'] as $image)
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
	                         <td style="width: 32%;">
	                        	@foreach($schools as $school)
		                            @if($data['school_name'] == $school['id'])
		                                @if($trainer_data['completions'] != null)
		                                    @foreach($trainer_data['completions'] as $completion)
		                                    	@if($school['school_name'] == $completion['school_name'])
		                                            @if($completion['status'] == 1)
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
	                         <td style="width: 32%;">
	                        	@foreach($schools as $school)
		                            @if($data['school_name'] == $school['id'])
		                                @if($trainer_data['distributions'] != null)
		                                    @foreach($trainer_data['distributions'] as $distribution)
		                                    	@if($school['school_name'] == $distribution['school_name'])
		                                           	@if($distribution['status'] == 1)
		                                                Complete <i class="bi-check-circle-fill nav-icn success-icon"></i>
		                                            @else
		                                                Approval Pending <i class="bi bi-info-circle-fill dash-pending"></i>
		                                            @endif
		                                        @endif
		                                    @endforeach
		                                @endif
		                            @endif
		                        @endforeach
	                        </td>
	                        <td style="text-align: center;">
	                        	@foreach($schools as $sch)
		                            @if($data['school_name'] == $sch['id'])
			                        	@if($data['route_date'] == null)
			                        		<span class="not-started">Not Started</span>
			                        	@elseif($data['paid_status'] == 1)
                            				<span class="paid">Paid</span>
			                        	@elseif($sch['status'] == 0)
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
</body>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttns/2.4.2/js/buttons.print.min.js"></script>
<script type="text/javascript">

$('#trainerDetail').DataTable( {
	ordering: false,
    dom: 'Bfrtip',
    pageLength : 25,
    buttons: [
        'excel'
    ]
} );

</script>
