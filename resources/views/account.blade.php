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
            <div class="sidebar col-sm-4 col-md-3" style="padding-right: 0">

                <div class="panel panel-default panel-flush">
                    <div class="panel-body">

                        <ul class="nav nav-sidebar spark-settings-stacked-tabs" role="tablist">
                                
                            <li role="presentation" class="active">
                                <a href="#plan_details" aria-controls="plan_details" role="tab" data-toggle="tab">
                                    <i class="icon icon-silverware"></i>Plan Details
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#delivery_info" aria-controls="delivery_info" role="tab" data-toggle="tab">
                                    <i class="icon icon-truck"></i>Delivery Information
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#delivery_history" aria-controls="delivery_history" role="tab" data-toggle="tab">
                                    <i class="icon icon-truck"></i>Delivery History
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#account_info" aria-controls="account_info" role="tab" data-toggle="tab">
                                    <i class="icon icon-user"></i>Account Information
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#payment_info" aria-controls="payment_info" role="tab" data-toggle="tab">
                                    <i class="icon icon-creditcard"></i>Payment Information
                                </a>
                            </li>
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
            <div class="main col-sm-8 col-md-9">
                <div class="tab-content">

                    <!-- Plan Details -->
                    <div role="tabpanel" class="tab-pane active" id="plan_details">

                        <h2>Plan Details</h2>
                        <a href="#" class="edit-link" data-toggle="modal" data-target="#editPlan"><i class="fa fa-pencil"></i> Edit</a>
                        <div v-if="user">

                            <h4>Meals</h4>

							<?php
							//split the sku into a string 0202030000
							$sku = str_split($userProduct->sku,2);
							
							if ($sku[0]=="01"){
								$BoxType = "Vegetarian";
								$BoxSelectVeg = true;
								$BoxSelectOmn = false;
							}
							if ($sku[0]=="02"){
								$BoxType = "Omnivore";
								$BoxSelectVeg = false;
								$BoxSelectOmn = true;
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
							$prefs = $userSubscription->dietary_preferences;
                            
							if (strpos($prefs, 'Red meat') !== false) $redmeat = true; else $redmeat = false;
                            if (strpos($prefs, 'Poultry') !== false) $poultry = true; else $poultry = false;
                            if (strpos($prefs, 'Fish') !== false) $fish = true; else $fish = false;
                            if (strpos($prefs, 'Lamb') !== false) $lamb = true; else $lamb = false;
                            if (strpos($prefs, 'Pork') !== false) $pork = true; else $pork = false;
                            if (strpos($prefs, 'Shellfish') !== false) $shellfish = true; else $shellfish = false;
                            if (strpos($prefs, 'Nut Free') !== false) $nutfree = true; else $nutfree = false;
                            if (strpos($prefs, 'Gluten Free') !== false) $glutenfree = true; else $glutenfree = false;
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
                                <div class="col-sm-8">Tuesday</div>
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
                                            <div class="row">
                                                <div class="col-sm-3" style="line-height: 55px"><b>Gluten free*?</b></div>
                                                <div class="col-sm-9"><span class="checkbox" style="margin-left: -10px">{!! Form::checkbox('prefs[]', '9', $glutenfree, array('id'=>'glutenfree', 'class'=>'form-control')) !!} <label for="glutenfree" class="inline">Yes</label> <span class="footnote">* Gluten free meal plans are an additional $1.50 per adult meal.</span></div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3" style="line-height: 42px"><b>Nut free?</b></div>
                                                <div class="col-sm-9">
                                                    <span class="checkbox nomargin" style="margin-left: -10px">{!! Form::checkbox('prefs[]', '7', $nutfree, array('id'=>'nutfree', 'class'=>'form-control')) !!} <label for="nutfree" class="inline">Yes</label></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-3"><b>Dietary Preferences</b></div>
                                                <div class="col-sm-9">
                                                    <div>Uncheck the foods you don’t eat below:</div>
                                                    <div class="col-xs-4 checkbox nopadding" style="margin-left: -10px">
                                                        {!! Form::checkbox('prefs[]', '1', $redmeat, array('id'=>'redmeat', 'class'=>'form-control pref', '@click'=>'selectOmni', 'v-model'=>'prefs.redmeat')) !!} <label for="redmeat">Red Meat</label>
                                                        {!! Form::checkbox('prefs[]', '2', $poultry, array('id'=>'poultry', 'class'=>'form-control pref', '@click'=>'selectOmni', 'v-model'=>'prefs.poultry')) !!} <label for="poultry">Poultry</label>
                                                    </div>
                                                    <div class="col-xs-4 checkbox nopadding">
                                                        {!! Form::checkbox('prefs[]', '3', $fish, array('id'=>'fish', 'class'=>'form-control pref', '@click'=>'selectOmni', 'v-model'=>'prefs.fish')) !!} <label for="fish">Fish</label>
                                                        {!! Form::checkbox('prefs[]', '4', $lamb, array('id'=>'lamb', 'class'=>'form-control pref', '@click'=>'selectOmni', 'v-model'=>'prefs.lamb')) !!} <label for="lamb">Lamb</label>
                                                    </div>
                                                    <div class="col-xs-4 checkbox nopadding">
                                                        {!! Form::checkbox('prefs[]', '5', $pork, array('id'=>'pork', 'class'=>'form-control pref', '@click'=>'selectOmni', 'v-model'=>'prefs.pork')) !!} <label for="pork">Pork</label>
                                                        {!! Form::checkbox('prefs[]', '6', $shellfish, array('id'=>'shellfish', 'class'=>'form-control pref', '@click'=>'selectOmni', 'v-model'=>'prefs.shellfish')) !!} <label for="shellfish">Shellfish</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row padbottom">
                                                <div class="col-sm-3"></div>
                                                <div class="col-sm-9">
                                                    You will receive a mixture of: @{{ concatPrefs }} <span v-if="plan_type == 'Omnivore Box'">and</span> vegetarian dishes.
                                                </div>
                                            </div>
                                            <div class="row padbottom" style="line-height: 42px">
                                                <div class="col-sm-3"><b>Delivery Day</b></div>
                                                <div class="col-sm-9">Boxes are delivered on Tuesdays.</div>
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
                                                    {!! Form::text('zip', $shippingAddress->shipping_zip, array('class' => 'form-control')) !!}
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
                    
                    <!-- Delivery History -->
                    <div role="tabpanel" class="tab-pane fade" id="delivery_history">

                        <h2>Delivery History <a href="/delivery-schedule" class="sidelink alt">see upcoming delivery schedule</a></h2>
                        
                        <div v-if="user">

                            <?php function cmp($a, $b){
                                return strcmp($a->ship_date, $b->ship_date);
                            }
                            usort($shipments, "cmp");
                            //var_dump($shipments); ?>
                        
                            @foreach ($shipments as $shipment)

                                @if (count($shipment) > 0) 
                                
                                    <div class="week">
                                        <h4>{{ $shipment->ship_date }}
                                            
                                            <div class="subtitle">
                                                @if ($shipment->cost != 0) Order Total: ${{ number_format( $shipment->cost, 2 ) }} @endif
                                                <span class="promo_note">Credit Card</span>
                                            </div>
                                        </h4>
                                        <div class="row">
                                            
                                            @foreach ($shipment->menus as $menu)
                                            <div class="col-xs-4">
                                                @if($menu->menu()->first()->image)
                                                <img src="{{$menu->menu()->first()->image}}" />
                                                @else
                                                <img height="100px" src="/img/foodpot.jpg"  class="center-block" />
                                                @endif
                                                <p class="caption">{{$menu->menu()->first()->menu_title}}<br/>
                                                    <em>{{$menu->menu()->first()->menu_description}}</em>
                                                </p>
                                            </div>
                                            @endforeach

                                        
                                        </div>
                                    </div>
                                    

                                @endif

                            @endforeach
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
                                <div class="col-sm-4"><b>Password</b></div>
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
					<?php
					
					//loop over referrals to get count
					$outstanding_count = 0;
					$new_sub_count = 0;
					foreach ($referrals as $ref) {
					
						if ($ref->did_subscribe==1) {
							$new_sub_count++;
						}else{
							$outstanding_count++;
						}
						
					}
					
					
					?>


                    <div role="tabpanel" class="tab-pane fade" id="referrals">

                        <h2>Customer Referrals</h2>
                        
                        <div v-if="user">
                            <h6 class="alt">Refer-A-Friend Tracker</h6>
                            Get Your Next Box Free! Refer 3 friends and your next box is on us.

                            <div class="referrals_status row">
                                <div class="col-xs-4 nosidepadding item @if ($new_sub_count > 0) on @endif"><span><i class="icon icon-pot"></i><p>1</p></span></div>
                                <div class="col-xs-4 nosidepadding item @if ($new_sub_count > 1) on @endif"><span><i class="icon icon-pot"></i><p>2</p></span></div>
                                <div class="col-xs-4 nosidepadding item last @if ($new_sub_count > 2) on @endif"><span><i class="icon icon-pot"></i><p>3</p></span></div>
                            </div>
                            
                            <div class="row">

                                <div class="col-md-4">
                                    
                                    <div class="sidebar">
                                        <h6>SEND A NEW REFERRAL TO:</h6>

                                        <form method="POST" action="{{ url('account') }}/{{ $user->id }}" accept-charset="UTF-8" class="meals">
                                            
                                                    {{ csrf_field() }}
                                        
                                            <input type="hidden" name="user_id" value="{{$user->id}}" />
                                            <input type="hidden" name="update_type" value="referrals" />

                                            <div class="padding">
                                                {!! Form::text('name', '', array('class' => 'form-control', 'placeholder' => 'NAME')) !!}
                                            </div>
                                            <div class="padbottom">
                                                {!! Form::email('email', '', array('class' => 'form-control', 'placeholder' => 'EMAIL')) !!}
                                            </div>
                                            <div class="padbottom">
                                                {!! Form::textarea('message', 'I thought you\'d love One Potato, a meal delivery service for busy families. Lorem ipsum dolor sit amet...', array('class' => 'form-control')) !!}
                                            </div>
                                            
                                            <div class="padbottom">
                                                <button type="submit" class="btn btn-primary">Send Message</button>
                                            </div>

                                        </form>
                                    </div>
                                </div>

                                <div class="col-md-8">

                                    <h6 class="padbottom">SENT INVITES</h6>
                                    
                                    <div class="referrals bordertop">
                                        @foreach ($referrals as $outstanding) 
                                        <div class="row nomargin padding borderbottom">
                                            <div class="col-xs-3 nosidepadding text"><b>{{$outstanding->friend_name}}</b></div>
                                            <div class="col-xs-6 nosidepadding text">{{$outstanding->referral_email}}</div>
                                            
                                            @if ($outstanding->did_subscribe!=1)
                                                <div class="col-xs-3 nosidepadding status"><button type="button" class="btn btn-primary small">resend</button></div>
                                            @elseif ($outstanding->did_subscribe==1)
                                                <div class="col-xs-3 nosidepadding status footnote text-left">redeemed on {{ date('n/j/y',strtotime($outstanding->referral_applied)) }}</div>
                                            @endif
                                            
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- .row -->
    </div>
</account>
@endsection
