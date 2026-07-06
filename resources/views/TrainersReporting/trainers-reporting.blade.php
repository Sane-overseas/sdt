@extends('layouts.app')
  
@section('content')
<body>
<div class="container">
    <div id="exTab1" > 
        <ul  class="nav nav-pills row">
            <li class="col card1 active"><a href="#tots" class="dash-text" data-toggle="tab">Total Traniers</a></br><span class="repot-values"></span></li>
            <li class="col card1"><a href="#ongot" class="dash-text" data-toggle="tab">OnGoing Trainers</a></br><span class="repot-values"></span></li>  
            <li class="col card1"><a href="#notwt" class="dash-text" data-toggle="tab">Not Working Trainers</a></br><span class="repot-values"></span></li>
            <li class="col card1"><a href="#tsd" class="dash-text" data-toggle="tab">Trainers Schools Data</a></br><span class="repot-values"></span></li>
            <li class="col card1"><a href="#claim" class="dash-text" data-toggle="tab">Claim Traniers</a></br><span class="repot-values"></span></li>
        </ul>
        <div class="tab-content clearfix">
            <div class="tab-pane active" id="tots">
                @include('TrainersReporting.total-traniers')
            </div>
            <div class="tab-pane" id="ongot">
                @include('TrainersReporting.ongoing-trainers')
            </div>
            <div class="tab-pane" id="notwt">
                @include('TrainersReporting.notworking-trainers')
            </div>
            <div class="tab-pane" id="tsd">
                @include('TrainersReporting.trainers-schools-data')
            </div>
            <div class="tab-pane" id="claim">
                @include('TrainersReporting.claim-traniers')
            </div>
        </div>
    </div>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript">
   $('a[data-toggle="tab"]').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
        var id = $(e.target).attr("href");
        localStorage.setItem('activeTab', id)
    });

    var selectedTab = localStorage.getItem('activeTab');
    if (selectedTab != null) {
        $('a[data-toggle="tab"][href="' + selectedTab + '"]').tab('show');
    }
</script>
@endsection

