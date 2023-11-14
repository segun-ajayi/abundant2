<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <meta name="description" content="Abundant Grace Multipurpose Cooperative Society">
        <meta name="author" content="Segun Ajayi">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Abundant Grace Multipurpose Cooperative Society</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('vendors/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendors/sweetalert/sweetalert.css') }}">
        @yield('css')
        @livewireStyles

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
        <header class="app-header navbar">
        <a class="navbar-brand" href="#">
            <img class="navbar-brand-full" src="{{ asset('img/brand/logo.png') }}" width="89" height="25" alt="CoreUI Logo">
        </a>
        <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class="nav navbar-nav d-md-down-none">
            <li class="nav-item px-3">
                <a class="nav-link" href="/home">Home</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link" href="/members">Members</a>
            </li>
            <li class="nav-item px-3">
                <a class="nav-link" href="{{ route('profile.show') }}">My Profile</a>
            </li>
        </ul>
        <ul class="nav navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link nav-link" data-toggle="dropdown" href="/" role="button" aria-haspopup="true" aria-expanded="false">
                    <img class="img-avatar" src="{{ asset(Auth::user()->profile_photo_url) }}" alt="{{ Auth::user()->member->name }}">
                </a>
            </li>
            <li>
                <p class="text-muted" style="position: relative; bottom: -10px"><b>{{ Auth::user()->member->name }}</b></p>
            </li>
            <li style="margin-left: 20px">
                <a href="{{ route('logout') }}" class="btn btn-outline-primary"
                   onclick="event.preventDefault();
                     document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out"> Log out</i>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        </ul>
        <button class="navbar-toggler aside-menu-toggler d-md-down-none" type="button" data-toggle="aside-menu-lg-show">
            <span class="navbar-toggler-icon"></span>
        </button>
        <button class="navbar-toggler aside-menu-toggler d-lg-none" type="button" data-toggle="aside-menu-show">
            <span class="navbar-toggler-icon"></span>
        </button>
    </header>
        <div class="app-body">
            <div class="sidebar">
                <nav class="sidebar-nav">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                <i class="nav-icon icon-speedometer"></i> Home
                            </a>
                        </li>
                        <li class="nav-title">Functions</li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('members') }}">
                                <i class="nav-icon icon-people"></i> Members</a>
                        </li>
                        <li class="nav-item nav-dropdown">
                            <a class="nav-link nav-dropdown-toggle" href="#">
                                <i class="nav-icon icon-calculator"></i> Attendance</a>
                            <ul class="nav-dropdown-items">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('markAttendance') }}">
                                        <i class="nav-icon icon-check"></i> Mark Attendance</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('post') }}">
                                <i class="nav-icon icon-paper-plane"></i> Post</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('post') }}">
                                <i class="nav-icon icon-handbag"></i> Loans</a>
                        </li>
                        <li class="nav-title">Reports</li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('analysis') }}">
                                <i class="nav-icon icon-graph"></i> Monthly Analysis</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dividendReport') }}">
                                <i class="nav-icon icon-list"></i> Dividend Report</a>
                        </li>
                        <li class="nav-item nav-dropdown">
                            <a class="nav-link nav-dropdown-toggle" href="#">
                                <i class="nav-icon icon-settings"></i> Settings</a>
                            <ul class="nav-dropdown-items">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('profile.show') }}">
                                        <i class="nav-icon icon-user"></i> My Profile</a>
                                </li>
                                @if(Auth::user()->role == 'admin')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('add_member') }}">
                                            <i class="nav-icon icon-user-follow"></i> Add Member</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('deleted_members') }}">
                                            <i class="nav-icon icon-trash"></i> Deleted Members</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('upload_member') }}">
                                            <i class="nav-icon icon-arrow-up-circle"></i> Upload Members</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        @if(Auth::user()->role == 'admin')
                            <li class="nav-title">Admin</li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('index') }}">
                                    <i class="nav-icon icon-people"></i> Excos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dividend') }}">
                                    <i class="nav-icon icon-calculator"></i> Dividend</a>
                            </li>
                        @endif
                    </ul>
                </nav>
                {{--        <button class="sidebar-minimizer brand-minimizer" type="button"></button>--}}
            </div>
            <main class="main">
                <!-- Breadcrumb-->
                @if(isset($pMonth))
                    @livewire('admin.pay-month')
                @endif
                <div class="container-fluid">
                    <div class="animated fadeIn">
                        <div class="row justify-content-center">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <footer class="app-footer">
            <div>
                <a href="#">Abundant Grace Co-Operative Society</a>
                <span>&copy; 2020 Abundant Grace.</span>
            </div>
            <div class="ml-auto">
                <span>Powered by</span>
                <a href="#">Abundant Grace</a>
            </div>
        </footer>
        @stack('modals')

        @livewireScripts
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/popper.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/pace.min.js') }}"></script>
        <script src="{{ asset('js/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ asset('js/coreui.min.js') }}"></script>
        <!-- Plugins and scripts required by this view-->
        <script src="{{ asset('js/toastr.js') }}"></script>
        <script src="{{ asset('vendors/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('vendors/sweetalert/sweetalert.min.js') }}"></script>
        @yield('script')
        <script>
            Livewire.on('toast', (type, message) => {
                if(type === 'suc') {
                    toastr.success(message);
                }
                if (type === 'warn'){
                    toastr.warning(message);
                }
                if (type === 'err'){
                    toastr.error(message);
                }
            });
        </script>
        @if(Session::has('suc'))
            <script>
                toastr.success("{{ Session::get('suc') }}");
            </script>
        @endif
        @if(Session::has('err'))
            <script>
                toastr.error("{{ Session::get('err') }}");
            </script>
        @endif
        @if(Session::has('inf'))
            <script>
                toastr.info("{{ Session::get('inf') }}");
            </script>
        @endif
        @if(Session::has('war'))
            <script>
                toastr.warning("{{ Session::get('war') }}");
            </script>
        @endif
        @if(Session::has('valerr'))
            {{--    {{ dd(Session::get('valerr')) }}--}}
            {{--    @foreach(Session::get('valerr') as $k => $v)--}}
            <script>
                toastr.error("{{ Session::get('valerr') }}");
            </script>
            {{--    @endforeach--}}

        @endif
    </body>
</html>
