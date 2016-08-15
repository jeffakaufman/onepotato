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
                    <h1>Congratulations!
                        <div class="panel-subtitle">Your first box will arrive on {{ date('F d', strtotime($start_date)) }}.</div>
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        
        <div class="panel panel-default">

            <div class="panel-heading text-center extrapadding">Meals to be delivered
                <div class="panel-subtitle"></div>
            </div>
            <div class="panel-body font16 extrapadding text-center">
                
                <div id="congrats" class="row" data-start-date="{{ $start_date }}" data-meal1="{{ $meal1 }}" data-meal2="{{ $meal2 }}" data-meal3="{{ $meal3 }}">
                        
                    <div id="menu" class="col-xs-12 extrapadding">

                        <div class="meal col-xs-4 font11 thinpadding" v-for="meal in firstMenu" track-by="id">
                            <a href="#" data-toggle="modal" data-target="#imagemodal-@{{ meal.id }}"><img :src="meal.image" alt="" class="meal_image"></a>
                            <div class="text-center">@{{ meal.menu_title }}</div>
                            
                            <div id="imagemodal-@{{ meal.id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title">@{{ meal.menu_title }}</h4>
                                  </div>
                                  <div class="modal-body">
                                    <img :src="meal.image" id="imagepreview">
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                        
                    </div>

                </div>
                        
            </div>

        </div>
    </div>
    <!-- <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default panel-form">

            <div class="panel-heading text-left extrapadding">Log in
                <div class="panel-subtitle">Please use your email address to log into our site to manage
your account.</div>
            </div>
            <div class="panel-body font16 extrapadding text-center">
            
                <button type="submit" class="btn btn-primary" onclick="location.href='/login';">Log in</button>
            </div>

        </div>
    </div> -->
</div>
@endsection
