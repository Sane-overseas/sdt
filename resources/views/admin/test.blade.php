@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Manage Schools</h2>
    <div>
        <!-- Delete District Button -->
<button class="btn btn-danger" onclick="showDeleteDistrictModal()">Delete District</button>

<!-- Delete Block Button -->
<button class="btn btn-warning" onclick="showDeleteBlockModal()">Delete Block</button>

    </div>

    <!-- Search & Filter -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by School Name">
        </div>
        <div class="col-md-6">
            <select id="districtFilter" class="form-select">
                <option value="">All Districts</option>
                @foreach($districts as $district)
                    <option value="{{ $district->id }}">{{ $district->district }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Schools Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>School Name</th>
                <th>School Code</th>
                <th>Block</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="schoolTable">
            @foreach($schools as $school)
            <tr data-id="{{ $school->id }}">
                <td contenteditable="true" class="editable school_name">{{ $school->school_name }}</td>
                <td contenteditable="true" class="editable school_code">{{ $school->school_code }}</td>
                <td contenteditable="true" class="editable block">{{ $school->block }}</td>
                {{-- <td>
                    <select class="form-control block-select">
                        @foreach($blocks as $block)
                            <option value="{{ $block->block }}" {{ $school->block == $block->block ? 'selected' : '' }}>
                                {{ $block->block }}
                            </option>
                        @endforeach
                    </select>
                </td> --}}
                <td>
                    <button class="btn btn-success btn-sm saveBtn">Save</button>
                    <button class="btn btn-danger btn-sm deleteBtn">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>




<!-- Delete District Modal -->
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
// Search Functionality
document.getElementById("searchInput").addEventListener("keyup", function() {
    let searchText = this.value.toLowerCase();
    document.querySelectorAll("#schoolTable tr").forEach(row => {
        let schoolName = row.querySelector(".school_name").textContent.toLowerCase();
        row.style.display = schoolName.includes(searchText) ? "" : "none";
    });
});

// Filter Schools by District
document.getElementById("districtFilter").addEventListener("change", function() {
    let districtId = this.value;

    fetch("/admin/schools/filter/" + districtId)
    .then(response => response.json())
    .then(data => {
        let tableBody = document.getElementById("schoolTable");
        tableBody.innerHTML = "";
        data.forEach(school => {
            tableBody.innerHTML += `
                <tr data-id="${school.id}">
                    <td contenteditable="true" class="editable school_name">${school.school_name}</td>
                    <td contenteditable="true" class="editable school_code">${school.school_code}</td>
                    <td contenteditable="true" class="editable block">${school.block}</td>
                    <td>
                        <button class="btn btn-success btn-sm saveBtn">Save</button>
                        <button class="btn btn-danger btn-sm deleteBtn">Delete</button>
                    </td>
                </tr>`;
        });
    });
});

// Save Edited Data
document.addEventListener("click", function(event) {
    if (event.target.classList.contains("saveBtn")) {
        let row = event.target.closest("tr");
        let schoolId = row.getAttribute("data-id");
        let schoolName = row.querySelector(".school_name").textContent;
        let schoolCode = row.querySelector(".school_code").textContent;
        let block = row.querySelector(".block").textContent;

        fetch("{{ route('admin.schools.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ id: schoolId, school_name: schoolName, school_code: schoolCode, block: block })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        });
    }
});

// Delete School
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

// function confirmDeleteDistrict() {
//         let districtId = prompt("Enter District ID to delete:");
//         if (districtId && confirm("Are you sure? This will delete all related blocks and schools!")) {
//             fetch(`/admin/delete-district/${districtId}`, {
//                 method: "DELETE",
//                 headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
//             }).then(response => location.reload());
//         }
//     }

//     function confirmDeleteBlock() {
//         let blockId = prompt("Enter Block ID to delete:");
//         if (blockId && confirm("Are you sure? This will delete all related schools!")) {
//             fetch(`/admin/delete-block/${blockId}`, {
//                 method: "DELETE",
//                 headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
//             }).then(response => location.reload());
//         }
//     }

    // Show Delete District Modal
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



    $(document).ready(function() {
        $(".editable").on("input", function() {
            $(this).addClass("edited");
        });

        $(".blockSelect").each(function() {
            let schoolRow = $(this).closest("tr");
            let district_id = schoolRow.find(".districtSelect").val();
            let blockDropdown = $(this);

            if (district_id) {
                loadBlocks(district_id, blockDropdown);
            }
        });

    //     Function to load blocks when district changes
    //     function loadBlocks(district_id, blockDropdown) {
    //         $.ajax({
    //             url: "/admin/blocks/" + district_id,
    //             type: "GET",
    //             success: function(response) {
    //                 blockDropdown.empty();
    //                 $.each(response, function(index, block) {
    //                     blockDropdown.append(`<option value="${block.block}">${block.block}</option>`);
    //                 });
    //             }
    //         });
    //     }

    //     // Save Updated Data
    //     $(".saveBtn").click(function() {
    //         let row = $(this).closest("tr");
    //         let school_id = row.data("id");
    //         let school_name = row.find(".school_name").text();
    //         let school_code = row.find(".school_code").text();
    //         let block = row.find(".blockSelect").val();

    //         $.ajax({
    //             url: "/admin/update-school",
    //             type: "POST",
    //             data: {
    //                 _token: "{{ csrf_token() }}",
    //                 id: school_id,
    //                 school_name: school_name,
    //                 school_code: school_code,
    //                 block: block
    //             },
    //             success: function(response) {
    //                 alert(response.message);
    //             }
    //         });
    //     });
    // });
</script>


@endsection
  // Save Edited Data
// document.addEventListener("DOMContentLoaded", function () {
//     function attachSaveEvent() {
//         document.querySelectorAll(".saveBtn").forEach(button => {
//             button.onclick = function () {
//                 let row = this.closest("tr");
//                 let schoolId = row.getAttribute("data-id");
//                 let schoolName = row.querySelector(".school_name").textContent.trim();
//                 let schoolCode = row.querySelector(".school_code").textContent.trim();
//                 let block = row.querySelector(".block").textContent.trim();

//                 fetch("{{ route('admin.schools.update') }}", {
//                     method: "POST",
//                     headers: {
//                         "Content-Type": "application/json",
//                         "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
//                     },
//                     body: JSON.stringify({
//                         id: schoolId,
//                         school_name: schoolName,
//                         school_code: schoolCode,
//                         block: block
//                     })
//                 })
//                 .then(response => response.json())
//                 .then(data => {
//                     if (data.success) {
//                         alert("School updated successfully!");
//                         location.reload(); // Reload to see changes
//                     } else {
//                         alert("Error: " + data.error);
//                     }
//                 })
//                 .catch(error => console.error("Fetch Error:", error));
//             };
//         });
//     }

//     // Attach event on page load
//     attachSaveEvent();

//     // Re-attach event after filtering
//     document.querySelector("#filterDistrict").addEventListener("change", function () {
//         let districtId = this.value;

//         fetch(`/admin/schools/filter/${districtId}`)
//             .then(response => response.text())
//             .then(html => {
//                 document.querySelector("#schoolTableBody").innerHTML = html;
//                 attachSaveEvent(); // Re-bind save buttons after filtering
//             });
//     });
// });

// Delete School



updateschool.blade.php

@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3>Update Schools</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/update-school">
                    @csrf
                    {{-- <div class="mb-3">
                        <label class="form-label">Select District</label>
                        <select class="form-control" name="district_id" id="districtSelect" required>
                            <option value="">Select District</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Block</label>
                        <select class="form-control" name="block" id="blockSelect" required>
                            <option value="">Select Block</option>
                        </select>
                    </div> --}}
                    <div class="mb-3">
                        <label class="form-label">Select Block</label>
                        <input type="text" class="form-control" name="block" value="{{ $block }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">School Name</label>
                        <input type="text" class="form-control" name="school_name" value="{{ $school_name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">School Code</label>
                        <input type="text" class="form-control" name="school_code" value="{{ $school_code }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Student</label>
                        <input type="text" class="form-control" name="total_students" value="{{ $total_students }}" required>
                    </div>

                    <input type="hidden" name="id" value="{{ $id }}">
                    <button type="submit" class="btn btn-success">Update School</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif
        });
    </script>
@endsection



// import the schools in bulk
    // public function import(Request $request)
    // {

    //     $request->validate([
    //         'file' => 'required|mimes:csv,xlsx'
    //     ]);

    //     Excel::import(new SchoolImport, $request->file('file'));

    //     return back()->with('success', 'Schools imported successfully.');
    // }



    //     public function import(Request $request)
    // {
    //     $request->validate([
    //         'district_id' => 'required|exists:districts,id',
    //         'file' => 'required|file|mimes:xlsx,csv'
    //     ]);

    //     try {
    //         // Pass the selected district_id to the import class
    //         Excel::import(new SchoolImport($request->district_id), $request->file('file'));

    //         return back()->with('success', 'Schools imported successfully into the selected district!');
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Error importing schools: ' . $e->getMessage());
    //     }
    // }
