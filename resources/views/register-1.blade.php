<?php 
session_start();
    if( isset( $_SESSION['registered']) ) header("Location: /account");
?>
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
	    <div class="col-md-10 col-md-offset-1">
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
	            <div class="panel-body nopadding row">
					
					<div class="col-sm-6">


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
							
							<div class="panel panel-default panel-form panel-register">
						        <!-- Name -->
								<div class="clearfix">
							        <div class="form-row col-sm-6 thinpadding">
							            <input type="name" class="form-control" name="firstname" placeholder="First Name" tabindex="1" value="{{old('firstname')}}" autofocus required>
							        </div>
							        <div class="form-row col-sm-6 thinpadding">
							            <input type="name" class="form-control" name="lastname" placeholder="Last Name" tabindex="2" value="{{old('lastname')}}" required>
							        </div>
								</div>
						        <!-- E-Mail Address -->
						        <div class="clearfix">
						        	<div class="form-row col-sm-12 thinpadding">
						            	<input type="email" class="form-control" name="email" tabindex="3" placeholder="E-Mail Address" value="{{old('email')}}" required>
						            </div>
						        </div>

						        <!-- Password -->
						        <div class="clearfix">
						        	<div class="form-row col-sm-6 thinpadding">
						            	<input type="password" class="form-control" name="password" tabindex="4" placeholder="Password" required>
						            </div>
								<!-- Password Confirmation -->
									<div class="form-row col-sm-6 thinpadding">
						            	<input type="password" class="form-control" name="password_confirmation" tabindex="5" placeholder="Confirm Password">
									</div>
						        </div>
						        <!-- Zip Code -->
						        <div class="clearfix">
						        	<div class="form-row col-sm-12 thinpadding">
						            	<input type="text" class="form-control" name="zip" tabindex="6" placeholder="Delivery Zip Code" value="{{old('zip')}}">
						            </div>
						        </div>
						    </div>

						    <div class="clearfix">
					            <div class="col-sm-7 disclaimer">By clicking GET STARTED you are agreeing to our <a href="/terms" target="_blank" style="color: #a8a8a8; text-decoration: underline;">Terms of Use and Privacy Policy</a>.</div>
					            <div class="col-sm-5 nosidepadding text-right reg-button">
					            	<button class="btn btn-primary">Continue</button>
					            </div>
						    </div>
						{!! Form::close() !!}
					</div>
					<div class="col-sm-6 thinpadding text-right">
						<div style="position: absolute; top: -10px; right: -10px;"><img src="/img/badge_free_shipping.png"></div>
	        			<img src="/img/p_register.jpg" style="width: 100%">
					</div>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="row buckets">
	    <div class="col-sm-3 col-md-offset-1 text-center">
	        <h5>NO COMMITMENT</h5>
	        <div class="div"></div>
	        <p>Skip weeks, change your family size, or cancel anytime.</p>
	    </div>
	    <div class="col-sm-4 text-center">
	        <h5>CUSTOMIZED TO YOUR FAMILY</h5>
	        <div class="div"></div>
	        <p>Customize to your family’s size and take advantage of special kid’s pricing. We’ll personalize your menus based on your dietary preferences. </p> 
	    </div>
	    <div class="col-sm-3 text-center">
	        <h5>CONVENIENT DELIVERY</h5>
	        <div class="div"></div>
	        <p>Meals arrive in a recyclable insulated box so food stays fresh until you open it.</p>
	    </div>
	</div>
	<div class="row">
	    <div class="footnote pad col-md-10 col-md-offset-1">One Potato meals feature organic ingredients whenever possible. All organic ingredients are clearly labeled upon delivery.</div>
	</div>
</div>
@endsection