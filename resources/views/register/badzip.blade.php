@extends('spark::layouts.app')

@section('content')

    <div class="container">
	
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Join Our Waiting List
                            <div class="panel-subtitle">We are stocking our kitchen -- almost ready to make dinner time as fun as it should be.</div>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default panel-form">

                    <form class="form-horizontal" role="form" method="post"  action="/register/waiting_list">
                    {{ csrf_field() }}

                        <div class="panel-body">
                        
                            <div class="row extrapadding">

                                <p>Join our list of families interested in One Potato and we'll send you meals on us once we're in your neighborhood.</p>

                                
                                <div class="form-row padding">
                                    <!-- E-Mail Address -->
                                    <input type="email" class="form-control" name="email" placeholder="Your email address" value="{{$data->email}}" autofocus>
                                </div>
                                <div class="form-row padding">
                                    <!-- First Name -->
                                    <input type="text" class="form-control" name="firstname" placeholder="Your first name" value="{{$data->firstname}}">
                                </div>
                                <div class="form-row padding">
                                    <!-- Last Name -->
                                    <input type="text" class="form-control" name="lastname" placeholder="Your last name" value="{{$data->lastname}}">
                                </div>
                                <div class="form-row padding">
                                    <!-- Zip Code -->
                                        <input type="text" class="form-control" name="zip" placeholder="Your zip code" value="{{$data->zip}}">
                                </div>
                            </div>

                            <div class="text-center">
                                <div style="display: inline-block" class="text-center">
                                    <button class="btn btn-primary">
                                        Sign Up
                                    </button>
                                </div>
                            </div>

                        </div>
                 
                    </form>
                </div>

             </form>
            </div>
        </div>

    </div>
@endsection
