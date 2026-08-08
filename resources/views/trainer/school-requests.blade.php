@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <div class="row margin-tb align-items-center mb-3">
        <div class="col-md-8">
            <h2 class="heading mb-1">Request Schools</h2>
            <p class="text-muted mb-0 small">
                Block: <strong>{{ $user->block ?: '—' }}</strong>
                · District: <strong>{{ $user->district ?: '—' }}</strong>
                · Max <strong>{{ \App\Services\SchoolRequestService::MAX_ACTIVE_SLOTS }}</strong> schools at a time
                · Left: <strong>{{ $remaining }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Upload</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(empty($user->block))
        <div class="alert alert-warning">Your block is not set. Please ask admin to set it first.</div>
    @else
        <div class="card mb-4">
            <div class="card-header"><strong>Schools in your block</strong></div>
            <div class="card-body">
                @if($available->isEmpty())
                    <p class="text-muted mb-0">No free schools in your block right now.</p>
                @elseif($remaining <= 0)
                    <p class="text-muted mb-0">You already have {{ \App\Services\SchoolRequestService::MAX_ACTIVE_SLOTS }} schools. Finish one, then you can ask for more.</p>
                @else
                    <form method="POST" action="{{ route('trainer.school-requests.store') }}" id="schoolRequestForm">
                        @csrf
                        <p class="small text-muted mb-2">
                            Tick up to <strong>{{ $remaining }}</strong> school{{ $remaining == 1 ? '' : 's' }}.
                            Admin will approve your request.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="availableSchoolsTable">
                                <thead>
                                    <tr>
                                        <th style="width:40px;"></th>
                                        <th>School</th>
                                        <th>Code</th>
                                        <th>Total Hrs</th>
                                        <th>Hrs/Day</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($available as $school)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="school_ids[]" value="{{ $school->id }}" class="school-check">
                                            </td>
                                            <td>{{ $school->school_name }}</td>
                                            <td>{{ $school->school_code }}</td>
                                            <td>{{ $school->training_hours !== null ? number_format((float)$school->training_hours, 1) : '—' }}</td>
                                            <td>{{ $school->daily_training_hours !== null ? number_format((float)$school->daily_training_hours, 1) : '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnRequestSchools">Send for Approval</button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><strong>My requests</strong></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead>
                    <tr>
                        <th>School</th>
                        <th>Approval</th>
                        <th>Training</th>
                        <th>Requested</th>
                        <th>Note</th>
                        <th>Auth Letter</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myRequests as $row)
                        <tr>
                            <td>{{ $schoolNames[$row->school_name] ?? ('#'.$row->school_name) }}</td>
                            <td>
                                @if($row->approval_status === 'pending')
                                    <span class="badge badge-warning">Pending Approval</span>
                                @elseif($row->approval_status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-success">Approved</span>
                                @endif
                            </td>
                            <td>
                                @if(($row->approval_status ?? 'approved') !== 'approved')
                                    —
                                @elseif((int)$row->status === 1)
                                    <span class="badge badge-info">Completed</span>
                                @elseif(!empty($row->route_date))
                                    In progress
                                @else
                                    Not started
                                @endif
                            </td>
                            <td>{{ $row->created_at?->format('d-m-Y H:i') }}</td>
                            <td>{{ $row->approval_note ?: '—' }}</td>
                            <td>
                                @if(($row->approval_status ?? '') === 'approved' && !empty($row->auth_letter_path))
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('trainer.school-requests.auth-letter', $row->id) }}">
                                        Download
                                    </a>
                                @elseif(($row->approval_status ?? '') === 'approved')
                                    <span class="text-muted small">Preparing…</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">No requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function () {
    var maxSelect = {{ (int) $remaining }};

    function showPickPopup(en, hi) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Alert',
                html: '<p style="margin:0 0 8px;font-weight:600;">' + en + '</p>'
                    + '<p style="margin:0;color:#555;">' + hi + '</p>',
                confirmButtonText: 'OK / ठीक है',
                confirmButtonColor: '#0d6efd',
                allowOutsideClick: true,
            });
            return;
        }
        // fallback
        alert(en + '\n' + hi);
    }

    function maxMsg() {
        var en = 'You can choose only ' + maxSelect + ' school' + (maxSelect === 1 ? '' : 's') + '.';
        var hi = maxSelect === 1
            ? 'आप सिर्फ 1 स्कूल चुन सकते हैं।'
            : 'आप सिर्फ ' + maxSelect + ' स्कूल चुन सकते हैं।';
        showPickPopup(en, hi);
    }

    $('#schoolRequestForm').on('submit', function (e) {
        var n = $('.school-check:checked').length;
        if (n < 1) {
            e.preventDefault();
            showPickPopup(
                'Please choose at least 1 school.',
                'कृपया कम से कम 1 स्कूल चुनें।'
            );
            return;
        }
        if (n > maxSelect) {
            e.preventDefault();
            maxMsg();
        }
    });

    $(document).on('change', '.school-check', function () {
        if ($('.school-check:checked').length > maxSelect) {
            this.checked = false;
            maxMsg();
        }
    });
})();
</script>
@endsection
