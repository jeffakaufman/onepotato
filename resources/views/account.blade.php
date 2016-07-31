@extends('spark::layouts.app')

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
<account :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-xs-12">
                <h1>Account Settings</h1>
            </div>
        </div>

        <!-- .row -->
        <div class="row">
            <!-- Tabs -->
            <div class="sidebar col-md-4">

                <div class="panel panel-default panel-flush">
                    <div class="panel-body">

                        <ul class="nav nav-sidebar spark-settings-stacked-tabs" role="tablist">
                                
                            <!-- Plan Details Link -->
                            <li role="presentation" class="active">
                                <a href="#plan_details" aria-controls="plan_details" role="tab" data-toggle="tab">
                                    <i class="icon icon-silverware"></i>Plan Details
                                </a>
                            </li>

                            <!-- Delivery Information Link -->
                            <li role="presentation">
                                <a href="#delivery_info" aria-controls="delivery_info" role="tab" data-toggle="tab">
                                    <i class="icon icon-truck"></i>Delivery Information
                                </a>
                            </li>
                            

                            <!-- Account Information Link -->
                            <li role="presentation">
                                <a href="#account_info" aria-controls="account_info" role="tab" data-toggle="tab">
                                    <i class="icon icon-user"></i>Account Information
                                </a>
                            </li>

                            <!-- Payment Method Link -->
                            <li role="presentation">
                                <a href="#payment_info" aria-controls="payment_info" role="tab" data-toggle="tab">
                                    <i class="icon icon-creditcard"></i>Payment Information
                                </a>
                            </li>

                            <!-- Referrals Link -->
                            <li role="presentation">
                                <a href="#referrals" aria-controls="referrals" role="tab" data-toggle="tab">
                                    <i class="icon icon-talkbubble"></i>Referrals
                                </a>
                            </li>

                        </ul>

                    </div>
                </div>
                
            </div>

            <!-- Tab Panels -->
            <div class="main col-md-8">
                <div class="tab-content">

                    <!-- Plan Details -->
                    <div role="tabpanel" class="tab-pane active" id="plan_details">

                        <h2>Plan Details</h2>
                        <a href="#" class="edit-link" data-toggle="modal" data-target="#editPlan"><i class="fa fa-pencil"></i> Edit</a>
                        <div v-if="user">

                            <h4>Meals</h4>

							<?php
							//split the sku into a string
							$sku = str_split($userProduct->sku,2);
							
							if ($sku[0]=="01"){
								$BoxType = "Vegetarian";
								$BoxSelectVeg = "true";
								$BoxSelectOmn = "false";
							}
							if ($sku[0]=="02"){
								$BoxType = "Omnivore";
								$BoxSelectVeg = "false";
								$BoxSelectOmn = "true";
							}
							
							if ($sku[2]=="00"){
								$PlanType = "Adult Plan";
								$PlanTypeSelect = "adult";
								$FamilySize = "0 Children";
								$ChildSelect = 0;
							}else{
									$PlanType = "Family";
									$PlanTypeSelect = "family";
									$FamilySize = (integer)$sku[2] . " Children";
									$ChildSelect = (integer)$sku[2];
							}
							
							
							?>

                            <div class="row padding">
                                <div class="col-sm-4"><b>Plan Type</b></div>
                                <div class="col-sm-8">{{$PlanType}}</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Family Size</b></div>
                                <div class="col-sm-8">{{$FamilySize}}</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Box Type</b></div>
                                <div class="col-sm-8">{{$BoxType}}</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Delivery Day</b></div>
                                <div class="col-sm-8">Wednesday</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Changeable By</b></div>
                                <div class="col-sm-8"></div>
                            </div>

                            <div id="editPlan" class="modal fade" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">MEALS</h4>
                                        </div>
                                     
										<form method="POST" action="{{ url('account') }}/{{ $user->id }}" accept-charset="UTF-8" class="meals">
										
									            {{ csrf_field() }}
									
										<input type="hidden" name="user_id" value="{{$user->id}}" />
										<input type="hidden" name="update_type" value="meals" />
                                        <div class="modal-body">
                                            <p>Changes will only apply to deliveries scheduled on or after Thursday, July 21st.</p>

                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 47px"><b>Plan Type</b></div>
                                                <div class="col-sm-9">
                                                    <label class="select inline">
                                                        {!! Form::select('plan_size', array('adult' => 'Adult Plan', 'family' => 'Family Plan'), $PlanTypeSelect, array('class' => 'form-control plan-type')) !!}
                                                        
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3"><b>Family Size</b></div>
                                                <div class="col-sm-9">Number of children: &nbsp; {!! Form::text('children', $ChildSelect, array('pattern' => '[0-9]*', 'class' => 'number')); !!}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-3" style="line-height: 42px"><b>Box Type</b></div>
                                                <div class="col-sm-9">
                                                    <div class="col-xs-6 col-md-4 radio nomargin nosidepadding">
                                                        {!! Form::radio('plan_type', 'Omnivore Box', $BoxSelectOmn, array('id'=>'plan_type1', '@click'=>'selectAllOmnivore', 'v-model'=>'plan_type')) !!} <label for="plan_type1">Omnivore Box</label>
                                                    </div>
                                                    <div class="col-xs-6 col-md-4 radio nomargin nosidepadding">
                                                        {!! Form::radio('plan_type', 'Vegetarian Box', $BoxSelectVeg, array('id'=>'plan_type2', '@click'=>'selectAllVegetarian', 'v-model'=>'plan_type')) !!} <label for="plan_type2">Vegetarian Box</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 55px"><b>Gluten free*?</b></div>
                                                <div class="col-sm-9"><span class="checkbox" style="margin-left: -10px"><input id="glutenfree" type="checkbox" name="glutenfree" value="1"> <label for="glutenfree" class="inline">Yes</label> <span class="footnote">* Gluten free meal plans are an additional $1.50 per adult meal.</span></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-3"><b>Dietary Preferences</b></div>
                                                <div class="col-sm-9">
                                                    <div>Uncheck the foods you don’t eat below:</div>
                                                    <div class="col-xs-3 checkbox nopadding" style="margin-left: -10px">
                                                        {!! Form::checkbox('prefs[]', '1', false, array('id'=>'redmeat', 'class'=>'form-control', '@click'=>'selectOmni', 'v-model'=>'redmeat')) !!} <label for="redmeat">Red Meat</label>
                                                        {!! Form::checkbox('prefs[]', '2', true, array('id'=>'poultry', 'class'=>'form-control', '@click'=>'selectOmni', 'v-model'=>'poultry')) !!} <label for="poultry">Poultry</label>
                                                    </div>
                                                    <div class="col-xs-3 checkbox nopadding">
                                                        {!! Form::checkbox('prefs[]', '3', true, array('id'=>'fish', 'class'=>'form-control', '@click'=>'selectOmni', 'v-model'=>'fish')) !!} <label for="fish">Fish</label>
                                                        {!! Form::checkbox('prefs[]', '4', false, array('id'=>'lamb', 'class'=>'form-control', '@click'=>'selectOmni', 'v-model'=>'lamb')) !!} <label for="lamb">Lamb</label>
                                                    </div>
                                                    <div class="col-xs-3 checkbox nopadding">
                                                        {!! Form::checkbox('prefs[]', '5', true, array('id'=>'pork', 'class'=>'form-control', '@click'=>'selectOmni', 'v-model'=>'pork')) !!} <label for="pork">Pork</label>
                                                        {!! Form::checkbox('prefs[]', '6', true, array('id'=>'shellfish', 'class'=>'form-control', '@click'=>'selectOmni', 'v-model'=>'shellfish')) !!} <label for="shellfish">Shellfish</label>
                                                    </div>
                                                    <div class="col-xs-3 checkbox nopadding">
                                                        {!! Form::checkbox('prefs[]', '7', true, array('id'=>'nuts', 'class'=>'form-control', '@click'=>'selectOmni', 'v-model'=>'nuts')) !!} <label for="nuts">Nuts</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3"></div>
                                                <div class="col-sm-9">
                                                    You will receive a mixture of:
                                                    <span v-show="redmeat"> red meat</span> 
                                                    <span v-show="fish"> fish</span> 
                                                    <span v-show="pork"> pork</span> 
                                                    <span v-show="poultry"> poultry</span> 
                                                    <span v-show="lamb"> lamb</span> 
                                                    <span v-show="shellfish"> shellfish</span> 
                                                    <span v-show="nuts"> nut</span> dishes.
                                                </div>
                                            </div>
                                            <div class="row padbottom" style="line-height: 42px">
                                                <div class="col-sm-3"><b>Delivery Day</b></div>
                                                <div class="col-sm-9">Boxes are delivered on Wednesdays.</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                     	</form>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->

                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <div role="tabpanel" class="tab-pane fade" id="delivery_info">

                        <h2>Delivery Information</h2>
                        <a href="#" class="edit-link" data-toggle="modal" data-target="#editDelivery"><i class="fa fa-pencil"></i> Edit</a>
                        <div v-if="user">

                            <h4>Address</h4>

                            <div class="row padding">
                                <div class="col-sm-4"><b>Location</b></div>
                                <div class="col-sm-8">{{ucwords($shippingAddress->address_type)}}</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Name</b></div>
                                <div class="col-sm-8">{{$shippingAddress->shipping_first_name}} {{$shippingAddress->shipping_last_name}}</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Address</b></div>
                                <div class="col-sm-8">
                                    {{$shippingAddress->shipping_address}}<br>
									@if ($shippingAddress->shipping_address_2)
                                    {{$shippingAddress->shipping_address_2}}<br>
									@endif
                                    {{$shippingAddress->shipping_city}}, {{$shippingAddress->shipping_state}} {{$shippingAddress->shipping_zip}}
                                </div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Phone</b></div>
                                <div class="col-sm-8">{{$shippingAddress->phone1}}</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Delivery Instructions</b></div>
                                <div class="col-sm-8">{{$shippingAddress->delivery_instructions}}</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Child 1 Birthday</b></div>
                                <div class="col-sm-8"></div>
                            </div>

                            <div id="editDelivery" class="modal fade" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">DELIVERY ADDRESS</h4>
                                        </div>
                                       		<form method="POST" action="{{ url('account') }}/{{ $user->id }}#delivery_info" accept-charset="UTF-8" class="delivery_address">

										            {{ csrf_field() }}

											<input type="hidden" name="user_id" value="{{$user->id}}" />
											<input type="hidden" name="update_type" value="delivery_address" />
                                        <div class="modal-body">
                                            <p>Changes will only apply to deliveries scheduled on or after Thursday, July 21st.</p>

                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 42px"><b>Address</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::text('address1', $shippingAddress->shipping_address, array('class' => 'form-control')) !!}
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 42px"><b>Address 2</b></div>
                                                <div class="col-sm-9">{!! Form::text('address2', $shippingAddress->shipping_address_2, array('class' => 'form-control')) !!}</div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 42px"><b>City</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::text('city', $shippingAddress->shipping_city, array('class' => 'form-control')) !!}
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 47px"><b>State</b></div>
                                                <div class="col-sm-9">
                                                    <label class="select">
                                                        {!! Form::select('state', array(
                                                            'AL'=>'Alabama',
                                                            'AK'=>'Alaska',
                                                            'AZ'=>'Arizona',
                                                            'AR'=>'Arkansas',
                                                            'CA'=>'California',
                                                            'CO'=>'Colorado',
                                                            'CT'=>'Connecticut',
                                                            'DE'=>'Delaware',
                                                            'DC'=>'District of Columbia',
                                                            'FL'=>'Florida',
                                                            'GA'=>'Georgia',
                                                            'HI'=>'Hawaii',
                                                            'ID'=>'Idaho',
                                                            'IL'=>'Illinois',
                                                            'IN'=>'Indiana',
                                                            'IA'=>'Iowa',
                                                            'KS'=>'Kansas',
                                                            'KY'=>'Kentucky',
                                                            'LA'=>'Louisiana',
                                                            'ME'=>'Maine',
                                                            'MD'=>'Maryland',
                                                            'MA'=>'Massachusetts',
                                                            'MI'=>'Michigan',
                                                            'MN'=>'Minnesota',
                                                            'MS'=>'Mississippi',
                                                            'MO'=>'Missouri',
                                                            'MT'=>'Montana',
                                                            'NE'=>'Nebraska',
                                                            'NV'=>'Nevada',
                                                            'NH'=>'New Hampshire',
                                                            'NJ'=>'New Jersey',
                                                            'NM'=>'New Mexico',
                                                            'NY'=>'New York',
                                                            'NC'=>'North Carolina',
                                                            'ND'=>'North Dakota',
                                                            'OH'=>'Ohio',
                                                            'OK'=>'Oklahoma',
                                                            'OR'=>'Oregon',
                                                            'PA'=>'Pennsylvania',
                                                            'RI'=>'Rhode Island',
                                                            'SC'=>'South Carolina',
                                                            'SD'=>'South Dakota',
                                                            'TN'=>'Tennessee',
                                                            'TX'=>'Texas',
                                                            'UT'=>'Utah',
                                                            'VT'=>'Vermont',
                                                            'VA'=>'Virginia',
                                                            'WA'=>'Washington',
                                                            'WV'=>'West Virginia',
                                                            'WI'=>'Wisconsin',
                                                            'WY'=>'Wyoming',
                                                        ), $shippingAddress->shipping_state, array('class' => 'form-control plan-type')) !!}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 42px"><b>Zip</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::text('zip', $shippingAddress->zip, array('class' => 'form-control')) !!}
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 42px"><b>Phone</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::text('phone', $shippingAddress->phone1, array('class' => 'form-control')) !!}
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3"><b>Delivery Instructions</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::textarea('delivery_instructions', 
                                                        $shippingAddress->delivery_instructions, 
                                                        array('class' => 'form-control')) !!}
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3"><b>Child 1 Birthday</b></div>
                                                <div class="col-sm-9">
                                                    <div class="col-xs-6 thinpadding first">
                                                        <label class="select">
                                                            {!! Form::select('child_bday1_month', array(
                                                                'January', 
                                                                'February', 
                                                                'March', 
                                                                'April', 
                                                                'May', 
                                                                'June', 
                                                                'July', 
                                                                'August', 
                                                                'September', 
                                                                'October', 
                                                                'November', 
                                                                'December'
                                                            ), 'April', array('class'=>'form-control')) !!}
                                                        </label>
                                                    </div>
                                                    <div class="col-xs-4 thinpadding last">
                                                        {!! Form::text('child_bday1_day', '17', array('class' => 'form-control')) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                       </form>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                            
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div role="tabpanel" class="tab-pane fade" id="account_info">

                        <h2>Account Information</h2>
                        <a href="#" class="edit-link" data-toggle="modal" data-target="#editAccount"><i class="fa fa-pencil"></i> Edit</a>
                        <div v-if="user">
                            
                            <h4>Account</h4>

                            <div class="row padding">
                                <div class="col-sm-4"><b>Name</b></div>
                                <div class="col-sm-8">{{$user->name}}</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Email</b></div>
                                <div class="col-sm-8">{{$user->email}}</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4">Password</div>
                                <div class="col-sm-8">****</div>
                            </div>

                            <div id="editAccount" class="modal fade" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">DELIVERY ADDRESS</h4>
                                        </div>
                                       		<form method="POST" action="{{ url('account') }}/{{ $user->id }}#account" accept-charset="UTF-8" class="account">

										            {{ csrf_field() }}

											<input type="hidden" name="user_id" value="{{$user->id}}" />
											<input type="hidden" name="update_type" value="account" />
                                        <div class="modal-body">

                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 47px"><b>First Name</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::text('first_name', $user->first_name, array('class' => 'form-control')) !!}
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3"><b>Last Name</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::text('last_name', $user->last_name, array('class' => 'form-control')) !!}
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 42px"><b>Email</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::email('email', $user->email, array('class' => 'form-control')) !!}
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 55px"><b>Password</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::password('password', array('class' => 'form-control')) !!}
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->

                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div role="tabpanel" class="tab-pane fade" id="payment_info">

                        <h2>Payment Information</h2>
                        <a href="#" class="edit-link" data-toggle="modal" data-target="#editPayment"><i class="fa fa-pencil"></i> Edit</a>
                        <div v-if="user">
                            
                            <h4>Credit Card</h4>

                            <div class="row padding">
                                <div class="col-sm-4"><b>Type</b></div>
                                <div class="col-sm-8">{{$user->card_brand}}</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Number (Last Four Digits)</b></div>
                                <div class="col-sm-8">{{$user->card_last_four}}</div>
                            </div>
                            

                            <div id="editPayment" class="modal fade" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">CREDIT CARD</h4>
                                        </div>
                                       	<form class="form-horizontal" role="form" id="payment-form" method="post"  action="{{ url('/account/') }}/{{$user->id}}">
	
											 {{ csrf_field() }}

										<input type="hidden" name="user_id" value="{{$user->id}}" />
										<input type="hidden" name="update_type" value="payment" />
															
                                        <div class="modal-body">

                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 42px"><b>Type</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::text('', '', array('class' => 'form-control')) !!}
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 42px"><b>Number</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::text('', '', array('class' => 'form-control card-number')) !!}
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 47px"><b>Expiration</b></div>
                                                <div class="col-sm-5">
                                                    <label class="select">
                                                        {!! Form::select('', array(
                                                            '01'=>'January', 
                                                            '02'=>'February', 
                                                            '03'=>'March', 
                                                            '04'=>'April', 
                                                            '05'=>'May', 
                                                            '06'=>'June', 
                                                            '07'=>'July', 
                                                            '08'=>'August', 
                                                            '09'=>'September', 
                                                            '10'=>'October', 
                                                            '11'=>'November', 
                                                            '12'=>'December'
                                                        ), '05', array('class'=>'form-control card-expiry-month')) !!}
                                                    </label>
                                                </div>
                                                <div class="col-sm-4">
                                                    {!! Form::text('', '', array('class' => 'form-control card-expiry-year')) !!}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-3" style="line-height: 42px"><b>CVC</b></div>
                                                <div class="col-sm-9">
                                                    {!! Form::text('', '', array('class' => 'form-control card-cvc')) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                      </form>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->

                        </div>

                    </div>

                    <!-- Referrals -->
                    <div role="tabpanel" class="tab-pane fade" id="referrals">

                        <h2>Customer Referrals</h2>
                        
                        <div v-if="user">

                            <div class="row">
                                <div class="col-sm-8"><h4 class="thin">You have <span class="highlight">2</span> outstanding referrals:</h4></div>
                                <div class="col-sm-4"><button type="submit" class="btn btn-primary small">resend all</button></div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Jason Stick</b></div>
                                <div class="col-sm-4">jason@yahoo.com</div>
                                <div class="col-sm-4"><a href="#" class="sidelink">resend</a></div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Molly Ringwald</b></div>
                                <div class="col-sm-4">molly@ringwald.com</div>
                                <div class="col-sm-4"><a href="#" class="sidelink">resend</a></div>
                            </div>

                            <h4 class="thin">You have referred <span class="highlight">4</span> new customers so far. Refer two more and receive another free box!</h4>

                            <div class="row padding">
                                <div class="col-sm-4"><b>Jason Stick</b></div>
                                <div class="col-sm-4">jason@yahoo.com</div>
                                <div class="col-sm-4 footnote text-left">redeemed on 12/15/15</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Molly Ringwald</b></div>
                                <div class="col-sm-4">molly@ringwald.com</div>
                                <div class="col-sm-4 footnote text-left">redeemed on 12/15/15</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Name</b></div>
                                <div class="col-sm-4">name@domain.com</div>
                                <div class="col-sm-4 footnote text-left">redeemed on 12/15/15</div>
                            </div>
                            <div class="row padding">
                                <div class="col-sm-4"><b>Name</b></div>
                                <div class="col-sm-4">name@domain.com</div>
                                <div class="col-sm-4 footnote text-left">redeemed on 12/15/15</div>
                            </div>

                            <h4>SEND A NEW REFERRAL TO:</h4>

                            {!! Form::open(array('url' => '/account', 'class' => 'referrals')) !!}

                                <div class="row padding">
                                    <div class="col-sm-3" style="line-height: 42px"><b>Name</b></div>
                                    <div class="col-sm-9">
                                        {!! Form::text('name', '', array('class' => 'form-control')) !!}
                                    </div>
                                </div>
                                <div class="row padbottom">
                                    <div class="col-sm-3" style="line-height: 42px"><b>Email</b></div>
                                    <div class="col-sm-9">
                                        {!! Form::email('email', '', array('class' => 'form-control')) !!}
                                    </div>
                                </div>
                                <div class="row padbottom">
                                    <div class="col-sm-3"><b></b></div>
                                    <div class="col-sm-9">
                                        {!! Form::textarea('message', '', array('class' => 'form-control')) !!}
                                    </div>
                                </div>
                                
                                <div class="row padbottom">
                                    <div class="col-sm-3"><b></b></div>
                                    <div class="col-sm-9">
                                        <button type="submit" class="btn btn-primary">Send Message</button>
                                    </div>
                                </div>

                            {!! Form::close() !!}

                        </div>
                    </div>

                </div>
            </div>
        </div><!-- .row -->
    </div>
</account>
@endsection
