@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <div class="row margin-tb align-items-center mb-3">
        <div class="col-md-8">
            <h2 class="heading mb-1">School Requests</h2>
            <p class="text-muted mb-0 small">Trainer school requests — pending, approved, and rejected stay on this page.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered" id="schoolRequestsTable">
                <thead>
                    <tr>
                        <th>Trainer</th>
                        <th>Email</th>
                        <th>Block</th>
                        <th>School</th>
                        <th>Hrs</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th style="min-width:200px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $row)
                        @php
                            $school = $schools[$row->school_name] ?? null;
                            $status = $row->approval_status ?? 'approved';
                        @endphp
                        <tr>
                            <td>{{ $row->user->instructor_name ?? '—' }}</td>
                            <td>{{ $row->user->email ?? '—' }}</td>
                            <td>{{ $row->block ?: '—' }}</td>
                            <td>{{ $school->school_name ?? ('#'.$row->school_name) }}</td>
                            <td>
                                {{ $school && $school->training_hours !== null ? number_format((float)$school->training_hours, 1).' total' : '—' }}
                                @if($school && $school->daily_training_hours !== null)
                                    / {{ number_format((float)$school->daily_training_hours, 1) }}/day
                                @endif
                            </td>
                            <td>{{ $row->created_at?->format('d-m-Y H:i') }}</td>
                            <td>
                                @if($status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                    @if($row->approval_note)
                                        <div class="small text-muted mt-1">{{ $row->approval_note }}</div>
                                    @endif
                                @else
                                    <span class="badge badge-success">Approved</span>
                                    @if($row->approved_at)
                                        <div class="small text-muted mt-1">{{ $row->approved_at->format('d-m-Y H:i') }}</div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($status === 'pending')
                                    <form method="POST" action="{{ route('admin.school-requests.approve', $row->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.school-requests.reject', $row->id) }}" class="d-inline reject-form">
                                        @csrf
                                        <input type="hidden" name="note" class="reject-note" value="">
                                        <button type="button" class="btn btn-sm btn-danger btn-reject">Reject</button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted">No school requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function () {
    // Bind before DataTable — if DT fails, Reject must still work
    $(document).on('click', '.btn-reject', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $form = $(this).closest('form.reject-form');
        if (!$form.length) {
            $form = $(this).closest('form');
        }

        function submitReject(note) {
            $form.find('.reject-note').val(note || '');
            $form.trigger('submit');
        }

        if (typeof Swal !== 'undefined' && Swal.fire) {
            Swal.fire({
                title: 'Reject this request?',
                input: 'text',
                inputPlaceholder: 'Optional note for trainer',
                showCancelButton: true,
                confirmButtonText: 'Reject',
                confirmButtonColor: '#d33'
            }).then(function (result) {
                if (result.isConfirmed) {
                    submitReject(result.value || '');
                }
            });
            return;
        }

        if (window.confirm('Reject this school request?')) {
            var note = window.prompt('Optional note for trainer:', '') || '';
            submitReject(note);
        }
    });

    if ($.fn.DataTable && $('#schoolRequestsTable').length) {
        try {
            $('#schoolRequestsTable').DataTable({ pageLength: 25, order: [] });
        } catch (err) {
            console.warn('School requests DataTable init skipped', err);
        }
    }
})();
</script>
@endsection
