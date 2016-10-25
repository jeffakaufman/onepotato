<?php 
session_start();
    if( isset( $_SESSION['registered']) ) session_destroy();
    else if (isset( $user->id)) header("Location: /account");
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
						@if($title)
							{!! $title !!}
						@else
	                    	Let’s get started!
						@endif
	                    <div class="panel-subtitle">
							@if($subtitle)
								{!! $subtitle !!}
							@else
								Everything you need to make organic & delicious dinners the whole family will love delivered straight to your door each week.
							@endif
						</div>
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
							            <input type="name" class="form-control" name="firstname" placeholder="First Name*" tabindex="1" value="{{old('firstname')}}" autofocus required>
							        </div>
							        <div class="form-row col-sm-6 thinpadding">
							            <input type="name" class="form-control" name="lastname" placeholder="Last Name*" tabindex="2" value="{{old('lastname')}}" required>
							        </div>
								</div>
						        <!-- E-Mail Address -->
						        <div class="clearfix">
						        	<div class="form-row col-sm-12 thinpadding">
						            	<input type="email" class="form-control" name="email" tabindex="3" placeholder="E-Mail Address*" value="{{old('email')}}" required>
						            </div>
						        </div>

						        <!-- Password -->
						        <div class="clearfix">
						        	<div class="form-row col-sm-6 thinpadding">
						            	<input type="password" class="form-control" name="password" tabindex="4" placeholder="Password*" required>
						            </div>
								<!-- Password Confirmation -->
									<div class="form-row col-sm-6 thinpadding">
						            	<input type="password" class="form-control" name="password_confirmation" tabindex="5" placeholder="Confirm Password*">
									</div>
						        </div>
						        <!-- Zip Code -->
						        <div class="clearfix">
						        	<div class="form-row col-sm-12 thinpadding">
						            	<input type="text" class="form-control" name="zip" tabindex="6" placeholder="Delivery Zip Code*" value="{{old('zip')}}">
						            </div>
						        </div>
						    </div>

						    <div class="clearfix">
					            <div class="col-sm-7 disclaimer">By clicking GET STARTED you are agreeing to our <a href="/terms" target="_blank" style="color: #a8a8a8; text-decoration: underline;">Terms of Use and Privacy Policy</a>.</div>
					            <div class="col-sm-5 nosidepadding text-right reg-button">
					            	<button class="btn btn-primary">GET STARTED</button>
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
<div id="thanksgiving" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h1 class="modal-title">Imagine a Thanksgiving week...</h1>
                <h4>With less shopping, less cooking, less stress -- and a whole lot less expense</h4>
            </div>
            <div class="modal-body">
            	<div class="row">
                	<p class="col-xs-12 col-sm-7"><img src="/img/thanksgiving.jpg"></p>
	                <div class="col-xs-12 col-sm-5 font16">
	                	<p>Whether you’re cooking for your entire family, or just love eating leftovers all weekend, One Potato has you covered!</p>

						<p>Our November 22nd delivery will be <strong>Thanksgiving in a Box</strong> - all for the <i>same price of your normal weekly One Potato delivery.</i></p>
					</div>
				</div>
				<p>Hosting more than just your family? Order another box (or two)! <b><a href="mailto:hello@onepotato.com">Email</a> us by Friday, November 11th</b> at <a href="mailto:hello@onepotato.com">hello@onepotato.com</a> so we can guide you through the ordering process.</p>

				<p>Have a great Thanksgiving, and as always, we are thankful for your business!</p>

				<p><b>The Potatoes,</b><br>
				Jenna, Chris & Catherine</p>
				
				<p><strong>Note: </strong>Since the holiday is really about giving and family, for every Thanksgiving box you order One Potato will donate a meal to a family in need through the <strong><a href="http://allianceofmoms.org/" target="_blank">Alliance of Moms</a></strong> charity . <a href="http://allianceofmoms.org/" target="_blank">Click here</a> to find out more about what they do.</p>
                
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type='text/javascript'>
	$(document).ready(function() {
		$('#thanksgiving').modal();
	});
</script>


@endsection