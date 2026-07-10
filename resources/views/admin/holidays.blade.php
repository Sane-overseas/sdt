@extends('layouts.app')

@section('content')
<div class="container mt-2">
    <div class="row margin-tb mb-3">
        <div class="col-md-8">
            <h2 class="heading">Manage Holidays</h2>
            <!-- <p class="text-muted mb-0">Set holidays in advance. Trainers' route plans will exclude these dates from working days.</p> -->
            <p class="text-muted mb-0"><strong>Auto holidays:</strong> Every Sunday and 2nd Saturday of each month are always excluded.</p>
        </div>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif

    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        <p>{{ $message }}</p>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Add Holiday</h5>
            <form action="{{ route('holidays.store') }}" method="POST" class="form-row align-items-end">
                @csrf
                <div class="form-group col-md-4">
                    <label for="holiday_date">Date</label>
                    <input type="date" name="holiday_date" id="holiday_date" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group col-md-5">
                    <label for="holiday_title">Title (optional)</label>
                    <input type="text" name="title" id="holiday_title" class="form-control" placeholder="e.g. Republic Day" autocomplete="off">
                </div>
                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary btn-block">Add Holiday</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
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
                    <td>
                        <strong>{{ $holiday->holiday_date->format('d/m/Y') }}</strong>
                    </td>
                    <td>{{ $holiday->title ?: '—' }}</td>
                    <td>{{ $holiday->created_at->format('d/m/Y') }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editHolidayModal{{ $holiday->id }}">
                            Edit
                        </button>
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
@endsection
