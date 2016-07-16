@extends('spark::layouts.app')

@section('content')
<preferences :user="user" inline-template>
    <div id="planType">
        PLAN TYPE: Family, 2 children <a href="#" class="sidelink">(change)</a>
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
        <div class="row">
            <div class="col-md-5 col-md-offset-1">
                <div class="panel panel-default panel-form marginless">
                    <div class="panel-heading text-left extrapadding">dietary preferences</div>
                    <div class="panel-body font16">
                        <div class="row">
                            <div class="col-xs-12 extrapadding">Choose your box: <a href="#" class="sidelink">What's this?</a></div>
                        </div>
                        <div class="row nowrap extrapadding">
                            <div class="col-xs-6 radio nosidepadding"><input id="plan_type1" type="radio" name="plan_type" value="Omnivore Box"> <label for="plan_type1">Omnivore Box</label></div>
                            <div class="col-xs-6 radio nosidepadding"><input id="plan_type2" type="radio" name="plan_type" value="Vegetarian Box"> <label for="plan_type2">Vegetarian Box</label></div>
                        </div>
                        <div class="row extrapadding">
                            <div class="col-xs-12 checkbox nosidepadding"><input id="glutenfree" type="checkbox" name="glutenfree" value="Gluten free"> <label for="glutenfree">Gluten free*</label></div>
                            <div class="col-xs-12 text-left footnote">* Gluten free meal plans are an additional $x.xx per week.</div>
                        </div>
                        <div class="row padtop">
                            <div class="col-xs-12 extrapadding">Uncheck the foods you don’t eat below:</div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3 checkbox">
                                <input id="beef" name="prefs[]" type="checkbox" value="1" class="form-control" /> <label for="beef">Beef</label>
                                <input id="poultry" name="prefs[]" type="checkbox" value="2" class="form-control" /> <label for="poultry">Poultry</label>
                            </div>
                            <div class="col-xs-3 checkbox">
                                <input id="fish" name="prefs[]" type="checkbox" value="3" class="form-control" /> <label for="fish">Fish</label>
                                <input id="lamb" name="prefs[]" type="checkbox" value="4" class="form-control" /> <label for="lamb">Lamb</label>
                            </div>
                            <div class="col-xs-3 checkbox">
                                <input id="pork" name="prefs[]" type="checkbox" value="5" class="form-control" /> <label for="pork">Pork</label>
                                <input id="shellfish" name="prefs[]" type="checkbox" value="6" class="form-control" /> <label for="shellfish">Shellfish</label>
                            </div>
                            <div class="col-xs-3 checkbox">
                                <input id="nuts" name="prefs[]" type="checkbox" value="7" class="form-control" /> <label for="nuts">Nuts</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="note text-center">
                    <h4>Your Dietary Profile</h4>
                    You will receive a mixture of poultry, red meat, seafood and vegetarian dishes.
                </div>
            </div>
            <div class="col-md-5">
                <div class="panel panel-default panel-form">
                    <div class="panel-heading text-left extrapadding">DELIVERY TIMELINE</div>

                    <div class="panel-body font16">
                        <div class="row">
                            <div class="col-xs-12 padbottom extrapadding">
                                Box will be delivered on
                                <select name="delivery_day" type="select" class="form-control inline">
                                    <option></option>
                                    <option value="mondays">Mondays</option>
                                    <option value="tuesdays">Tuesdays</option>
                                    <option value="wednesdays">Wednesdays</option>
                                    <option value="thursdays">Thursdays</option>
                                    <option value="fridays">Fridays</option>
                                </select>
                                by 8 p.m.
                            </div>
                            <div class="col-xs-12 padbottom extrapadding">
                                My first box will arrive on
                                <select name="first_day" type="select" class="form-control inline">
                                    <option value="may18">May 18, 2016</option>
                                </select>
                            </div>
                            <div class="col-xs-12 padding extrapadding">
                                <div class="panel-subtitle2">your first week’s menu</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 extrapadding">
                                <div class="col-xs-12 col-sm-4 font11 thinpadding first">
                                    <img src="/img/preferences_meal1.jpg" alt="">
                                    <div class="col-xs-9 col-xs-offset-1 padding nosidepadding text-center">Sweet Balsamic Chicken with Avocado Toast and Cauliflower</div>
                                </div>
                                <div class="col-xs-12 col-sm-4 font11 thinpadding">
                                    <img src="/img/preferences_meal2.jpg" alt="">
                                    <div class="col-xs-9 col-xs-offset-1 padding nosidepadding text-center">Sweet Corn Manchego Enchiladas with Corn Salsa</div>
                                </div>
                                <div class="col-xs-12 col-sm-4 font11 thinpadding last">
                                    <img src="/img/preferences_meal3.jpg" alt="">
                                    <div class="col-xs-9 col-xs-offset-1 padding nosidepadding text-center">Salmon Sheet Pan Dinner with Early Summer Vegetables  and Orzo Salad</div>
                                </div>
                            </div>
                        </div>
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
    </div>
</preferences>
@endsection
