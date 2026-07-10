@extends('layouts.app')

@section('content')
<div class="container mt-2">
    <div class="row margin-tb mb-3">
        <div class="col-md-8">
            <h2 class="heading">Academic Sessions</h2>
        </div>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success"><p>{{ $message }}</p></div>
    @endif

    @if ($message = Session::get('error'))
    <div class="alert alert-danger"><p>{{ $message }}</p></div>
    @endif

    @if ($currentSession)
    <div class="alert alert-info">
        <strong>Viewing:</strong> {{ $currentSession->name }}
        @if($activeSession && $currentSession->id === $activeSession->id)
            <span class="badge badge-success ml-2">Active Session</span>
        @else
            <span class="badge badge-secondary ml-2">Archive View</span>
            <a href="{{ route('academic-sessions.reset-view') }}" class="btn btn-sm btn-light ml-2">Back to active session</a>
        @endif
    </div>  
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Create New Session</h5>
            <p class="text-muted small">Previous active session will be <strong>closed</strong>. New session becomes <strong>active</strong>. Existing data stays in old session.</p>
            <form action="{{ route('academic-sessions.store') }}" method="POST" class="form-row align-items-end"
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
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
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
                    <td colspan="5" class="text-center text-muted">No sessions yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
