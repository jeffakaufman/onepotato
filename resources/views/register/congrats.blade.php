@extends('spark::layouts.app')

@section('content')
<delivery :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
	
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Congratulation!
                            <div class="panel-subtitle">Your first box will arrive on May 18.</div>
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default panel-form">

                    <div class="panel-heading text-left extrapadding">Login Information
                        <div class="panel-subtitle">Please use your email address to log into our site to manage
your account. You will also need to create a password.</div>
                    </div>
                    <div class="panel-body font16 extrapadding text-center">
                    
                        @include('spark::shared.errors')

                        <form class="form-horizontal" role="form">
                            {{ csrf_field() }}

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

                            <!-- Login Button -->
                            <button type="submit" class="btn btn-primary">
                                Login
                            </button>
                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>
</delivery>
@endsection
