@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Schools</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('schools.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add School
            </a>
            <button type="button" id="exportExcelButton" class="btn btn-outline-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>
    <div>
        <!-- Delete District Button -->
<button class="btn btn-danger" onclick="showDeleteDistrictModal()">Delete District</button>

<!-- Delete Block Button -->
<button class="btn btn-warning" onclick="showDeleteBlockModal()">Delete Block</button>

    </div>
<br>
    <!-- Advanced Search & Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-filter"></i> Search & Filter Schools
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-school"></i> Search School
                    </label>
                    <input type="text" id="searchInput" class="form-control" placeholder="School name or code">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-map-marker-alt"></i> District
                    </label>
                    <select id="districtFilter" class="form-select">
                        <option value="">All Districts</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->district }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-user"></i> Trainer
                    </label>
                    <input type="text" id="trainerFilter" class="form-control" placeholder="Trainer name">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-image"></i> Image Status
                    </label>
                    <select id="imageStatusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="null">Not Uploaded</option>
                        <option value="0">Uploaded</option>
                        <option value="1">Approved</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-video"></i> Video Status
                    </label>
                    <select id="videoStatusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="null">Not Uploaded</option>
                        <option value="0">Uploaded</option>
                        <option value="1">Approved</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-file-check"></i> UC Status
                    </label>
                    <select id="ucStatusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="null">Not Uploaded</option>
                        <option value="0">Uploaded</option>
                        <option value="1">Approved</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-layer-group"></i> Block
                    </label>
                    <input type="text" id="blockFilter" class="form-control" placeholder="Block name">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i> Clear All Filters
                    </button>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Filters work together - you can combine multiple filters for precise results
                </small>
            </div>
        </div>
    </div>

    <!-- Results Counter -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="badge bg-primary" id="resultsCount">{{ count($schools) }}</span>
            <span class="text-muted">school(s) found</span>
        </div>
    </div>

    <!-- Schools Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>School Name</th>
                    <th>School Code</th>
                    <th>Block</th>
                    {{-- <th>District</th> --}}
                    <th>Total Student</th>
                    <th>Image Status</th>
                    <th>Video Status</th>
                    <th>UC Status</th>
                    <th>Trainer Name</th>
                    <th>Action</th>
                </tr>
            </thead>
        <tbody id="schoolTable">
            @foreach($schools as $school)
            <tr data-id="{{ $school->id }}"
                data-district-id="{{ $school->district_id }}"
                data-school-name="{{ strtolower($school->school_name) }}"
                data-school-code="{{ strtolower($school->school_code) }}"
                data-block="{{ strtolower($school->block) }}"
                data-trainer-name="{{ $school->trainer_name ? strtolower($school->trainer_name) : '' }}"
                data-image-status="{{ $school->image_status_value === null ? 'null' : $school->image_status_value }}"
                data-video-status="{{ $school->video_status_value === null ? 'null' : $school->video_status_value }}"
                data-uc-status="{{ $school->uc_status_value === null ? 'null' : $school->uc_status_value }}">
                <td contenteditable="true" name="school_name" class="school_name">{{ $school->school_name }}</td>
                <td contenteditable="true" name="school_code" class="school_code">{{ $school->school_code }}</td>
                <td contenteditable="true" name="block" class="block">{{ $school->block }}</td>
                {{-- <td contenteditable="true" name="block" class="block">{{ $school->district }}</td> --}}
                <td contenteditable="true" name="total_students" class="total_students">{{ $school->total_students }}</td>

                {{-- Image Status --}}
                <td class="text-center">
                    @if($school->image_status_value === null)
                        <span class="badge bg-secondary">Not Uploaded</span>
                    @elseif($school->image_status_value == 0)
                        <span class="badge bg-warning text-dark">Uploaded</span>
                    @elseif($school->image_status_value == 1)
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-secondary">Not Uploaded</span>
                    @endif
                </td>

                {{-- Video Status --}}
                <td class="text-center">
                    @if($school->video_status_value === null)
                        <span class="badge bg-secondary">Not Uploaded</span>
                    @elseif($school->video_status_value == 0)
                        <span class="badge bg-warning text-dark">Uploaded</span>
                    @elseif($school->video_status_value == 1)
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-secondary">Not Uploaded</span>
                    @endif
                </td>

                {{-- UC Status --}}
                <td class="text-center">
                    @if($school->uc_status_value === null)
                        <span class="badge bg-secondary">Not Uploaded</span>
                    @elseif($school->uc_status_value == 0)
                        <span class="badge bg-warning text-dark">Uploaded</span>
                    @elseif($school->uc_status_value == 1)
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-secondary">Not Uploaded</span>
                    @endif
                </td>

                {{-- Trainer Name --}}
                <td>
                    @if($school->trainer_name)
                        <span class="badge bg-info text-dark">
                            <i class="fas fa-user"></i> {{ $school->trainer_name }}
                        </span>
                    @else
                        <span class="text-muted small">Not Assigned</span>
                    @endif
                </td>

                <td>
                    {{-- <button class="btn btn-success btn-sm saveBtn">Save</button> --}}
                    <button class="btn btn-success btn-sm saveBtn"><a href="/update/{{$school['id']}}"> Update</a></button>
                    <button class="btn btn-danger btn-sm deleteBtn">Delete</button>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>



<div class="modal fade" id="deleteDistrictModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select District to Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="districtSelect" class="form-control">
                    <option value="">Select District</option>
                    @foreach(App\Models\District::all() as $district)
                        <option value="{{ $district->id }}">{{ $district->district }}</option>
                    @endforeach
                </select>
                <button class="btn btn-danger mt-2" onclick="deleteDistrict()">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Block Modal -->
<div class="modal fade" id="deleteBlockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select District & Block to Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="districtSelectForBlock" class="form-control" onchange="fetchBlocks()">
                    <option value="">Select District</option>
                    @foreach(App\Models\District::all() as $district)
                        <option value="{{ $district->id }}">{{ $district->district }}</option>
                    @endforeach
                </select>

                <select id="blockSelect" class="form-control mt-2">
                    <option value="">Select Block</option>
                </select>

                <button class="btn btn-warning mt-2" onclick="deleteBlock()">Delete</button>
            </div>
        </div>
    </div>
</div>




<script>
// Comprehensive Filter Function
function applyFilters() {
    const searchText = document.getElementById("searchInput").value.toLowerCase();
    const districtId = document.getElementById("districtFilter").value;
    const trainerText = document.getElementById("trainerFilter").value.toLowerCase();
    const blockText = document.getElementById("blockFilter").value.toLowerCase();
    const imageStatus = document.getElementById("imageStatusFilter").value;
    const videoStatus = document.getElementById("videoStatusFilter").value;
    const ucStatus = document.getElementById("ucStatusFilter").value;

    const rows = document.querySelectorAll("#schoolTable tr");
    let visibleCount = 0;

    rows.forEach(row => {
        const schoolName = row.getAttribute("data-school-name") || "";
        const schoolCode = row.getAttribute("data-school-code") || "";
        const rowDistrictId = row.getAttribute("data-district-id") || "";
        const trainerName = row.getAttribute("data-trainer-name") || "";
        const block = row.getAttribute("data-block") || "";
        const rowImageStatus = row.getAttribute("data-image-status") || "";
        const rowVideoStatus = row.getAttribute("data-video-status") || "";
        const rowUcStatus = row.getAttribute("data-uc-status") || "";

        let show = true;

        // Search filter (school name or code)
        if (searchText && !schoolName.includes(searchText) && !schoolCode.includes(searchText)) {
            show = false;
        }

        // District filter
        if (districtId && rowDistrictId !== districtId) {
            show = false;
        }

        // Trainer filter
        if (trainerText && !trainerName.includes(trainerText)) {
            show = false;
        }

        // Block filter
        if (blockText && !block.includes(blockText)) {
            show = false;
        }

        // Image status filter
        if (imageStatus !== "" && rowImageStatus !== imageStatus) {
            show = false;
        }

        // Video status filter
        if (videoStatus !== "" && rowVideoStatus !== videoStatus) {
            show = false;
        }

        // UC status filter
        if (ucStatus !== "" && rowUcStatus !== ucStatus) {
            show = false;
        }

        if (show) {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    });

    // Update results counter
    const resultsCount = document.getElementById("resultsCount");
    if (resultsCount) {
        resultsCount.textContent = visibleCount;
    }

    // Show/hide "no results" message
    let noResultsMsg = document.getElementById("noResultsMessage");
    if (visibleCount === 0 && rows.length > 0) {
        if (!noResultsMsg) {
            noResultsMsg = document.createElement("tr");
            noResultsMsg.id = "noResultsMessage";
            noResultsMsg.innerHTML = `
                <td colspan="9" class="text-center py-4 text-muted">
                    <i class="fas fa-search"></i> No schools found matching the selected filters.
                </td>
            `;
            document.getElementById("schoolTable").appendChild(noResultsMsg);
        }
        noResultsMsg.style.display = "";
    } else if (noResultsMsg) {
        noResultsMsg.style.display = "none";
    }
}

// Clear all filters
document.getElementById("clearFilters").addEventListener("click", function() {
    document.getElementById("searchInput").value = "";
    document.getElementById("districtFilter").value = "";
    document.getElementById("trainerFilter").value = "";
    document.getElementById("blockFilter").value = "";
    document.getElementById("imageStatusFilter").value = "";
    document.getElementById("videoStatusFilter").value = "";
    document.getElementById("ucStatusFilter").value = "";
    applyFilters();
});

// Add event listeners to all filter inputs
document.getElementById("searchInput").addEventListener("keyup", applyFilters);
document.getElementById("districtFilter").addEventListener("change", applyFilters);
document.getElementById("trainerFilter").addEventListener("keyup", applyFilters);
document.getElementById("blockFilter").addEventListener("keyup", applyFilters);
document.getElementById("imageStatusFilter").addEventListener("change", applyFilters);
document.getElementById("videoStatusFilter").addEventListener("change", applyFilters);
document.getElementById("ucStatusFilter").addEventListener("change", applyFilters);




// Export filtered schools to Excel
const exportButton = document.getElementById("exportExcelButton");
if (exportButton) {
    exportButton.addEventListener("click", function () {
        const params = new URLSearchParams();

        const searchValue = document.getElementById("searchInput").value.trim();
        const districtValue = document.getElementById("districtFilter").value;
        const trainerValue = document.getElementById("trainerFilter").value.trim();
        const blockValue = document.getElementById("blockFilter").value.trim();
        const imageValue = document.getElementById("imageStatusFilter").value;
        const videoValue = document.getElementById("videoStatusFilter").value;
        const ucValue = document.getElementById("ucStatusFilter").value;

        if (searchValue) params.append("search", searchValue);
        if (districtValue) params.append("district_id", districtValue);
        if (trainerValue) params.append("trainer", trainerValue);
        if (blockValue) params.append("block", blockValue);
        if (imageValue !== "") params.append("image_status", imageValue);
        if (videoValue !== "") params.append("video_status", videoValue);
        if (ucValue !== "") params.append("uc_status", ucValue);

        const exportUrl = "{{ route('schools.export') }}";
        const query = params.toString();
        window.location.href = query ? `${exportUrl}?${query}` : exportUrl;
    });
}

document.addEventListener("click", function(event) {
    if (event.target.classList.contains("deleteBtn")) {
        let row = event.target.closest("tr");
        let schoolId = row.getAttribute("data-id");

        if (confirm("Are you sure you want to delete this school?")) {
            fetch("{{ route('admin.schools.delete') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ id: schoolId })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                row.remove();
            });
        }
    }
});




function showDeleteDistrictModal() {
        $('#deleteDistrictModal').modal('show');
    }

    // Show Delete Block Modal
    function showDeleteBlockModal() {
        $('#deleteBlockModal').modal('show');
    }

    // Fetch Blocks when District is selected
    function fetchBlocks() {
        let district_id = document.getElementById('districtSelectForBlock').value;
        if (district_id) {
            fetch(`/admin/blocks/${district_id}`)
                .then(response => response.json())
                .then(data => {
                    let blockSelect = document.getElementById('blockSelect');
                    blockSelect.innerHTML = '<option value="">Select Block</option>';
                    data.forEach(block => {
                        blockSelect.innerHTML += `<option value="${block.id}">${block.block}</option>`;
                    });
                });
        }
    }

    // Delete District
    function deleteDistrict() {
        let district_id = document.getElementById('districtSelect').value;
        if (!district_id) {
            alert('Please select a district!');
            return;
        }

        fetch(`/admin/delete-district/${district_id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }})
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
    }

    // Delete Block
    function deleteBlock() {
        let district_id = document.getElementById('districtSelectForBlock').value;
        let block_id = document.getElementById('blockSelect').value;

        if (!district_id || !block_id) {
            alert('Please select both district and block!');
            return;
        }

        fetch(`/admin/delete-block/${block_id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }})
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
    }

    $(document).ready(function () {
        $('#districtSelect').change(function () {
            let district_id = $(this).val();

            if (district_id) {
                $.ajax({
                    url: "{{ route('admin.getBlocks', '') }}/" + district_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#blockSelect').empty().append('<option value="">Select Block</option>');
                        $.each(data, function (key, value) {
                            $('#blockSelect').append('<option value="' + value.id + '">' + value.block + '</option>');
                        });
                    },
                    error: function () {
                        alert('Error fetching blocks.');
                    }
                });
            } else {
                $('#blockSelect').empty().append('<option value="">Select District First</option>');
            }
        });
    });





</script>

@endsection
