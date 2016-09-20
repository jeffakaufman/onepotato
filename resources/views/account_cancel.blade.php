@extends('spark::layouts.app')

@section('scripts')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">Stripe.setPublishableKey("{{ env('STRIPE_KEY') }}");</script>
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

    $("#cancelReasonSelect").change(function() {
        switch($(this).val()) {
            case 'other':
                $("#specifyCancelReasonDiv").fadeIn();
                break;

            default:
                $("#specifyCancelReasonDiv").fadeOut();
                break;
        }
    });

    switch($("#cancelReasonSelect").val()) {
        case 'other':
            $("#specifyCancelReasonDiv").show();
            break;

        default:
            $("#specifyCancelReasonDiv").hide();
            break;
    }

}); // Document ready.

// Function handles the Stripe response:
function stripeResponseHandler(status, response) {

    // Check for an error:
    //if (response.error) {

    //  reportError(response.error.message);

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
@endsection

@section('content')
<account :user="user" inline-template>
    <div id="account" class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-xs-12">
                <h1>Account Settings</h1>
            </div>
        </div>

        <!-- .row -->
        <div class="row">
            <!-- Tabs -->
            <!-- Tab Panels -->
            <div class="main col-sm-8 col-md-9">
                <div class="tab-content">

                    <!-- Plan Details -->
						<!-- Cancel Account Information -->
				            <div role="tabpanel" class="tab-pane active" id="cancel">

				                <h2>Cancel Account</h2>
								<div><p>To cancel your account, please fill out the form below and click "Cancel Account."</p></div>
									<form method="POST" action="/accountcancel" accept-charset="UTF-8" id="cancel">
										{{ csrf_field() }}
										<input type="hidden" name="user_id" value="{{$user->id}}" />
				                    <div class="row padding">
				                        <div class="col-sm-4"><b>What's the primary reason for cancelling your account with us?</b></div>
				                        <div class="col-sm-8">{!! Form::select('cancel_reason', array('Delivery Is Unreliable or Inconvenient' => 'Delivery Is Unreliable or Inconvenient'
				                        									,'Meals Take Too Long To Prepare' => 'Meals Take Too Long To Prepare'
				                        									,'We\'re Traveling Or Moving' => 'We\'re Traveling Or Moving'
				                        									,'Too Much Packaging' => 'Too Much Packaging'
				                        									,'Don\'t Have Time To Cook Right Now' => 'Don\'t Have Time To Cook Right Now'
				                        									,'Child Portion Sizes Are Too Small' => 'Child Portion Sizes Are Too Small '
				                        									,'We Only Want To Cook 1 Or 2 Nights A Week' => 'We Only Want To Cook 1 Or 2 Nights A Week'
				                        									,'Too Expensive' => 'Too Expensive'
				                        									,'Recipes Don’t Meet Our Dietary Needs (E.G. Egg Allergy, Dairy Free)' => 'Recipes Don’t Meet Our Dietary Needs (E.G. Egg Allergy, Dairy Free)'
				                        									,'Recipes Are Too Adventurous For Our Family' => 'Recipes Are Too Adventurous For Our Family'
				                        									,'other' => 'Other')
				                        									, 'Delivery Is Unreliable or Inconvenient'
				                        									, array('class' => 'form-control plan-type', 'id'=>'cancelReasonSelect')) !!}</div>
				                    </div>
				                    <div class="row padding" id="specifyCancelReasonDiv" style="display:none;">
				                        <div class="col-sm-4"><b>Please specify:</b></div>
				                        <div class="col-sm-8">{!! Form::text('cancel_specify', '', array('class' => 'form-control', 'placeholder' => '')) !!}</div>
				                    </div>
				                    <div class="row padding">
				                        <div class="col-sm-4"><b>Is there anything else you want to tell us to improve?</b></div>
				                        <div class="col-sm-8"> {!! Form::textarea('cancel_suggestions', '', array('class' => 'form-control')) !!}</div>
				                    </div>
									 <div class="row padding"><p>{{ $cancelMessage }}</p><button type="submit" class="btn btn-primary">CANCEL ACCOUNT</button></div>
								</form>
				               
				            </div>
						<!--end cancel account -->

                </div>


						


            </div>

		

        </div><!-- .row -->

		 


    </div>
</account>
@endsection
