<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>SOPL - @yield('title')</title>
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="{{  asset('/images/logo.jpg') }}"/>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" >
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')
            @if(Auth::user() == null)
               <a id="clicked" href="{{ route('login') }}"></a>
            @elseif(Auth::user()->role == 1)
            <div id="app">
                <nav class="navbar navbar-expand-md navbar-light menu-bar shadow-sm">
                    <div class="container">
                        <div class="" id="navbarSupportedContent">
                            <!-- Right Side Of Navbar -->
                            <ul class="navbar-nav ml-auto snip1198">
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::path() == 'dashboard' ? 'active' : '' }}" id="autoload" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::path() == 'logs' ? 'active' : '' }}" href="{{ route('logs') }}">Logs</a>
                                </li>
                                <div class="dropdown">
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::path() == 'uploaded-data' ? 'active' : '' }}" href="{{ route('uploaded-data') }}">Uploaded Data</a>
                                        <div class="dropdown-content">
                                            <a href="{{ route('rejected-uc') }}">Rejected UC</a>
                                            <a href="{{ route('approval-pending-uc') }}">Approval Pending UC</a>
                                            <a href="{{ route('emergency-approved-uc') }}">Emergency Approved UC</a>
                                        </div>
                                    </li>
                                </div>
                                <div class="dropdown">
                                    <li class="nav-item">
                                    <a class="nav-link {{ Request::path() == 'schools-reporting' ? 'active' : '' }}" href="{{ route('schools-reporting') }}">Schools Reporting</a>
                                        <div class="dropdown-content">
                                            <a href="{{ route('paid-schools') }}">Paid Schools</a>
                                            <a href="{{ route('unpaid-schools') }}">Unpaid Schools</a>
                                            <a href="{{ route('today-assigned') }}">Assigned Schools</a>
                                            <a href="{{ route('route-plan-schools') }}">Route Plane Schools</a>
                                        </div>
                                    </li>
                                </div>
                                <div class="dropdown">
                                    <li class="nav-item">
                                        <a class="nav-link dropbtn {{ Request::path() == 'add_trainers' ? 'active' : '' }}" href="{{ route('add_trainers') }}">Trainers Reporting</a>
                                        <div class="dropdown-content">
                                            <a href="{{ route('ongoing-schools') }}">OnGoing Trainers</a>
                                            <a href="{{ route('not-workig-trainers') }}">Not Working Trainers</a>
                                            <a href="{{ route('trainers-schools-data') }}">Trainers Schools Data</a>
                                            <a href="{{ route('claim-trainers') }}">Claim Traniers</a>
                                             <a href="{{ route('advance-payment') }}">Advance Payment</a>
                                        </div>
                                    </li>
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::path() == 'cordinators' ? 'active' : '' }}" href="{{ route('cordinators') }}">Cordinators</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ Request::path() == 'settings' ? 'active' : '' }}" href="{{ route('settings') }}">Settings</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::path() == 'schools/add' ? 'active' : '' }}" href="{{ route('schools.create') }}">Add Schools</a>
                                </li>
                                <li class="nav-item">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                       <button class="logout-btn" type="submit">Log Out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                @if((isset($currentState) && $currentState) || (isset($currentAcademicSession) && $currentAcademicSession && Auth::user()->role == 1))
                <div class="container-fluid py-1" style="background:#1a3a5c;color:#fff;font-size:14px;">
                    <div class="container d-flex justify-content-between align-items-center flex-wrap">
                        <span class="d-flex flex-wrap align-items-center">
                            @if(isset($currentState) && $currentState)
                            <span class="mr-3 mb-1">
                                State: <strong>{{ $currentState->name }}</strong>
                                <span class="badge badge-light ml-1">{{ $currentState->code }}</span>
                            </span>
                            @endif
                            @if(isset($currentAcademicSession) && $currentAcademicSession && Auth::user()->role == 1)
                            <span class="mb-1">
                                Session: <strong>{{ $currentAcademicSession->name }}</strong>
                                @if(isset($activeAcademicSession) && $currentAcademicSession->id === $activeAcademicSession->id)
                                    <span class="badge badge-success ml-1">Active</span>
                                @else
                                    <span class="badge badge-warning ml-1">Archive</span>
                                    <span class="badge badge-secondary ml-1">Read only</span>
                                @endif
                            </span>
                            @endif
                        </span>
                        <span class="d-flex flex-wrap align-items-center">
                            @if(isset($allStates) && $allStates->count() && Auth::user()->role == 1)
                            <form action="{{ route('states.switch') }}" method="POST" class="form-inline m-0 mr-2 mb-1">
                                @csrf
                                <select name="state_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                    @foreach($allStates as $st)
                                        <option value="{{ $st->id }}" {{ isset($currentState) && $currentState && $currentState->id == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @endif
                            @if(isset($allAcademicSessions) && $allAcademicSessions->count() && Auth::user()->role == 1)
                            <form action="{{ route('academic-sessions.switch') }}" method="POST" class="form-inline m-0 mb-1">
                                @csrf
                                <select name="session_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                    @foreach($allAcademicSessions as $s)
                                        <option value="{{ $s->id }}" {{ isset($currentAcademicSession) && $currentAcademicSession && $currentAcademicSession->id == $s->id ? 'selected' : '' }}>{{ $s->name }}{{ $s->is_active ? ' (Active)' : '' }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @endif
                        </span>
                    </div>
                </div>
                @endif
                <main class="py-4">
                    @yield('content')
                </main>
            </div>
            @elseif(Auth::user()->role == 2)
            <div class="header-div row">
                <h2 class="header-text col-7">
                   Daily Attendance System
                </h2>
                <div class="col-5">
                    <div class="dropdown trainer-pl">
                      <button class=" dropdown-toggle auth-btn" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-expanded="false">{{ Auth::user()->instructor_name }}  </button>
                      <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        @if(Auth::user()->role == 0)
                        <li> <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link></li>
                        @endif
                        <li><form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form></li>
                      </ul>
                    </div>
                </div>
            </div>
            <div class="min-h-screen bg-gray-100">
            <!-- Page Content -->
                <nav class="navbar navbar-expand-md navbar-light trainer-menu-bar shadow-sm">
                    <div class="container">
                        <div class="" >
                            <!-- Right Side Of Navbar -->
                            <ul class="snip1198">
                                <li class="nav-item">
                                    <a class="nav-link trainer-nav-link {{ Request::path() == 't-dashboard' ? 'active' : '' }}" href="{{ route('t-dashboard') }}">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link trainer-nav-link {{ Request::path() == 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">Upload</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link trainer-nav-link {{ Request::path() == 'trainer-reporting' ? 'active' : '' }}" href="{{ route('trainer-reporting') }}">Cordinator Panel</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                @if(isset($currentState) && $currentState)
                <div class="container-fluid py-1" style="background:#004857;color:#fff;font-size:14px;">
                    <div class="container">
                        State: <strong>{{ $currentState->name }}</strong>
                        @if(isset($activeAcademicSession) && $activeAcademicSession)
                        | Active Session: <strong>{{ $activeAcademicSession->name }}</strong>
                        @endif
                    </div>
                </div>
                @elseif(isset($activeAcademicSession) && $activeAcademicSession)
                <div class="container-fluid py-1" style="background:#004857;color:#fff;font-size:14px;">
                    <div class="container">
                        Active Session: <strong>{{ $activeAcademicSession->name }}</strong>
                    </div>
                </div>
                @endif
                <main>
                     @yield('content')
                </main>
            </div>
            @else
                @if(Auth::user()->active_status == 1)
                <div class="header-div row">
                    <h2 class="header-text col-7">
                       Daily Attendance System
                    </h2>
                    <div class="col-5">
                        <div class="dropdown trainer-pl">
                          <button class=" dropdown-toggle auth-btn" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-expanded="false">{{ Auth::user()->instructor_name }}  </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            @if(Auth::user()->role == 0)
                            <li> <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link></li>
                            @endif
                            <li><form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form></li>
                          </ul>
                        </div>
                    </div>
                </div>
                <div class="min-h-screen bg-gray-100">
                <!-- Page Content -->
                    <nav class="navbar navbar-expand-md navbar-light trainer-menu-bar shadow-sm">
                        <div class="container">
                            <div class="" >
                                <!-- Right Side Of Navbar -->
                                <ul class="snip1198">
                                    <li class="nav-item">
                                        <a class="nav-link trainer-nav-link {{ Request::path() == 't-dashboard' ? 'active' : '' }}" href="{{ route('t-dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link trainer-nav-link {{ Request::path() == 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">Upload</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                    @if(isset($currentState) && $currentState)
                    <div class="container-fluid py-1" style="background:#004857;color:#fff;font-size:14px;">
                        <div class="container">
                            State: <strong>{{ $currentState->name }}</strong>
                            @if(isset($activeAcademicSession) && $activeAcademicSession)
                            | Session: <strong>{{ $activeAcademicSession->name }}</strong>
                            @endif
                        </div>
                    </div>
                    @elseif(isset($activeAcademicSession) && $activeAcademicSession)
                    <div class="container-fluid py-1" style="background:#004857;color:#fff;font-size:14px;">
                        <div class="container">
                            Session: <strong>{{ $activeAcademicSession->name }}</strong>
                        </div>
                    </div>
                    @endif
                    <main>
                         @yield('content')
                    </main>
                </div>
                @else
                  @include('deactive')
                @endif
            @endif
        </div>
    </body>
</html>
<script type="text/javascript">
    document.getElementById("clicked").click();
</script>
