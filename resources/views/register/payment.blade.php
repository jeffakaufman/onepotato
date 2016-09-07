<?php 
session_start();
    if( isset( $_SESSION['registered']) ) header("Location: /account");
?>
@extends('spark::layouts.app')

@section('register_nav')
<script>
$('#register5').addClass('active');
</script>
@endsection

@section('content')
<?php
    if (Session::has('children')) $children = Session::get('children');
    if (Session::has('plantype')) $plantype = Session::get('plantype');
    if (Session::has('dietprefs')) $dietprefs = Session::get('dietprefs');
    if (Session::has('glutenfree')) $glutenfree = Session::get('glutenfree');
    if (Session::has('firstname')) $firstname = Session::get('firstname');
    if (Session::has('lastname')) $lastname = Session::get('lastname');
    if (Session::has('address')) $address = Session::get('address');
    if (Session::has('address2')) $address2 = Session::get('address2');
    if (Session::has('city')) $city = Session::get('city');
    if (Session::has('state')) $state = Session::get('state');
    if (Session::has('zip')) $zip = Session::get('zip');
    if (Session::has('phone')) $phone = Session::get('phone');
?>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script type="text/javascript">Stripe.setPublishableKey("{{ env('STRIPE_KEY') }}");</script>

<script>
/*stripe code*/

$(document).ready(function() {

	// Watch for a form submission:
	$("#payment-form").click(function(event) {

		// Flag variable:
		var error = false;

		// disable the submit button to prevent repeated clicks:
		$('#submitBtn').attr("disabled", true);
        var messages = '';
		// Get the values:
		var ccNum = $('.card-number').val(), cvcNum = $('.card-cvc').val(), expMonth = $('.card-expiry-month').val(), expYear = $('.card-expiry-year').val();
		
		console.log ("CC Num: " + ccNum);
		
		// Validate the number:
		if (!Stripe.card.validateCardNumber(ccNum)) {
			error = true;
			messages += 'The credit card number appears to be invalid.<br>';
		}

		// Validate the CVC:
		if (!Stripe.card.validateCVC(cvcNum)) {
			error = true;
			messages += 'The CVC number appears to be invalid.<br>';
		}

		// Validate the expiration:
		if (!Stripe.card.validateExpiry(expMonth, expYear)) {
			error = true;
			messages += 'The expiration date appears to be invalid.<br>';
		}
        if (error) {
            $('#submitErrors').html(messages).slideDown();
        }

		// Validate other form elements, if needed!
	
		// Check for errors:
		if (!error) {
			console.log ('sending to Stripe');
			$('#submitErrors').html('').slideUp();
			// Get the Stripe token:
			Stripe.card.createToken({
				number: ccNum,
				cvc: cvcNum,
				exp_month: expMonth,
				exp_year: expYear
			}, stripeResponseHandler);

		}else{
			
				$("#submitBtn").removeAttr("disabled"); // Re-enable submission
				console.log ("errors - removed disabled");
			
		}
		
		
		// Prevent the form from submitting:
		return false;

	}); // Form submission

}); // Document ready.

// Function handles the Stripe response:
function stripeResponseHandler(status, response) {

	// Check for an error:
	if (response.error) {

		//	reportError(response.error.message);
		$('.bad_cc').slideDown();
	
		$('#submitBtn').attr("disabled", "false"); // Re-enable submission
		console.log ("STRIPE ERRORS!!");
		return false;

	} else { 
		// No errors, submit the form:

	  var f = $("#payment-form");

	  // Token contains id, last4, and card type:
	  var token = response['id'];

	  console.log(token);

	  // Insert the token into the form so it gets submitted to the server
	  f.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

	  // Submit the form:
		console.log ("FORM SUBMITTED!");
	  f.get(0).submit();

	}

} // End of stripeResponseHandler() function.

var cc_number_saved = "";
function checkLuhn(input) {
  var sum = 0;
  var numdigits = input.length;
  var parity = numdigits % 2;
  for(var i=0; i < numdigits; i++) {
    var digit = parseInt(input.charAt(i))
    if(i % 2 == parity) digit *= 2;
    if(digit > 9) digit -= 9;
    sum += digit;
  }
  return (sum % 10) == 0;
}
</script>


<div id="payment">
    <div class="container">

       	<form class="form-horizontal" role="form" id="payment-form" method="post"  action="{{ url('/register/payment') }}">
							 {{ csrf_field() }}
							<input type="hidden" name="start_date" value="{{ $start_date }}" />
							<input type="hidden" name="user_id" value="{{ $user->id }}" />

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Enter your billing information.
                            <div class="panel-subtitle">You will receive future deliveries at ${{ $product->cost }} per week.<br>
                                You can skip a week or cancel your account at any time with 6 days’ notice.</div>
                        </h1>
						<div class="stripe-server-error" style="color:red !important;">{{ $stripeError or '' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <a href="{{ route('register.delivery') }}" style="position: absolute; margin-top: -2em;">
                    <i class="fa fa-caret-left" aria-hidden="true"></i> BACK</a>
                <div class="panel panel-default panel-form">
            
                    <div class="panel-heading text-left extrapadding">Billing Information</div>
                    <div class="panel-body font16 nopadding">
                        <div class="row nowrap extrapadding">
                            <div class="checkbox nosidepadding nomargin"><input id="same_as_delivery" type="checkbox" name="same_as_delivery" value="Same as Delivery" checked> <label for="same_as_delivery">Same as Delivery</label></div>
                        </div>
                    </div>

                    <div class="panel-heading text-left extrapadding">Address</div>
                    <div class="panel-body font16">
                    
                        <div class="row extrapadding">

                            <!-- First Name -->
                            <div class="form-row col-sm-6 thinpadding first">
                                <input type="text" class="form-control" name="firstname" placeholder="First Name" value="{{$firstname}}" required autofocus>
                            </div>

                            <!-- Last Name -->
                            <div class="form-row col-sm-6 thinpadding last">
                                <input type="text" class="form-control" name="lastname" placeholder="Last Name" value="{{$lastname}}" required>
                            </div>
                        </div>
                        <div class="row extrapadding">
                            <!-- Address -->
                            <div class="form-row col-sm-6 thinpadding first">
                                <input type="text" name="address" class="form-control" lazy placeholder="Address" value="{{$address}}" required>
                            </div>

                            <!-- Address Line 2 -->
                            <div class="form-row col-sm-6 thinpadding last">
                                <input type="text" name="address_2" class="form-control" lazy placeholder="Address Line 2" value="{{$address2}}">
                            </div>
                        </div>
                        <div class="row extrapadding">
                            <!-- City -->
                            <div class="form-row col-sm-6 thinpadding first">
                                <input type="text" name="city" class="form-control" lazy placeholder="City" value="{{$city}}" required>
                            </div>

                            <!-- State & ZIP Code -->
                            <div class="form-row col-sm-4 thinpadding">

                                <label class="select">
                                    <select name="state" type="select" class="form-control" required>
                                        <option value="AZ" @if( $state == 'AZ') selected @endif>Arizona</option>
                                        <option value="CA" @if( $state == 'CA') selected @endif>California</option>
                                        <option value="UT" @if( $state == 'UT') selected @endif>Utah</option>
                                    </select>
                                </label>
                            </div>

                            <!-- Zip Code -->
                            <div class="form-row col-sm-2 thinpadding last">
                                <input type="text" name="zip" class="form-control" placeholder="Zip" value="{{$zip}}" required>
                            </div>
                        </div>
                        <div class="row extrapadding">
                            <!-- Phone -->
                            <div class="form-row col-sm-12 thinpadding first last">
                                <input type="text" name="phone" class="form-control" placeholder="Phone Number" value="{{$phone}}" required>
                            </div>
                        </div>

                    </div>

                    <div class="panel-heading text-left extrapadding">Payment Information</div>
                    <div id="payment_info" class="panel-body font16">
                        <div class="row form-group extrapadding">
                            <div class="col-xs-6 thinpadding first">
                                <label class="select">
                                    <select type="select" class="form-control" required>
                                        <option v-for="card in cards" value="@{{ card }}">@{{ card }}</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <div class="row form-group extrapadding">
                            <div class="col-xs-12 thinpadding first last">
                                <input type="text" class="form-control card-number" maxlength="19" placeholder="Card Number" required onblur="
                                      cc_number_saved = this.value;
                                      this.value = this.value.replace(/[^\d]/g, '');
                                      if(!checkLuhn(this.value)) {
                                        $('.bad_cc').slideDown();
                                      } else $('.bad_cc').slideUp();"
                                    onfocus="
                                      if(this.value != cc_number_saved) this.value = cc_number_saved;" lazy>
                            </div>
                        </div>
                        <div class="row form-group extrapadding bad_cc" style="display:none">
                            <div class="col-xs-12 help-block thinpadding">
                                Sorry, that is not a valid number - please try again!
                            </div>
                        </div>
                        <div class="row form-group extrapadding">
                            <div class="col-xs-5 thinpadding first">
                                <label class="select">
                                    <select type="select" v-model="expiry_month" @change="checkDate" class="form-control card-expiry-month" required>
                                        <option value="">Expiration Month</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </label>
                            </div>
                            <div class="col-xs-4 thinpadding">
                                <label class="select">
                                    <select type="select" v-model="expiry_year" @change="checkDate" class="form-control card-expiry-year" required>
                                        <option value="">Expiration Year</option>
                                        <option value="16">2016</option>
                                        <option value="17">2017</option>
                                        <option value="18">2018</option>
                                        <option value="19">2019</option>
                                        <option value="20">2020</option>
                                        <option value="21">2021</option>
                                        <option value="22">2022</option>
                                        <option value="23">2023</option>
                                        <option value="24">2024</option>
                                    </select>
                                </label>
                            </div>
                            <div class="col-xs-3 thinpadding last">
                                <input type="text" class="form-control card-cvc" pattern="[0-9]*" required placeholder="CVC" lazy>
                            </div> 
                        </div>
                        <div class="row form-group extrapadding" v-show="bad_expiry || bad_expiry2">
                            <div class="col-xs-12 help-block thinpadding" v-show="bad_expiry" transition="expand">
                                Expiration date should not be in the past.
                            </div>
                            <div class="col-xs-12 help-block thinpadding" v-show="bad_expiry2" transition="expand">
                                Expiration date is required.
                            </div>
                        </div>
                        <div class="row form-group extrapadding"
                             @if($prefilledCoupon)
                                style="display:none"
                             @endif
                        >
                            <div class="col-xs-6 col-sm-5 thinpadding first">
                                <label class="select">
                                    <select type="select" class="form-control" name="promotype" @change="validatePromo" v-model="promotype">
                                        <!-- <option value="" selected>Code type</option>-->
                                        <!-- <option v-for="code in promos" :value="code.key">@{{ code.label }}</option> -->
                                        <option value="coupon">Coupon</option>
                                        <option value="referral">Referral code</option>
                                        <option value="giftcard">Gift card number</option>
                                    </select>
                                </label>
                            </div>
                            <div class="col-xs-6 col-sm-5 thinpadding last">
                                <input type="hidden" name="product_cost" value="{{ $product->cost }}" v-model="product_cost">
                                <input type="text" name="promocode" class="form-control" v-model="promocode" @keyup="validatePromo" value="{{$prefilledCoupon}}">
                            </div>
                            <div class="hidden-xs col-sm-2 thinpadding last" style="line-height: 42px">
                                <a data-toggle="tooltip" data-placement="right" data-title="Lorem ipsum." class="sidelink">what's this?</a>
                            </div>
                        </div>
                        <div v-show="wrongCode && promocode != ''" transition="expand">
                            <div class="col-xs-5 col-sm-4 thinpadding first"></div>
                            <div class="col-xs-7 col-sm-8 row thinpadding">Sorry, this code is not valid.</div>
                        </div>
                    </div>

                </div>
                
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading text-left nopa">Order Summary</div>
                    <div class="panel-body">
                        <div class="row padbottom">
                            <div class="col-sm-7">
                                <h5>PLAN TYPE</h5>
                                @if ($children == 0) Adult @else Family, {{ $children }} children @endif <a href="{{ route('register.select_plan') }}" class="sidelink">(change)</a>
                            </div>
                            <div class="col-sm-5">
                                <!-- <h5>DELIVERY DAY</h5>
                                Wednesday <a href="{{ URL::route('register.preferences') }}" class="sidelink">(change)</a> -->
                            </div>
                        </div>
                        <div class="row padbottom">
                            <div class="col-sm-7">
                                <h5>DIETARY PROFILE</h5>
                                {{ $plantype }} <br>
                                {{ $dietprefs }} <a href="{{ route('register.preferences') }}" class="sidelink">(change)</a>
                            </div>
                            <div class="col-sm-5">
                                <h5>FIRST DELIVERY</h5>
                                {{ date('F j', strtotime($start_date)) }} <a href="{{ route('register.preferences') }}" class="sidelink">(change)</a>
                            </div>
                        </div>
                        <div class="row padbottom">
                            <div class="col-sm-12">
                                <h5>DELIVER TO YOUR HOME:</h5>
                                <span id="addy1" class="hide">{{ $firstname }}</span><span id="addy2" class="hide">{{ $lastname }}</span>
                                <span id="addy3">{{ $address }}</span>, @if ($address2) <span id="addy4">{{ $address2 }}</span>, @endif <span id="addy5">{{ $city }}</span>, <span id="addy6">{{ $state }}</span> <span id="addy7">{{ $zip }}</span> <a href="{{ route('register.delivery') }}" class="sidelink">(change)</a><span id="addy8" class="hide">{{ $phone }}</span>
                            </div>
                        </div>
                        
                        <p>&nbsp;</p>

                        <div class="row padtop font16">
                            <div class="panel-heading text-left">order TOTAL FOR {{ date('F j', strtotime($start_date)) }}</div>
                            <div class="panel-body">
                                <div class="col-xs-12 col-sm-8 thinpadding">
                                    <div class="col-xs-7 nosidepadding eh">{{ $product->product_description }} </div>
                                    <div class="col-xs-5 text-right nosidepadding eh bottom"> ${{ $product->cost }}</div>
                                </div>
                                @if ($glutenfree == 'checked')
                                <div class="col-xs-12 col-sm-8 thinpadding">
                                    <div class="col-xs-7 nosidepadding">Gluten free</div>
                                    <div class="col-xs-5 nosidepadding text-right">$3.00</div>
                                </div>
                                @endif
                                <div id="code" class="col-xs-12 col-sm-8 thinpadding" v-show="hasCode">
                                    <div class="col-xs-7 nosidepadding label2">Referral code</div>
                                    <div id="discount" class="col-xs-5 nosidepadding text-right discount">-$XX.XX</div>
                                </div>
                                <div class="col-xs-12 col-sm-8 thinpadding total">
                                    <div class="col-xs-7 nosidepadding">TOTAL</div>
                                    <div id="totalcost" class="col-xs-5 nosidepadding text-right">${{ $product->cost }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row padtop">
                            <div class="col-xs-12">
                                <div style="display: inline-block" class="text-left">
                                    <button class="btn btn-primary" id="submitBtn" @click="checkDate">
                                        Place Order
                                    </button>
                                    <div id="submitErrors"></div>
                                    <div class="disclaimer text-left padtop">By clicking “Place Order” you agree to purchasing a continuous subscription, receiving deliveries and being billed to your designated payment method weekly, unless you a skip a delivery through your Delivery Schedule page or cancel your subscription.  You may cancel your subscription by contacting us and following the instructions in our response, on or before the “Changeable By” date reflected in your Account Settings. For more information see our Terms of Use and FAQ.</div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
<script>
$(function() {
    $('.checkbox').on('click', '#same_as_delivery', function() {
        if ($(this).prop("checked") == true) {
            $('input[name=firstname]').val($('#addy1').text());
            $('input[name=lastname]').val($('#addy2').text());
            $('input[name=address]').val($('#addy3').text());
            $('input[name=address_2]').val($('#addy4').text());
            $('input[name=city]').val($('#addy5').text());
            $('select[name=state]').val($('#addy6').text());
            $('input[name=zip]').val($('#addy7').text());
            $('input[name=phone]').val($('#addy8').text());
        } else {
            $('input[name=firstname], input[name=lastname], input[name=address], input[name=address_2], input[name=city], select[name=state], input[name=zip], input[name=phone]').val('');
        }
    });
@if($prefilledCoupon)
    document.getElementById('ValidatePromoElement').dispatchEvent(new Event('click'));
@endif
//    console.log();
//    Vue.methods.validatePromo();

});
</script>
@endsection
