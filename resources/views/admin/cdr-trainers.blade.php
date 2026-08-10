@extends('layouts.app')

@section('content')
<div class="container mt-2">
	<div class="row">
		<div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">
                {{ isset($coordinator) ? ($coordinator->instructor_name.' — Team') : 'Coordinator Trainers' }}
            </h2>
	        </div>
	        <div class="col-md-2">
	        	<a href="{{ route('trainer-reporting') }}" class="back-btn">Back</a>
	        </div>
	    </div>
	</div>
	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		<p>{{ $message }}</p>
	</div>
	@endif
	@if ($message = Session::get('error'))
	<div class="alert alert-danger">
		<p>{{ $message }}</p>
	</div>
	@endif

	@if(isset($coordinator))
	<div class="row margin-tb mt-2">
		<div class="col-md-10">
			<h3 class="heading" style="font-size:1.25rem;">Coordinator’s own training schools</h3>
			<p class="text-muted small mb-2">Schools this coordinator is training personally (not via sub-trainers).</p>
		</div>
		<div class="col-md-2 text-right">
			<a href="{{ url('getData/'.$coordinator->id) }}" class="btn btn-sm btn-outline-primary">Open profile</a>
		</div>
	</div>
	<div class="card-body">
		<table class="table table-bordered" id="coordinatorOwnSchools">
			<thead>
				<tr>
					<th>#</th>
					<th>School Name</th>
					<th>Route Plan</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			@forelse(($ownSchoolRows ?? []) as $i => $row)
				<tr>
					<td>{{ $i + 1 }}</td>
					<td><strong>{{ $row['school_name'] }}</strong></td>
					<td>{{ $row['route_date'] ?: 'Not started' }}</td>
					<td>
						@if((int) $row['status'] === 1)
							Complete
						@elseif(!empty($row['route_date']))
							Pending
						@else
							Not started
						@endif
					</td>
				</tr>
			@empty
				<tr>
					<td></td>
					<td class="text-muted">No schools assigned to this coordinator for personal training.</td>
					<td></td>
					<td></td>
				</tr>
			@endforelse
			</tbody>
		</table>
	</div>
	@endif

	<div class="row margin-tb mt-3">
		<div class="col-md-12">
			<h3 class="heading" style="font-size:1.25rem;">Sub-trainers</h3>
		</div>
	</div>
	<div class="card-body">
		<table class="table table-bordered" id="cordinatortrainersSchoolsData">
			<thead>
				<tr>
					<th>Id</th>
					<th>Name</th>
					<th>Trainer code</th>
					<th>Trainer Number</th>
					<th>District</th>
					<th>Assigned Schools</th>
					<th>Complete</th>
					<th>Pending</th>
					<th>Not Started</th>
					<th>Get Data</th>
				</tr>
			</thead>
			<tbody>
			@forelse($trainers as $data)
					<tr class="data{{$data['id']}}">
					    <td>{{$data['id']}}</td>
					    <td><strong>{{$data['instructor_name']}}</strong></td>
					    <td>{{$data['instructor_code']}}</td>
					    <td>{{$data['instructor_number']}}</td>
					    <td>{{$data['district']}}</td>
					    <td>{{ count($data['asigned_schools'] ?? []) }}</td>
					    <td>
					    	<?php $complete_schools = 0; $pending_schools = 0; $not_started = 0; ?>
							@foreach(($data['asigned_schools'] ?? []) as $a_schools)
	                        	@if($a_schools['status'] == 1)
	                        		<?php $complete_schools++ ?>
	                        	@endif
	                        	@if($a_schools['status'] == 0 && !empty($a_schools['route_date']))
	                        		<?php $pending_schools++ ?>
	                        	@endif
	                        	@if(empty($a_schools['route_date']))
	                        		<?php $not_started++ ?>
	                        	@endif
		                    @endforeach
		                    {{ $complete_schools }}
					    </td>
					    <td>{{ $pending_schools }}</td>
					    <td>{{ $not_started }}</td>
					    <td style="text-align:center;">
					    	<a href="{{ url('getData/'.$data['id']) }}" class="btn saveStopitems" title="Open trainer">
					    		<i class="bi bi-caret-down-square save-icon"></i>
					    	</a>
					    </td>
					</tr>
			@empty
				<tr>
					<td></td>
					<td class="text-muted">No sub-trainers under this coordinator.</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			@endforelse
        	</tbody>
		</table>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript">
$('#cordinatortrainersSchoolsData').DataTable({
    ordering: false,
    dom: 'Bfrtip',
   	pageLength : 10,
    buttons: ['copy', 'csv', 'excel', 'print']
});
if ($('#coordinatorOwnSchools').length) {
	$('#coordinatorOwnSchools').DataTable({ ordering: false, pageLength: 10 });
}
</script>
@endsection
