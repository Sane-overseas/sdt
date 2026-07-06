@extends('layouts.app')
  
@section('content')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @section('title', 'Edit Trainer')   
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    .uplode-btn {
        color: #fff;
        border-radius: 5px;
        padding: 6px 12px;
    }
    i.uplode-btn {
        background-color: #004857;
        color: #fff;
        border-radius: 4px;
        padding: 5px 20px;
    }
    input.route_date {
        width: 63%;
    }
    i.bi.bi-calendar-date-fill {
        color: #d91212;
    }
    i.bi.bi-clock-fill {
        color: #035362;
    }
    .up-save {
        font-weight: 400;
        border-radius: 5px;
        color: #fff;
        background: #383535;
        padding: 2px 14px;
    }
    .send_btn {
        font-size: 12px;
        border-radius: 5px;
        color: #fff;
        background: #006170;
        padding: 5px 8px;
    }
    .close-btn {
        font-weight: 400;
        border-radius: 5px;
        color: #fff;
        background: #006170;
        padding: 2px 14px;
    }
    .up-save:hover {
        color: #fff;
    }
    button.applyBtn.btn.btn-sm.btn-primary {
        border: 2px solid #ed0000;
        background-color: #ed0000;
    }
    .drp-calendar.right {
        display: none !important;
    }
    .route_date {
        margin-left: 4px;
    }
    .start_date {
        width: 45%;
    }
    .end_date {
        width: 45%;
    }
    select.yearselect {
        display: none;
    }
    .modal-body {
        overflow: auto;
    }
    .modal-header {
        background: #258e72 !important;
    }
</style>
<body>
    <div class="container"> 
        <form action="javascript:void(0)" id="trainerForm" name="trainerForm" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            <div class="md-container">
                <div class="modal-header">
                <h4 class="modal-title">Add Schools</h4>
                </div>
                <?php $role = Auth::user()->role; ?>
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
                <div > 
                    <div class="form-group col-md">
                        <label>School Name</label>
                        <input class="selectAll" type="checkbox" id="selectAll" >Select All Schools for Selected Block
                        <select name="school_name[]" class="form-control multiple_select" id="check_school" multiple> 
                        </select>
                    </div>
                </div>
                <div class="save-trainer">
                    <button type="submit" class="btn btn-primary float-right update-btn" id="btn-save">Save</button>
                </div>
            </div>
        </form> 
        <div class="card-body">
            <table class="table table-bordered" id="trainerAssSchollsNew">
                <thead>
                    <tr>
                        <th>Assigned Schools ( {{count($trainer_data['asigned_schools'])}} )</th>
                        <th>District</th>
                        <th>Block</th>
                        <th>Assigned Date</th>
                        <th>Assigned by</th>
                        <th>Route Plan</th>
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
                            <td class="{{$data['id']}}">
                        @if($data['route_date']) 
                            <i class="bi bi-calendar-date-fill"></i> {{$data['route_date']}}  <i class="bi bi-clock-fill"></i>  {{date('H:i', strtotime($data['start_route_plan']))}} - {{date('H:i', strtotime($data['end_route_plan']))}}
                        @else
                            <span class="add-route-text">Please Add Route Plan</span>
                        @endif  
                        <a href="{{ route('route-plan', $data['id']) }}" id="suspendd" data-toggle="modal"
                        data-target="#demoModal{{ $data['id'] }}"
                        class="send_btn ml-3"><i class="bi bi-pencil-square"></i></a>
                        <form id="uplodeForm" action="{{ route('route-plan', $data['id']) }}" method="POST">
                            @csrf
                         <!-- Modal Example Start-->
                             <div class="modal fade note-model" id="demoModal{{ $data['id'] }}" value="{{$data['id']}}" tabindex="-1" role="dialog" aria- labelledby="demoModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="demoModalLabel{{ $data['id'] }}" style="color: #000 ;">
                                                Add Route Plan</h5>
                                            <button type="button" class="close"
                                                data-dismiss="modal" aria- label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            @include('partials.route-plan-fields', ['data' => $data])
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
                            <td> 
                                @if($data['uc_submitted'] == 0)
                                <a href="javascript:void(0)" data-url="{{ route('a-school' ,[$data['id'] ,$data['school_name']]) }}" class="btn" id="asignedSchoolDelete"><i class="bi bi-x-circle-fill remove"></i> </a>
                                @else
                                    <span class="compete">UC Received</span>
                                @endif
                                <a href="{{ url('upload-data/'.$data['id']) }}" class="@if($data['route_date'] == null) disable @endif"><i class="bi bi-upload uplode-btn @if($data['route_date'] == null) disable @endif"></i></a>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">

    const trainerAssSchollsNew =  $('#trainerAssSchollsNew').DataTable( {
        ordering: false,
        dom: 'Bfrtip',
        pageLength : 30,
        stateSave: true,
        searchHighlight: true,
    });

    $("#check_school").select2({
        closeOnSelect: false,
    });

    $("#selectAll").click(function(){
        if($("#selectAll").is(':checked') ){
            $("#check_school > option").prop("selected","selected");
            $("#check_school").trigger("change");
        }else{
            $("#check_school > option").prop('selected', false);
            $("#check_school").trigger("change");
         }
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
        var ajaxurl = '/add-schools-new';

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
                    title: 'Schools Add successfully',
                    showConfirmButton: false,
                    timer: 2000
                }).then(function(isConfirm) {
                    if (isConfirm) {
                        location.reload();
                      } 
                    });
            },
            error: function (data) {  
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
                  title: 'Somethig Wrong Please Check!',
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
        $("#check_school").html('');
        $.ajax({
            type: "POST",
            url: '/blockdata', 
            data: { id: id },
            success: function (result) {
                $('#check_block').html('<option value="">Select Block</option>');
                $('#check_school').html('<option value="">Select School</option>');
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
        $("#check_school").html('');
        $.ajax({
            type: "POST",
            url: '/schooldata', 
            data: { value: blockValue },
            success: function (result) {
               
                $.each(result.school, function (key, value) {
                    $("#check_school").append('<input type="checkbox" id="ckbCheckAll" />');
                    $("#check_school").append('<option value="' + value
                        .id + '">' + value.school_name + '</option>');                 
                });  
            }
        })
    });

    $(".multiple_select").mousedown(function(e){
        e.preventDefault();
        var select = this;
        var scroll = select.scrollTop;
        e.target.selected = !e.target.selected; 
        setTimeout(function(){select.scrollTop = scroll;}, 0);
        $(select).focus();
    }).mousemove(function(e){e.preventDefault()});

        
    $("#check_school").on('change', function() {
        var selected = $("#check_school").val().toString();
        console.log(selected);
        var document_style = document.documentElement.style;

        if(selected !== ""){
            document_style.setProperty('--text', "'Selected: "+selected+"'");
        }  
         else{
            document_style.setProperty('--text', "'Select School'");
        }
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
<script src="{{ asset('js/route-plan-holidays.js') }}"></script>
@endsection