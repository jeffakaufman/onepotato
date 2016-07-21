@extends('spark::layouts.app')

@section('content')


<!-- Basic Profile -->
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 v-if="paidPlans.length > 0">
                    Profile
                </h1>

                <h1 v-else>
                    Let’s get started!
                    <div class="panel-subtitle">Everything you need to make organic & delicious dinners
the whole family will love delivered straight to your door each week.</div>
                </h1>
            </div>

            <div class="panel-body nopadding">
                <!-- Generic Error Message -->
                <div class="alert alert-danger" v-if="registerForm.errors.has('form')">
                    @{{ registerForm.errors.get('form') }}
                </div>

                <!-- Invitation Code Error -->
                <div class="alert alert-danger" v-if="registerForm.errors.has('invitation')">
                    @{{ registerForm.errors.get('invitation') }}
                </div>

                <!-- Registration Form -->
              

				<form class="form-horizontal" role="form" method="post">
					 {{ csrf_field() }}

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
				        <div class="field" :class="{'has-error': registerForm.errors.has('email')}">

				            <input type="email" class="form-control" name="email" v-model="registerForm.email" placeholder="E-Mail Address">

				            <span class="help-block" v-show="registerForm.errors.has('email')">
				                @{{ registerForm.errors.get('email') }}
				            </span>

				        </div>

				        <!-- Password -->
				        <div class="field" :class="{'has-error': registerForm.errors.has('password')}">

				            <input type="password" class="form-control" name="password" v-model="registerForm.password" placeholder="Password">

				            <span class="help-block" v-show="registerForm.errors.has('password')">
				                @{{ registerForm.errors.get('password') }}
				            </span>

				        </div>

				        <!-- Password Confirmation -->
				        <div class="field" :class="{'has-error': registerForm.errors.has('password_confirmation')}">

				            <input type="password" class="form-control" name="password_confirmation" v-model="registerForm.password_confirmation" placeholder="Confirm Password">

				            <span class="help-block" v-show="registerForm.errors.has('password_confirmation')">
				                @{{ registerForm.errors.get('password_confirmation') }}
				            </span>

				        </div>
				    </div>

				    <div class="reg-field">
				        <!-- Zip Code -->
				        <div class="field">
				            <input type="text" class="form-control" placeholder="Delivery Zip Code" v-model="registerForm.zip" lazy>

				            <span class="help-block" v-show="registerForm.errors.has('zip')">
				                @{{ registerForm.errors.get('zip') }}
				            </span>
				        </div>
				    </div>

				    <div class="reg-button">
				        <!-- Terms And Conditions -->
				        <div v-if=" ! selectedPlan || selectedPlan.price == 0">
				            <!-- <div class="form-group" style="display: none" :class="{'has-error': registerForm.errors.has('terms')}">
				                <div class="checkbox">
				                    <label>
				                        <input type="checkbox" name="terms" v-model="registerForm.terms" checked>
				                        I Accept The <a href="/terms" target="_blank">Terms Of Service</a>
				                    </label>

				                    <span class="help-block" v-show="registerForm.errors.has('terms')">
				                        @{{ registerForm.errors.get('terms') }}
				                    </span>
				                </div>
				            </div> -->

				            <button class="btn btn-primary" @click.prevent="register" :disabled="registerForm.busy">
				                <span v-if="registerForm.busy">
				                    <i class="fa fa-btn fa-spinner fa-spin"></i>Registering
				                </span>

				                <span v-else>
				                    Get Started
				                </span>
				            </button>
				            <div class="disclaimer">By clicking GET STARTED you are agreeing to our <a href="/terms" target="_blank">Terms of Use and Privacy Policy</a>.</div>

				        </div>
				    </div>
				</form>

            </div>
        </div>
    </div>
</div>

<div class="row" v-if="paidPlans.length == 0">
    <div class="col-md-4 col-md-offset-2">
        <h5>NO COMMITMENT</h5>
        <p>Skip weeks, change your family size, or cancel anytime. </p>

        <h5>CUSTOMIZED TO YOUR FAMILY</h5>
        <p>Customize to your family’s size and take advantage of special kid’s pricing. We’ll personalize your menus based on your dietary preferences.</p> 

        <h5>CONVENIENT DELIVERY</h5>
        <p>Meals arrive in a recyclable insulated box so food stays fresh until you open it.</p>
    </div>
    <div class="col-md-4">
        <div style="position: absolute; top: -5px; right: 0;"><img src="/img/badge_free_shipping.png"></div>
        <img src="/img/p_register.jpg">
    </div>
</div>
<div class="row" v-if="paidPlans.length == 0">
    <div class="footnote pad col-md-8 col-md-offset-2">One Potato meals feature organic ingredients whenever possible. All organic ingredients are clearly labeled upon delivery.</div>
</div>




@section('content')