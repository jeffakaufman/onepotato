@extends('spark::layouts.app')

@section('register_nav')
<script>
$('#register5').addClass('active');
</script>
@endsection

@section('content')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">Stripe.setPublishableKey("pk_test_JnXPsZ2vOrTOHzTEHd6eSi92");</script>
<script>
/*stripe code*/

$(document).ready(function() {

	// Watch for a form submission:
	$("#payment-form").submit(function(event) {

		// Flag variable:
		//var error = false;

		// disable the submit button to prevent repeated clicks:
		$('#submitBtn').attr("disabled", "disabled");

		// Get the values:
		var ccNum = $('.card-number').val(), cvcNum = $('.card-cvc').val(), expMonth = $('.card-expiry-month').val(), expYear = $('.card-expiry-year').val();
		
		console.log (ccNum);
		
		
		// Validate the number:
		if (!Stripe.card.validateCardNumber(ccNum)) {
			error = true;
			console.log ('The credit card number appears to be invalid.');
		}

		// Validate the CVC:
		if (!Stripe.card.validateCVC(cvcNum)) {
			error = true;
			console.log ('The CVC number appears to be invalid.');
		}

		// Validate the expiration:
		if (!Stripe.card.validateExpiry(expMonth, expYear)) {
			error = true;
			console.log ('The expiration date appears to be invalid.');
		}

		// Validate other form elements, if needed!
		error = false;
		// Check for errors:
		if (!error) {

			// Get the Stripe token:
			Stripe.card.createToken({
				number: ccNum,
				cvc: cvcNum,
				exp_month: expMonth,
				exp_year: expYear
			}, stripeResponseHandler);

		}
		
		
		// Prevent the form from submitting:
		return false;

	}); // Form submission

}); // Document ready.

// Function handles the Stripe response:
function stripeResponseHandler(status, response) {

	// Check for an error:
	//if (response.error) {

	//	reportError(response.error.message);

	//} else { // No errors, submit the form:

	  var f = $("#payment-form");

	  // Token contains id, last4, and card type:
	  var token = response['id'];

	  console.log(token);

	  // Insert the token into the form so it gets submitted to the server
	  f.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

	  // Submit the form:
	  f.get(0).submit();

	//}

} // End of stripeResponseHandler() function.


</script>


<payment :user="user" inline-template>
    <div class="container">

       	<form class="form-horizontal" role="form" id="payment-form" method="post"  action="{{ url('/register/payment') }}">
							 {{ csrf_field() }}

							<input type="hidden" name="user_id" value="{{ $user->id }}" />

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
                                <div class="checkbox nosidepadding nomargin"><input id="same_as_delivery" type="checkbox" name="same_as_delivery" value="Same as Delivery"> <label for="same_as_delivery">Same as Delivery</label></div>
                            </div>
                        </div>

                        <div class="panel-heading text-left extrapadding">Address</div>
                        <div class="panel-body font16">
                        
                            <div class="row extrapadding">

                                <!-- First Name -->
                                <div class="form-row col-sm-6 thinpadding first">
                                    <input type="text" class="form-control" name="firstname" v-model="registerForm.firstname" placeholder="First Name" autofocus>
                                </div>

                                <!-- Last Name -->
                                <div class="form-row col-sm-6 thinpadding last">
                                    <input type="text" class="form-control" name="lastname" v-model="registerForm.lastname" placeholder="Last Name" autofocus>
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- Address -->
                                <div class="form-row col-sm-6 thinpadding first">
                                    <input type="text" name="address" class="form-control" v-model="registerForm.address" lazy placeholder="Address">
                                </div>

                                <!-- Address Line 2 -->
                                <div class="form-row col-sm-6 thinpadding last">
                                    <input type="text" name="address_2" class="form-control" lazy placeholder="Address Line 2">
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- City -->
                                <div class="form-row col-sm-6 thinpadding first">
                                    <input type="text" name="city" class="form-control" lazy placeholder="City">
                                </div>

                                <!-- State & ZIP Code -->
                                <div class="form-row col-sm-4 thinpadding">
                                    <input type="text" name="state" class="form-control" placeholder="State" lazy>
                                </div>
                                <!-- Zip Code -->
                                <div class="form-row col-sm-2 thinpadding last">
                                    <input type="text" name="zip" class="form-control" placeholder="Zip" lazy>
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- Phone -->
                                <div class="form-row col-sm-12 nosidepadding">
                                    <input type="text" name="phone" class="form-control" placeholder="Phone Number" lazy>
                                </div>
                            </div>

                        </div>

                        <div class="panel-heading text-left extrapadding">Payment Information</div>
                        <div id="payment_info" class="panel-body font16">
                            <div class="row form-group extrapadding">
                                <div class="col-xs-6 nosidepadding">
                                    <select type="select" class="form-control">
                                        <option v-for="card in cards" value="@{{ card }}">@{{ card }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group extrapadding">
                                <div class="col-xs-12 nosidepadding">
                                    <input type="text" placeholder="Card Number" lazy>
									
                                </div>
                            </div>
                            <div class="row form-group extrapadding">
                                <div class="col-xs-6 thinpadding first">
                               
									<input type="text" class="form-control card-expiry-month" value="">
                                </div>
                                <div class="col-xs-4 thinpadding">
                                    <input type="text" class="form-control card-expiry-year" value="">
                                </div>
                                <div class="col-xs-2 thinpadding last">
                                    <input type="text" class="form-control card-cvc" placeholder="CVC" lazy>
								
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
</form>
            </div>
        </div>
    </div>
</payment>
@endsection
