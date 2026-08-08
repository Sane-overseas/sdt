@extends('layouts.app')

@section('content')
<div class="container mt-3 mb-5">
    <div class="row align-items-center mb-3">
        <div class="col-md-8">
            <h2 class="heading mb-1">Trainer Needs (Graph)</h2>
            <p class="text-muted mb-0">
                State: <strong>{{ $summary['state_name'] }}</strong>
                · Rule: <strong>1 trainer = {{ $summary['schools_per_trainer'] }} schools</strong>
            </p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('schools-reporting') }}" class="btn btn-secondary btn-sm">Back to Schools Reporting</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background:#eef6ff;">
                <div class="card-body text-center">
                    <div class="text-muted small">Total Schools</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $summary['schools'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background:#fff4e5;">
                <div class="card-body text-center">
                    <div class="text-muted small">Trainers Required</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $summary['required'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background:#e8f8ef;">
                <div class="card-body text-center">
                    <div class="text-muted small">Working Now</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $summary['working'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background:#fdecea;">
                <div class="card-body text-center">
                    <div class="text-muted small">Still Need</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $summary['still_need'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <strong>Required vs Working Trainers</strong>
        </div>
        <div class="card-body">
            @if(count($rows) === 0)
                <p class="text-muted mb-0">No districts found for this state.</p>
            @else
                <canvas id="trainerNeedsChart" height="110"></canvas>
            @endif
        </div>
    </div>

    <div class="row">
        @forelse($rows as $row)
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-2">{{ $row['district'] }}</h5>
                        <p class="mb-2 font-weight-bold">
                            In {{ $row['district'] }}, total trainers required is {{ $row['trainer_required'] }}.
                        </p>
                        <div class="small mb-2">
                            Schools: <strong>{{ $row['schools'] }}</strong>
                            · Working now: <strong>{{ $row['working'] }}</strong>
                            · Still need: <strong>{{ $row['still_need'] }}</strong>
                        </div>
                        <div class="progress" style="height:10px;">
                            @php
                                $pct = $row['trainer_required'] > 0
                                    ? min(100, round(($row['working'] / $row['trainer_required']) * 100))
                                    : 0;
                            @endphp
                            <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                 role="progressbar"
                                 style="width: {{ $pct }}%;"
                                 aria-valuenow="{{ $pct }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        </div>
                        <div class="small text-muted mt-1">{{ $pct }}% covered</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border">No district data to show.</div>
            </div>
        @endforelse
    </div>
</div>

@if(count($rows) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    var labels = @json($chart['labels']);
    var required = @json($chart['required']);
    var working = @json($chart['working']);
    var ctx = document.getElementById('trainerNeedsChart');
    if (!ctx || typeof Chart === 'undefined') return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Required',
                    data: required,
                    backgroundColor: 'rgba(245, 158, 11, 0.75)',
                    borderColor: 'rgba(217, 119, 6, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Working',
                    data: working,
                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                    borderColor: 'rgba(5, 150, 105, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    title: { display: true, text: 'Trainers' }
                },
                x: {
                    ticks: { maxRotation: 45, minRotation: 0 }
                }
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        afterBody: function (items) {
                            if (!items.length) return '';
                            var i = items[0].dataIndex;
                            var need = Math.max(0, (required[i] || 0) - (working[i] || 0));
                            return 'Still need: ' + need;
                        }
                    }
                }
            }
        }
    });
})();
</script>
@endif
@endsection
