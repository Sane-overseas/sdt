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
@endphp
<div class="row route-plan-fields">
    <strong>Route Plan</strong>
    <small class="text-muted d-block mb-2 col-12">Sundays, 2nd Saturdays and public holidays are excluded from working days.</small>
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
        <input type="time" name="intime" class="form-control" value="{{ $data['start_route_plan'] }}">
    </div>
    <div class="form-group col-12">
        <span>Outtime:</span>
        <input type="time" name="outtime" class="form-control" value="{{ $data['end_route_plan'] }}">
    </div>
    <input type="hidden" id="id" name="id" class="form-control" value="{{ $data['id'] }}">
</div>
