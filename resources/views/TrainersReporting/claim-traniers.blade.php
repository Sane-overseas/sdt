@extends('layouts.app')

@section('content')
@php
	$sessionPaidCounts = $sessionPaidCounts ?? [];
	$sessionAdvanceTotals = $sessionAdvanceTotals ?? [];
@endphp
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
<div class="container mt-2">
	<div class="row">
		<div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Claim Traniers</h2>
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
	@if(isset($isReadOnlySessionView) && $isReadOnlySessionView)
	<div class="alert alert-warning">
		Archive session is read-only. Payments are locked.
	</div>
	@endif
	<div class="card-body">
		<table class="table table-bordered" id="trainerClaim">
			<thead>
				<tr>
					<th>Id</th>
					<th>Name</th>
					<th>Trainer code</th>
					<th>Trainer Number</th>
					<th>District</th>
					<th>Claim Note</th>
					<th>New Amount</th>
					<th>Total Amount</th>
					<th>Payment History</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
			@foreach($trainers as $data)
				<tr class="data{{$data['id']}}">
				    <td>{{$data['id']}}</td>
				    <td>{{$data['instructor_name']}}</td>
				    <td>{{$data['instructor_code']}}</td>
				    <td>{{$data['instructor_number']}}</td>
				    <td>{{$data['district']}}</td>
				    <td>{{$data['claim_note']}}</td> 
			    <td>
			    	@php
				    	$complete_schools = 0;
				    	foreach (($data['asigned_schools'] ?? []) as $a_schools) {
				    		if (($a_schools['status'] ?? 0) == 1) {
				    			$complete_schools++;
				    		}
				    	}
				    	$paidSchoolsInSession = ($sessionPaidCounts ?? [])[$data['id']] ?? 0;
				    	$advanceAmount = ($sessionAdvanceTotals ?? [])[$data['id']] ?? 0;
				    	$pedding_schools = max(0, $complete_schools - $paidSchoolsInSession);
				    	$totalAmount = number_format(($data['amount'] ?? 0) * $complete_schools, 2);
				    	$newAmount = number_format(($data['amount'] ?? 0) * $pedding_schools, 2);
			    	@endphp
			    	@if($paidSchoolsInSession == 0)
			    		{{ $totalAmount }}
			    	@else
			    		{{ $newAmount }}
			    	@endif
			    </td>
			    <td>{{ number_format((($data['amount'] ?? 0) * $paidSchoolsInSession) + $advanceAmount, 2) }}</td> 
            		<td class="{{$data['id']}}"> 
                        <a href="#" id="suspendd" data-toggle="modal" data-target="#demoModal{{ $data['id'] }}" class="send_btn ml-3">Check</i></a>
                            <form id="uplodeForm" action="{{ route('paid_status')}}" method="POST">
                        	@csrf
                         <!-- Modal Example Start-->
                             <div class="modal fade note-model" id="demoModal{{ $data['id'] }}" value="{{$data['id']}}" tabindex="-1" role="dialog" aria- labelledby="demoModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="demoModalLabel{{ $data['id'] }}" style="color: #fff ;">
                                                Paid</h5>
                                            <button type="button" class="close"
                                                data-dismiss="modal" aria- label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                        	<strong>Payment History (Current Session)</strong></br>
                        	<div class="mt-2 mb-2">
                        		<span class="remark">Paid Schools: {{ $paidSchoolsInSession }}</span></br>
                        		<span class="remark">Advance: {{ number_format($advanceAmount, 2) }}</span></br>
                        	</div>
											<div class="form-row">
												 <div class="form-group col">
												 	<label for="inputEmail4">Extra Amount</label>
											      	<input class="ml-2 checkExtraAmount" type="checkbox" {{ (isset($isReadOnlySessionView) && $isReadOnlySessionView) ? 'disabled' : '' }}>
											      	<input type="input" class="form-control enabled" name="extra_amount" value="{{$data['extra_amount']}}" disabled>
											    </div>
											</div>
	                                        <div class="form-row">
												<div class="form-group col">
											      <label for="inputPassword4">Trainer</label>
											      <input type="text" class="form-control " name="trainer_name" value="{{$data['instructor_name']}}" readonly>
											    </div>
											    @if($data['paid_schools'] == null)
	                   							<div class="form-group col">
											      <label for="inputEmail4">Amount</label>
											      <input type="text" class="form-control" name="amount" value="{{$totalAmount}}" readonly>
											    </div>
											    <div class="form-group col">
											      <label for="inputEmail4">Schools</label>
											      <input type="number" class="form-control" name="schools" value="{{$complete_schools}}" readonly>
											    </div> 
							                   	@else
							                   	<div class="form-group col">
											      <label for="inputEmail4">Amount</label>
											      <input type="text" class="form-control" name="amount" value="{{$newAmount}}" readonly>
											    </div>
											    <div class="form-group col">
											      <label for="inputEmail4">Schools</label>
											      <input type="number" class="form-control" name="schools" value="{{$pedding_schools}}" readonly>
											    </div> 
	                   							@endif 
											    <div class="form-group col">
											    	<label for="inputEmail4">Paid Date</label>
											      	<input type="date" class="form-control" name="paid_date" required {{ (isset($isReadOnlySessionView) && $isReadOnlySessionView) ? 'disabled' : '' }}>
											    </div>
											    <input type="hidden" class="form-control" name="id" value="{{$data['id']}}">
											</div>
                                    	</div>
                                    	<table>
                                    		<tbody>
                                    			<tr>
													<th>School Name</th>
													<th>Status</th>
												</tr>
                                    		</tbody>
                                    		<tbody>
                                    			@foreach($data['asigned_schools'] as  $a_schools)
	                                    				@if($a_schools['status'] == 1 && $a_schools['paid_status'] == 0)
													<tr>
														<td>
															@foreach($schools as $school)
										                        @if($a_schools['school_name'] == $school['id'])
										                        	{{$school['school_name']}}
										                        @endif
										                    @endforeach 
														</td>
														<td style="text-align: center;">
														 	@foreach($schools as $school)
										                        @if($a_schools['school_name'] == $school['id'])
										                        	<input type="checkbox" data-id="{{$school['id']}}" name="paid_status[]" class="paid-status" value="{{$school['id']}}" {{$a_schools['paid_status'] == 1 ? 'checked' : '' }} {{ (isset($isReadOnlySessionView) && $isReadOnlySessionView) ? 'disabled' : '' }}>
										                        @endif
										                    @endforeach 
										                </td>    
													</tr>  
													@endif 	
							                    @endforeach
                                    		</tbody>
                                    	</table>
                                        <div class="modal-footer">
                                            <button type="button" class="close-btn"
                                                data-dismiss="modal">Close</button>
                                            <button  type="submit" class="up-save" {{ (isset($isReadOnlySessionView) && $isReadOnlySessionView) ? 'disabled' : '' }}>Paid</button>    	
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- Modal Example End-->
                        </form>
                    </td>
                    <td>
                    @if(($sessionPaidCounts[$data['id']] ?? 0) > 0 || ($sessionAdvanceTotals[$data['id']] ?? 0) > 0)
                    	<span class="compete">Paid</span>
                    @else
                    	<span class="pending">Unpaid</span>
                    @endif
                	</td> 
				</tr>	
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
const isReadOnlySessionView = @json(isset($isReadOnlySessionView) && $isReadOnlySessionView);

$('.checkExtraAmount').change(function () {
	let status = $(this).prop('checked') === true ? 1 : 0;
	if(status == 1){
		$('.enabled').prop('disabled', false);
	}else{
		$('.enabled').prop('disabled', true);
	} 
});

$('#trainerClaim').DataTable( {
	ordering: false,
    dom: 'Bfrtip',
   	pageLength : 100,
    buttons: [
         'excel', 
    ]
} );

let paidStatus = Array.prototype.slice.call(document.querySelectorAll('.paid-status'));

paidStatus.forEach(function(html) {
    let switchery = new Switchery(html,  { size: 'small' });
});

$(document).ready(function(){
    if (isReadOnlySessionView) {
    return;
    }
    $('.paid-status').change(function () {
        let status = $(this).prop('checked') === true ? 1 : 0;
        let school_id = $(this).data('id');

        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/school-paid-status',
            data: {'paid_status': status, 'school_id': school_id},
            success: function (data) {
                const Toast = Swal.mixin({
                     toast: true,
                     position: 'top-end',
                     showConfirmButton: false,
                     timer: 1000,
                     timerProgressBar: true,
                     didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                     }
                })

                Toast.fire({
                  icon: 'success',
                  title: 'Paid status are changed !'
                })
            }
        });
    });
});



</script>
@endsection