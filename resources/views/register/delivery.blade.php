@extends('spark::layouts.app')

@section('register_nav')
<script>
$('#register4').addClass('active');
</script>
@endsection

@section('content')
<delivery :user="user" inline-template>
    <div id="planType">
        PLAN TYPE: @if ($children == 0) Adult @else Family, {{ $children }} children @endif <a href="{{ URL::route('register_select_plan') }}" class="sidelink">(change)</a>
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
                                <div class="col-xs-4 radio nosidepadding nomargin"><input id="delivery_home" type="radio" name="delivery_loc" v-model="delivery_loc" value="Home" checked> <label for="delivery_home">Home</label></div>
                                <div class="col-xs-4 radio nosidepadding nomargin"><input id="delivery_busines" type="radio" name="delivery_loc" v-model="delivery_loc" value="Business"> <label for="delivery_busines">Business</label></div>
                            </div>
                        </div>

                        <div class="panel-heading text-left extrapadding">Address</div>
                        <div class="panel-body font16">
                        
                            <div class="row extrapadding">

                                <!-- First Name -->
                                <div class="form-row col-sm-6 thinpadding first">
                                    <input type="text" class="form-control" name="firstname" placeholder="First Name" autofocus>
                                </div>

                                <!-- Last Name -->
                                <div class="form-row col-sm-6 thinpadding last">
                                    <input type="text" class="form-control" name="lastname" placeholder="Last Name" autofocus>
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- Address -->
                                <div class="form-row col-sm-6 thinpadding first">
                                    <input type="text" class="form-control" name="address" lazy placeholder="Address">
                                </div>

                                <!-- Address Line 2 -->
                                <div class="form-row col-sm-6 thinpadding last">
                                    <input type="text" class="form-control" name="address_line_2" lazy placeholder="Address Line 2">
                                </div>
                            </div>
                            <div class="row extrapadding">
                                <!-- City -->
                                <div class="form-row col-sm-6 thinpadding first">
                                        <input type="text" class="form-control" name="city" lazy placeholder="City">
                                </div>

                                <!-- State & ZIP Code -->
                                <div class="form-row col-sm-4 thinpadding">
                                    <label class="select">
                                        <select name="state" type="select" class="form-control">
                                            <option>Select</option>
                                            <option v-for="state in states" value="@{{ state.abbr }}">@{{ state.state }}</option>
                                        </select>
                                    </label>
                                </div>
                                <!-- Zip Code -->
                                <div class="form-row col-sm-2 thinpadding last">
                                    <input type="text" class="form-control" name="zip" placeholder="Zip" v-model="registerForm.zip" lazy>
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

                        @if ($children > 0)
                            <div class="panel-heading text-left extrapadding">Family Information <a href="#" class="sidelink">what's this?</a>
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
                                                        <option value={{ $x }}>{{ $x }}</option>;
                                                    @endfor
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                @endfor

                            </div>
                        @endif
                 

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
