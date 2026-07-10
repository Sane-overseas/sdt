@extends('layouts.app')

@section('content')
<div class="container mt-2">
    <div class="row margin-tb mb-3">
        <div class="col-md-8">
            <h2 class="heading">States</h2>
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

    @if ($currentState)
    <div class="alert alert-info">
        <strong>Viewing:</strong> {{ $currentState->name }} ({{ $currentState->code }})
        <a href="{{ route('states.reset-view') }}" class="btn btn-sm btn-light ml-2">Reset view</a>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Add New State</h5>
            <!-- <p class="text-muted small mb-3">Each state has its own schools, trainers, coordinators, and districts. Data does not mix across states.</p> -->
            <form action="{{ route('states.store') }}" method="POST" class="form-row align-items-end">
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
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
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
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No states yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
