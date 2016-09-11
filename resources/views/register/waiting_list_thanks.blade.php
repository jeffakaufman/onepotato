@extends('spark::layouts.app')

@section('register-nav')
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <!-- Collapsed Hamburger -->
            <div class="hamburger">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#spark-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <!-- Branding Image -->
            @include('spark::nav.brand')

            <div class="collapse navbar-collapse" id="spark-navbar-collapse">
                <!-- Left Side Of Navbar -->
                @includeIf('spark::nav.user-left')

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/login" class="navbar-link">Login</a></li>
                    <li><a href="/register" class="navbar-link">Register</a></li>
                </ul>
            </div>
            
        </div>

    </div>
</nav>
@endsection

@section('content')
<div class="container">
    <!-- Application Dashboard -->

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading with-subtitle">
                    <h1>Thanks!
                        <div class="panel-subtitle">We will inform you.</div>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
