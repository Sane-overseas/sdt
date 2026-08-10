@extends('layouts.app')
  
@section('content')
@php
    $auth = Auth::user();
    $isAdmin = $auth && (int) $auth->role === 1;
    $isCoordinator = $auth && (int) $auth->role === 2;
    $canEditTrainer = $isAdmin
        || ($isCoordinator && (
            (int) ($auth->school_assigned_status ?? 0) === 1
            || (int) ($auth->id) === (int) ($trainer_data['id'] ?? 0)
        ));
    $canUploadData = $isAdmin
        || ($isCoordinator && (
            (int) ($auth->data_upload_status ?? 0) === 1
            || (int) ($auth->id) === (int) ($trainer_data['id'] ?? 0)
        ));
@endphp
<body>
<div class="container">
    <div id="exTab1" > 
        <ul  class="nav nav-pills row ">
            @if($canEditTrainer)
            <li class="col card1"><a href="#1a" class="dash-text" data-toggle="tab">Edit Trainer</a></li>
            @endif
            @if($canUploadData)
            <li class="col card1"><a href="#2a" class="dash-text" data-toggle="tab">Upload Data</a></li>
            @endif           
        </ul>
        <div class="tab-content clearfix">
            @if($canEditTrainer)
            <div class="tab-pane {{ $canEditTrainer ? 'active' : '' }}" id="1a">
                @include('admin.edit-trainer', ['canEditTrainer' => $canEditTrainer, 'isAdmin' => $isAdmin])
            </div>
            @endif
            @if($canUploadData)
            <div class="tab-pane {{ !$canEditTrainer && $canUploadData ? 'active' : '' }}" id="2a">
                @include('admin.upload-data')
            </div>
            @endif
            @if(!$canEditTrainer && !$canUploadData)
            <div class="alert alert-warning mt-3">
                You can view this trainer, but edit/assign and upload are disabled.
                Ask admin to enable permissions on the Coordinators page.
            </div>
            @endif
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
