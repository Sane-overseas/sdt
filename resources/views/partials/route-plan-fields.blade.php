@php
    $routeParts = !empty($data['route_date']) ? array_map('trim', explode('-', $data['route_date'], 2)) : [null, null];
    $existingStart = '';
    if (!empty($routeParts[0])) {
        try {
            $existingStart = \Carbon\Carbon::createFromFormat('d/m/Y', trim($routeParts[0]))->format('Y-m-d');
        } catch (\Exception $e) {
            $existingStart = '';
        }
    }
    $existingWorkingDays = $data['working_days'] ?? '';

    $holidayDistrictId = null;
    $holidayStateId = null;
    $requiredHours = $data['required_hours'] ?? null;
    if (!empty($data['school_name'])) {
        $schoolForHoliday = \App\Models\School::with('district')->find($data['school_name']);
        $holidayDistrictId = $schoolForHoliday?->district_id;
        $holidayStateId = $schoolForHoliday?->district?->state_id;
        if ($requiredHours === null && $schoolForHoliday) {
            $requiredHours = \App\Services\TrainingHoursService::getForSchool((int) $schoolForHoliday->id);
        }
    }

    $intimeValue = !empty($data['start_route_plan']) ? substr((string) $data['start_route_plan'], 0, 5) : '';
    $outtimeValue = !empty($data['end_route_plan']) ? substr((string) $data['end_route_plan'], 0, 5) : '';
@endphp
<div class="row route-plan-fields"
     data-district-id="{{ $holidayDistrictId }}"
     data-state-id="{{ $holidayStateId }}"
     data-required-hours="{{ $requiredHours !== null ? $requiredHours : '' }}">
    <strong>Route Plan</strong>
    @if($requiredHours !== null)
    <p class="col-12 mb-2 text-muted small">
        Required training hours for this school: <strong>{{ number_format((float) $requiredHours, 2) }} hrs</strong>
        (covered by working days × daily intime–outtime).
    </p>
    @else
    <p class="col-12 mb-2 text-muted small">
        No training hours set for this school.  
    </p>
    @endif
    <div class="form-group col-12">
        <span>Start Date:</span>
        <input type="date" name="start_date" class="form-control route-plan-start" value="{{ $existingStart }}" required>
    </div>
    <div class="form-group col-12">
        <span>Working Days (school visit days):</span>
        <input type="number" name="working_days" class="form-control route-plan-working-days" min="1" value="{{ $existingWorkingDays }}" required>
    </div>
    <div class="form-group col-12">
        <span>Calculated End Date (holidays excluded):</span>
        <input type="text" class="form-control route-plan-end-display" readonly placeholder="Auto-calculated">
    </div>
    <p class="route-plan-holiday-note text-info small col-12"></p>
    <div class="form-group col-12">
        <span>Intime:</span>
        <input type="time" name="intime" class="form-control route-plan-intime" value="{{ $intimeValue }}" required>    
    </div>
    <div class="form-group col-12">
        <span>Outtime:</span>
        <input type="time" name="outtime" class="form-control route-plan-outtime" value="{{ $outtimeValue }}" required>
    </div>
    <div class="form-group col-12">
        <span>Planned Training Hours:</span>
        <input type="text" class="form-control route-plan-hours-display" readonly placeholder="working days × daily hours">
    </div>
    <p class="route-plan-hours-note small col-12"></p>
    <input type="hidden" id="id" name="id" class="form-control" value="{{ $data['id'] }}">
</div>
