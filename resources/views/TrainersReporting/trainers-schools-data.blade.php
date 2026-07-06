@extends('layouts.app')

@section('content')
<div class="container mt-2">
	<div class="row">
		<div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Trainers Schools Data</h2>
	        </div>
	        <div class="col-md-2">
	        
	        </div>
	    </div> 
	</div>
	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		<p>{{ $message }}</p>
	</div>
	@endif
	<div class="card-body">
		<table class="table table-bordered" id="trainersSchoolsData">
			<thead>
				<tr>
					<th>Id</th>
					<th>Name</th>
					<th>Trainer code</th>
					<th>Trainer Number</th>
					<th>District</th>
					<th>Total Schools</th>
					<th>Complete Schools</th>
					<th>Pending Schools</th>
					<th>Paid Schools</th>
				</tr>
			</thead>
			<tbody>
			@foreach($trainersWithAssigned as $data)
				@if($data['asigned_schools'] != null)
					<tr class="data{{$data['id']}}">
					    <td>{{$data['id']}}</td>
					    <td>{{$data['instructor_name']}}</td>
					    <td>{{$data['instructor_code']}}</td>
					    <td>{{$data['instructor_number']}}</td>
					    <td>{{$data['district']}}</td>
					    <td>{{count($data['asigned_schools'])}}</td>
					    <td>
					    	<?php $complete_schools = 0; ?>		
							@foreach($data['asigned_schools'] as  $a_schools)
	                        	@if($a_schools['status'] == 1 && $a_schools['paid_status'] == 0)
	                        		<?php $complete_schools++ ?> 
	                        	@endif  	
		                    @endforeach
		                    {{ $complete_schools }}
					    </td>
					    <td>
					    	<?php $pending_schools = 0; ?>		
							@foreach($data['asigned_schools'] as  $a_schools)
	                        	@if($a_schools['status'] == 0)
	                        		<?php $pending_schools++ ?> 
	                        	@endif  	
		                    @endforeach
		                    {{ $pending_schools }}
					    </td>
					    <td>
					    	<?php $paidSchools = 0; ?>		
							@foreach($data['asigned_schools'] as  $a_schools)
	                        	@if($a_schools['paid_status'] == 1)
	                        		<?php $paidSchools++ ?> 
	                        	@endif  	
		                    @endforeach
		                    {{ $paidSchools }}
					    </td>
					</tr>
				@endif	
			@endforeach
        	</tbody>
		</table>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttns/2.4.2/js/buttons.print.min.js"></script>
<script type="text/javascript">
let trainersSchoolsData = $('#trainersSchoolsData').DataTable( {
    ordering: false,
    dom: 'Bfrtip',
   	pageLength : 25,
    buttons: [
         'excel'
    ]
} );

trainersSchoolsData.on('click', 'tbody tr', function (evt) {
    let data = trainersSchoolsData.row(this).data();
    let id = $(this).find("td:first").text();
    let newUrl = "trainer_schools_data/"+id;
   	location.href= newUrl; 
});

</script>
@endsection