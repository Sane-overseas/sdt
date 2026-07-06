@extends('layouts.app')
  
@section('content')
<body>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <td><strong>Months</strong></td>
                @foreach($totalSchools as $moths)
                    <th>{{$moths['month']}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Complete Schools</strong></td>
                @foreach($totalSchools as $moths)
                    <td>{{$moths['count']}}</td>
                @endforeach
            </tr>
            
        </tbody>
    </table>
    <div id="exTab1" > 
        <ul  class="nav nav-pills row">
             <li class="col card1 active"><a href="#tots" class="dash-text" data-toggle="tab">Total Schools</a></br><span class="repot-values"></span></li>
            <li class="col card1"><a href="#assigned" class="dash-text" data-toggle="tab">Assigned Schools</a></br><span class="repot-values"></span></li>
           <!--  <li class="col card1"><a href="#ongs" class="dash-text" data-toggle="tab">OnGoing Schools</a>
            </li>
            <li class="col card1"><a href="#trad" class="dash-text" data-toggle="tab">Completed schools</a>
            </li> -->
            <li class="col card1"><a href="#certi" class="dash-text" data-toggle="tab">Certified Schools</a>  
            </li>  
            <li class="col card1"><a href="#pending" class="dash-text" data-toggle="tab">Not Assigned Schools</a>
            </li>
           <!--  <li class="col card1"><a href="#notstarted" class="dash-text" data-toggle="tab">Not Started Schools</a>
            </li> -->
        </ul>
        <div class="tab-content clearfix">
            <div class="tab-pane active" id="tots">
                @include('districtReporting.total-schools')
            </div>
            <div class="tab-pane" id="assigned">
                @include('districtReporting.assigned')
            </div>
           <!--  <div class="tab-pane" id="ongs">
                @include('districtReporting.ongoing')
            </div>
            <div class="tab-pane" id="trad">
                @include('districtReporting.complete')
             </div> -->
             <div class="tab-pane" id="certi">
                @include('districtReporting.certified')
             </div>
             <div class="tab-pane" id="pending">
                @include('districtReporting.pending')
             </div>
             <!-- <div class="tab-pane" id="notstarted">
                @include('districtReporting.not-started')
             </div> -->
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

