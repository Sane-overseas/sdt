@extends('layouts.app')
  
@section('content')
<style type="text/css">
    a.btn.ml-4.btn-danger {
        float: left;
        margin: 0px !important;
    }
    .checkbox-div {
        margin-left: 21px !important;
        margin-top: 10px !important;
    }
    .cler-btn {
        margin-right: -5%;
    }
</style>
<body>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="container">
    <div id="exTab1" > 
        <ul  class="nav nav-pills row">
           <li class="col col card1 active"><a href="#1a" class="dash-text" data-toggle="tab">Videos</a></li>
            <li class="col col card1"><a href="#2a" class="dash-text" data-toggle="tab">Images</a></li>
            <li class="col col card1"><a href="#3a" class="dash-text" data-toggle="tab">UC</a></li>
            <li class="col col card1"><a href="#4a" class="dash-text" data-toggle="tab">DC</a></li>
        </ul>
        <div class="container">
            <form id="custom_form" action="{{ route('custom-date-data') }}" method="get">
                 <div style="margin: 20px 0px;" class="row">
                    <div class="col-md-3">
                        <strong>Get One Day Records:</strong>
                       <input type="date" name="custom_date_data" class="form-control"/>
                    </div>
                    <div class="col-md-3">
                        <strong>Get Multiple  Days Records:</strong>
                        <input type="text" name="route_date" id="custom_date" class="form-control" value="" />
                    </div>
                    <div class="col-md-2 ">
                        <strong>Get all Records:</strong><br>
                        <div class="checkbox-div">
                            <input type="checkbox" name="all_recordes" class="form-check-input "  /> 
                            <label class="form-check-label " for="flexCheckDefault">All Records</label>
                        </div>
                    </div>
                    <div class="col-md-2 mt-3 cler-btn">
                      <button type="submit" class="btn btn-success filter ml-4">Submit</button>
                    </div>
                    <div class="col-md-2 mt-3">
                      <a href="{{ route('uploaded-data') }}" class="btn ml-4 btn-danger cler-btn">Clear</a>  
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-content clearfix">
             <div class="tab-pane active" id="1a">
                @include('uploadedData.all_videos')
            </div>
            <div class="tab-pane" id="2a">
                @include('uploadedData.all_images')
            </div>
            <div class="tab-pane" id="3a">
                @include('uploadedData.all_completion')
            </div>
            <div class="tab-pane" id="4a">
                @include('uploadedData.all_distributions')
             </div>
        </div>
    </div>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

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


    $('#custom_date').daterangepicker({
        autoUpdateInput: false,
        endDate: moment(),
        locale: {
          cancelLabel: 'Clear'
        }
    });
    $('#custom_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' / ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('#custom_date').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    </script>
@endsection

