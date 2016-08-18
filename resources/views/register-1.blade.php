@extends('spark::layouts.app')

@section('register_nav')
<script>
$('#register1').addClass('active');
</script>
@endsection

@section('content')
<!-- Basic Profile -->
<div class="container">
	<div class="row">
	    <div class="col-md-8 col-md-offset-2">
	        <div class="panel panel-default">
	            <div class="panel-heading">
	                
	                <h1>
	                    Let’s get started!
	                    <div class="panel-subtitle">Everything you need to make organic & delicious dinners
	the whole family will love delivered straight to your door each week.</div>
	                </h1>
	            </div>
				 <!-- Display Validation Errors -->
				        @include('errors.errors')
	            <div class="panel-body nopadding">

	                <!-- Registration Form -->
	                {!! Form::open(['url' => '/register', 'role' => 'form', 'class' => 'form-horizontal']) !!}
					
					    <!-- Team Name -->
					    @if (Spark::usesTeams())
					        <div class="field" :class="{'has-error': registerForm.errors.has('team')}" v-if=" ! invitation">

					            <input type="name" class="form-control" name="team" v-model="registerForm.team" autofocus placeholder="Team Name">

					            <span class="help-block" v-show="registerForm.errors.has('team')">
					                @{{ registerForm.errors.get('team') }}
					            </span>

					        </div>
					    @endif

					    <div class="reg-field">
					        <!-- Name -->
					        <!-- <div class="field" :class="{'has-error': registerForm.errors.has('name')}">

					            <input type="name" class="form-control" name="name" v-model="registerForm.name" placeholder="Name" autofocus>

					            <span class="help-block" v-show="registerForm.errors.has('name')">
					                @{{ registerForm.errors.get('name') }}
					            </span>

					        </div> -->

					        <!-- E-Mail Address -->
					        <div class="field">

					            <input type="email" class="form-control" name="email" tabindex="1" placeholder="E-Mail Address">

					        </div>

					        <!-- Password -->
					        <div class="field">

					            <input type="password" class="form-control" name="password" tabindex="3" placeholder="Password">

					        </div>

					    </div>

					    <div class="reg-field">
					        <!-- Zip Code -->
					        <div class="field">
					            <input type="text" class="form-control" name="zip" tabindex="2" placeholder="Delivery Zip Code" value="{{ old('zip') }}" lazy>
					        </div>

					        <!-- Password Confirmation -->
					        <div class="field">

					            <input type="password" class="form-control" name="password_confirmation" tabindex="4" placeholder="Confirm Password">

					        </div>
					    </div>

					    <div class="reg-button">
				            <button class="btn btn-primary">
				                Get Started
				            </button>
				            <div class="disclaimer" style="margin: 10px 0;">By clicking GET STARTED you are agreeing to our <a href="/terms" target="_blank">Terms of Use and Privacy Policy</a>.</div>
					    </div>
					{!! Form::close() !!}

	            </div>
	        </div>
	    </div>
	</div>

	<div class="row">
	    <div class="col-sm-6 col-md-4 col-md-offset-2">
	        <h5>COMMITMENT-FREE</h5>
	        <p>Skip deliveries, change your family size, or cancel anytime.</p>

	        <h5>CUSTOMIZED</h5>
	        <p>Customize your box based on family size, dietary preferences and allergies.  Take advantage of special kid’s pricing. </p> 

	        <h5>CONVENIENT DELIVERY</h5>
	        <p>Receive meals in a recyclable insulated box so food stays fresh.</p>
	    </div>
	    <div class="col-sm-6 col-md-4 text-right">
	        <div style="position: absolute; top: -5px; right: 0;"><img src="/img/badge_free_shipping.png"></div>
	        <img src="/img/p_register.jpg" style="width: 100%">
	    </div>
	</div>
	<div class="row">
	    <div class="footnote pad col-md-8 col-md-offset-2">One Potato meals feature organic ingredients whenever possible. All organic ingredients are clearly labeled upon delivery.</div>
	</div>
</div>
@endsection