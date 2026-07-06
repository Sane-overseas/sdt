<?php
$id ="";
$block ="";
$school_name ="";
$school_code ="";
$total_students ="";
?>

@foreach($school as $data)
<?php
$id =$data['id'];
$block =$data['block'];
$school_name =$data['school_name'];
$school_code =$data['school_code'];
$total_students =$data['total_students'];
?>
@endforeach



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
