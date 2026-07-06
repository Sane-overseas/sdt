<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
<style type="text/css">
    
</style>
@include('layouts.navigation')

<!-- Page Heading -->
@if (isset($header))
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </header>
@endif
<div class="container mt-3">
    <div id="exTab123" > 
        <ul  class="nav nav-pills row">
            <li class="col card1 active"><a href="#tots" class="dash-text" data-toggle="tab">Total Schools</a></br><span class="repot-values">{{count($schools)}}</span></li>
            <li class="col card1"><a href="#assigned" class="dash-text" data-toggle="tab">Assigned Schools</a></br><span class="repot-values">{{count($a_schools)}}</span></li>
            <li class="col card1"><a href="#ongs" class="dash-text" data-toggle="tab">OnGoing Schools</a></br>
                <?php $total_ongoing = 0; ?>
                @foreach($schoolsWithAssigned as $school)
                    @if($school->end_date != null && $school->status == 0)
                        <?php $total_ongoing++ ?> 
                    @endif
                @endforeach 
            <span class="repot-values">{{$total_ongoing}}</span>
            </li>
            <li class="col card1"><a href="#trad" class="dash-text" data-toggle="tab">Complete schools</a>
            </br>
                <?php $total_trained = 0; ?>
                @foreach($schoolsWithAssigned as $school)
                    @if($school->status == 1)
                        <?php $total_trained++ ?> 
                    @endif
                @endforeach 
            <span class="repot-values">{{$total_trained}}</span>
            </li>
            <li class="col card1"><a href="#certi" class="dash-text" data-toggle="tab">Certified Schools</a>
            </br>
                <?php $total_certified = 0; ?>
                @foreach($distribution as $school)
                    <?php $total_certified++ ?> 
                @endforeach 
            <span class="repot-values">{{$total_certified}}</span>
            </li>  
            <li class="col card1"><a href="#pending" class="dash-text" data-toggle="tab">Pending Schools</a></br>
                <?php $total_pending = 0; ?>
                @foreach($schools as $school)
                        @if($school['status'] == 0)
                        <?php $total_pending++ ?> 
                    @endif
                @endforeach 
                <span class="repot-values">{{$total_pending}}</span>
            </li>
        </ul>
        <div class="tab-content clearfix">
            <div class="tab-pane active" id="tots">
                @include('SchoolsReporting.total-schools')
            </div>
            <div class="tab-pane" id="assigned">
                @include('SchoolsReporting.assigned-schools')
            </div>
            <div class="tab-pane" id="ongs">
                @include('SchoolsReporting.ongoing-schools')
            </div>
            <div class="tab-pane" id="trad">
                @include('SchoolsReporting.trained-schools')
            </div>
            <div class="tab-pane" id="certi">
               @include('SchoolsReporting.certified-schools')
            </div>
            <div class="tab-pane" id="pending">
               @include('SchoolsReporting.pending-schools')
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>
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
