@extends('layouts.app')

@section('content')
<div class="container mt-3 mb-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="heading mb-0">My ID Card</h2>
                <a href="{{ $downloadUrl }}" class="btn btn-primary">Download ID Card</a>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="text-center p-3" style="background:#f5f7f8;border-radius:8px;">
                <img src="{{ $idCardUrl }}"
                     alt="ID Card - {{ $user->instructor_code }}"
                     class="img-fluid"
                     style="max-width:360px;width:100%;border:1px solid #d0d7de;border-radius:6px;background:#fff;">
                <p class="mt-3 mb-0 text-muted">
                    {{ $user->instructor_name }} ({{ $user->instructor_code }})
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
