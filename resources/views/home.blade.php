<!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Sistema de Trámite Documentario">
        <meta name="keyword" content="Sistema de Trámite Documentario">
        <link rel="shortcut icon" href="img/favicon.png">
        <!-- Id for Channel Notification -->
        <meta name="userId" content="{{ auth()->check() ? auth()->user()->id : ''}}">
        <title>Sistema de Trámite Documentario</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{asset('css/templates.css')}}" rel="stylesheet">
    </head>

    <body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
        <div id="app">
            <header class="app-header navbar">
                <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
                  <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="#"></a>
                <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
                  <span class="navbar-toggler-icon"></span>
                </button>
                <ul class="nav navbar-nav ml-auto">
                    @if(\App\ChargeAssignment::charge()==2 || \App\ChargeAssignment::charge()==3)
                        <notification :ruta="ruta"></notification>
                    @endif
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <!--<img src="img/avatars/6.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">-->
                            <span class="d-md-down-none">{{ auth()->user()->person->firstName }} </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-header text-center">
                                <strong>Cuenta</strong>
                            </div>
                            <a
                                class="dropdown-item"
                                href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();"
                            >
                                <i class="fa fa-lock"></i> Cerrar sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </li>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                </ul>
            </header>
            <div class="app-body">
                @if(auth()->check())
                    @if(\App\ChargeAssignment::charge()==1)
                        @include('templates.super')
                    @elseif(\App\ChargeAssignment::charge()==2)
                        @include('templates.admin')
                    @elseif(\App\ChargeAssignment::charge()==3)
                        @include('templates.internal')
                    @elseif(\App\ChargeAssignment::charge()==4)
                        @include('templates.external')
                    @endif
                @endif
                @yield('content')
            </div>
        </div>
        {{--<footer class="app-footer">
            <span><a href="">.....</a> &copy; 2019</span>
            <span class="ml-auto">Desarrollado por <a href="">.....</a></span>
        </footer>--}}
        <script src="{{asset('js/app.js')}}"></script>
        <script src="{{asset('js/templates.js')}}"></script>
    </body>
</html>
