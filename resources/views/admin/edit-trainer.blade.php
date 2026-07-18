<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
	@section('title', 'Edit Trainer')
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">
	@vite(['resources/sass/app.scss', 'resources/js/app.js'])
	<title></title>
</head>
<style type="text/css">
	.back-button {
		font-size: 20px;
        font-weight: 600;
        color: #000;
        background: #fff;
    }
    .save-trainer {
	    padding: 16px;
	}
    .school-tools {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    .school-search {
        max-width: 320px;
    }
    .school-checklist {
        border: 1px solid #d8dde3;
        border-radius: 8px;
        background: #fff;
        padding: 10px;
        max-height: 280px;
        overflow-y: auto;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        align-content: start;
    }
    .school-check-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin: 0;
        padding: 8px 10px;
        border-radius: 6px;
        font-size: 13px;
        border: 1px solid #e8ecef;
        background: #fafbfc;
        cursor: pointer;
        min-height: 40px;
    }
    .school-check-item input {
        flex-shrink: 0;
        margin-top: 2px;
    }
    .school-check-item span {
        line-height: 1.3;
        word-break: break-word;
    }
    .school-check-item:hover {
        background: #f0f6fa;
        border-color: #c5d9e8;
    }
    .school-empty {
        color: #6c757d;
        font-size: 13px;
        padding: 10px 8px;
        grid-column: 1 / -1;
    }
    @media (max-width: 992px) {
        .school-checklist {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 768px) {
        .school-tools {
            display: block;
        }
        .school-search {
            margin-top: 8px;
            max-width: 100%;
        }
        .school-checklist {
            grid-template-columns: 1fr;
            max-height: 220px;
        }
        .school-check-item {
            font-size: 13px;
            padding: 8px;
        }
    }
</style>
<body>
	<div class="container">
		<div class="row margin-tb">
	        <div class="col-6">
	            <h2 class="heading ">{{$trainer_data['instructor_name']}} - {{$trainer_data['instructor_code']}}</h2>
	        </div>
	        <div class="col-6">
	        	<button class="float-right back-button col-2" onclick="history.back()">BACK</button>
	        	@if(Auth::user()->role == 1)
	        	<a href=" {{ route('schools-reporting') }}" class="float-right back-button " style="text-decoration:none ; margin-right: 10px;" target="_blank">Check Assigned School</a>
	        	@endif
	        </div>
	    </div>
		<form action="javascript:void(0)" id="trainerForm" name="trainerForm" class="form-horizontal" enctype="multipart/form-data">
			@csrf
			<div class="md-container">
				<div class="modal-header">
				<h4 class="modal-title">Edit Trainer</h4>
				</div>
				<?php $role = Auth::user()->role; ?>
				<div class="form-row">
				     <div class="form-group col-md">
				      <label for="inputEmail4">Trainer Name</label>
				      <input type="text" class="form-control " name="trainer_name" value="{{$trainer_data['instructor_name']}}"  @if($role == 2) readonly @endif>
				      <input type="hidden" class="form-control " name="id" value="{{$trainer_data['id']}}">
				    </div>
				    <div class="form-group col-md">
				      <label for="inputPassword4">Email</label>
				      <input type="email" class="form-control " name="email"  value="{{$trainer_data['email']}}"  @if($role == 2) readonly @endif>
				    </div>
				    <div class="form-group col-md">
				      <label for="inputEmail4">Amount per School</label>
				      <input type="number" class="form-control" name="amount" value="{{$trainer_data['amount']}}"  @if($role == 2) readonly @endif>
				    </div>
                    <div class="form-group col-md">
                        <label for="inputEmail4">Incentive Amount</label>
                        <input type="number" class="form-control" name="extra_amount" value="{{$trainer_data['extra_amount']}}"  @if($role == 2) readonly @endif>
                      </div>
				    @if($trainer_data['role'] == 2)
				    {{-- <div class="form-group col-md">
				      <label for="inputEmail4">Incentive Amount</label>
				      <input type="number" class="form-control" name="extra_amount" value="{{$trainer_data['extra_amount']}}"  @if($role == 2) readonly @endif>
				    </div> --}}
				    @endif
				</div>
				<div class="form-row">
                    {{-- <div class="form-group col-md">
                        <label for="inputEmail4">Total Amount</label>
                        <input type="number" class="form-control" name="total_amount" value="{{$trainer_data['total_amount']}}"  @if($role == 2) readonly @endif>
                      </div> --}}
					<div class="form-group col-md">
				      <label for="inputPassword4">Trainer code</label>
				      <input type="text" class="form-control " name="code" value="{{$trainer_data['instructor_code']}}" @if($role == 2) readonly @endif>
				    </div>
				     <div class="form-group col-md">
				      <label for="inputEmail4">Number</label>
				      <input type="number" class="form-control" name="number" value="{{$trainer_data['instructor_number']}}"  @if($role == 2) readonly @endif>
				    </div>
				    <div class="form-group col-md">
				      <label for="inputPassword4">Cordinator</label>
				      <select name="cordinator" class="form-control" @if($role == 2) readonly @endif>
					       <option value="" selected>Select Cordinator</option>
					        @if(isset($cordinator))
								@foreach($cordinator as $key => $data)
					        		<option value="{{$data['id']}}" {{ $trainer_data['cordinator_id'] == $data['id'] ? 'selected' : '' }}>{{$data['cordinator_name']}}</option>
					        	@endforeach
					        @endif
					      </select>
				    </div>
				     <div class="form-group col-md">
                        <label>District Name</label>
                        <select name="district_name" class="form-control"  @if($role == 2) readonly @endif>
                            <option value="">Select District</option>
                            @if(isset($district))
                                @foreach($district as $key => $data)
                                    <option value="{{$data['district']}}" {{ $trainer_data['district'] == $data['district'] ? 'selected' : '' }}>{{$data['district']}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
				</div>
				<div class="row">
                    <div class="form-group col-md">
                        <label>District</label>
                        <select name="district" class="form-control" id="check_distt">
                            <option value="">Select District</option>
                            @if(isset($district))
                                @foreach($district as $key => $data)
                                    <option value="{{$data['id']}}">{{$data['district']}}</option>
                                @endforeach
                            @endif
                        </select>
                	</div>
                    <div class="form-group col-md">
                        <label>Block Name</label>
                        <select name="block" class="form-control" id="check_block" >
                            <option value="">Select Block</option>
                        </select>
                    </div>
				</div>
				<div class="row">
                    <div class="form-group col-12">
                        <label>School Name</label>
                        <div class="school-tools">
                            <label class="mb-0">
                                <input class="selectAll" type="checkbox" id="selectAll"> Select All Schools for Selected Block
                            </label>
                            <input type="text" id="schoolSearch" class="form-control school-search" placeholder="Search schools...">
                        </div>
                        <div id="schoolChecklist" class="school-checklist">
                            <div class="school-empty">Select district and block to load schools.</div>
                        </div>
                    </div>
				</div>
				<div class="save-trainer">
					<button type="submit" class="btn btn-primary float-right update-btn" id="btn-save">Save</button>
				</div>
			</div>
		</form>
		<div class="card-body">
	        <table class="table table-bordered" id="trainerAssScholls">
	            <thead>
	                <tr>
	                    <th>Assigned Schools ( {{count($trainer_data['asigned_schools'])}} )</th>
	                    <th>District</th>
	                    <th>Block</th>
	                    <th>Assigned Date</th>
	                    <th>Assigned by</th>
	                    <th>Remove School</th>
	                </tr>
	            </thead>
	            <tbody>
	                @foreach($trainer_data['asigned_schools'] as $data)
	                    <tr>
	                        <td>
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
	                        <td>{{date('d/m/y', strtotime($data['created_at']))}}</td>
	                        <td>
	                        	@foreach($user as $us_data )
	                        		@if($data['asigned_by'] == $us_data['id'])
	                        			{{$us_data['instructor_name']}}
	                        		@endif
	                        	@endforeach
	                        </td>
	                        <td>
	                        	@if($data['uc_submitted'] == 0)
	                            <a href="javascript:void(0)" data-url="{{ route('a-school' ,[$data['id'] ,$data['school_name']]) }}" class="btn" id="asignedSchoolDelete"><i class="bi bi-x-circle-fill remove"></i> </a>
	                            @else
	                            	<span class="compete">UC Received</span>
	                            @endif
							</td>
	                    </tr>
	                @endforeach
	            </tbody>
	        </table>
    	</div>
	</div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript">

	const trainerAssScholls =  $('#trainerAssScholls').DataTable( {
	    ordering: false,
	    dom: 'Bfrtip',
	    pageLength : 30,
	    stateSave: true,
	    searchHighlight: true,
	});

    function renderSchoolChecklist(list) {
        var $checklist = $("#schoolChecklist");
        $checklist.html('');

        if (!list || !list.length) {
            $checklist.html('<div class="school-empty">No schools available for this block.</div>');
            return;
        }

        $.each(list, function (key, value) {
            var item = '' +
                '<label class="school-check-item" data-name="' + String(value.school_name).toLowerCase() + '">' +
                    '<input type="checkbox" name="school_name[]" value="' + value.id + '">' +
                    '<span>' + value.school_name + '</span>' +
                '</label>';
            $checklist.append(item);
        });
    }

    $("#selectAll").on('change', function () {
        var checked = $(this).is(':checked');
        $('#schoolChecklist input[type="checkbox"]').prop('checked', checked);
    });

    $("#schoolSearch").on('input', function () {
        var query = String($(this).val() || '').toLowerCase().trim();
        $("#schoolChecklist .school-check-item").each(function () {
            var name = $(this).data('name');
            $(this).toggle(!query || String(name).indexOf(query) !== -1);
        });
    });

	$("#btn-save").click(function (e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        e.preventDefault();

        var formData =  $("#trainerForm").serialize()
        var type = "POST";
        var ajaxurl = '/update-trainer';

        $.ajax({
            type: type,
            url: ajaxurl,
            data: formData,
            dataType: 'json',
            success: function (data) {
            	var allInputs = document.querySelectorAll(['input','select']);
         		allInputs.forEach(singleInput => singleInput.value = '');
            	Swal.fire({
					position: 'center',
					icon: 'success',
					title: 'Successfully Data  Updated!',
					showConfirmButton: false,
					timer: 2000
				}).then(function(isConfirm) {
					if (isConfirm) {
					    location.reload();
					  }
					});
            },
            error: function (data) {
                const obj = JSON.parse(data.responseText);
				const Toast = Swal.mixin({
					toast: true,
					position: 'top-end',
					showConfirmButton: false,
					timer: 3000,
					timerProgressBar: true,
					didOpen: (toast) => {
					    toast.addEventListener('mouseenter', Swal.stopTimer)
					    toast.addEventListener('mouseleave', Swal.resumeTimer)
					}
				})

				Toast.fire({
				  icon: 'error',
				  title: obj.message
				})
            }
        });
    });

    $('#check_distt').change(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var id = this.value;
        $("#check_block").html('');
        $("#schoolChecklist").html('<div class="school-empty">Select block to load schools.</div>');
        $("#selectAll").prop('checked', false);
        $("#schoolSearch").val('');
        $.ajax({
            type: "POST",
            url: '/blockdata',
            data: { id: id },
            success: function (result) {
                $('#check_block').html('<option value="">Select Block</option>');
                $.each(result.block, function (key, value) {
                    $("#check_block").append('<option value="' + value.block + '">' + value.block + '</option>');
                });
            }
        })
    });

    $('#check_block').change(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var blockValue = this.value;
        $("#selectAll").prop('checked', false);
        $("#schoolSearch").val('');
        if (!blockValue) {
            $("#schoolChecklist").html('<div class="school-empty">Select block to load schools.</div>');
            return;
        }
        $("#schoolChecklist").html('<div class="school-empty">Loading schools...</div>');
        $.ajax({
            type: "POST",
            url: '/schooldata',
            data: { value: blockValue },
            success: function (result) {
                renderSchoolChecklist(result.school || []);
            }
        })
    });

	$('body').on('click', '#asignedSchoolDelete', function () {

      var userURL = $(this).data('url');
      var trObj = $(this);

      	$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });

      Swal.fire({
		  title: 'Are you sure?',
		  icon: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
		  if (result.isConfirmed) {
		     $.ajax({
                url: userURL,
                type: 'DELETE',
                dataType: 'json',
                success: function (data) {
                	trObj.parents("tr").remove();
                	const Toast = Swal.mixin({
						 toast: true,
						 position: 'top-end',
						 showConfirmButton: false,
						 timer: 2000,
						 timerProgressBar: true,
						 didOpen: (toast) => {
						    toast.addEventListener('mouseenter', Swal.stopTimer)
						    toast.addEventListener('mouseleave', Swal.resumeTimer)
						 }
					})

					Toast.fire({
					  icon: 'success',
					  title: 'It was succesfully deleted! !'
					})
	            },
	            error: function (data){
	            	const Toast = Swal.mixin({
						 toast: true,
						 position: 'top-end',
						 showConfirmButton: false,
						 timer: 4000,
						 timerProgressBar: true,
						 didOpen: (toast) => {
						    toast.addEventListener('mouseenter', Swal.stopTimer)
						    toast.addEventListener('mouseleave', Swal.resumeTimer)
						 }
					})

					Toast.fire({
					  icon: 'error',
					  title: 'Some data add in this School Please Check!'
					})
	            },
            });
		  }
		})
   });
</script>
