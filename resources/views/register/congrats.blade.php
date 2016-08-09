@extends('spark::layouts.app')

@section('register-nav')
<?php 
    if (Session::has('redmeat')) $redmeat = Session::get('redmeat');
    if (Session::has('poultry')) $poultry = Session::get('poultry');
    if (Session::has('redmeat')) $redmeat = Session::get('redmeat');
    if (Session::has('fish')) $fish = Session::get('fish');
    if (Session::has('lamb')) $lamb = Session::get('lamb');
    if (Session::has('pork')) $pork = Session::get('pork');
    if (Session::has('shellfish')) $shellfish = Session::get('shellfish');
    if (Session::has('nuts')) $nuts = Session::get('nuts');
?>
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
<delivery :user="user" inline-template>
    <div id="congrats" class="container" data-start-date="{{ date('Y-m-d', strtotime($start_date)) }}">
        <!-- Application Dashboard -->
	
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Congratulations!
                            <div class="panel-subtitle">Your first box will arrive on {{ date('F d', strtotime($start_date)) }}.</div>
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default panel-form">

                    <div class="panel-heading text-left extrapadding">Meals to be delivered
                        <div class="panel-subtitle"></div>
                    </div>
                    <div class="panel-body font16 extrapadding text-center">
                        {{ $first_delivery }}


                        <!-- <div class="col-xs-4 font11 thinpadding first">
                            <img src="/img/preferences_meal1.jpg" alt="">
                            <div class="col-xs-9 col-xs-offset-1 padding nosidepadding text-center">Sweet Balsamic Chicken with Avocado Toast and Cauliflower</div>
                        </div>
                        <div class="col-xs-4 font11 thinpadding">
                            <img src="/img/preferences_meal2.jpg" alt="">
                            <div class="col-xs-9 col-xs-offset-1 padding nosidepadding text-center">Sweet Corn Manchego Enchiladas with Corn Salsa</div>
                        </div>
                        <div class="col-xs-4 font11 thinpadding last">
                            <img src="/img/preferences_meal3.jpg" alt="">
                            <div class="col-xs-9 col-xs-offset-1 padding nosidepadding text-center">Salmon Sheet Pan Dinner with Early Summer Vegetables  and Orzo Salad</div>
                        </div> -->

                    </div>
                    <div class="panel-footer font16 extrapadding text-center">
                        Please use your email address to log into our site to manage
your account. You will also need to create a password.
                        <p><button type="submit" class="btn btn-primary" onclick="location.href='/login';">Login</button></p>
                    </div>

                </div>

            </div>
        </div>
    </div>
</delivery>
@endsection
