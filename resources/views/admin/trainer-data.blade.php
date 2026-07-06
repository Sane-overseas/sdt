@extends('layouts.app')
  
@section('content')
<body>
<div class="container">
    <div id="exTab1" > 
        <ul  class="nav nav-pills row ">
            @if($trainer_data['role'] == 0 || $trainer_data['school_assigned_status'] == 1)
            <li class="col card1"><a href="#1a" class="dash-text" data-toggle="tab">Edit Trainer</a></li>
            @endif
            @if($trainer_data['role'] == 0 || $trainer_data['data_upload_status'] == 1)
            <li class="col card1"><a href="#2a" class="dash-text" data-toggle="tab">Upload Data</a></li>
            @endif           
        </ul>
        <div class="tab-content clearfix">
            <div class="tab-pane active" id="1a">
                @include('admin.edit-trainer')
            </div>
            <div class="tab-pane" id="2a">
                @include('admin.upload-data')
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