<x-app-layout>
    <x-slot name="header">
        <h2 class="header-text">
            {{ __('Daily Attendance System') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(Auth::user()->role == 0)
                        @include('welcome') 
                    @else
                        @include('admin.admin-dashboard') 
                    @endif    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
