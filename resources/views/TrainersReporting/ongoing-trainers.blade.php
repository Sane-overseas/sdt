@extends('layouts.app')

@section('content')
<div class="container mt-2">
	<div class="row">
		<div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">OnGoing Trainers</h2>
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
		<table class="table table-bordered" id="trainerAssigned">
			<thead>
				<tr>
					<th>Id</th>
					<th>Name</th>
					<th>Email</th>
					<th>Trainer code</th>
					<th>Trainer Number</th>
					<th>District</th>
				</tr>
			</thead>
			<tbody>
			@foreach($trainersWithAssigned as $data)
				@if($data['asigned_schools'] != null)
					<tr class="data{{$data['id']}}">
					    <td>{{$data['id']}}</td>
					    <td>{{$data['instructor_name']}}</td>
					    <td>{{$data['email']}}</td>
					    <td>{{$data['instructor_code']}}</td>
					    <td>{{$data['instructor_number']}}</td>
					    <td>{{$data['district']}}</td>
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

$('#trainerAssigned').DataTable( {
	ordering: false,
    dom: 'Bfrtip',
   	pageLength : 25,
    buttons: [
         'excel', 
    ]
} );

</script>
@endsection