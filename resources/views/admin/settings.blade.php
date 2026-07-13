@extends('layouts.app')

@section('content')
<div class="container mt-2">
    <div class="row margin-tb mb-3">
        <div class="col-md-12">
            <h2 class="heading">Settings</h2>
            <p class="text-muted mb-0">Manage states, academic sessions, and holidays from one place.</p>
        </div>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success"><p>{{ $message }}</p></div>
    @endif

    @if ($message = Session::get('error'))
    <div class="alert alert-danger"><p>{{ $message }}</p></div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- States --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">States</h5>
        </div>
        <div class="card-body">
            @if ($currentState)
            <div class="alert alert-info py-2">
                <strong>Viewing:</strong> {{ $currentState->name }} ({{ $currentState->code }})
                <a href="{{ route('states.reset-view') }}" class="btn btn-sm btn-light ml-2">Reset view</a>
            </div>
            @endif

            <form action="{{ route('states.store') }}" method="POST" class="form-row align-items-end mb-4">
                @csrf
                <div class="form-group col-md-4">
                    <label>State Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Uttar Pradesh" value="{{ old('name') }}" required>
                </div>
                <div class="form-group col-md-3">
                    <label>State Code</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. UP" maxlength="10" value="{{ old('code') }}" required>
                </div>
                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary btn-block">Create State</button>
                </div>
            </form>

            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>State</th>
                        <th>Code</th>
                        <th>Districts</th>
                        <th>Trainers</th>
                        <th>Coordinators</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($states as $state)
                    @php
                        $districtCount = \App\Models\District::where('state_id', $state->id)->count();
                        $trainerCount = \App\Models\User::where('state_id', $state->id)->where('role', 0)->count();
                        $coordinatorCount = \App\Models\Cordinator::where('state_id', $state->id)->count();
                    @endphp
                    <tr>
                        <td><strong>{{ $state->name }}</strong></td>
                        <td>{{ $state->code }}</td>
                        <td>{{ $districtCount }}</td>
                        <td>{{ $trainerCount }}</td>
                        <td>{{ $coordinatorCount }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editStateModal{{ $state->id }}">Edit</button>
                            <form action="{{ route('states.switch') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="state_id" value="{{ $state->id }}">
                                <button type="submit" class="btn btn-sm btn-info">View</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No states yet. Create your first state above.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Academic Sessions --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Academic Sessions</h5>
        </div>
        <div class="card-body">
            @if ($currentSession)
            <div class="alert alert-info py-2">
                <strong>Viewing:</strong> {{ $currentSession->name }}
                @if($activeSession && $currentSession->id === $activeSession->id)
                    <span class="badge badge-success ml-2">Active Session</span>
                @else
                    <span class="badge badge-secondary ml-2">Archive View</span>
                    <a href="{{ route('academic-sessions.reset-view') }}" class="btn btn-sm btn-light ml-2">Back to active session</a>
                @endif
            </div>
            @endif

            <p class="text-muted small">Create a session when you start a new academic year. Previous active session will be closed automatically.</p>
            <form action="{{ route('academic-sessions.store') }}" method="POST" class="form-row align-items-end mb-4"
                onsubmit="return confirm('Create new session? The current active session will be closed.');">
                @csrf
                <div class="form-group col-md-3">
                    <label>Session Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. 2026-27" required>
                </div>
                <div class="form-group col-md-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary btn-block">Create & Activate</button>
                </div>
            </form>

            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Session</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th>Records</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                    @php
                        $recordCount = \App\Models\AsignedSchool::withoutGlobalScopes()
                            ->where('session_id', $session->id)->count();
                    @endphp
                    <tr>
                        <td><strong>{{ $session->name }}</strong></td>
                        <td>
                            @if($session->start_date && $session->end_date)
                                {{ $session->start_date->format('d/m/Y') }} – {{ $session->end_date->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($session->is_active)
                                <span class="badge badge-success">Active</span>
                            @elseif($session->status === 'closed')
                                <span class="badge badge-secondary">Closed</span>
                            @else
                                <span class="badge badge-light">{{ $session->status }}</span>
                            @endif
                        </td>
                        <td>{{ $recordCount }} assignments</td>
                        <td>
                            <form action="{{ route('academic-sessions.switch') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="session_id" value="{{ $session->id }}">
                                <button type="submit" class="btn btn-sm btn-info">View</button>
                            </form>
                            @if(!$session->is_active)
                            <form action="{{ route('academic-sessions.activate', $session->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Activate this session? Current active session will be closed.');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning">Activate</button>
                            </form>
                            @else
                            <form action="{{ route('academic-sessions.close', $session->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Close this session?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary">Close</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No sessions yet. Create your first session above.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Holidays --}}
    @php $holidayState = $currentState ?? $states->first(); @endphp
    <div class="card mb-4" id="holidays-section">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0">Holidays</h5>
            @if($states->count())
            <form action="{{ route('states.switch') }}" method="POST" class="form-inline m-0">
                @csrf
                <label class="mr-2 mb-0 small text-muted">State for holidays:</label>
                <select name="state_id" id="holidayHeaderState" class="form-control form-control-sm" onchange="this.form.submit()">
                    @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ ($holidayState && $holidayState->id == $state->id) ? 'selected' : '' }}>
                            {{ $state->name }} ({{ $state->code }})
                        </option>
                    @endforeach
                </select>
            </form>
            @endif
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                <strong>Auto holidays:</strong> Every Sunday and 2nd Saturday of each month are always excluded.
            </p>

            @if($states->count())
            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="border rounded p-3 h-100" style="background:#f8f9fa;">
                        <h6 class="mb-3">Add Holiday</h6>
                        <form action="{{ route('holidays.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>State</label>
                                <select name="state_id" id="holidayFormState" class="form-control" required>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ ($holidayState && $holidayState->id == $state->id) ? 'selected' : '' }}>
                                            {{ $state->name }} ({{ $state->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="holiday_date">Date</label>
                                <input type="text" name="holiday_date" id="holiday_date" class="form-control" placeholder="Click to pick date" required autocomplete="off" readonly>
                            </div>
                            <div class="form-group">
                                <label for="holiday_title">Title (optional)</label>
                                <input type="text" name="title" id="holiday_title" class="form-control" placeholder="e.g. Republic Day" autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Add Holiday</button>
                        </form>
                        <div class="mt-3 small">
                            <span class="d-inline-block mr-3"><span style="display:inline-block;width:12px;height:12px;background:#dc3545;border-radius:2px;"></span> Custom holiday</span>
                            <span class="d-inline-block mr-3"><span style="display:inline-block;width:12px;height:12px;background:#6c757d;border-radius:2px;"></span> Sunday</span>
                            <span class="d-inline-block"><span style="display:inline-block;width:12px;height:12px;background:#fd7e14;border-radius:2px;"></span> 2nd Saturday</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 mb-4">
                    <div class="border rounded p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0" id="calendarMonthLabel"></h6>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="calendarPrev">&laquo; Prev</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="calendarToday">Today</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="calendarNext">Next &raquo;</button>
                            </div>
                        </div>
                        <div id="holidayCalendar" class="holiday-calendar"></div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-warning mb-4">
                Please first create a state in the <strong>States</strong> section above, then you can add holidays here.
            </div>
       
            @endif

            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Added On</th>
                        <th style="width: 160px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($holidays as $holiday)
                    <tr>
                        <td><strong>{{ $holiday->holiday_date->format('d/m/Y') }}</strong></td>
                        <td>{{ $holiday->title ?: '—' }}</td>
                        <td>{{ $holiday->created_at->format('d/m/Y') }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editHolidayModal{{ $holiday->id }}">Edit</button>
                            <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this holiday?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No holidays added yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($states as $state)
<div class="modal fade" id="editStateModal{{ $state->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('states.update', $state->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit State</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>State Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $state->name }}" required>
                    </div>
                    <div class="form-group mb-0">
                        <label>State Code</label>
                        <input type="text" name="code" class="form-control" value="{{ $state->code }}" maxlength="10" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@foreach($holidays as $holiday)
<div class="modal fade" id="editHolidayModal{{ $holiday->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('holidays.update', $holiday->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Holiday</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="holiday_date" class="form-control" value="{{ $holiday->holiday_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Title (optional)</label>
                        <input type="text" name="title" class="form-control" value="{{ $holiday->title }}" placeholder="e.g. Republic Day">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
.holiday-calendar table { width: 100%; table-layout: fixed; }
.holiday-calendar th { text-align: center; font-size: 12px; padding: 6px 2px; color: #666; }
.holiday-calendar td {
    height: 64px; vertical-align: top; text-align: center; padding: 4px 2px;
    border: 1px solid #eee; font-size: 13px; position: relative; cursor: pointer;
}
.holiday-calendar td.empty { background: #fafafa; cursor: default; }
.holiday-calendar .day-num { font-weight: 600; display: block; }
.holiday-calendar .day-label {
    font-size: 9px; display: block; color: #fff; border-radius: 3px;
    padding: 1px 2px; margin-top: 2px; line-height: 1.2; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
}
.holiday-calendar td.is-sunday { background: #f1f3f5; }
.holiday-calendar td.is-second-saturday { background: #fff4e6; }
.holiday-calendar td.is-custom { background: #fde8ea; }
.holiday-calendar td.is-today { outline: 2px solid #007bff; outline-offset: -2px; }
</style>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
(function () {
    const holidayMap = @json($holidayMap);

    let viewDate = Object.keys(holidayMap).length
        ? moment(Object.keys(holidayMap).sort().reverse()[0], 'YYYY-MM-DD')
        : moment();

    if ($('#holiday_date').length) {
        $('#holiday_date').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: true,
            locale: { format: 'YYYY-MM-DD' },
            minDate: moment().subtract(5, 'years'),
            maxDate: moment().add(5, 'years'),
        });
    }

    function isSecondSaturday(date) {
        return date.day() === 6 && Math.ceil(date.date() / 7) === 2;
    }

    function renderCalendar() {
        if (!$('#holidayCalendar').length) {
            return;
        }

        const start = viewDate.clone().startOf('month');
        const end = viewDate.clone().endOf('month');
        $('#calendarMonthLabel').text(viewDate.format('MMMM YYYY'));

        let html = '<table><thead><tr>';
        ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(function (d) {
            html += '<th>' + d + '</th>';
        });
        html += '</tr></thead><tbody><tr>';

        for (let i = 0; i < start.day(); i++) {
            html += '<td class="empty"></td>';
        }

        const cursor = start.clone();
        while (cursor.isSameOrBefore(end, 'day')) {
            if (cursor.day() === 0 && cursor.date() !== 1) {
                html += '</tr><tr>';
            }

            const key = cursor.format('YYYY-MM-DD');
            let classes = [];
            let labels = [];

            if (cursor.day() === 0) {
                classes.push('is-sunday');
                labels.push('Sunday Off');
            } else if (isSecondSaturday(cursor)) {
                classes.push('is-second-saturday');
                labels.push('2nd Sat Off');
            }

            if (holidayMap[key]) {
                classes.push('is-custom');
                labels.push(holidayMap[key] + ' Off');
            }

            if (cursor.isSame(moment(), 'day')) {
                classes.push('is-today');
            }

            html += '<td class="' + classes.join(' ') + '" data-date="' + key + '" title="' + (labels.join(' / ') || '') + '">';
            html += '<span class="day-num">' + cursor.date() + '</span>';
            labels.forEach(function (label) {
                const color = (label.indexOf('Sunday') === 0) ? '#6c757d'
                    : (label.indexOf('2nd Sat') === 0) ? '#fd7e14'
                    : '#dc3545';
                html += '<span class="day-label" style="background:' + color + ';">' + label + '</span>';
            });
            html += '</td>';

            cursor.add(1, 'day');
        }

        html += '</tr></tbody></table>';
        $('#holidayCalendar').html(html);

        $('#holidayCalendar td[data-date]').on('click', function () {
            const date = $(this).data('date');
            const picker = $('#holiday_date').data('daterangepicker');
            if (picker) {
                picker.setStartDate(date);
                picker.setEndDate(date);
            }
            $('#holiday_date').val(date);
        });
    }

    // Add Holiday state change → reload page with that state's calendar
    $('#holidayFormState').on('change', function () {
        const stateId = $(this).val();
        const $header = $('#holidayHeaderState');
        if ($header.length) {
            $header.val(stateId);
            $header.closest('form').submit();
        }
    });

    $('#calendarPrev').on('click', function () {
        viewDate.subtract(1, 'month');
        renderCalendar();
    });
    $('#calendarNext').on('click', function () {
        viewDate.add(1, 'month');
        renderCalendar();
    });
    $('#calendarToday').on('click', function () {
        viewDate = moment();
        renderCalendar();
    });

    renderCalendar();
})();
</script>
@endsection
