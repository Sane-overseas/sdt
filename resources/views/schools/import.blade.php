@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Bulk Import Schools</h2>
            <p class="text-muted mb-0">Import multiple schools from Excel or CSV file</p>
        </div>
        <a href="{{ route('admin.manageschool') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Manage Schools
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-file-import"></i> Import Schools from File
            </h4>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="alert alert-light border-start border-primary border-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 text-primary">
                            <i class="fas fa-info-circle"></i> File Format Requirements
                        </h6>
                        <p class="mb-2">Your Excel/CSV file must contain these columns in the exact order:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> <strong>School Name</strong></li>
                                    <li><i class="fas fa-check text-success"></i> <strong>School Code</strong></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> <strong>Block</strong></li>
                                    <li><i class="fas fa-check text-success"></i> <strong>Total Students</strong></li>
                                </ul>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb"></i> Download the template to ensure correct formatting.
                        </small>
                    </div>
                    <div class="ms-3">
                        <a href="{{ route('schools.download-template') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('schools.import') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="district_id" class="form-label fw-semibold">
                                <i class="fas fa-map-marker-alt"></i> Select District
                            </label>
                            <select id="district_id" class="form-select" name="district_id" required>
                                <option value="">-- Choose District --</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}">{{ $district->district }}</option>
                                @endforeach
                            </select>
                            @error('district_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">
                                <i class="fas fa-file-upload"></i> Select File
                            </label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.csv,.xls" required>
                            <div class="form-text small">Excel (.xlsx, .xls) or CSV (.csv) files only</div>
                            @error('file')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('admin.manageschool') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Manage Schools
                    </a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-upload"></i> Import Schools
                    </button>
                </div>
            </form>

            <div class="mt-4">
                <h6 class="text-muted mb-3">
                    <i class="fas fa-table"></i> Sample File Format
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm bg-light">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center">School Name</th>
                                <th class="text-center">School Code</th>
                                <th class="text-center">Block</th>
                                <th class="text-center">Total Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ABC Primary School</td>
                                <td>12345</td>
                                <td>Block A</td>
                                <td class="text-center">150</td>
                            </tr>
                            <tr>
                                <td>XYZ High School</td>
                                <td>67890</td>
                                <td>Block B</td>
                                <td class="text-center">300</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
