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
            </div>
            @endif

            <form action="{{ route('states.store') }}" method="POST" enctype="multipart/form-data" class="form-row align-items-end mb-4">
                @csrf
                <div class="form-group col-md-3">
                    <label>State Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Uttar Pradesh" value="{{ old('name') }}" required>
                </div>
                <div class="form-group col-md-2">
                    <label>State Code</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. UP" maxlength="10" value="{{ old('code') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>State Logo (right side)</label>
                    <input type="file" name="logo" class="form-control-file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                </div>
                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary btn-block">Create State</button>
                </div>
            </form>

            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Logo</th>
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
                        $logoUrl = $state->logoUrl();
                    @endphp
                    <tr>
                        <td>
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $state->code }} logo" style="max-height:40px;max-width:80px;object-fit:contain;">
                            @else
                                <span class="text-muted small">No logo</span>
                            @endif
                        </td>
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
                        <td colspan="7" class="text-center text-muted">No states yet. Create your first state above.</td>
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
                <strong>Auto Off:</strong> Every Sunday and 2nd Saturday are Off by default.
                Click a date on the calendar to toggle Off ↔ Working (you can also mark Sunday / 2nd Saturday as Working).
            </p>

            @if($states->count())
            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="border rounded p-3 h-100" style="background:#f8f9fa;">
                        <h6 class="mb-3">Add Holiday</h6>
                        <form action="{{ route('holidays.store') }}" method="POST" id="holidayAddForm">
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
                                <label>Apply Holiday To</label>
                                <select name="scope" id="holidayScope" class="form-control" required>
                                    <option value="state">Entire State</option>
                                    <option value="district">Specific District</option>
                                </select>
                            </div>
                            <div class="form-group" id="holidayDistrictGroup" style="display:none;">
                                <label>District</label>
                                <select name="district_id" id="holidayDistrict" class="form-control">
                                    <option value="">Select District</option>
                                    @foreach($holidayDistricts as $district)
                                        <option value="{{ $district->id }}">{{ $district->district }}</option>
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
                            <span class="d-inline-block mr-3"><span style="display:inline-block;width:12px;height:12px;background:#dc3545;border-radius:2px;"></span> Custom Off</span>
                            <span class="d-inline-block mr-3"><span style="display:inline-block;width:12px;height:12px;background:#6c757d;border-radius:2px;"></span> Sunday</span>
                            <span class="d-inline-block mr-3"><span style="display:inline-block;width:12px;height:12px;background:#fd7e14;border-radius:2px;"></span> 2nd Saturday</span>
                            <span class="d-inline-block"><span style="display:inline-block;width:12px;height:12px;background:#28a745;border-radius:2px;"></span> Forced Working</span>
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
                        <th>Scope</th>
                        <th>Added On</th>
                        <th style="width: 160px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($holidays as $holiday)
                    <tr>
                        <td><strong>{{ $holiday->holiday_date->format('d/m/Y') }}</strong></td>
                        <td>
                            {{ $holiday->title ?: '—' }}
                            @if(($holiday->entry_type ?? 'off') === 'working')
                                <span class="badge badge-success">Working</span>
                            @endif
                        </td>
                        <td>
                            @if($holiday->district_id)
                                <span class="badge badge-info">{{ $holiday->district->district ?? 'District' }}</span>
                            @else
                                <span class="badge badge-secondary">Entire State</span>
                            @endif
                        </td>
                        <td>{{ $holiday->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if(($holiday->entry_type ?? 'off') !== 'working')
                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editHolidayModal{{ $holiday->id }}">Edit</button>
                            @endif
                            <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this entry?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No holidays added yet.</td>
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
            <form action="{{ route('states.update', $state->id) }}" method="POST" enctype="multipart/form-data">
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
                    <div class="form-group">
                        <label>State Code</label>
                        <input type="text" name="code" class="form-control" value="{{ $state->code }}" maxlength="10" required>
                    </div>
                    <div class="form-group mb-0">
                        <label>State Logo (right side)</label>
                        @if($state->logoUrl())
                            <div class="mb-2">
                                <img src="{{ $state->logoUrl() }}" alt="{{ $state->code }} logo" style="max-height:50px;max-width:100px;object-fit:contain;">
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control-file" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                        <small class="text-muted">Leave empty to keep current logo.</small>
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
                        <label>Apply Holiday To</label>
                        <select name="scope" class="form-control holiday-edit-scope" data-target="editDistrict{{ $holiday->id }}" required>
                            <option value="state" {{ $holiday->district_id ? '' : 'selected' }}>Entire State</option>
                            <option value="district" {{ $holiday->district_id ? 'selected' : '' }}>Specific District</option>
                        </select>
                    </div>
                    <div class="form-group edit-district-group" id="editDistrict{{ $holiday->id }}" style="{{ $holiday->district_id ? '' : 'display:none;' }}">
                        <label>District</label>
                        <select name="district_id" class="form-control">
                            <option value="">Select District</option>
                            @foreach($holidayDistricts as $district)
                                <option value="{{ $district->id }}" {{ (int) $holiday->district_id === (int) $district->id ? 'selected' : '' }}>
                                    {{ $district->district }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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
.holiday-calendar td.is-working { background: #e8f8ee; }
.holiday-calendar td.is-today { outline: 2px solid #007bff; outline-offset: -2px; }
.holiday-calendar td.toggling { opacity: 0.5; pointer-events: none; }
</style>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
(function () {
    let holidayMap = @json($holidayMap);
    let workingMap = @json($workingMap ?? new \stdClass());
    const districtsByState = @json($districtsByState);
    const toggleUrl = @json(route('holidays.toggle'));
    const csrfToken = @json(csrf_token());

    let viewDate = moment();

    if ($('#holiday_date').length) {
        $('#holiday_date').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: true,
            locale: { format: 'YYYY-MM-DD' },
            minDate: moment().subtract(5, 'years'),
            maxDate: moment().add(5, 'years'),
        });
    }

    function fillDistrictOptions(stateId, $select, selectedId) {
        $select.empty().append('<option value="">Select District</option>');
        const list = districtsByState[stateId] || districtsByState[String(stateId)] || [];
        list.forEach(function (d) {
            const selected = selectedId && String(selectedId) === String(d.id) ? ' selected' : '';
            $select.append('<option value="' + d.id + '"' + selected + '>' + d.district + '</option>');
        });
    }

    function toggleScopeUi() {
        if ($('#holidayScope').val() === 'district') {
            $('#holidayDistrictGroup').show();
            $('#holidayDistrict').prop('required', true);
        } else {
            $('#holidayDistrictGroup').hide();
            $('#holidayDistrict').prop('required', false).val('');
        }
    }

    $('#holidayScope').on('change', toggleScopeUi);

    $('#holidayFormState').on('change', function () {
        const stateId = $(this).val();
        fillDistrictOptions(stateId, $('#holidayDistrict'));
        const $header = $('#holidayHeaderState');
        if ($header.length) {
            $header.val(stateId);
            $header.closest('form').submit();
        }
    });

    $('.holiday-edit-scope').on('change', function () {
        const target = $(this).data('target');
        const $group = $('#' + target);
        if ($(this).val() === 'district') {
            $group.show();
            $group.find('select[name="district_id"]').prop('required', true);
        } else {
            $group.hide();
            $group.find('select[name="district_id"]').prop('required', false).val('');
        }
    });

    function isSecondSaturday(date) {
        return date.day() === 6 && Math.ceil(date.date() / 7) === 2;
    }

    function currentScopePayload() {
        const scope = $('#holidayScope').val() || 'state';
        const payload = {
            state_id: $('#holidayFormState').val(),
            scope: scope,
            _token: csrfToken,
        };
        if (scope === 'district') {
            payload.district_id = $('#holidayDistrict').val();
        }
        return payload;
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

            const isForcedWorking = !!workingMap[key];
            const isSunday = cursor.day() === 0;
            const is2ndSat = isSecondSaturday(cursor);

            if (isForcedWorking) {
                classes.push('is-working');
                labels.push(workingMap[key] || 'Working');
            } else if (holidayMap[key]) {
                classes.push('is-custom');
                labels.push(holidayMap[key]);
            } else if (isSunday) {
                classes.push('is-sunday');
                labels.push('Sunday Off');
            } else if (is2ndSat) {
                classes.push('is-second-saturday');
                labels.push('2nd Sat Off');
            }

            if (cursor.isSame(moment(), 'day')) {
                classes.push('is-today');
            }

            html += '<td class="' + classes.join(' ') + '" data-date="' + key + '" title="' + (labels.join(' / ') || 'Click to mark Off') + '">';
            html += '<span class="day-num">' + cursor.date() + '</span>';
            labels.forEach(function (label) {
                let color = '#dc3545';
                if (label.indexOf('Sunday') === 0) color = '#6c757d';
                else if (label.indexOf('2nd Sat') === 0) color = '#fd7e14';
                else if (String(label).toLowerCase().indexOf('working') !== -1) color = '#28a745';
                html += '<span class="day-label" style="background:' + color + ';">' + label + '</span>';
            });
            html += '</td>';

            cursor.add(1, 'day');
        }

        html += '</tr></tbody></table>';
        $('#holidayCalendar').html(html);

        $('#holidayCalendar td[data-date]').off('click').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const $cell = $(this);
            if ($cell.hasClass('toggling')) {
                return;
            }

            const date = String($cell.attr('data-date') || '');
            if (!date) {
                return;
            }

            const picker = $('#holiday_date').data('daterangepicker');
            if (picker) {
                picker.setStartDate(date);
                picker.setEndDate(date);
            }
            $('#holiday_date').val(date);

            const payload = currentScopePayload();
            if (!payload.state_id) {
                alert('Select a state first.');
                return;
            }
            if (payload.scope === 'district' && !payload.district_id) {
                alert('Select a district first, or choose Entire State.');
                return;
            }

            const title = ($('#holiday_title').val() || '').trim();
            if (title) {
                payload.title = title;
            }

            payload.holiday_date = date;
            $cell.addClass('toggling');

            const body = new URLSearchParams();
            Object.keys(payload).forEach(function (key) {
                if (payload[key] !== undefined && payload[key] !== null && payload[key] !== '') {
                    body.append(key, payload[key]);
                }
            });

            fetch(toggleUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                },
                body: body.toString(),
                credentials: 'same-origin',
            })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, status: response.status, data: data };
                }).catch(function () {
                    return { ok: false, status: response.status, data: null };
                });
            })
            .then(function (result) {
                if (!result.ok || !result.data) {
                    let msg = 'Toggle failed (' + result.status + ')';
                    if (result.data) {
                        if (result.data.message) {
                            msg = result.data.message;
                        } else if (result.data.errors) {
                            msg = Object.values(result.data.errors).flat().join('\n');
                        }
                    }
                    alert(msg);
                    $cell.removeClass('toggling');
                    return;
                }

                const res = result.data;
                let label = (res.holiday && res.holiday.title)
                    ? res.holiday.title
                    : (res.label || 'Off');
                if (res.holiday && res.holiday.entry_type === 'working' && (!label || label === 'Off')) {
                    label = 'Working';
                }
                if (payload.scope === 'district') {
                    const districtName = ($('#holidayDistrict option:selected').text() || '').trim();
                    if (districtName) {
                        label = label + ' (' + districtName + ')';
                    }
                }

                delete holidayMap[date];
                delete workingMap[date];

                if (res.holiday && res.holiday.entry_type === 'working') {
                    workingMap[date] = label;
                } else if (res.status === 'off') {
                    const m = moment(date, 'YYYY-MM-DD');
                    const isAutoOff = m.day() === 0 || isSecondSaturday(m);
                    if (!isAutoOff) {
                        holidayMap[date] = label;
                    }
                }

                renderCalendar();
            })
            .catch(function (err) {
                alert(err && err.message ? err.message : 'Toggle failed. Please refresh and try again.');
                $cell.removeClass('toggling');
            });
        });
    }

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

    toggleScopeUi();
    renderCalendar();
})();
</script>
@endsection
