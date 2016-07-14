@extends('spark::layouts.app-admin')

@section('page_header')

@include('menu-edit')
    <h1>
        New Plan
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection
<home :menu="menu" inline-template>

<div class="container">
		
		<form action="{{ url('user/new/subscription') }}/{{ $user->id }}" method="POST" class="form-horizontal">

		 	<div class="row">
	            <div class="col-md-8 col-md-offset-2">
	                <div class="panel panel-default updatesubscription">
	                    <div class="panel-heading">New Subscription</div>
			        <div class="panel-body">
			        <!-- Display Validation Errors -->
				        @include('errors.errors')


			        <!-- New Task Form -->

			            {{ csrf_field() }}
						<input type="hidden" name="user_id" value="{{ $user->id }}" />
					
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
			                        <i class="fa fa-plus"></i> Save Subscription
			                    </button>
			                </div>
			            </div>
				</div>
			</div>
		</div>

	</div>


			        </form>

<!--End Container -->
</div>
</home>
@endsection








		
	