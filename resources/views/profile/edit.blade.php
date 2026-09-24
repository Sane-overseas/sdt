@extends('layouts.app')
  
@section('content')

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            @if(in_array((int) Auth::user()->role, [0, 2], true))
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 mb-2">
                                <i class="bi bi-bank2 mr-1 text-primary"></i> {{ __('Bank & KYC Details') }}
                            </h2>
                            <p class="text-sm text-muted mb-4">
                                {{ __('Registered bank account and KYC documents for payment settlement.') }}
                            </p>
                        </header>

                        @php
                            $user = Auth::user();
                            $hasBank = !empty($user->account_number) && !empty($user->ifsc_code);
                        @endphp

                        @if($hasBank)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <th style="width: 40%; background: #f8f9fa;">Bank Name</th>
                                            <td><strong>{{ $user->bank_name ?? '—' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th style="background: #f8f9fa;">Account Holder</th>
                                            <td><strong>{{ $user->account_holder_name ?? ($user->instructor_name ?? '—') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th style="background: #f8f9fa;">Account Number</th>
                                            <td><strong>{{ $user->account_number ?? '—' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th style="background: #f8f9fa;">IFSC Code</th>
                                            <td><strong>{{ $user->ifsc_code ?? '—' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th style="background: #f8f9fa;">PAN Number</th>
                                            <td><strong>{{ $user->pan_number ?? '—' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th style="background: #f8f9fa;">Uploaded Documents</th>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @if(!empty($user->pan_doc))
                                                        <a href="{{ media_url('trainer_data', basename($user->pan_doc)) ?: url('/m/r/'.basename($user->pan_doc)) }}" target="_blank" class="badge badge-info mr-2" style="padding: 6px 10px; font-size: 11px;">
                                                            <i class="bi bi-file-earmark-image"></i> View PAN Card
                                                        </a>
                                                    @endif
                                                    @if(!empty($user->passbook_doc))
                                                        <a href="{{ media_url('trainer_data', basename($user->passbook_doc)) ?: url('/m/r/'.basename($user->passbook_doc)) }}" target="_blank" class="badge badge-info" style="padding: 6px 10px; font-size: 11px;">
                                                            <i class="bi bi-file-earmark-image"></i> View Passbook / Cheque
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning py-2 mb-0" style="font-size: 13px;">
                                <i class="bi bi-info-circle-fill mr-1"></i> No bank details uploaded yet.
                            </div>
                        @endif
                    </section>
                </div>
            </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

           <!--  <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div> -->
        </div>
    </div>
@endsection
