@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Bulk Import Schools</h2>
            <p class="text-muted mb-0">Import multiple schools from Excel or CSV file</p>
        </div>
        <a href="{{ route('admin.manageschool') }}" class="btn btn-outline-secondary">
            Back to Manage Schools
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Import Schools from File</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="alert alert-light border">
                <h6 class="mb-2 text-primary">File Format Requirements</h6>
                <p class="mb-2">Your Excel/CSV file must contain these columns:</p>
                <div class="row">
                    <div class="col-md-6">
                        <ul>
                            <li><strong>School Name</strong></li>
                            <li><strong>School Code</strong></li>
                            <li><strong>Block</strong></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li><strong>Total Students</strong></li>
                            <li><strong>Total Training Hours</strong> (e.g. 60)</li>
                            <li><strong>Daily Training Hours</strong> (e.g. 2)</li>
                        </ul>
                    </div>
                </div>
                <small class="text-muted">
                    Download the template to ensure correct formatting. Same School Code in the selected district will be updated; new codes will be created.
                </small>
                <div class="mt-2">
                    <a href="{{ route('schools.download-template') }}" class="btn btn-primary btn-sm">
                        Download Template
                    </a>
                </div>
            </div>

            <form id="bulkImportForm" method="POST" action="{{ route('schools.import') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="district_id" class="form-label">Select District</label>
                            <select id="district_id" class="form-control" name="district_id" required>
                                <option value="">-- Choose District --</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}" @selected(old('district_id') == $district->id)>{{ $district->district }}</option>
                                @endforeach
                            </select>
                            @error('district_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="file" class="form-label">Select File</label>
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
                        Back to Manage Schools
                    </a>
                    <button type="submit" class="btn btn-success px-4" id="bulkImportBtn">
                        Import Schools
                    </button>
                </div>
            </form>

            <div class="mt-4">
                <h6 class="text-muted mb-3">Sample File Format</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm bg-light">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center">School Name</th>
                                <th class="text-center">School Code</th>
                                <th class="text-center">Block</th>
                                <th class="text-center">Total Students</th>
                                <th class="text-center">Total Training Hours</th>
                                <th class="text-center">Daily Training Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ABC Primary School</td>
                                <td>12345</td>
                                <td>Block A</td>
                                <td class="text-center">150</td>
                                <td class="text-center">60</td>
                                <td class="text-center">2</td>
                            </tr>
                            <tr>
                                <td>XYZ High School</td>
                                <td>67890</td>
                                <td>Block B</td>
                                <td class="text-center">300</td>
                                <td class="text-center">60</td>
                                <td class="text-center">2</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
@if (session('success'))
if (typeof Swal !== 'undefined') {
    Swal.fire({
        icon: 'success',
        title: 'Import successful',
        text: @json(session('success'))
    });
}
@elseif (session('error'))
if (typeof Swal !== 'undefined') {
    Swal.fire({
        icon: 'error',
        title: 'Import failed',
        text: @json(session('error'))
    });
}
@endif

var bulkImportForm = document.getElementById('bulkImportForm');
if (bulkImportForm) {
    bulkImportForm.addEventListener('submit', function () {
        var btn = document.getElementById('bulkImportBtn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Importing…';
        }
    });
}
</script>
@endsection
