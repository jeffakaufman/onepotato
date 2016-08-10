@extends('spark::layouts.app')

@section('register_nav')
<script>
$('#register3').addClass('active');
</script>
@endsection

@section('content')
<?php 
    if (Session::has('children')) $children = Session::get('children');
    if (Session::has('zip')) $zip = Session::get('zip');
    if (Session::has('user_id')) $user_id = Session::get('user_id');
    if (Session::has('adult_price')) $adult_price = Session::get('adult_price');
    if (Session::has('family1_price')) $family1_price = Session::get('family1_price');
    if (Session::has('omni')) $omni = Session::get('omni');
    if (Session::has('veg')) $veg = Session::get('veg');
    if (Session::has('glutenfree')) $glutenfree = Session::get('glutenfree');
    if (Session::has('redmeat')) $redmeat = Session::get('redmeat');
    if (Session::has('poultry')) $poultry = Session::get('poultry');
    if (Session::has('redmeat')) $redmeat = Session::get('redmeat');
    if (Session::has('fish')) $fish = Session::get('fish');
    if (Session::has('lamb')) $lamb = Session::get('lamb');
    if (Session::has('pork')) $pork = Session::get('pork');
    if (Session::has('shellfish')) $shellfish = Session::get('shellfish');
    if (Session::has('nuts')) $nuts = Session::get('nuts');
    if (Session::has('upcoming_dates')) $upcoming_dates = Session::get('upcoming_dates');
    if (Session::has('start_date')) $start_date = Session::get('start_date'); else $start_date = '';
?>
<div id="preferences">
    <div id="planType">
        PLAN TYPE: @if ($children == 0) Adult @else Family, {{ $children }} children @endif <a href="{{ route('register.select_plan') }}" class="sidelink">(change)</a>
    </div>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading with-subtitle">
                        <h1>Tell us your preferences.
                            <div class="panel-subtitle">You can update your preferences at any time from your account.</div>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <form class="form-horizontal" role="form" method="post"  action="{{ url('/register/preferences') }}">
             {{ csrf_field() }}
                <input type="hidden" name="zip" value="{{ $zip }}" />
            <input type="hidden" name="children" value="{{$children}}" />
            <input type="hidden" name="user_id" value="{{ isset($user_id) ? $user_id : $user->id }}" />
            <input type="hidden" name="adult_price" value="{{ $adult_price }}" />
            <input type="hidden" name="family1_price" value="{{ $family1_price }}" />
            <div class="row">
                <div class="col-sm-6 col-md-5 col-md-offset-1">
                    <div class="panel panel-default panel-form marginless">
                        <div class="panel-heading text-left extrapadding">dietary preferences</div>
                        <div class="panel-body font16">
                            <div class="row">
                                <div class="col-xs-12 extrapadding">Choose your box: <a data-toggle="tooltip" data-title="Lorem ipsum dolor" class="sidelink">what's this?</a></div>
                            </div>
                            <div class="row nowrap extrapadding">
                                <div class="col-xs-6 radio nosidepadding"><input id="plan_type1" type="radio" v-model="plan_type" @click="selectAllOmnivore" name="plan_type" value="Omnivore Box"  @if(isset($omni)) {{ $omni }} @else checked @endif> <label for="plan_type1">Omnivore Box</label></div>
                                <div class="col-xs-6 radio nosidepadding"><input id="plan_type2" type="radio" v-model="plan_type" @click="selectAllVegetarian" name="plan_type" value="Vegetarian Box" @if(isset($veg)) {{ $veg }} @endif> <label for="plan_type2">Vegetarian Box</label></div>
                            </div>
                            <div class="row extrapadding">
                                <div class="col-xs-12 checkbox nosidepadding"><input id="glutenfree" type="checkbox" name="prefs[]" value="9" @if (isset($glutenfree)) {{ $glutenfree }} @endif> <label for="glutenfree">Gluten free*</label></div>
                                <div class="col-xs-12 text-left footnote">* Gluten free meal plans are an additional $1.50 per adult meal.</div>
                            </div>
                            <div class="row padtop">
                                <div class="col-xs-12 extrapadding">We'd like to receive the following foods:</div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 checkbox">
                                    <input id="redmeat" v-model="prefs.redmeat" @click="selectOmni" name="prefs[]" type="checkbox" value="1" class="form-control" @if(isset($redmeat)) {{ $redmeat }} @else checked @endif /> <label for="redmeat">Red Meat</label>
                                    <input id="poultry" v-model="prefs.poultry" @click="selectOmni" name="prefs[]" type="checkbox" value="2" class="form-control" @if(isset($poultry)) {{ $poultry }} @else checked @endif /> <label for="poultry">Poultry</label>
                                </div>
                                <div class="col-xs-3 checkbox">
                                    <input id="fish" v-model="prefs.fish" @click="selectOmni" name="prefs[]" type="checkbox" value="3" class="form-control" @if(isset($fish)) {{ $fish }} @else checked @endif /> <label for="fish">Fish</label>
                                    <input id="lamb" v-model="prefs.lamb" @click="selectOmni" name="prefs[]" type="checkbox" value="4" class="form-control" @if(isset($lamb)) {{ $lamb }} @else checked @endif /> <label for="lamb">Lamb</label>
                                </div>
                                <div class="col-xs-3 checkbox">
                                    <input id="pork" v-model="prefs.pork" @click="selectOmni" name="prefs[]" type="checkbox" value="5" class="form-control" @if(isset($pork)) {{ $pork }} @else checked @endif /> <label for="pork">Pork</label>
                                    <input id="shellfish" v-model="prefs.shellfish" @click="selectOmni" name="prefs[]" type="checkbox" value="6" class="form-control" @if(isset($shellfish)) {{ $shellfish }} @else checked @endif /> <label for="shellfish">Shellfish</label>
                                </div>
                                <div class="col-xs-3 checkbox">
                                    <input id="nuts" v-model="prefs.nuts" name="prefs[]" type="checkbox" value="7" class="form-control" @if(isset($nuts)) {{ $nuts }} @else checked @endif /> <label for="nuts">Nuts</label>
                                </div>
                                <!-- <div class="col-xs-3 checkbox" v-for="pref in prefs">
                                    <input id="@{{ pref.key }}" name="prefs[]" type="checkbox" v-model="$data[pref.key]" value="@{{ pref.id }}" class="form-control"  /> <label for="@{{ pref.key }}">@{{ pref.name | capitalize }}</label>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="note text-center">
                        <h4>Your Dietary Profile</h4>
                        You will receive a mixture of:
                        <span v-show="prefs.redmeat"> red meat</span> 
                        <span v-show="prefs.fish"> fish</span> 
                        <span v-show="prefs.pork"> pork</span> 
                        <span v-show="prefs.poultry"> poultry</span> 
                        <span v-show="prefs.lamb"> lamb</span> 
                        <span v-show="prefs.shellfish"> shellfish</span> 
                        <span v-show="prefs.nuts"> nut</span> dishes.
                    </div>
                </div>
                <div class="col-sm-6 col-md-5">
                    <div class="panel panel-default panel-form">
                        <div class="panel-heading text-left extrapadding">DELIVERY TIMELINE</div>
    
                        <div class="panel-body font16">
                            <div class="row">
                                <div class="col-xs-12 padbottom extrapadding">
                                    My first box will arrive on
                                    <label class="select inline">
                                        {!! Form::select('start_date', isset($upcoming_dates) ? $upcoming_dates : $upcomingDates,$start_date,array('id'=>'startDate', 'class'=>'form-control', 'v-on:change'=>'fetchNewMenu', 'required')); !!}
                                    </label>
                                </div>
                                <div class="col-xs-12 padding extrapadding">
                                    <div class="panel-subtitle2">your first week’s menu</div>
                                </div>
                            </div>
                            <div class="row">
                                
                                <menu :prefs="prefs"></menu>
    
                            </div>
    
                            <template id="menu-template">
                                
                                <div id="menu" class="col-xs-12 extrapadding">
    
                                    <div class="meal col-xs-4 font11 thinpadding" v-for="meal in filteredMenu" track-by="id">
                                        <a href="#" data-toggle="modal" data-target="#imagemodal-@{{ meal.id }}"><img :src="meal.image" alt="" class="meal_image"></a>
                                        <div class="col-xs-9 col-xs-offset-1 padding nosidepadding text-center">@{{ meal.menu_title }}</div>

                                        <input type="hidden" name="menus_id[]" value="@{{ meal.id }}" />
                                        
                                        <div id="imagemodal-@{{ meal.id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                          <div class="modal-dialog">
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <h4 class="modal-title" id="myModalLabel">@{{ meal.menu_title }}</h4>
                                              </div>
                                              <div class="modal-body">
                                                <img :src="meal.image" id="imagepreview">
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                            </template>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-right">
                            <button class="btn btn-primary">continue to delivery information</button>
                        </div>
                        <div class="col-xs-12 text-right disclaimer padding">
                            No Commitment. Skip, cancel or change your family size any time. 
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
