@extends('layouts.app')

@section('content')
<style>
/* Mobile-only styles for Coordinator Panel (/trainer-reporting) */
.coord-panel-page {
    padding-bottom: 16px;
}
.coord-panel-page .table-scroll {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: #fff;
}
.coord-panel-page .table-scroll table {
    margin-bottom: 0;
    width: 100% !important;
}
.coord-panel-page #scopeCoordinatorTable {
    min-width: 980px;
}
.coord-panel-page #trainerTable {
    min-width: 780px;
}
.coord-panel-page #ownSchoolsTable {
    min-width: 420px;
}
@media (max-width: 767.98px) {
    .coord-panel-page.container {
        max-width: 100%;
        padding-left: 8px;
        padding-right: 8px;
    }
    .coord-panel-page .summary-cards-row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    .coord-panel-page .summary-cards-row .td-div {
        width: calc(50% - 10px) !important;
        max-width: calc(50% - 10px);
        flex: 0 0 calc(50% - 10px);
        margin: 5px !important;
        padding: 12px 10px !important;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    .coord-panel-page .summary-cards-row .trainer-as-hed {
        font-size: 13px !important;
        line-height: 1.25;
    }
    .coord-panel-page .summary-cards-row .trainer-as-amt {
        font-size: 18px !important;
        float: none !important;
        align-self: flex-end;
    }
    .coord-panel-page .heading {
        width: auto !important;
        max-width: 100%;
        font-size: 16px !important;
        padding: 6px 10px !important;
    }
    .coord-panel-page .card-body {
        padding: 0 !important;
    }
    .coord-panel-page .alert {
        font-size: 13px;
        margin-left: 0;
        margin-right: 0;
    }
    .coord-panel-page .dataTables_wrapper .dataTables_length,
    .coord-panel-page .dataTables_wrapper .dataTables_filter {
        float: none !important;
        text-align: left !important;
        margin: 8px 0;
    }
    .coord-panel-page .dataTables_wrapper .dataTables_filter input {
        width: 100% !important;
        max-width: 100%;
        margin-left: 0 !important;
        display: block;
        margin-top: 4px;
        box-sizing: border-box;
    }
    .coord-panel-page .dataTables_wrapper .dataTables_info,
    .coord-panel-page .dataTables_wrapper .dataTables_paginate {
        float: none !important;
        text-align: left !important;
        margin-top: 8px;
    }
    .coord-panel-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.3em 0.6em !important;
    }
}
</style>

<div class="container mt-2 coord-panel-page">
    <div class="row m-2 summary-cards-row">
        <div class="col-6 col-md total-div td-div">
            <span class="trainer-as-hed total-text">Assigned Schools</span>
            <span class="trainer-as-amt total-text">{{$totalScholls}}</span>
        </div>
        <div class="col-6 col-md compete-div td-div">
            <span class="trainer-as-hed complete-text">Complete</span>
            <span class="trainer-as-amt complete-text">{{$completeSchools}}</span>
        </div>
        <div class="col-6 col-md pending-div td-div">
            <span class="trainer-as-hed pending-text">Pending</span>
            <span class="trainer-as-amt pending-text">{{ $pendingSchools }}</span>
        </div>
        <div class="col-6 col-md not-started-dev td-div">
            <span class="trainer-as-hed pending-text">Not Started</span>
            <span class="trainer-as-amt pending-text">{{ $notstartedSchools }}</span>
        </div>
    </div>

    @if(!empty($isStateCoordinator))
    <div class="row margin-tb">
        <div class="col-12 col-md-10">
            <h2 class="heading">Coordinators</h2>
        </div>
    </div>
    <div class="card-body">
        <div class="table-scroll">
        <table class="table table-bordered" id="scopeCoordinatorTable">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Code</th>
                    <th>Number</th>
                    <th>Level</th>
                    <th>District</th>
                    <th>Trainers</th>
                    <th>Own Schools</th>
                    <th>Assigned Schools</th>
                    <th>Completed</th>
                    <th>Pending</th>
                    <th>Not Started</th>
                    <th>Get Data</th>
                </tr>
            </thead>
            <tbody>
            @forelse(($scope_coordinators ?? []) as $coord)
            <tr>
                <td>{{ $coord['id'] }}</td>
                <td><strong>{{ $coord['instructor_name'] }}</strong></td>
                <td>{{ $coord['email'] }}</td>
                <td>{{ $coord['instructor_code'] }}</td>
                <td>{{ $coord['instructor_number'] }}</td>
                <td>{{ (($coord['coordinator_level'] ?? 'district') === 'state') ? 'State' : 'District' }}</td>
                <td>
                    {{ (($coord['coordinator_level'] ?? 'district') === 'state')
                        ? 'All Districts'
                        : ($coord['district'] ?: '—') }}
                </td>
                <td>{{ $coord['trainers_count'] ?? 0 }}</td>
                <td>{{ $coord['own_schools_count'] ?? 0 }}</td>
                <td><span class="total">{{ $coord['assigned_total'] ?? 0 }}</span></td>
                <td><span class="compete">{{ $coord['assigned_complete'] ?? 0 }}</span></td>
                <td><span class="pending">{{ $coord['assigned_pending'] ?? 0 }}</span></td>
                <td><span class="not-started">{{ $coord['assigned_not_started'] ?? 0 }}</span></td>
                <td style="text-align: center;">
                    <a href="{{ url('cordinator_data/'.$coord['id']) }}" class="btn saveStopitems" title="View trainers &amp; schools">
                        <i class="bi bi-caret-down-square save-icon"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td></td>
                <td class="text-muted">No other coordinators in your area.</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
    @endif

    @if(!empty($ownSchoolRows))
    <div class="row margin-tb">
        <div class="col-12 col-md-10">
            <h2 class="heading">My Training Schools</h2>
            <p class="text-muted small">Schools you are training personally. Manage route plan / upload from <a href="{{ route('dashboard') }}">Upload</a>.</p>
        </div>
    </div>
    <div class="card-body">
        <div class="table-scroll">
        <table class="table table-bordered" id="ownSchoolsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>School Name</th>
                    <th>Route Plan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @foreach($ownSchoolRows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $row['school_name'] }}</strong></td>
                    <td>{{ $row['route_date'] ?: 'Not started' }}</td>
                    <td>
                        @if((int) $row['status'] === 1)
                            Complete
                        @elseif(!empty($row['route_date']))
                            Pending
                        @else
                            Not started
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @endif

    <div class="row margin-tb">
        <div class="col-12 col-md-10">
            <h2 class="heading">Trainers</h2>
        </div>
        @if(Auth::user()->role == 1)
        <div class="col-md-2">
            <a class="btn btn-info" onClick="add()" href="javascript:void(0)">Add Trainer</a>
        </div>
        @endif
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        <p>{{ $message }}</p>
    </div>
    @endif
    @if(Auth::user()->role == 2)
    <div class="alert alert-info py-2">
        Permissions:
        <strong>{{ !empty($canEditTrainer) ? 'Edit/Assign Schools ON' : 'Edit/Assign Schools OFF' }}</strong>
        |
        <strong>{{ !empty($canUploadData) ? 'Data Upload ON' : 'Data Upload OFF' }}</strong>
        @if(empty($canEditTrainer) && empty($canUploadData))
            <span class="d-block small">Ask administrator to enable permissions from Coordinators page to edit/assign or upload.</span>
        @endif
    </div>
    @endif
    <div class="card-body">
        <div class="table-scroll">
        <table class="table table-bordered" id="trainerTable">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Trainer code</th>
                    <th>Trainer Number</th>
                    <th>District</th>
                    <th>Assigned Schools</th>
                    <th>Completed</th>
                    <th>Pending</th>
                    <th>Not Started</th>
                    <th>Get Data</th>
                </tr>
            </thead>
            <tbody>
            @foreach($cordinator_trainers as $data)
            <tr class="data{{$data['id']}}">
                <td>{{$data['id']}}</td>
                <td><strong>{{$data['instructor_name']}}</strong></td>
                <td>{{$data['email']}}</td>
                <td>{{$data['instructor_code']}}</td>
                <td>{{$data['instructor_number']}}</td>
                <td>{{ $data['district'] ?? '—' }}</td>
                <td>
                    <?php $totalSchools = 0; ?>
                    @foreach($data['asigned_schools'] as $scholls)
                        <?php $totalSchools++ ?>
                    @endforeach
                    <span class="total">{{$totalSchools}}</span>
                </td>
                <td>
                    <?php
                        $completeSchools = 0;
                        $pendingSchools = 0;
                        $notstartedSchools = 0;
                    ?>
                    @foreach($data['asigned_schools'] as $scholls)
                        @if($scholls['status'] == 1)
                            <?php $completeSchools++ ?>
                        @endif
                        @if($scholls['status'] == 0 && $scholls['route_date'] != null)
                            <?php $pendingSchools++ ?>
                        @endif
                        @if($scholls['route_date'] == null)
                            <?php $notstartedSchools++ ?>
                        @endif
                    @endforeach
                   <span class="compete">{{$completeSchools}}</span>
                </td>
                <td><span class="pending">{{$pendingSchools}}</span></td>
                <td><span class="not-started">{{ $notstartedSchools}}</span></td>
                <td style="text-align: center;">
                    <a href="{{ url('getData/'.$data['id']) }}" class="btn saveStopitems" id="{{$data['id']}}">
                        <i class="bi bi-caret-down-square save-icon"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
<!-- boostrap trainer model -->
<div class="modal fade" id="trainer-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="trainerModal"></h4>
                <button type="submit" class="btn btn-primary float-right" id="btn-save">Save</button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="trainerForm" name="trainerForm" class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-row">
                         <div class="form-group col">
                          <label for="inputEmail4">Trainer Name</label>
                          <input type="text" class="form-control " name="trainer_name" >
                        </div>
                        <div class="form-group col">
                          <label for="inputPassword4">Email</label>
                          <input type="email" class="form-control " name="email" >
                        </div>
                         <div class="form-group col">
                          <label for="inputPassword4">Password</label>
                          <input type="text" class="form-control " name="password" value="Sopl@1634" >
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col">
                          <label for="inputPassword4">Trainer code</label>
                          <input type="text" class="form-control " name="code" >
                        </div>
                         <div class="form-group col">
                          <label for="inputEmail4">Number</label>
                          <input type="number" class="form-control" name="number" >
                        </div>
                        <div class="form-group col">
                            <label>District Name</label>
                            <select name="district_name" class="form-control" id="check_distt">
                                <option value="">Select District</option>
                                @if(isset($district))
                                    @foreach($district as $key => $data)
                                        <option value="{{$data['district']}}">{{$data['district']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
var dtMobileOpts = {
    autoWidth: false,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
};

$('#trainerTable').DataTable($.extend({}, dtMobileOpts, { ordering: true }));
if ($('#scopeCoordinatorTable').length) {
    $('#scopeCoordinatorTable').DataTable($.extend({}, dtMobileOpts, { ordering: false }));
}
if ($('#ownSchoolsTable').length) {
    $('#ownSchoolsTable').DataTable($.extend({}, dtMobileOpts, { ordering: false }));
}

function add(){
    $('#trainerForm').trigger("reset");
    $('#trainerModal').html("Add Trainer");
    $('#trainer-modal').modal('show');
    $('#id').val('');
}

jQuery(document).ready(function($){
    $("#btn-save").click(function (e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        e.preventDefault();

        var formData = new FormData(document.getElementById("trainerForm"));
        $.ajax({
            type: 'POST',
            url: "create",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
                Swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: 'Trainer has been Created',
                  showConfirmButton: false,
                  timer: 1500
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
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                  icon: 'error',
                  title: 'Something Wrong Please Check !'
                })
            }
        });
    });
});
</script>
@endsection
