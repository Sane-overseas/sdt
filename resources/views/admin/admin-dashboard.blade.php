@extends('layouts.app')

@section('content')
@section('title', 'Dashboard')
<div class="container">
    <div id="exTab1" >
        <ul  class="nav nav-pills row">
             <li class="col dash-text bg-green-box card1">
                <span class="dash-text">Total Trainers</span><span class="dash-text f-right">{{$trainers->count()}}</span></br>
                <span class="dash-text">Active Trainers</span><span class="dash-text f-right">
                <?php $total_Active_trainers = 0; ?>
                    @foreach($trainersWithAssigned as $data)
                        @if($data['asigned_schools'] != null)
                            <?php $total_Active_trainers++; ?>
                        @endif
                    @endforeach
                {{$total_Active_trainers}}
                </span></br>
                <span class="dash-text">InActive Trainers</span><span class="dash-text f-right">
                {{$trainers->count() - $total_Active_trainers - $holdTrainers}}
                </span></br>
                <span class="dash-text">Hold Trainers</span><span class="dash-text f-right">
                {{$holdTrainers}}</span>
            </li>
            <li class="col dash-name bg-green-box card1">
                <span class="dash-text">Total Schools</span><span class="dash-text f-right">{{$totalScholls}}</span></br>
                <span class="dash-text">Complete Schools</span><span class="dash-text f-right">{{$completeScholls}}</span></br>
                <span class="dash-text">Pending Schools</span><span class="dash-text f-right">{{$totalScholls - $completeScholls}}</span></br>
                <span class="dash-text">Paid Schools</span><span class="dash-text f-right">{{$paidSchools}}</span>
            </li>
            <li class="col dash-name bg-green-box card1">
                <span class="dash-text">Total Students</span><span class="dash-text f-right">{{$totalStudents}}</span></br>
                <span class="dash-text">Certified Students</span><span class="dash-text f-right">{{$completeStudents}}</span></br>
                <span class="dash-text">Non-Certified Students</span><span class="dash-text f-right">{{$totalStudents - $completeStudents}}</span>
            </li>
        </ul>
         <ul  class="nav nav-pills row">
            <li class="col dash-name bg-green-box card1">
                <span class="dash-text">Assigned Schools</span><span class="dash-text f-right">{{$totalasignedSchools}}</span></br>
                <span class="dash-text">Active Schools</span><span class="dash-text f-right">
                {{$activeAsignedSchools}}
                </span></br>
                <span class="dash-text">InActive Schools</span><span class="dash-text f-right">
                {{$totalasignedSchools - $activeAsignedSchools}}
                </span></br>
                <span class="dash-text">Certified  Schools</span><span class="dash-text f-right">
                {{$distribution}}</span>
            </li>
            <li class="col dash-name bg-green-box card1">
                <span class="dash-text">Total UC Received</span><span class="dash-text f-right">
               {{$ucReceived}}
                </span></br>
                <span class="dash-text">Approved UC </span><span class="dash-text f-right">
               {{$approvedUc}}</span></br>
                <span class="dash-text">Approval Pending UC </span><span class="dash-text f-right">
               {{$notAssignrdUc}}
                </span></br>
                <span class="dash-text">Rejected UC </span><span class="dash-text f-right">
               {{$rejecteduc}}
                </span></br>
                <span class="dash-text">UC Pending</span><span class="dash-text f-right">
                <?php $number = 0; ?>
                    @foreach($asignedSchools as $data)
                        @if($data['uc_submitted'] == 0 && $data['route_date'] != null)
                             <?php $number++ ?>
                        @endif
                    @endforeach
                {{ $number }}
                </span></br>
            </li>
            <li class="col dash-name bg-green-box card1"><span class="dash-text">Total Cordinator</span><span class="dash-text f-right">{{$cordinator->count()}}</span></li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    window.setTimeout( function() {
      window.location.reload();
    }, 30000);

    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@endsection
