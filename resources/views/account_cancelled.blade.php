@extends('spark::layouts.app')

@section('scripts')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">Stripe.setPublishableKey("{{ env('STRIPE_KEY') }}");</script>
<script>
/*stripe code*/
$(document).ready(function() {

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
                Your account is closed now.
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <h1>Reactivate Your Account</h1>
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

				                {{--<h2>Cancel Account</h2>--}}
								<div><p>Your account is currently closed. {{ $reactivateMessage }}
                                        If you’d like to resume your deliveries, click “Reactivate Account” below."</p></div>

									<form method="POST" action="/account_reactivate" accept-charset="UTF-8" id="cancel">
										{{ csrf_field() }}
										<input type="hidden" name="user_id" value="{{$user->id}}" />
									 <div class="row padding"><button type="submit" class="btn btn-primary">REACTIVATE ACCOUNT</button></div>
								</form>

                                <div><p style="font-style: italic;">By clicking “Reactivate Account,” you agree you are purchasing a continuous subscription and will receive weekly deliveries billed to your designated payment method. You can skip a delivery on our website, or cancel your subscription by contacting us and following the instructions we provide you in our response, on or before the “Changeable By” date reflected in your Account Settings. For more information see our Terms of Use and FAQs.</p></div>

				            </div>
						<!--end cancel account -->

                </div>


						


            </div>

		

        </div><!-- .row -->

		 


    </div>
</account>
@endsection
