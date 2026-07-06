<style type="text/css">
    i.uplode-btn {
        background-color: #004857;
        color: #fff;
        border-radius: 4px;
        padding: 5px 20px;
    }

    input.route_date {
        width: 63%;
    }

    i.bi.bi-calendar-date-fill {
        color: #d91212;
    }

    i.bi.bi-clock-fill {
        color: #035362;
    }

    .up-save {
        font-weight: 400;
        border-radius: 5px;
        color: #fff;
        background: #383535;
        padding: 2px 14px;
    }

    .send_btn {
        font-size: 12px;
        border-radius: 5px;
        color: #fff;
        background: #006170;
        padding: 5px 8px;
    }

    .close-btn {
        font-weight: 400;
        border-radius: 5px;
        color: #fff;
        background: #006170;
        padding: 2px 14px;
    }

    .up-save:hover {
        color: #fff;
    }

    button.applyBtn.btn.btn-sm.btn-primary {
        border: 2px solid #ed0000;
        background-color: #ed0000;
    }

    .drp-calendar.right {
        display: none !important;
    }

    .route_date {
        margin-left: 4px;
    }

    .start_date {
        width: 45%;
    }

    .end_date {
        width: 45%;
    }

    select.yearselect {
        display: none;
    }
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<div class="container mt-2">
    <div class="row">
        <div class="col-6">
            <h3 class="heading mb-3 mt-2">Assigned Schools</h3>
        </div>
        <div class="col-6">
            <button class="float-right back-button col-2" onclick="history.back()">BACK</button>
        </div>
    </div>
    <div>
        <table class="table table-bordered" id="uploadTable">
            <thead>
                <tr>
                    <th>School Name</th>
                    <th>Route Plan</th>
                    <th>School Status</th>
                    <th>Upload</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trainer_data['asigned_schools'] as $key => $data)
                    <?php $route_date = explode('-', $data['route_date']); ?>
                    <tr>
                        <td>
                            @foreach ($school as $sch)
                                @if ($data['school_name'] == $sch['id'])
                                    <strong>{{ $sch['school_name'] }}</strong>
                                @endif
                            @endforeach
                        </td>
                        <td class="{{ $data['id'] }}">
                            @if ($data['route_date'])
                                <i class="bi bi-calendar-date-fill"></i> {{ $data['route_date'] }} <i
                                    class="bi bi-clock-fill"></i>
                                {{ date('H:i', strtotime($data['start_route_plan'])) }} -
                                {{ date('H:i', strtotime($data['end_route_plan'])) }}
                            @else
                                <span class="add-route-text">Please Add Route Plan</span>
                            @endif
                            <a href="{{ route('route-plan', $data['id']) }}" id="suspendd" data-toggle="modal"
                                data-target="#demoModal{{ $data['id'] }}" class="send_btn ml-3"><i
                                    class="bi bi-pencil-square"></i></a>
                            <form id="uplodeForm" action="{{ route('route-plan', $data['id']) }}" method="POST">
                                @csrf
                                <!-- Modal Example Start-->
                                <div class="modal fade note-model" id="demoModal{{ $data['id'] }}"
                                    value="{{ $data['id'] }}" tabindex="-1" role="dialog" aria-
                                    labelledby="demoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="demoModalLabel{{ $data['id'] }}"
                                                    style="color: #fff ;">
                                                    Add Route Plan</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-
                                                    label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                @include('partials.route-plan-fields', ['data' => $data])
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="close-btn"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="up-save">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Example End-->
                            </form>
                        </td>
                        <td>
                            @if ($data['route_date'] != null && $data['status'] == 0)
                                <span class="pending">OnGoing</span>
                            @elseif($data['paid_status'] == 1)
                                <span class="paid">Paid</span>
                            @elseif($data['status'] == 1)
                                <span class="compete">Completed</span>
                            @elseif($data['route_date'] == null)
                                <span class="not-started">Not Started</span>
                            @endif
                        </td>
                        <td style="text-align:center;"><a href="{{ url('upload-data/' . $data['id']) }}"
                                class="@if ($data['route_date'] == null) disable @endif"><i
                                    class="bi bi-upload uplode-btn @if ($data['route_date'] == null) disable @endif"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript">
    $('#uploadTable').DataTable({
        ordering: false,
        info: false,
        responsive: true,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All']
        ]
    });

    // $('.input-picker').daterangepicker({
    //     singleDatePicker: true,
    // 	showDropdowns: true,
    //     endDate: moment(),
    //     dateLimit: { days: 9 },
    //     locale: {
    //       cancelLabel: 'Clear',
    //       format: 'DD/MM/YYYY',
    //     }
    // });

    // $('.input-picker').on('apply.daterangepicker', function(ev, picker) {
    //     $(this).val(picker.startDate.format('DD/MM/YYYY'));
    // });

    // $('.input-picker').on('cancel.daterangepicker', function(ev, picker) {
    //     $(this).val('');
    // });
</script>
<script src="{{ asset('js/route-plan-holidays.js') }}"></script>
