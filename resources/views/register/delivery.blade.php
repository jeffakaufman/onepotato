<?php session_start();
    if( isset( $_SESSION['registered']) ) header("Location: /account");
?>
@extends('spark::layouts.app')

@section('register_nav')
<script>
$('#register4').addClass('active');
</script>
@endsection

@section('content')
<?php 
    if (Session::has('user_id')) $user_id = Session::get('user_id');
    if (Session::has('zip')) $zip = Session::get('zip');
    if (Session::has('children')) $children = Session::get('children');
    if (Session::has('plantype')) $plantype = Session::get('plantype');
    if (Session::has('dietprefs')) $dietprefs = Session::get('dietprefs');
    if (Session::has('start_date')) $start_date = Session::get('start_date');
    if (Session::has('delivery_loc')) $loc = Session::get('delivery_loc');
    if (Session::has('firstname')) $firstname = Session::get('firstname');
    if (Session::has('lastname')) $lastname = Session::get('lastname');
    if (Session::has('address')) $address = Session::get('address');
    if (Session::has('address2')) $address2 = Session::get('address2');
    if (Session::has('city')) $city = Session::get('city');
    if (Session::has('state')) $state = Session::get('state');
    if (Session::has('phone')) $phone = Session::get('phone');
    if (Session::has('instructions')) $instructions = Session::get('instructions');
?>
<delivery inline-template>
    <div id="planType">
        PLAN TYPE: 
        @if ($children == 0) Adult @else Family, {{ $children }} 

            @if ($children == 1) child
            @else children
            @endif 

        @endif <a href="{{ route('register.select_plan') }}" class="sidelink">(change)</a>
    </div>
    <div class="container">
        <!-- Application Dashboard -->
	
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Where shall we deliver your meals?
                            <div class="panel-subtitle">You can change the address and family information at any time in Account Management.</div>
                        </h1>
                    </div>
                </div>
            </div>
        </div>		
<form class="form-horizontal" role="form" method="post"  action="{{ url('/register/delivery') }}">
					 {{ csrf_field() }}

					<input type="hidden" name="start_date" value="{{$start_date}}" />
					<input type="hidden" name="user_id" value="{{ isset($user_id) ? $user_id : $user->id }}" />
                    <!-- <input type="hidden" name="children" value="{{ $children }}" /> -->
                    <!-- <input type="hidden" name="plantype" value="{{ $plantype }}" /> -->

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <a href="{{ route('register.preferences') }}" style="position: absolute; margin-top: -2em;">
                    <i class="fa fa-caret-left" aria-hidden="true"></i> BACK</a>
                <div class="panel panel-default panel-form">
        
                        <div class="panel-heading text-left extrapadding">Delivery Location <a data-toggle="tooltip" data-placement="right" data-title="Shipping is free and convenient. Meals are carefully packaged in an insulated, recyclable box so food stays fresh even if you’re not home when we deliver." class="sidelink">more details</a></div>
                        <div class="panel-body font16 nopadding">
                            <div class="row nowrap extrapadding">
                                <div class="col-xs-6 col-sm-4 radio thinpadding nomargin"><input id="delivery_home" type="radio" name="delivery_loc" value="home" @if (isset($loc) && $loc == 'home') checked @else checked @endif> <label for="delivery_home">Home</label></div>
                                <div class="col-xs-6 col-sm-4 radio thinpadding nomargin"><input id="delivery_busines" type="radio" name="delivery_loc" value="business" @if (isset($loc) && $loc == 'business') checked @endif> <label for="delivery_busines">Business</label></div>
                            </div>
                        </div>

                        <div class="panel-heading text-left extrapadding">Address</div>
                        <div class="panel-body font16">
                        
                            <div class="row extrapadding">

                                <!-- First Name -->
                                <div class="form-row col-sm-6 thinpadding first">
                                    <input type="text" class="form-control" name="firstname" placeholder="First Name" value="@if (isset($firstname)){{$firstname}}@endif" autofocus required>
                                </div>

                                <!-- Last Name -->
                                <div class="form-row col-sm-6 thinpadding last">
                                    <input type="text" class="form-control" name="lastname" placeholder="Last Name" value="@if (isset($lastname)){{$lastname}}@endif" required>
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- Address -->
                                <div class="form-row col-sm-6 thinpadding first">
                                    <input type="text" class="form-control" name="address" lazy placeholder="Address" value="@if (isset($address)){{$address}}@endif" required>
                                </div>

                                <!-- Address Line 2 -->
                                <div class="form-row col-sm-6 thinpadding last">
                                    <input type="text" class="form-control" name="address_line_2" lazy placeholder="Address Line 2" value="@if (isset($address2)){{$address2}}@endif">
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- City -->
                                <div class="form-row col-sm-6 thinpadding first">
                                    <input type="text" class="form-control" name="city" lazy placeholder="City" value="@if (isset($city)){{$city}}@endif" required>
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
                                    <input type="text" class="form-control" name="zip" placeholder="Zip" value="@if (isset($zip)){{$zip}}@endif" required>
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- Phone -->
                                <div class="form-row col-sm-12 thinpadding first last">
                                    <input type="text" class="form-control" name="phone" placeholder="Phone Number" value="@if (isset($phone)){{$phone}}@endif" required>
                                </div>
                            </div>

                        </div>

                        <div class="panel-heading text-left extrapadding">Special Delivery Instructions <a data-toggle="tooltip" data-title="Please be as specific as possible. Instructions such as “leave at door” should indicate the type of door (e.g. exterior or interior door) and include any other helpful context, such as a code to enter the building." class="sidelink">what's this?</a></div>
                        <div class="panel-body font16">
                            <div class="row extrapadding">
                                <div class="col-sm-12 thinpadding first last"><textarea name="delivery_instructions" class="form-control">@if (isset($instructions)){{ $instructions }}@endif</textarea></div>
                            </div>
                        </div>
<?php /*
                        @if ($children > 0)
                            <div class="panel-heading text-left extrapadding">Family Information <a data-toggle="tooltip" data-title="Lorem ipsum dolor" class="sidelink">what's this?</a>
                                <div class="panel-subtitle">We love celebrations! Share your child’s birthday and we will send a little surprise in time for their big day.</div>
                            </div>
                        
                            <div class="panel-body font16">

                                @for ($i = 1; $i <= $children; $i++)
                                    <div id="bday_select" class="row extrapadding">
                                        <div class="col-xs col-xs-2 thinpadding first field-label">
                                            Child {{ $i }}
                                        </div>
                                        <div class="col-xs col-xs-6 thinpadding">
                                            <label class="select">
                                                <select name="child_bday{{$i}}_month" type="select" class="form-control">
                                                    <option>Month</option>
                                                    <option v-for="month in months" value="@{{ month }}">@{{ month }}</option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="col-xs col-xs-4 thinpadding last">
                                            <label class="select">
                                                <select name="child_bday{{$i}}_day" type="select" class="form-control">
                                                    <option>Day</option>
                                                    @for($x=1;$x<=31;$x++)
                                                        <option value="{{ $x }}">{{ $x }}</option>;
                                                    @endfor
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                @endfor

                            </div>
                        @endif
*/?>                        

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
