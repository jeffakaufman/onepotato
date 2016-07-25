@extends('spark::layouts.app')

@section('register_nav')
<script>
$('#register2').addClass('active');
</script>
@endsection

@section('content')
<select_plan :user="user" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Pick the plan that’s best for your family.
                            <div class="panel-subtitle">No Commitment. Skip, cancel or change your family size any time.<br>
Both plans include 3 meals per week.</div>
                        </h1>
                    </div>
                </div>
            </div>
        </div><!-- .row -->
			<form class="form-horizontal" role="form" method="post"  action="{{ url('/register/select_plan') }}">
				 {{ csrf_field() }}
				<input type="hidden" name="children" value="0" />
				<input type="hidden" name="user_id" value="{{ $user->id }}" />
        <div class="row">
            <div class="col-md-5 col-md-offset-1">
                <div class="panel panel-default panel-form text-center">
                    <div class="panel-heading with-subtitle">Adult Plan
                        <div class="panel-subtitle">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
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
                            <div class="plan-info col-xs-6 text-left">$X.XX*</div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right"></div>
                            <div class="plan-info col-xs-6 text-left"></div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right">Weekly cost from:</div>
                            <div class="plan-info col-xs-6 text-left">$XX.XX</div>
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
				
					<input type="hidden" name="user_id" value="{{ $user->id }}" />
            <div class="col-md-5">
                <div class="panel panel-default panel-form text-center">
                    <div class="panel-heading with-subtitle">Family Plan
                        <div class="panel-subtitle">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
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
                                    <select name="children" type="select" class="form-control inline">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right">Price per adult serving:</div>
                            <div class="plan-info col-xs-6 text-left">$X.XX*</div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right">Price per child serving:</div>
                            <div class="plan-info col-xs-6 text-left">$X.XX*</div>
                        </div>
                        <div class="row nowrap">
                            <div class="plan-info col-xs-6 text-right">Weekly cost from:</div>
                            <div class="plan-info col-xs-6 text-left">$XX.XX</div>
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
</select_plan>
@endsection
