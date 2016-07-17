@extends('spark::layouts.app-admin', ['menuitem' => 'users'])

@section('page_header')

@include('menu-edit')
    <h1>
        {{ $user->name }}'s Payments
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection
@section('content')
<home :menu="menu" inline-template>

<!--temp CSS-->
<style>
	
</style>
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

<!--end temp CSS-->
    <div class="container">
	
		<!--page sub nav-->
		@include('admin-menu',['submenu' => 'payment'])
		
	
		
		
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">User Information</div>

                    <div class="panel-body">
						<span class="menu_id">{{ $user->id }}</span>
                        <span class="menu_title">{{ $user->name }}</span>
						<span style="padding-left:10px;">{{ $user->email}}</span>
                    </div>
                </div>
            </div>
        </div>


		<!--edit form -->
		
	<form method="POST" class="form-horizontal" id="payment-form">
	
 {{ csrf_field() }}
	<input type="hidden" name="user_id" value="{{ $user->id }}" />
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default billingaddress">
            		<div class="panel-heading">Payment Information</div>
					<div class="panel-body">
		            	<div class="form-group">
			                <label for="menu_title" class="col-sm-3 control-label">Card Number</label>

			                <div class="col-sm-6">
			                    <input type="text" class="form-control card-number" value="">
			                </div>
						</div>
						<div class="form-group">
			                <label for="menu_title" class="col-sm-3 control-label">Exp Month</label>

			                <div class="col-sm-6">
			                    <input type="text" class="form-control card-expiry-month" value="">
			                </div>
						</div>
							<div class="form-group">
				                <label for="menu_title" class="col-sm-3 control-label">Exp Year</label>

				                <div class="col-sm-6">
				                    <input type="text" class="form-control card-expiry-year" value="">
				                </div>
							</div>
								<div class="form-group">
					                <label for="menu_title" class="col-sm-3 control-label">CVC</label>

					                <div class="col-sm-6">
					                    <input type="text" class="form-control card-cvc"  value="">
					                </div>
								</div>
								<div class="form-group">
					                <div class="col-sm-offset-3 col-sm-6">
					                    <button type="submit" class="btn btn-default">
					                        <i class="fa fa-plus"></i> Update Payment Information
					                    </button>
					                </div>
					            </div>
			
					</div>
		</div>
	</div>

</div>
		
		
	


	

	
		        </form>
		@include('csr-notes')
			
		    </div>

			 

		
		<!-- end edit form -->

    </div>
</home>
@endsection
