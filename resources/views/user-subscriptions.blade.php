@extends('spark::layouts.app')

@section('content')

<?php

function ReadableDietaryPreferences($diet_prefs) {
	
	$prefs = explode(",",$diet_prefs);
	$string_pref = "";
	
	foreach ($prefs as $pref) {
	
		if ($string_pref != "") {
			$string_pref .= ", ";
		}
		if ($pref=="1") {
			$string_pref .= "Beef ";
		}
		if ($pref=="2") {
			$string_pref .= "Poultry ";
		}
		if ($pref=="3") {
			$string_pref .= "Fish ";
		}
		if ($pref=="4") {
			$string_pref .= "Lamb ";
		}
		if ($pref=="5") {
			$string_pref .= "Pork ";
		}
		
		if ($pref=="6") {
			$string_pref .= "Shellfish ";
		}
		if ($pref=="7") {
			$string_pref .= "Nuts ";
		}
		if ($pref=="8") {
			$string_pref .= "Adventurous ";
		}
		if ($pref=="9") {
			$string_pref .= "Gluten Free ";
		}
		
		
		
		
	}
	
	return $string_pref;
}
	
	
	


?>
<home :menu="menu" inline-template>

<!--temp CSS-->
<style>

.updatesubscription {display:none;}
</style>
<script>

</script>
<!--end temp CSS-->
    <div class="container">

		<!--page sub nav-->
		@include('admin-menu')
		
		<!--end page nave -->
		
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">User Information</div>

                    <div class="panel-body">
					
                        <div class="menu_title"><strong>{{ $user->name }}</strong></div>
						<div style="">{{ $user->email}}</div>
                    </div>
                </div>
            </div>
        </div>

		<div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Current Subscription</div>
					
						
                    <div class="panel-body">
							<div>
						
	                        <div class="" style=""><strong>{{ $userProduct->product_description }}</strong></div>
							<div class="" style="">SKU: {{ $userProduct->sku }}</div>
							</div>
							<div>
							<span class="dietary_prefs">Dietary Preferences:</span>
							<span class="dietary_prefs">{{ ReadableDietaryPreferences($userSubscription->dietary_preferences) }}
								
							
							</div>
							<div>	<button type="button" onclick="$('.updatesubscription').toggle();" class="btn btn-default">
			                        <i class="fa fa-plus"></i> Change Subscription</button>
							</div>
                    </div>
                </div>
            </div>
        </div>

		<!--edit form -->
		

		
	<form action="{{ url('user/subscriptions') }}/{{ $user->id }}" method="POST" class="form-horizontal">
	
	 	<div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default updatesubscription">
                    <div class="panel-heading">Update Subscription</div>
		        <div class="panel-body">
		        <!-- Display Validation Errors -->
			        @include('errors.errors')
			 
					
		        <!-- New Task Form -->
		        
		            {{ csrf_field() }}
					<input type="hidden" name="user_id" value="{{ $user->id }}" />
					<input type="hidden" name="product_id" value="{{ $userProduct->id }}" />
		            
		            <div class="form-group">
		                <label for="plan_type" class="col-sm-3 control-label">Plan Type</label>

		                <div class="col-sm-6">
		                   	<select name="plan_type" type="select" class="form-control"><option></option><option value="omnivore">Omnivore</option><option value="vegetarian">Vegetarian</option></select>
		                </div>
					</div>
				
					 <div class="form-group">
			                <label for="gluten_free" class="col-sm-3 control-label">Gluten Free?</label>

			                <div class="col-sm-6">
			             
								      <input name="prefs[]" type="checkbox" value="9"  class="form-control" /> 
								   
							
			                </div>
					</div>
					
					 <div class="form-group">
			                <label for="plan_size" class="col-sm-3 control-label">Plan Size</label>

			                <div class="col-sm-6">
			                   	<select name="plan_size" type="select" class="form-control"><option></option><option value="adult">Adult Only</option><option value="family">Family Plan</option></select>
			                </div>
						</div>
						
					<div class="form-group" id="num_children_form">
				                <label for="num_children" class="col-sm-3 control-label">Number of Children</label>

				                <div class="col-sm-6">
				                   	<select name="num_children" type="select" class="form-control"><option>0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select>
				                </div>
					</div>
					
					<div class="form-group" id="num_children_form">
				                <label for="prefs" class="col-sm-3 control-label">Dietary Preferences</label>

				                <div class="col-sm-6">
				                   	<input name="prefs[]" type="checkbox" value="1" class="form-control" /> Beef
									<input name="prefs[]" type="checkbox" value="2" class="form-control" /> Poultry
									<input name="prefs[]" type="checkbox" value="3" class="form-control" /> Fish
									<input name="prefs[]" type="checkbox" value="4" class="form-control" /> Lamb
									<input name="prefs[]" type="checkbox" value="5" class="form-control" /> Pork
									<input name="prefs[]" type="checkbox" value="6" class="form-control" /> Shellfish
									<input name="prefs[]" type="checkbox" value="7" class="form-control" /> Nuts
									<input name="prefs[]" type="checkbox" value="8" class="form-control" /> I'm Adventurous!
									
				                </div>
					</div>

		           
		            <div class="form-group">
		                <div class="col-sm-offset-3 col-sm-6">
		                    <button type="submit" class="btn btn-default">
		                        <i class="fa fa-plus"></i> Update Subscription
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
