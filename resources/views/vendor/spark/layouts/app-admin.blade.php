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
    <link href="/admin/css/skins/skin-green.min.css" rel="stylesheet">

    <!-- Scripts -->
    @yield('scripts', '')

    <!-- Global Spark Object -->
    <script>
        window.Spark = <?php echo json_encode(array_merge(
            Spark::scriptVariables(), []
        )); ?>
    </script>
</head>
<body class="hold-transition skin-green sidebar-mini">
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
        </header>
        <aside class="main-sidebar">
        @include('spark::nav.admin');
        </aside>

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Dashboard
                    <small>Control panel</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">UI</a></li>
                    <li class="active">Buttons</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Main Content -->
                @yield('content')
            </section>
        </div>

        <footer class="main-footer">
            @include ('sitewide.footer')
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
