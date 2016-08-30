<?php 
session_start();
    if( isset( $_SESSION['registered']) ) header("Location: /account");
?>
@extends('spark::layouts.app')

@section('register_nav')
<script>
$('#register2').addClass('active');
</script>
@endsection

@section('content')
<?php 
    if (Session::has('children')) $children = Session::get('children');
    if (Session::has('zip')) $zip = Session::get('zip');
    if (Session::has('user_id')) $user_id = Session::get('user_id');
    if (Session::has('adult_price')) $adult_price = Session::get('adult_price');
    if (Session::has('family1_price')) $family1_price = Session::get('family1_price');
?>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Pick the plan that’s best for your family
                            <div class="panel-subtitle">No Commitment. Skip deliveries, change your family size, or cancel anytime.<br>
Both plans include 3 meals per week.</div>
                        </h1>
                    </div>
                </div>
            </div>
        </div><!-- .row -->
			<form class="form-horizontal" role="form" method="post"  action="{{ url('/register/select_plan') }}">
				 {{ csrf_field() }}
				<input type="hidden" name="children" value="{{ isset($children) ? $children : 0 }}" />
				<input type="hidden" name="user_id" value="{{ isset($user_id) ? $user_id : $user->id }}" />
				<input type="hidden" name="zip" value="{{$zip}}" />

        <div class="row">
            <div class="col-sm-6 col-md-5 col-md-offset-1">
                <div class="panel panel-default panel-form text-center">
                    <div class="panel-heading with-subtitle">Adult Plan
                        <div class="panel-subtitle">3 dinners a week for 2 adults.</div>
                    </div>
                    <div class="panel-body">
                        <div class="row image">
                            <div class="col-xs-12">
                                <img src="/img/p_plan_adult.jpg" alt="Adult Plan">
                            </div>
                        </div>
                        <div class="row nowrap" style="height: 52px;">
                            <div class="plan-info col-xs-6 text-right">Number of adults:</div>
                            <div class="plan-info col-xs-6 text-left">2</div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right">Price per adult serving:</div>
                            <div class="plan-info col-xs-6 text-left">${{ number_format( $adult_price / 6, 2 ) }}*</div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right"></div>
                            <div class="plan-info col-xs-6 text-left"></div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right">Minimum weekly cost:</div>
                            <div class="plan-info col-xs-6 text-left">${{ $adult_price }}</div>
                        </div>
                        <div class="row action">
                            <button class="btn btn-primary">Select</button>
                        </div>
                    </div>
                </div>
            </div>
			</form>
			<form class="form-horizontal" action="{{ url('/register/select_plan') }}" role="form" method="post">
					 {{ csrf_field() }}
				
					<input type="hidden" name="user_id" value="{{ isset($user_id) ? $user_id : $user->id }}" />
					<input type="hidden" name="zip" value="@if (isset($zip)) {{$zip}} @endif" />

            <div class="col-sm-6 col-md-5">
                <div class="panel panel-default panel-form text-center">
                    <div class="panel-heading with-subtitle">Family Plan
                        <div class="panel-subtitle">3 dinners a week. Customize your family size.</div>
                    </div>

                    <div class="panel-body">
                        <div class="row image">
                            <div class="col-xs-12">
                                <img src="/img/p_plan_family.jpg" alt="Family Plan">
                            </div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right">Number of children:</div>
                            <div class="plan-info col-xs-6 text-left field">
                                <label class="select inline">
                                    <select name="children" v-model="ch" type="select" class="form-control inline">
                                        <option value="1" @if (isset($children) && $children == 1) selected @elseif (!isset($children)) selected @endif>1</option>
                                        <option value="2" @if (isset($children) && $children == 2) selected @endif>2</option>
                                        <option value="3" @if (isset($children) && $children == 3) selected @endif>3</option>
                                        <option value="4" @if (isset($children) && $children == 4) selected @endif>4</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right">Price per adult serving:</div>
                            <div class="plan-info col-xs-6 text-left">${{ number_format( $adult_price / 6, 2 ) }}*</div>
                        </div>
                        <?php $childCost = $family1_price - $adult_price ?>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right">Price per child serving:</div>
                            <div class="plan-info col-xs-6 text-left">${{ number_format( $childCost / 3, 2 ) }}*</div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right">Minimum weekly cost:</div>
                            <div class="plan-info col-xs-6 text-left">
                                <span v-show="ch == 1">{{ $family1_price }}</span>
                                <span v-show="ch == 2">{{ $family1_price + $childCost }}</span>
                                <span v-show="ch == 3">{{ $family1_price + ($childCost * 2) }}</span>
                                <span v-show="ch == 4">{{ $family1_price + ($childCost * 3) }}</span>
                            </div>
                        </div>
                        <div class="row action">
                            <button class="btn btn-primary">Select</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .row -->
		</form>
        <div class="row">
            <div class="footnote pad col-md-8 col-md-offset-2">* Based on the typical omnivore meal plan</div>
        </div><!-- .row -->
    </div>

@endsection
