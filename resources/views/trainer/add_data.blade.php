@extends('layouts.app')
@section('title', 'Upload Data')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<style type="text/css">
    .back-button {
        font-weight: 600;
        color: #000;
    }
</style>
<div class="container">
    <div class="row mb-5 main-div">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-header bg-primary row">
                    <h3 class="text-white col-8">Upload Data</h3>
                    <button class="float-right back-button col-4" style="border-radius: 5px;border: 2px solid;margin: 0px !important;color: #fff !important;" onclick="history.back()">BACK</button>
                </div>
                <div class="card-body">
                   @if(session()->has('message'))
                        <div class="alert alert-success">
                            {{ session()->get('message') }}
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}
                        </div>
                    @endif
                    @if(isset($activeAcademicSession) && $activeAcademicSession)
                    <p class="text-muted small mb-3">Uploading for session: <strong>{{ $activeAcademicSession->name }}</strong></p>
                    @endif
                <form id="documentsForm"  enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" class="form-control" value="{{$school_data['id']}}"> 
                        <input type="hidden" name="user_id" value="{{$school_data['user_id']}}">
                        <div class="">
                            <div class="form-group ">
                                <strong>Cordinator Name</strong>
                                <input type="text" name="cordinator" class="form-control" value="{{$cordinators['cordinator_name']}} - {{$cordinators['cordinator_code']}}" readonly>   
                            </div> 
                            <div class="form-group ">
                                <strong>District Name</strong>
                                @foreach($district as $data)
                                    @if($data['id'] == $school_data['district'])    
                                        <input type="text" name="district" class="form-control" value="{{$data['district']}}" readonly>
                                    @endif
                                @endforeach
                            </div>
                            <div class="form-group ">
                                <strong>Block Name</strong> 
                                <input type="text" name="block" class="form-control" value="{{$school_data['block']}}" readonly>
                            </div>
                            <div class="form-group ">
                                <strong>School Name</strong>
                                @foreach($schools as $data)
                                    @if($data['id'] == $school_data['school_name'])    
                                        <input type="text" name="school_name" class="form-control" value="{{$data['school_name']}}" readonly>
                                        <input type="hidden" name="school_id" class="form-control" value="{{$data['id']}}" readonly>
                                    @endif
                                @endforeach
                            </div>
                            <div class="row ">
                                <strong>Route Plan</strong>
                                    <div class="form-group">
                                        <span>Date:</span>
                                        <input type="text" name="route_date" class="form-control input-picker" value="{{$school_data['route_date']}}" readonly>
                                    </div> 
                                    <div class="form-group">
                                        <span>Intime:</span>
                                        <input type="time" name="intime" class="form-control" value="{{$school_data['start_route_plan']}}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <span>Outtime:</span>
                                        <input type="time" name="outtime" class="form-control" value="{{$school_data['end_route_plan']}}" readonly>
                                    </div> 
                            </div>
                        </div>   
                        <div id="mynewTab"> 
                            <ul  class="nav nav-pills" id="newTab">
                                <li class="active data-tab"><a  href="#video" class="data-tab-a mytab" data-toggle="tab">Upload Videos</a> </li>
                                <li class="data-tab"><a href="#image" class="data-tab-a mytab" data-toggle="tab">Upload Images</a></li>      
                                <li class="data-tab"><a href="#completion" class="data-tab-a mytab" data-toggle="tab">Upload Completion Certificate</a></li>
                                <li class="data-tab"><a href="#distribution" class="data-tab-a mytab" data-toggle="tab">Upload Distribution Certificate</a></li>
                            </ul>
                             <div class="progress mt-2">
                                <div class="progress-bar"></div>
                            </div>
                            <div class="tab-content clearfix">
                                <div class="tab-pane active" id="video">
                                    @if(isset($user_videos->video_note) != null)
                                    <div class="note_div mt-3 mb-3">
                                        <span class="note-text">Note: {{ $user_videos->video_note }}</span>
                                    </div>
                                    @endif
                                    <ul class="main-data-div" >
                                        <li class="main-data-tab"><a  href="#1st-v" class="main-data-a" data-toggle="tab">1st Activity Video</a>@if(isset($user_videos->fst_video))<i class="bi-check-circle-fill nav-icn success-icon"></i> @else <i class="bi bi-dash-circle-fill nav-icn padding-icon"></i> @endif</li>
                                        <li class="main-data-tab"><a href="#2nd-v" class="main-data-a" data-toggle="tab">2nd Activity Video</a>@if(isset($user_videos->snd_video))<i class="bi-check-circle-fill nav-icn success-icon"></i> @else <i class="bi bi-dash-circle-fill nav-icn padding-icon"></i> @endif</li>      
                                    </ul>
                                    <div class="tab-content clearfix">
                                        <div class="tab-pane" id="1st-v">
                                            <span class="d-hed">Upload 1st Activity Video</span>
                                            <div class="flex items-center justify-center w-full">
                                                <div class="file-div ">
                                                    <input id="dropzone-file" name="fst_videos" type="file" class="file-input">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">mp4 only</p>
                                                </div>
                                            </div> 

                                        </div>
                                        <div class="tab-pane" id="2nd-v">
                                            <span class="d-hed">Upload 2nd Activity Video </span>
                                            <div class="flex items-center justify-center w-full">
                                                <div class="file-div ">
                                                    <input id="dropzone-file" name="snd_videos" type="file" class="file-input">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">mp4 only</p>
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="image">
                                    @if(isset($user_images->image_note) != null)
                                    <div class="note_div mt-3 mb-3">
                                        <span class="note-text">Note: {{ $user_images->image_note }}</span>
                                    </div>
                                    @endif
                                    <ul class="main-data-div" >
                                        <li class="main-data-tab"><a  href="#1st-i" class="main-data-a" data-toggle="tab">In front of School Board Image</a>@if(isset($user_images->ifsb_image))<i class="bi-check-circle-fill nav-icn success-icon"></i> @else <i class="bi bi-dash-circle-fill nav-icn padding-icon"></i> @endif</li>

                                        <li class="main-data-tab"><a href="#2nd-i" class="main-data-a" data-toggle="tab">Group Image</a>@if(isset($user_images->group_image))<i class="bi-check-circle-fill nav-icn success-icon"></i> @else <i class="bi bi-dash-circle-fill nav-icn padding-icon"></i> @endif</li>

                                        <li class="main-data-tab"><a  href="#3rd-i" class="main-data-a" data-toggle="tab">1st Activity Image</a>@if(isset($user_images->fst_aimage))<i class="bi-check-circle-fill nav-icn success-icon"></i> @else <i class="bi bi-dash-circle-fill nav-icn padding-icon"></i> @endif</li>

                                        <li class="main-data-tab"><a href="#4th-i" class="main-data-a" data-toggle="tab">2nd Activity Image</a>@if(isset($user_images->snd_aimage))<i class="bi-check-circle-fill nav-icn success-icon"></i> @else <i class="bi bi-dash-circle-fill nav-icn padding-icon"></i> @endif</li> 
                                        
                                        <li class="main-data-tab"><a  href="#5th-i" class="main-data-a" data-toggle="tab">3rd Activity Image</a>@if(isset($user_images->trd_aimage))<i class="bi-check-circle-fill nav-icn success-icon"></i> @else <i class="bi bi-dash-circle-fill nav-icn padding-icon"></i> @endif</li>        
                                    </ul>
                                    <div class="tab-content clearfix">
                                        <div class="tab-pane" id="1st-i">
                                            <span class="d-hed">Upload In front of School Board Image</span>
                                            <div class="flex items-center justify-center w-full">
                                                 <div class="file-div ">

                                                    <input id="dropzone-file" name="ifsb_image" type="file" class="file-input" accept=".png , .JPG , .JPEG" />
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"> PNG, JPEG or JPG</p>
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="tab-pane" id="2nd-i">
                                            <span class="d-hed">Upload Group Image</span>
                                             <div class="flex items-center justify-center w-full">
                                                <div class="file-div ">

                                                    <input id="dropzone-file" name="group_image" type="file" class="file-input" accept=".png , .JPG , .JPEG"/>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"> PNG, JPEG or JPG</p>
                                                </div>
                                            </div> 
                                        </div>
                                         <div class="tab-pane" id="3rd-i">
                                            <span class="d-hed">Upload 1st Activity Image</span>
                                             <div class="flex items-center justify-center w-full">
                                                <div class="file-div ">

                                                     <input id="dropzone-file" name="fst_aimage" type="file" class="file-input" accept=".png , .JPG , .JPEG"/>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"> PNG, JPEG or JPG</p>
                                                </div>  
                                            </div> 
                                        </div>
                                        <div class="tab-pane" id="4th-i">
                                            <span class="d-hed">Upload 2nd Activity Image</span>
                                              <div class="flex items-center justify-center w-full">
                                                <div class="file-div ">

                                                    <input id="dropzone-file" name="snd_aimage" type="file" class="file-input" accept=".png , .JPG , .JPEG"/>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"> PNG, JPEG or JPG</p>
                                                </div>  
                                            </div> 
                                        </div>
                                         <div class="tab-pane" id="5th-i">
                                            <span class="d-hed">Upload 3rd Activity Image</span>
                                             <div class="flex items-center justify-center w-full">
                                                <div class="file-div ">

                                                    <input id="dropzone-file" name="trd_aimage" type="file" class="file-input" accept=".png , .JPG , .JPEG"/>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"> PNG, JPEG or JPG</p>
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane mt-3" id="completion">
                                    @if(isset($user_completion->completion_note) != null)
                                    <div class="note_div mt-3 mb-3">
                                        <span class="note-text">Note: {{ $user_completion->completion_note }}</span>
                                    </div>
                                    @endif
                                   <span class="d-hed">Upload Completion Certificate</span>
                                    @if(isset($user_completion->completion_file))<i class="bi-check-circle-fill nav-icn success-icon"></i> @else <i class="bi bi-dash-circle-fill nav-icn padding-icon"></i> @endif 
                                   <div class="flex items-center justify-center w-full">
                                        <div class="file-div ">
                                            <input id="dropzone-file" name="completion_file" type="file" class="file-input" accept=".png , .JPG , .JPEG , .pdf"/>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or PDF</p>
                                        </div>
                                    </div> 
                                </div>
                                <div class="tab-pane mt-3" id="distribution">
                                    @if(isset($user_distribution->distribution_note) != null)
                                    <div class="note_div mt-3 mb-3">
                                        <span class="note-text">Note: {{ $user_distribution->distribution_note }}</span>
                                    </div>
                                    @endif
                                    @if(isset($user_distribution->complete_students) != null)
                                        <input type="number" name="complete_students" class="form-control mt-3 mb-3" value="{{$user_distribution->complete_students}}" placeholder="Complete Students for this School">
                                    @else
                                         <input type="number" name="complete_students" class="form-control mt-3 mb-3" placeholder="Complete Students for this School">
                                    @endif
                                    <span class="d-hed">Upload Distribution Certificate</span>
                                    @if(isset($user_distribution->complete_students))<i class="bi-check-circle-fill nav-icn success-icon"></i> @else <i class="bi bi-dash-circle-fill nav-icn padding-icon"></i> @endif
                                   <div class="flex items-center justify-center w-full">
                                        <div class="file-div ">
                                            <input id="dropzone-file" name="distribution_file" type="file" class="file-input" accept=".png , .JPG , .JPEG , .pdf"/>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or PDF</p>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="btn-div flex">
                            <button type="button" class="btn-submit justify-center"  id="documents-save" onclick="callAjax()">Submit</button>
                        </div>    
                    </form>    
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript">
   $('.mytab').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $('.mytab').on("shown.bs.tab", function (e) {
        var id = $(e.target).attr("href");
        localStorage.setItem('activeTab', id)
    });

    var selectedTab = localStorage.getItem('activeTab');
    console.log(selectedTab);
    if (selectedTab != null) {
        $('.mytab[href="' + selectedTab + '"]').tab('show');
    }

    function callAjax(e){
        $(".btn-submit").prepend('<i class="fa fa-spinner fa-spin"></i>');

        $(".btn-submit").attr("disabled", 'disabled');

        window.User = {!! json_encode(optional(auth()->user())->only('instructor_name')) !!}

        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = parseInt((evt.loaded / evt.total) * 100);
                        $(".progress-bar").width(percentComplete + '%');
                        $(".progress-bar").html(percentComplete+'%');
                    }
                }, false);
                return xhr;
            },
            url:'{{route('create-data')}}',
            method:"POST",
            data: new FormData(document.getElementById("documentsForm")),
            dataType:'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success: function(response){
                $(".btn-submit").find(".fa-spinner").remove();
                $(".btn-submit").removeAttr("disabled");
                Swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: ' Thank You '+window.User.instructor_name+' 😊',
                  showConfirmButton: true,
                   confirmButtonText: `<i class="fa fa-thumbs-up"></i>&nbsp;&nbsp; OK`,
                })
                .then(function(isConfirm) {
                if (isConfirm) {
                    location.reload();
                  } 
                });
            },
            error: function (data) {
                $(".btn-submit").find(".fa-spinner").remove();

                $(".btn-submit").removeAttr("disabled");
                const obj = JSON.parse(data.responseText);
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: true,
                    // timer: 3000,
                    // timerProgressBar: true,
                    // didOpen: (toast) => {
                    //     toast.addEventListener('mouseenter', Swal.stopTimer)
                    //     toast.addEventListener('mouseleave', Swal.resumeTimer)
                    // }
                })
                Toast.fire({
                  icon: 'error',
                  title: obj.message
                })
                }
        });
    }
</script>
@endsection
