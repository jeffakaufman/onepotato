@extends('spark::layouts.app')

@section('content')
<delivery :user="user" inline-template>
    <div id="planType">
        PLAN TYPE: Family, 2 children <a href="#" class="sidelink">(change)</a>
    </div>
    <div class="container">
        <!-- Application Dashboard -->
	
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Next, tell us where you would like your package to be delivered. 
                            <div class="panel-subtitle">You can change the address and family information at any time in Account Management.</div>
                        </h1>
                    </div>
                </div>
            </div>
        </div>		
<form class="form-horizontal" role="form" method="post"  action="{{ url('/register/delivery') }}">
					 {{ csrf_field() }}

					<input type="hidden" name="user_id" value="{{ $user->id }}" />
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default panel-form">

                  	

                        <div class="panel-heading text-left extrapadding">Delivery Location <a href="#" class="sidelink">more details</a></div>
                        <div class="panel-body font16 nopadding">
                            <div class="row nowrap extrapadding">
                                <div class="col-xs-4 radio nosidepadding nomargin"><input id="delivery_home" type="radio" name="delivery_loc" value="Home"> <label for="delivery_home">Home</label></div>
                                <div class="col-xs-4 radio nosidepadding nomargin"><input id="delivery_busines" type="radio" name="delivery_loc" value="Business"> <label for="delivery_busines">Business</label></div>
                            </div>
                        </div>

                        <div class="panel-heading text-left extrapadding">Address</div>
                        <div class="panel-body font16">
                        
                            <div class="row extrapadding">

                                <!-- First Name -->
                                <div class="form-row col-sm-6 thinpadding first">
                                        <input type="text" class="form-control" name="firstname" v-model="registerForm.firstname" placeholder="First Name" autofocus>

                                        <span class="help-block" v-show="registerForm.errors.has('firstname')">
                                            @{{ registerForm.errors.get('firstname') }}
                                        </span>
                                </div>

                                <!-- Last Name -->
                                <div class="form-row col-sm-6 thinpadding last">
                                        <input type="text" class="form-control" name="lastname" v-model="registerForm.lastname" placeholder="Last Name" autofocus>

                                        <span class="help-block" v-show="registerForm.errors.has('lastname')">
                                            @{{ registerForm.errors.get('lastname') }}
                                        </span>
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- Address -->
                                <div class="form-row col-sm-6 thinpadding first">
                                        <input type="text" class="form-control" name="address" v-model="registerForm.address" lazy placeholder="Address">

                                        <span class="help-block" v-show="registerForm.errors.has('address')">
                                            @{{ registerForm.errors.get('address') }}
                                        </span>
                                </div>

                                <!-- Address Line 2 -->
                                <div class="form-row col-sm-6 thinpadding last">
                                        <input type="text" class="form-control" name="address_line_2" v-model="registerForm.address_line_2" lazy placeholder="Address Line 2">

                                        <span class="help-block" v-show="registerForm.errors.has('address_line_2')">
                                            @{{ registerForm.errors.get('address_line_2') }}
                                        </span>
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- City -->
                                <div class="form-row col-sm-6 thinpadding first">
                                        <input type="text" class="form-control" name="city" v-model="registerForm.city" lazy placeholder="City">

                                        <span class="help-block" v-show="registerForm.errors.has('city')">
                                            @{{ registerForm.errors.get('city') }}
                                        </span>
                                </div>

                                <!-- State & ZIP Code -->
                                <div class="form-row col-sm-4 thinpadding">
                                        <input type="text" class="form-control" name="state" placeholder="State" v-model="registerForm.state" lazy>

                                        <span class="help-block" v-show="registerForm.errors.has('state')">
                                            @{{ registerForm.errors.get('state') }}
                                        </span>
                                </div>
                                <!-- Zip Code -->
                                <div class="form-row col-sm-2 thinpadding last">
                                        <input type="text" class="form-control" name="zip" placeholder="Zip" v-model="registerForm.zip" lazy>

                                        <span class="help-block" v-show="registerForm.errors.has('zip')">
                                            @{{ registerForm.errors.get('zip') }}
                                        </span>
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- Phone -->
                                <div class="form-row col-sm-12 nosidepadding">
                                        <input type="text" class="form-control" name="phone" placeholder="Phone Number" v-model="registerForm.phone" lazy>

                                        <span class="help-block" v-show="registerForm.errors.has('phone')">
                                            @{{ registerForm.errors.get('phone') }}
                                        </span>
                                </div>
                            </div>

                        </div>

                        <div class="panel-heading text-left extrapadding">Special Delivery Instructions <a href="#" class="sidelink">what's this?</a></div>
                        <div class="panel-body font16">
                            <div class="row extrapadding">
                                <div class="col-sm-12 nosidepadding"><textarea name="delivery_instructions" class="form-control"></textarea></div>
                            </div>
                        </div>

                        <div class="panel-heading text-left extrapadding">Family Information <a href="#" class="sidelink">what's this?</a>
                            <div class="panel-subtitle">We love celebrations! Share your child’s birthday and we will send a little surprise in time for their big day.</div>
                        </div>
                        <div class="panel-body font16">
                            <div id="bday_select" class="row extrapadding">
                                <div class="col-xs col-xs-2 thinpadding first">
                                    Child 1
                                </div>
                                <div class="col-xs col-xs-6 thinpadding">
                                    <select name="child_bday1_month" type="select" class="form-control">
                                        <option>Month</option>
                                        <option v-for="month in months" value="@{{ month }}">@{{ month }}</option>
                                    </select>
                                </div>
                                <div class="col-xs col-xs-4 thinpadding last">
                                    <select name="child_bday1_day" type="select" class="form-control">
                                        <option>Day</option>
                                        @for($x=1;$x<=31;$x++)
                                            <option value={{ $x }}>{{ $x }}</option>;
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                 

                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-sm-offset-6 text-right">
                        <div style="display: inline-block" class="text-center">
                            <button class="btn btn-primary">
                                Continue to billing
                            </button>
                            <div class="disclaimer">No Commitment.  Skip, cancel or <br>change your family size any time.</div>
                        </div>
                    </div>
                </div>
             </form>
            </div>
        </div>
    </div>
</delivery>
@endsection
