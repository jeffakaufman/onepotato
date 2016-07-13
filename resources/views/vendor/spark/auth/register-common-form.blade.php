<form class="form-horizontal" role="form">
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
