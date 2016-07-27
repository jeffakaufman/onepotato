<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'One Potato')</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="/admin/bootstrap/css/bootstrap.min.css">

    <!-- CSS -->
    {{--<link href="/css/sweetalert.css" rel="stylesheet">--}}
    {{--<link href="/css/fontello.css" rel="stylesheet">--}}
    {{--<link href="/css/fontello-embedded.css" rel="stylesheet">--}}
    {{--<link href="/css/app.css" rel="stylesheet">--}}
	{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>--}}
	<script src="/admin/plugins/jQuery/jquery-2.2.3.min.js"></script>
	<script src="/admin/plugins/jQueryUI/jquery-ui.min.js"></script>
	{{--<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">--}}
	{{--<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>--}}


    <script src="/admin/bootstrap/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="/admin/plugins/fastclick/fastclick.js"></script>
    <link href="/admin/css/AdminLTE.min.css" rel="stylesheet">
    <link href="/admin/css/skins/skin-red-light.min.css" rel="stylesheet">

    <!-- Scripts -->
    @yield('scripts', '')

    <!-- Global Spark Object -->
    <script>
        window.Spark = <?php echo json_encode(array_merge(
            Spark::scriptVariables(), []
        )); ?>
    </script>
</head>
<body class="hold-transition skin-red-light sidebar-mini">
    <div id="wrapper">
        <!-- Navigation -->
        {{--@if (Auth::check())--}}
            {{--@include('spark::nav.user')--}}
        {{--@else--}}
{{--            @if (Request::is('register*'))--}}
{{--                @include('spark::nav.register')--}}
            {{--@else--}}
                {{--@include('spark::nav.guest')--}}
            {{--@endif--}}
        {{--@endif--}}

        <header class="main-header">
            <!-- Logo -->
            <a href="/admin/dashboard" class="logo" style="background-color: white;padding: 0;">
                <img src="/img/mono-logo.png" style="width: 230px;"/>
                <!-- mini logo for sidebar mini 50x50 pixels -->
                {{--<span class="logo-mini"><b>A</b>LT</span>--}}
                <!-- logo for regular state and mobile devices -->
                {{--<span class="logo-lg"><b>Admin</b>LTE</span>--}}
            </a>

            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                @if(Auth::check())
                                    <img src="{{{ Auth::user()->photo_url }}}" class="user-image" alt="User Image">
                                @endif
                                <span class="hidden-xs">
                                    @if(Auth::check())
                                        {{{ Auth::user()->name }}}
                                    @else
                                        Unregistered
                                    @endif
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    @if(Auth::check())
                                        <img src="{{{ Auth::user()->photo_url }}}" class="img-circle"" alt="User Image">
                                    @endif

                                    <p>
                                        @if(Auth::check())
                                            {{{ Auth::user()->name }}}
                                        @else
                                            Unregistered
                                        @endif
                                        - Web Developer
                                        <small>Member since Nov. 2012</small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="user-body">
                                    <div class="row">
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Followers</a>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Sales</a>
                                        </div>
                                        <div class="col-xs-4 text-center">
                                            <a href="#">Friends</a>
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="/logout" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- Control Sidebar Toggle Button -->
                        <li>
                            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <aside class="main-sidebar">
        @include('spark::nav.admin');
        </aside>

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                @yield('page_header')
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Main Content -->
                @yield('content')
            </section>
        </div>

        <footer class="main-footer">
            {{--@include ('sitewide.footer')--}}
        </footer>
        <!-- Application Level Modals -->
        @if (Auth::check())
            @include('spark::modals.notifications')
            @include('spark::modals.support')
            @include('spark::modals.session-expired')
        @endif

    </div>
    <!-- JavaScript -->
    {{--<script src="/js/app.js"></script>--}}
    {{--<script src="/js/sweetalert.min.js"></script>--}}
    <script src="/admin/js/app.min.js"></script>
</body>
</html>
