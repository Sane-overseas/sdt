@extends('layouts.app')

@section('content')
<style type="text/css">
    #assignmentExcelReport {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    #assignmentExcelReport th,
    #assignmentExcelReport td {
        white-space: nowrap;
        vertical-align: middle;
    }
    #assignmentExcelReport th {
        background: #1a3a5c;
        color: #fff;
    }
    .status-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 12px;
        display: inline-block;
    }
    .status-completed { background: #d4edda; color: #155724; }
    .status-ongoing { background: #fff3cd; color: #856404; }
    .status-not-started { background: #d1ecf1; color: #0c5460; }
    .trainer-phone {
        display: block;
        font-size: 12px;
        color: #666;
        margin-top: 2px;
    }
    .assignment-report-meta {
        font-size: 14px;
        color: #555;
    }
</style>
<div class="container-fluid px-4">
    <div class="row align-items-center">
        <div class="col-lg-8 margin-tb">
            <h2 class="heading">District-wise Excel Report</h2>
            <p class="text-muted mb-0">District, District Coordinator, Block, Trainer &amp; School details</p>
        </div>
        <div class="col-lg-4 text-right margin-tb">
            <a href="{{ route('schools-reporting.assignment-excel.export', request()->only(['district_id', 'status'])) }}"
               class="btn btn-success">
                Download Excel
            </a>
        </div>
    </div>

    <form method="get" action="{{ route('schools-reporting.assignment-excel') }}" class="row align-items-end my-3">
        <div class="col-md-3">
            <label for="district_id" class="mb-1"><strong>District</strong></label>
            <select name="district_id" id="district_id" class="form-control">
                <option value="">All Districts</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ (string) $districtFilter === (string) $d->id ? 'selected' : '' }}>
                        {{ $d->district }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="status" class="mb-1"><strong>Status</strong></label>
            <select name="status" id="status" class="form-control">
                <option value="">All Status</option>
                <option value="not_started" {{ $statusFilter === 'not_started' ? 'selected' : '' }}>Not Started</option>
                <option value="ongoing" {{ $statusFilter === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="per_page" class="mb-1"><strong>Per page</strong></label>
            <select name="per_page" id="per_page" class="form-control">
                @foreach([25, 50, 100] as $n)
                    <option value="{{ $n }}" {{ (int) $perPage === $n ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 mt-2 mt-md-0">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('schools-reporting.assignment-excel') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-2 assignment-report-meta">
        <span>
            Showing {{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }}
            of {{ $rows->total() }} records
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="assignmentExcelReport">
            <thead>
                <tr>
                    <th>S. No</th>
                    <th>District</th>
                    <th>District Coordinator</th>
                    <th>Block</th>
                    <th>Trainer</th>
                    <th>Started Date</th>
                    <th>School Name</th>
                    <th>School Code</th>
                    <th>Total Students</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                    <tr>
                        <td>{{ ($rows->firstItem() ?? 1) + $i }}</td>
                        <td>{{ $row['district'] !== '' ? $row['district'] : '—' }}</td>
                        <td>{{ $row['district_coordinator'] }}</td>
                        <td>{{ $row['block'] !== '' ? $row['block'] : '—' }}</td>
                        <td>
                            <span>{{ $row['trainer'] }}</span>
                            @if($row['trainer_phone'] !== '')
                                <span class="trainer-phone">{{ $row['trainer_phone'] }}</span>
                            @endif
                        </td>
                        <td>{{ $row['route_plan'] }}</td>
                        <td>{{ $row['school_name'] }}</td>
                        <td>{{ $row['school_code'] }}</td>
                        <td>{{ $row['total_students'] }}</td>
                        <td>
                            @php
                                $statusClass = match ($row['status']) {
                                    'Completed' => 'status-completed',
                                    'Ongoing' => 'status-ongoing',
                                    default => 'status-not-started',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $row['status'] }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">No approved assigned schools found for this filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rows->hasPages())
        <div class="d-flex justify-content-center my-3">
            {{ $rows->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
