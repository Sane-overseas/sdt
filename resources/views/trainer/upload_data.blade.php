@extends('layouts.app')
@section('title', 'Upload Data')
@section('content')
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

        .modal-header {
            background: #e1e0e0 !important;
        }

        .modal-body {
            overflow: auto;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <div class="container mt-2">
        @if ($message = Session::get('success'))
        <div class="alert alert-success"><p>{{ $message }}</p></div>
        @endif
        @if ($message = Session::get('error'))
        <div class="alert alert-danger"><p>{{ $message }}</p></div>
        @endif
        <div>
            <h3 class="heading mb-3 mt-2">Assigned Schools</h3>
        </div>
        <div>
            <table class="table table-bordered" id="uploadTable">
                <thead>
                    <tr>
                        <th>School Name</th>
                        <th>Route Plan</th>
                        <th>Upload</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user['asigned_schools'] as $key => $data)
                        <?php $route_date = explode('-', $data['route_date']); ?>
                        <tr>
                            <td>
                                @foreach ($schools as $school)
                                    @if ($data['school_name'] == $school['id'])
                                        <strong>{{ $school['school_name'] }}</strong>
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
                                                        style="color: #000 ;">
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
                            <td style="text-align:center;"><a href="{{ url('upload-data/' . $data['id']) }}"
                                    class="@if ($data['route_date'] == null) disable @endif"><i
                                        class="bi bi-upload uplode-btn @if ($data['route_date'] == null) disable @endif"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!--  @if (isset($first_time_login) && $first_time_login == true && $asigned_schools != null)
    <script type="text/javascript">
        $(document).ready(function() {
            $('#exampleModal2').modal('show');
        });
    </script>
    @endif
     <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="color: red;">Alert: Your training is completed in below schools. Please upload the pending Documents. To proceed the Payments.</h5><br>
                <h5 style="color: red;">
                जरूरी सूचना : आपका प्रशिक्षण निम्न विद्यालयों में पूरा हुआ। कृपया लंबित दस्तावेज़ अपलोड करें। ताकि भुगतान को समय से दिया जाये !
                </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <table class="table table-bordered" id="uploadTable">
       <thead>
        <tr>
         <th>School Name</th>
         <th>Video</th>
         <th>Images</th>
         <th>UC</th>
         <th>DC</th>
         <th>Upload</th>
        </tr>
       </thead>
        <tbody>
         @foreach ($asigned_schools as $key => $data)
          @if ($data['status'] == 0)
    <tr>
          <td>
          @foreach ($schools as $school)
    @if ($data['school_name'] == $school['id'])
    <strong>{{ $school['school_name'] }}</strong>
    @endif
    @endforeach
          </td>
          <td>
           @foreach ($schools as $school)
    @if ($data['school_name'] == $school['id'])
    @if (isset($videos))
    @foreach ($videos as $video)
    @if ($school['school_name'] == $video['school_name'])
    <?php
    $cunt = [$video['fst_video'], $video['snd_video']];
    $value = count(array_filter($cunt));
    ?>
             @if ($value > 0)
    {{ $value }}/2
    @endif
             @if ($video['status'] == 0)
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
           @foreach ($schools as $school)
    @if ($data['school_name'] == $school['id'])
    @if (isset($images))
    @foreach ($images as $image)
    @if ($school['school_name'] == $image['school_name'])
    <?php
    $cunt = [$image['ifsb_image'], $image['group_image'], $image['fst_aimage'], $image['snd_aimage'], $image['trd_aimage']];
    $value = count(array_filter($cunt));
    ?>
             @if ($value > 0)
    {{ $value }}/5
    @endif
             {{-- @if ($image['status'] == 0) --}} //previous code
             @if (($image['status'] ?? null) == 0)
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
           @foreach ($schools as $school)
    @if ($data['school_name'] == $school['id'])
    @if (isset($completion))
    @foreach ($completion as $c_data)
    @if ($school['school_name'] == $c_data['school_name'])
    @if ($c_data['status'] == 1)
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
       @foreach ($schools as $school)
    @if ($data['school_name'] == $school['id'])
    @if (isset($distribution))
    @foreach ($distribution as $d_data)
    @if ($school['school_name'] == $d_data['school_name'])
    @if ($d_data['status'] == 1)
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
       <td style="text-align:center;"><a href="{{ url('upload-data/' . $data['id']) }}" class="@if ($data['route_date'] == null) disable @endif"><i class="bi bi-upload uplode-btn @if ($data['route_date'] == null) disable @endif"></i></a></td>
          </tr>
    @endif
         @endforeach
      </tbody>
      </table>
              </div>
            </div>
          </div>
        </div> -->
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
@endsection
