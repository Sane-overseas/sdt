@extends('layouts.app')

@section('content')
<style>
	.tsd-wrap { max-width: 100%; overflow-x: auto; }
	.tsd-stats .td-div { min-height: 72px; }
	#trainerDetail { width: 100% !important; font-size: 13px; }
	#trainerDetail th, #trainerDetail td {
		vertical-align: middle;
		white-space: nowrap;
		padding: 8px 10px;
	}
	#trainerDetail td.school-cell {
		white-space: normal;
		min-width: 160px;
		max-width: 220px;
	}
	#trainerDetail .school-name { display: block; font-weight: 600; line-height: 1.3; }
	#trainerDetail .hours-chip {
		display: inline-block;
		margin-top: 4px;
		padding: 2px 8px;
		border-radius: 999px;
		background: #e8f4ff;
		color: #0b5cab;
		font-size: 11px;
		font-weight: 600;
	}
	#trainerDetail .hours-chip.empty { background: #f1f1f1; color: #777; font-weight: 500; }
	#trainerDetail .loc-line { line-height: 1.35; white-space: normal; min-width: 110px; }
	#trainerDetail .loc-line small { color: #666; display: block; }
	#trainerDetail .upload-cell { text-align: center; white-space: nowrap; }
	#trainerDetail .upload-pill {
		display: inline-flex;
		align-items: center;
		gap: 4px;
		padding: 2px 6px;
		border-radius: 4px;
		background: #f7f7f7;
		font-size: 12px;
		margin: 1px 0;
	}
	.months-table { font-size: 12px; margin-bottom: 1rem; }
	.months-table th, .months-table td { padding: 6px 8px !important; text-align: center; }
	.months-table td:first-child, .months-table th:first-child { text-align: left; white-space: nowrap; }
</style>

@php
	$totalPlanned = 0;
	foreach ($trainer_data['asigned_schools'] as $asg) {
		$totalPlanned += (float) ($asg['planned_hours'] ?? 0);
	}
@endphp

<div class="container mt-2">
	<div class="row">
		<div class="col-lg-10 margin-tb">
			<h2 class="heading">{{ $trainer_data['instructor_name'] }} - {{ $trainer_data['instructor_number'] }}</h2>
		</div>
		<div class="col-lg-2">
			<a href="{{ route('trainers-schools-data') }}" class="back-btn">Back</a>
		</div>
	</div>

	<div class="card-body">
		<div class="row mb-2 tsd-stats">
			<div class="col-md td-div total-div">
				<span class="trainer-as-hed total-text">Total Schools</span><br>
				<span class="trainer-as-amt total-text">{{ count($trainer_data['asigned_schools']) }}</span>
			</div>
			<div class="col-md td-div compete-div">
				<span class="trainer-as-hed complete-text">Complete Schools</span>
				<span class="trainer-as-amt complete-text">
					@php $number = 0; @endphp
					@foreach ($trainer_data['asigned_schools'] as $data)
						@foreach ($schools as $school)
							@if ($data['school_name'] == $school['id'])
								@if ($school['status'] == 1 && $data['paid_status'] == 0)
									@php $number++ @endphp
								@endif
							@endif
						@endforeach
					@endforeach
					{{ $number }}
				</span>
			</div>
			<div class="col-md td-div pending-div">
				<span class="trainer-as-hed pending-text">Pending Schools</span><br>
				<span class="trainer-as-amt pending-text">
					@php $number = 0; @endphp
					@foreach ($trainer_data['asigned_schools'] as $data)
						@foreach ($schools as $school)
							@if ($data['school_name'] == $school['id'])
								@if ($school['status'] == 0 && $data['route_date'] !== null)
									@php $number++ @endphp
								@endif
							@endif
						@endforeach
					@endforeach
					{{ $number }}
				</span>
			</div>
			<div class="col-md not-started-dev td-div">
				<span class="trainer-as-hed pending-text">Not Started</span>
				<span class="trainer-as-amt pending-text">
					@php $number = 0; @endphp
					@foreach ($trainer_data['asigned_schools'] as $data)
						@foreach ($schools as $school)
							@if ($data['school_name'] == $school['id'])
								@if ($data['route_date'] == null)
									@php $number++ @endphp
								@endif
							@endif
						@endforeach
					@endforeach
					{{ $number }}
				</span>
			</div>
			<div class="col-md paid-dev td-div">
				<span class="trainer-as-hed pending-text">Paid Schools</span><br>
				<span class="trainer-as-amt pending-text">
					@php $paid = 0; @endphp
					@foreach ($trainer_data['asigned_schools'] as $data)
						@foreach ($schools as $school)
							@if ($data['school_name'] == $school['id'])
								@if ($data['paid_status'] == 1)
									@php $paid++ @endphp
								@endif
							@endif
						@endforeach
					@endforeach
					{{ $paid }}
				</span>
			</div>
			<div class="col-md td-div total-div">
				<span class="trainer-as-hed total-text">Training Hours</span><br>
				<span class="trainer-as-amt total-text">{{ number_format($totalPlanned, 1) }}</span>
			</div>
		</div>

		<div class="tsd-wrap">
			<table class="table table-bordered months-table">
				<thead>
					<tr>
						<td><strong>Months</strong></td>
						@foreach ($userArr as $moths)
							<th>{{ $moths['month'] }}</th>
						@endforeach
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong>Total Schools</strong></td>
						@foreach ($userArr as $moths)
							<td>{{ $moths['count'] }}</td>
						@endforeach
					</tr>
				</tbody>
			</table>
		</div>

		<div class="tsd-wrap">
			<table class="table table-bordered" id="trainerDetail">
				<thead>
					<tr>
						<th>School</th>
						<th>Location</th>
						<th>Completed</th>
						<th>Videos</th>
						<th>Images</th>
						<th>Completion</th>
						<th>Distribution</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($trainer_data['asigned_schools'] as $data)
						@php
							$schoolRow = null;
							foreach ($schools as $s) {
								if ($data['school_name'] == $s['id']) {
									$schoolRow = $s;
									break;
								}
							}
							$districtName = '';
							foreach ($district as $d) {
								if ($data['district'] == $d['id']) {
									$districtName = $d['district'];
									break;
								}
							}
							$plannedHrs = $data['planned_hours'] ?? null;
						@endphp
						<tr>
							<td class="school-cell">
								<span class="school-name">{{ $schoolRow['school_name'] ?? '—' }}</span>
								@if (!empty($plannedHrs))
									<span class="hours-chip">{{ number_format((float) $plannedHrs, 1) }} hrs</span>
								@else
									<span class="hours-chip empty">No hours</span>
								@endif
							</td>
							<td>
								<div class="loc-line">
									{{ $districtName ?: '—' }}
									<small>{{ $data['block'] }}</small>
								</div>
							</td>
							<td>
								@if ($data['end_date'] != null)
									{{ $data['end_date'] }}
									<small style="display:block;color:#666;">{{ date('M', strtotime($data['end_date'])) }}</small>
								@else
									—
								@endif
							</td>
							<td class="upload-cell">
								@if ($schoolRow && $trainer_data['videos'] != null)
									@foreach ($trainer_data['videos'] as $video)
										@if ($schoolRow['school_name'] == $video['school_name'])
											@php
												$value = count(array_filter([$video['fst_video'], $video['snd_video']]));
											@endphp
											<span class="upload-pill">
												@if ($value > 0){{ $value }}/2 @else 0/2 @endif
												@if ($video['status'] == 0)
													<i class="bi bi-info-circle-fill dash-pending"></i>
												@else
													<i class="bi-check-circle-fill nav-icn success-icon"></i>
												@endif
											</span>
										@endif
									@endforeach
								@else
									<span class="upload-pill">0/2</span>
								@endif
							</td>
							<td class="upload-cell">
								@if ($schoolRow && $trainer_data['images'] != null)
									@foreach ($trainer_data['images'] as $image)
										@if ($schoolRow['school_name'] == $image['school_name'])
											@php
												$value = count(array_filter([
													$image['ifsb_image'],
													$image['group_image'],
													$image['fst_aimage'],
													$image['snd_aimage'],
													$image['trd_aimage'],
												]));
											@endphp
											<span class="upload-pill">
												@if ($value > 0){{ $value }}/5 @else 0/5 @endif
												@if (($image['status'] ?? null) == 0)
													<i class="bi bi-info-circle-fill dash-pending"></i>
												@else
													<i class="bi-check-circle-fill nav-icn success-icon"></i>
												@endif
											</span>
										@endif
									@endforeach
								@else
									<span class="upload-pill">0/5</span>
								@endif
							</td>
							<td class="upload-cell">
								@if ($schoolRow && $trainer_data['completions'] != null)
									@foreach ($trainer_data['completions'] as $completion)
										@if ($schoolRow['school_name'] == $completion['school_name'])
											@if ($completion['status'] == 1)
												Complete <i class="bi-check-circle-fill nav-icn success-icon"></i>
											@else
												Pending <i class="bi bi-info-circle-fill dash-pending"></i>
											@endif
										@endif
									@endforeach
								@else
									Pending
								@endif
							</td>
							<td class="upload-cell">
								@if ($schoolRow && $trainer_data['distributions'] != null)
									@foreach ($trainer_data['distributions'] as $distribution)
										@if ($schoolRow['school_name'] == $distribution['school_name'])
											@if ($distribution['status'] == 1)
												Complete <i class="bi-check-circle-fill nav-icn success-icon"></i>
											@else
												Pending <i class="bi bi-info-circle-fill dash-pending"></i>
											@endif
										@endif
									@endforeach
								@endif
							</td>
							<td style="text-align: center;">
								@if ($schoolRow)
									@if ($data['route_date'] == null)
										<span class="not-started">Not Started</span>
									@elseif ($data['paid_status'] == 1)
										<span class="paid">Paid</span>
									@elseif ($schoolRow['status'] == 0)
										<span class="pending">Pending</span>
									@else
										<span class="compete">Completed</span>
									@endif
								@endif
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript">
$(function () {
	if (!$.fn.DataTable) {
		return;
	}
	$('#trainerDetail').DataTable({
		ordering: false,
		dom: 'Bfrtip',
		pageLength: 25,
		scrollX: true,
		stateSave: true,
		buttons: [
			{
				extend: 'excel',
				text: 'Download Excel'
			}
		]
	});
});
</script>
@endsection
