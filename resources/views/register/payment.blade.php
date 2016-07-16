@extends('spark::layouts.app')

@section('content')
<payment :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Enter your billing information.
                            <div class="panel-subtitle">You will receive future deliveries at XX per week.<br>
                                You can skip a week or cancel your account at any time with 6 days’ notice.</div>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default panel-form">

                    <form role="form">

                        <div class="panel-heading text-left extrapadding">Billing Information</div>
                        <div class="panel-body font16 nopadding">
                            <div class="row nowrap extrapadding">
                                <div class="radio nosidepadding nomargin"><input id="same_as_delivery" type="checkbox" name="same_as_delivery" value="Same as Delivery"> <label for="same_as_delivery">Same as Delivery</label></div>
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
                                        <input type="text" class="form-control" v-model="registerForm.address" lazy placeholder="Address">

                                        <span class="help-block" v-show="registerForm.errors.has('address')">
                                            @{{ registerForm.errors.get('address') }}
                                        </span>
                                </div>

                                <!-- Address Line 2 -->
                                <div class="form-row col-sm-6 thinpadding last">
                                        <input type="text" class="form-control" v-model="registerForm.address_line_2" lazy placeholder="Address Line 2">

                                        <span class="help-block" v-show="registerForm.errors.has('address_line_2')">
                                            @{{ registerForm.errors.get('address_line_2') }}
                                        </span>
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- City -->
                                <div class="form-row col-sm-6 thinpadding first">
                                        <input type="text" class="form-control" v-model="registerForm.city" lazy placeholder="City">

                                        <span class="help-block" v-show="registerForm.errors.has('city')">
                                            @{{ registerForm.errors.get('city') }}
                                        </span>
                                </div>

                                <!-- State & ZIP Code -->
                                <div class="form-row col-sm-4 thinpadding">
                                        <input type="text" class="form-control" placeholder="State" v-model="registerForm.state" lazy>

                                        <span class="help-block" v-show="registerForm.errors.has('state')">
                                            @{{ registerForm.errors.get('state') }}
                                        </span>
                                </div>
                                <!-- Zip Code -->
                                <div class="form-row col-sm-2 thinpadding last">
                                        <input type="text" class="form-control" placeholder="Zip" v-model="registerForm.zip" lazy>

                                        <span class="help-block" v-show="registerForm.errors.has('zip')">
                                            @{{ registerForm.errors.get('zip') }}
                                        </span>
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- Phone -->
                                <div class="form-row col-sm-12 nosidepadding">
                                        <input type="text" class="form-control" placeholder="Phone Number" v-model="" lazy>

                                        <span class="help-block" v-show="registerForm.errors.has('country')">
                                            @{{ registerForm.errors.get('phone') }}
                                        </span>
                                </div>
                            </div>

                        </div>

                        <div class="panel-heading text-left extrapadding">Payment Information</div>
                        <div id="payment_info" class="panel-body font16">
                            <div class="row form-group extrapadding">
                                <div class="col-xs-6 nosidepadding">
                                    <select name="creditcard" type="select" class="form-control">
                                        <option v-for="card in cards" value="@{{ card }}">@{{ card }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group extrapadding">
                                <div class="col-xs-12 nosidepadding">
                                    <input type="text" class="form-control" placeholder="Card Number" v-model="" lazy>
                                </div>
                            </div>
                            <div class="row form-group extrapadding">
                                <div class="col-xs-6 thinpadding first">
                                    <select name="expmonth" type="select" class="form-control">
                                        <option>Expiration Month</option>
                                        <option v-for="month in months" value="@{{ month }}">@{{ month }}</option>
                                    </select>
                                </div>
                                <div class="col-xs-4 thinpadding">
                                    <select name="expyear" type="select" class="form-control">
                                        <option>Expiration Year</option>
                                        <option v-for="year in years" value="@{{ year }}">@{{ year }}</option>
                                    </select>
                                </div>
                                <div class="col-xs-2 thinpadding last">
                                    <input type="text" class="form-control" placeholder="CVC" v-model="registerForm.cvc" lazy>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
                
            </div>

            <div class="col-md-6">

                <div class="panel panel-default">
                    <div class="panel-heading text-left">Order Summary</div>
                    <div class="panel-body">
                        <div class="row padbottom">
                            <div class="col-sm-7">
                                <h5>PLAN TYPE</h5>
                                Family, 2 children <a href="#" class="sidelink">(change)</a>
                            </div>
                            <div class="col-sm-5">
                                <h5>DELIVERY DAY</h5>
                                Wednesday <a href="#" class="sidelink">(change)</a>
                            </div>
                        </div>
                        <div class="row padbottom">
                            <div class="col-sm-7">
                                <h5>DIETARY PROFILE</h5>
                                Omnivore, gluten-free, no nuts <a href="#" class="sidelink">(change)</a>
                            </div>
                            <div class="col-sm-5">
                                <h5>FIRST DELIVERY</h5>
                                May 18 <a href="#" class="sidelink">(change)</a>
                            </div>
                        </div>
                        <div class="row padbottom">
                            <div class="col-sm-12">
                                <h5>DELIVER TO YOUR HOME:</h5>
                                222 South Doheny Drive, Beverly Hills, CA 90543 <a href="#" class="sidelink">(change)</a>
                            </div>
                        </div>
                        
                        <p>&nbsp;</p>

                        <div class="row padtop font16">
                            <div class="panel-heading text-left">order TOTAL FOR MAY 18</div>
                            <div class="panel-body">
                                <div class="col-xs-12 col-sm-7 nosidepadding">
                                    <div class="col-xs-7 nosidepadding">Omnivore plan</div>
                                    <div class="col-xs-5 text-right nosidepadding">$XX.XX</div>
                                </div>
                                <div class="col-xs-12 col-sm-7 nosidepadding">
                                    <div class="col-xs-7 nosidepadding">Gluten free</div>
                                    <div class="col-xs-5 nosidepadding text-right">$XX.XX</div>
                                </div>
                                <div class="col-xs-12 col-sm-7 nosidepadding">
                                    <div class="col-xs-7 nosidepadding">Referral code</div>
                                    <div class="col-xs-5 nosidepadding text-right discount">-$XX.XX</div>
                                </div>
                                <div class="col-xs-12 col-sm-7 nosidepadding total">
                                    <div class="col-xs-7 nosidepadding">TOTAL</div>
                                    <div class="col-xs-5 nosidepadding text-right">$XX.XX</div>
                                </div>
                            </div>
                        </div>

                        <div class="row padtop">
                            <div class="col-xs-12">
                                <div style="display: inline-block" class="text-left">
                                    <button class="btn btn-primary">
                                        Place Order
                                    </button>
                                    <div class="disclaimer text-left padtop">By clicking “Place Order” you agree you are purchasing a continuous subscription, and that you will receive deliveries and be billed to your designated payment method weekly unless you a skip a delivery through your Delivery Schedule page or cancel your subscription, by contacting us and following the instructions in our response, on or before the “Changeable By” date reflected in your Account Settings. For more information see our Terms of Use and FAQ. 
        </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                </div>

            </div>
        </div>
    </div>
</payment>
@endsection
