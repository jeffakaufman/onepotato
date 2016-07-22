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
                <ul class="nav navbar-nav navbar-left">
                    <li @if ($page == 'register')class="active"@endif><span><i class="icon icon-apron"></i>1. Welcome</span></li>
                    <li @if ($page == 'select_plan')class="active"@endif><span><i class="icon icon-silverware"></i>2. Select Plan</span></li>
                    <li @if ($page == 'preferences')class="active"@endif><span><i class="icon icon-sliders"></i>3. Preferences</span></li>
                    <li @if ($page == 'delivery')class="active"@endif><span><i class="icon icon-truck"></i>4. Delivery</span></li>
                    <li @if ($page == 'payment')class="active"@endif><span><i class="icon icon-creditcard"></i>5. Payment</span></li>
                </ul>

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
<delivery :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
	
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Congratulations!
                            <div class="panel-subtitle">Your first box will arrive on May 18.</div>
                        </h1>
                        <!-- Login Button -->
                        <button type="submit" class="btn btn-primary" onclick="location.href='/whats-cooking';">
                            See What's Cooking
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default panel-form">

                    <div class="panel-heading text-left extrapadding">Login Information
                        <div class="panel-subtitle">Please use your email address to log into our site to manage your account. You will also need to create a password.</div>
                    </div>
                    <div class="panel-body font16 extrapadding text-center">

                            

                    </div>

                </div>

            </div>
        </div> -->
    </div>
</delivery>
@endsection
