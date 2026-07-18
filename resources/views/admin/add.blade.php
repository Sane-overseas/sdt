<!-- Add these if not already included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">

            <a href="{{ route('admin.manageschool') }}" class="btn btn-primary ms-auto">Manage Schools</a>
        </div>
        <div class="card shadow">

            <div class="card-header bg-primary text-white">
                <h3>Add New School</h3>
            </div>
            <div class="card-body">
                @if(isset($currentState) && $currentState)
                <div class="alert alert-info py-2">
                    Adding schools for state: <strong>{{ $currentState->name }}</strong> ({{ $currentState->code }})
                </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form id="schoolForm" method="POST" action="{{ route('schools.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Select District</label>
                        <select id="districtSelect" class="form-select" name="district_id" required>
                            <option value="">-- Select District --</option>

                            @foreach ($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->district }}</option>
                            @endforeach

                        </select>

                        <button type="button" class="btn btn-link" data-bs-toggle="modal"
                            data-bs-target="#addDistrictModal">Add New District</button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Block</label>
                        <select id="blockSelect" class="form-select" name="block" required>
                            <option value="">-- Select Block --</option>
                        </select>
                        <button type="button" class="btn btn-link" data-bs-toggle="modal"
                            data-bs-target="#addBlockModal">Add New Block</button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">School Name</label>
                        <input type="text" class="form-control" name="school_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">School Code</label>
                        <input type="text" class="form-control" name="school_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Student</label>
                        <input type="text" class="form-control" name="total_students" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Training Hours</label>
                        <input type="number" class="form-control" name="training_hours" min="0.5" step="0.5" placeholder="e.g. 40" value="{{ old('training_hours') }}" required>
                    </div>

                    <button type="submit" class="btn btn-success">Add School</button>
                </form>



                {{-- Bulk Import Schools Section --}}
                <hr class="my-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Bulk Import Schools</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="alert alert-light border">
                            <h6 class="mb-2 text-primary">📋 Import Instructions</h6>
                            <p class="mb-2">Upload an Excel/CSV file with school data. Your file must include these columns:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="mb-2">
                                        <li><strong>School Name</strong></li>
                                        <li><strong>School Code</strong></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="mb-2">
                                        <li><strong>Block</strong></li>
                                        <li><strong>Total Students</strong></li>
                                    </ul>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Download the template to ensure correct formatting.
                            </small>
                        </div>

                        <form action="{{ route('schools.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Select District</label>
                                        <select name="district_id" class="form-select" required>
                                            <option value="">Choose District</option>
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
                                        <label class="form-label fw-semibold">Upload File</label>
                                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                                        <div class="form-text small">Excel (.xlsx, .xls) or CSV (.csv) files only</div>
                                        @error('file')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('schools.download-template') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-download"></i> Download Template
                                </a>
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="fas fa-upload"></i> Import Schools
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>

    {{-- Add District Modal --}}
    <div class="modal fade" id="addDistrictModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add New District</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>District Name</label>
                    <input type="text" id="newDistrict" class="form-control" placeholder="Enter District Name">
                    <button class="btn btn-primary mt-2" onclick="addDistrict()">Save</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Block Modal --}}
    <div class="modal fade" id="addBlockModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add New Block</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Select District</label>
                    <select id="blockDistrictSelect" class="form-select">
                        @foreach ($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->district }}</option>
                        @endforeach

                    </select>


                    <label class="mt-2">Block Name</label>
                    <input type="text" id="newBlock" class="form-control" placeholder="Enter Block Name">

                    <button class="btn btn-primary mt-2" onclick="addBlock()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('districtSelect').addEventListener('change', function() {
            let districtId = this.value;
            fetch(`/blocks/fetch/${districtId}`)
                .then(response => response.json())
                .then(data => {
                    let blockSelect = document.getElementById('blockSelect');
                    blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
                    data.forEach(block => {
                        blockSelect.innerHTML +=
                            `<option value="${block.block}">${block.block}</option>`;
                    });
                });
        });




        // $(document).ready(function() {
        //     $('#saveDistrictBtn').on('click', function() {
        //         let districtName = $('#district_name').val();

        //         if (!districtName.trim()) {
        //             alert("Please enter a district name!");
        //             return;
        //         }

        //         fetch("{{ route('districts.store') }}", {
        //                 method: "POST",
        //                 headers: {
        //                     "X-CSRF-TOKEN": "{{ csrf_token() }}",
        //                     "Content-Type": "application/json"
        //                 },
        //                 body: JSON.stringify({
        //                     district: districtName
        //                 })
        //             })
        //             .then(response => response.json())
        //             .then(data => {
        //                 if (data.success) {
        //                     alert("District added successfully!");
        //                     location.reload();
        //                 } else {
        //                     alert("Error: " + data.message);
        //                 }
        //             })
        //             .catch(error => {
        //                 console.error("Fetch error:", error);
        //                 alert("Failed to add district. Check console for details.");
        //             });
        //     });
        // });

        function addBlock() {
            let districtId = document.getElementById('blockDistrictSelect').value;
            let block = document.getElementById('newBlock').value;

            if (!districtId || !block.trim()) {
                alert("Please select a district and enter a block name!");
                return;
            }

            fetch("{{ route('blocks.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        district_id: districtId,
                        block: block
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    alert("Failed to add block. Check console for details.");
                });
        }

        function addDistrict() {
            let districtName = document.getElementById('newDistrict').value.trim();

            if (!districtName) {
                alert("Please enter a district name!");
                return;
            }

            fetch("{{ route('districts.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        district: districtName
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("District added successfully!");
                        location.reload(); // Reload page to reflect changes
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    alert("Failed to add district. Check console for details.");
                });
        }
    </script>
@endsection
