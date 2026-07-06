<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Trainer</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
</head>
<body>
<div class="container mt-2">
	<div class="row">
		<div class="col-lg-12 margin-tb">
			<div class="pull-left">
				<h2 class="heading">Today Logs</h2>
			</div>
		</div>
	</div>
	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		<p>{{ $message }}</p>
	</div>
	@endif
	<div class="card-body">
		<table class="table table-bordered" id="todayTable">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Trainer Number</th>
					<th>District</th>
					<th>School</th>
					<th>Route Plan</th>
					<th>Remark</th>
				</tr>
			</thead>
			<tbody>
				@foreach($today_logs as $today_data)
					@if($today_data->uc_submitted == 0)
						<tr>
							<td class="get-data">{{$today_data->user_id}}</td>
						    <td class="get-data">{{$today_data->instructor_name}}</td>
						    <td class="get-data">{{$today_data->instructor_number}}</td>
						    <td class="get-data">
						    	@foreach($district as $data)
						    		@if($data['id'] == $today_data->district)
						    			{{$data['district']}}
						    		@endif
						    	@endforeach
						    </td>
						     <td class="get-data">
						    	@foreach($schools as $school)
						    		@if($school['id'] == $today_data->school_name)
						    			{{$school['school_name']}}
						    		@endif
						    	@endforeach
						    </td>
						    <td class="get-data">
						    	<i class="bi bi-calendar-date-fill"></i> {{$today_data->route_date}}  <i class="bi bi-clock-fill"></i>  {{date('H:i', strtotime($today_data->start_route_plan))}} - {{date('H:i', strtotime($today_data->end_route_plan))}}
						    </td>
						    <td class="{{$today_data->id}}"> 
	                            	<a href="{{ route('remark', $today_data->id) }}" id="suspendd" data-toggle="modal"
		                            data-target="#demoModal{{ $today_data->id }}"
		                            class="send_btn ml-3">Add Remark</i></a>
		                            <form id="uplodeForm" action="{{ route('remark', $today_data->id) }}" method="POST">
	                            	@csrf
	                             <!-- Modal Example Start-->
	                                 <div class="modal fade note-model" id="demoModal{{ $today_data->id }}" value="{{$today_data->id}}" tabindex="-1" role="dialog" aria- labelledby="demoModalLabel" aria-hidden="true">
	                                    <div class="modal-dialog" role="document">
	                                        <div class="modal-content">
	                                            <div class="modal-header">
	                                                <h5 class="modal-title" id="demoModalLabel{{ $today_data->id }}" style="color: #fff ;">
	                                                    Remark</h5>
	                                                <button type="button" class="close"
	                                                    data-dismiss="modal" aria- label="Close">
	                                                    <span aria-hidden="true">&times;</span>
	                                                </button>
	                                            </div>
	                                            <div class="modal-body">
	                                            <div class="row remark-div">
	                                            	@if(isset($today_data->remark))
	                                            		<strong class="premark-had">Previous Remarks</strong>
		                                            	<?php
		                                            		$remarks = (explode("OR", $today_data->remark));
		                                            	?>
		                                            	@foreach($remarks as $key => $remark)
		                                            		<span class="remark">{{$remark}}</span>
		                                            	@endforeach
	                                            	@endif
				                                    <textarea rows="5" cols="15" class="form-control summernote" placeholder="Write here" name="remark"
                                                value="remark" id="remark" required></textarea> 
				                                        <input type="hidden" id="id" name="id" class="form-control " value="{{$today_data->id}}" >
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
					@endif	
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script type="text/javascript">

let todayTable = $('#todayTable').DataTable( {
    ordering: false,
    buttons: [
         'csv', 'excel', 'pdf', 'print'
    ]
} );

todayTable.on('click', 'tbody tr', function (evt) {
    let data = todayTable.row(this).data();
    let id = $(this).find("td:first").text();
    if($(evt.target).is(".get-data")){
    	let newUrl = "trainer_data/"+id;
   		location.href= newUrl;
    }
     
});

</script>
</html>