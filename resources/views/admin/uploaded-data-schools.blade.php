@extends('layouts.app')

@section('content')
<div class="container mt-2">
    <div class="row margin-tb">
        <div class="col-md-10">
            <h2 class="heading ">Uploded Schools Data</h2>
        </div>
        <div class="col-md-2">
        
        </div>
    </div> 
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="card-body">
       <table class="table table-bordered" id="UplodedDataSchools">
            <thead>
                <tr>
                    <th>Trainer</th>
                    <th>Number</th>
                    <th>School Name</th>
                    <th>District</th>
                    <th>Block</th>
                    <th>Video</th>
                    <th>Images</th>
                    <th>Completion</th>
                    <th>Distribution</th>
                    <th>School Status</th>
                </tr>
            </thead>
            <tbody>
            @foreach($a_schools as $key => $data)
                <tr>
                    <td>
                        @foreach($trainers as $d_data)
                            @if($d_data['id'] == $data['user_id'])
                                 {{$d_data['instructor_name']}}
                            @endif
                        @endforeach
                    </td>
                    <td>
                    @foreach($trainers as $d_data)
                        @if($d_data['id'] == $data['user_id'])
                             {{$d_data['instructor_number']}}
                        @endif
                    @endforeach
                    </td>
                    <td>
                        @foreach($schools as $school)
                            @if($data['school_name'] == $school['id'])
                                {{$school['school_name']}}
                            @endif
                        @endforeach     
                    </td>
                    <td>@foreach($district as $d_data)
                            @if($d_data['id'] == $data['district'])
                                 {{$d_data['district']}}
                            @endif
                        @endforeach</td>
                    <td>{{$data['block']}}</td>
                    <td>
                        @foreach($schools as $school)
                            @if($data['school_name'] == $school['id'])
                                @if(isset($videos))
                                    @foreach($videos as $video)
                                        @if($school['school_name'] ==  $video['school_name'])
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
                    <td>
                        @foreach($schools as $school)
                            @if($data['school_name'] == $school['id'])
                                @if(isset($images))
                                    @foreach($images as $image)
                                        @if($school['school_name'] == $image['school_name'])
                                            <?php
                                            $cunt = array($image['ifsb_image'] ,$image['group_image'] ,$image['fst_aimage'] ,$image['snd_aimage'],$image['trd_aimage']);
                                            $value = count(array_filter($cunt));
                                            ?>
                                            @if($value > 0){{$value}}/5 @endif
                                            @if($image['status'] == 0) 
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
                    <td>
                        @foreach($schools as $school)
                            @if($data['school_name'] == $school['id'])
                                @if(isset($completion))
                                    @foreach($completion as $c_data)
                                        @if($school['school_name'] == $c_data['school_name'])
                                            @if($c_data['status'] == 1)
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
                    <td>
                        @foreach($schools as $school)
                            @if($data['school_name'] == $school['id'])
                                @if(isset($distribution))
                                    @foreach($distribution as $d_data)
                                        @if($school['school_name'] == $d_data['school_name'])
                                            @if($d_data['status'] == 1)
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
                    <td style="text-align: center;">   
                    @foreach($schools as $school)   
                        @if($data['school_name'] == $school['id'])
                            @if($data['route_date'] == null)
                                <span class="not-started">Not Started</span>
                            @elseif($school['status'] == 0) 
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
@endsection
@push('head')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript">

$('#UplodedDataSchools').DataTable( {
    ordering: false,
    dom: 'Bfrtip',
    pageLength : 25,
    buttons: [
        'excel'
    ]
});

</script>
@endpush
