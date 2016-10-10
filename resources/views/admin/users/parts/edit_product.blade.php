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

if (strpos($prefs, 'Red Meat') !== false) $redmeat = true; else $redmeat = false;
if (strpos($prefs, 'Poultry') !== false) $poultry = true; else $poultry = false;
if (strpos($prefs, 'Fish') !== false) $fish = true; else $fish = false;
if (strpos($prefs, 'Lamb') !== false) $lamb = true; else $lamb = false;
if (strpos($prefs, 'Pork') !== false) $pork = true; else $pork = false;
if (strpos($prefs, 'Shellfish') !== false) $shellfish = true; else $shellfish = false;
if (strpos($prefs, 'Nut Free') !== false) $nutfree = true; else $nutfree = false;
if (strpos($prefs, 'Gluten Free') !== false) $glutenfree = true; else $glutenfree = false;
?>

<form method="POST" action="/admin/user_details/{{ $user->id }}/edit_product" accept-charset="UTF-8" class="meals">
    {{ csrf_field() }}
    <input type="hidden" name="user_id" value="{{$user->id}}" />

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Update user product</h4>
    </div>
    <div class="modal-body">
        <p>Changes will only apply to deliveries scheduled on or after {{ $changeDate }}.</p>

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
            <div class="col-sm-9" id="planChildrenDiv">Number of children: &nbsp; {!! Form::text('children', $ChildSelect, array('pattern' => '[0-9]*', 'class' => 'number')); !!}</div>
        </div>
        <div class="row">
            <div class="col-sm-3" style="line-height: 42px"><b>Box Type</b></div>
            <div class="col-sm-9">
                <div class="row">
                <div class="col-xs-6 col-md-4  ">
                    {!! Form::radio('plan_type', 'Omnivore Box', $BoxSelectOmn, array('id'=>'plan_type1',)) !!} <label for="plan_type1">Omnivore Box</label>
                </div>
                <div class="col-xs-6 col-md-4  ">
                    {!! Form::radio('plan_type', 'Vegetarian Box', $BoxSelectVeg, array('id'=>'plan_type2',)) !!} <label for="plan_type2">Vegetarian Box</label>
                </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3" style="line-height: 55px"><b>Gluten free*?</b></div>
            <div class="col-sm-9"><span class="checkbox" style="margin-left: -10px">{!! Form::checkbox('prefs[]', '9', $glutenfree, array('id'=>'glutenfree', 'class'=>'')) !!} <label for="glutenfree" class="inline">Yes</label></div>
        </div>
        <div class="row padbottom">
            <div class="col-sm-3" style="line-height: 42px"><b>Nut free?</b></div>
            <div class="col-sm-9">
                <span class="checkbox nomargin" style="margin-left: -10px">{!! Form::checkbox('prefs[]', '7', $nutfree, array('id'=>'nutfree', 'class'=>'')) !!} <label for="nutfree" class="inline">Yes</label></div>
        </div>
        <div class="row">
            <div class="col-sm-3"><b>Dietary Preferences</b></div>
            <div class="col-sm-9">
                <div>Uncheck the foods user doesn't eat below:</div>
                <div class="col-xs-4  " style="margin-left: -10px">
                    {!! Form::checkbox('prefs[]', '1', $redmeat, array('id'=>'redmeat', 'class'=>'pref',)) !!} <label for="redmeat">Red Meat</label>
                    <br/>
                    {!! Form::checkbox('prefs[]', '2', $poultry, array('id'=>'poultry', 'class'=>'pref', )) !!} <label for="poultry">Poultry</label>
                </div>
                <div class="col-xs-4  ">
                    {!! Form::checkbox('prefs[]', '3', $fish, array('id'=>'fish', 'class'=>'pref', )) !!} <label for="fish">Fish</label>
                    <br />
                    {!! Form::checkbox('prefs[]', '4', $lamb, array('id'=>'lamb', 'class'=>'pref', )) !!} <label for="lamb">Lamb</label>
                </div>
                <div class="col-xs-4  ">
                    {!! Form::checkbox('prefs[]', '5', $pork, array('id'=>'pork', 'class'=>'pref', )) !!} <label for="pork">Pork</label>
                    <br />
                    {!! Form::checkbox('prefs[]', '6', $shellfish, array('id'=>'shellfish', 'class'=>'pref', )) !!} <label for="shellfish">Shellfish</label>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="color: #666666;">Cancel</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>

