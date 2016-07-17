@extends('spark::layouts.app-admin', ['menuitem' => 'users'])

@section('page_header')

@include('menu-edit')
    <h1>
        {{ $user->name }}'s Referrals
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection
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
		@include('admin-menu',['submenu' => 'referrals'])
		
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
                    <div class="panel-heading">Current Referrals</div>
					<?PHP
					
						$subsrcribeCount = 0;
					
					?>
						
                    <div class="panel-body">
						@foreach ($referrals as $referral)
							
							 <?PHP if ($referral->did_subscribe==1) { 
									$subsrcribeCount += 1;
									?><strong><?php } ?>
								
							  <?php if ($referral->did_subscribe==1) {$hasSubscribed=" did subscribe.";}else{$hasSubscribed=" did not subscribe.";}?>
						
							   <div>{{ $referral->referral_email }} was sent on {{ $referral->created_at }} and {{ $hasSubscribed }}
		                        </div>
							<?PHP if ($referral->did_subscribe==1) { ?></strong><?php } ?>
		
						@endforeach
						<br />
						<div>You have <strong><?PHP echo $subsrcribeCount ?> referrals</strong>! Get <strong><?PHP echo 5-$subsrcribeCount?> more</strong> for a free week!</div>
                    </div>
                </div>
            </div>
        </div>

		<!--edit form -->
		

		
	<form action="{{ url('user/referrals') }}/{{ $user->id }}" method="POST" class="form-horizontal">
		
		<div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Send a New Referral for this User</div>
					
						
                    <div class="panel-body">
						<!-- Display Validation Errors -->
					        @include('errors.errors')


				        <!-- New Task Form -->

				            {{ csrf_field() }}
							<input type="hidden" name="user_id" value="{{ $user->id }}" />
							
							<div class="form-group">
								<label for="send_email" class="col-sm-3 control-label">Send Referral to this Email:</label>

				                <div class="col-sm-6">
				                    <input type="text" name="send_email" id="send_email" class="form-control" value="">
				                </div>
				            </div>
				
							<div class="form-group">
								<label for="custom_message" class="col-sm-3 control-label">Add a Custom Message:</label>

				                <div class="col-sm-6">
				                    <input type="text" name="custom_message" id="custom_message" class="form-control" value="">
				                </div>
				            </div>
							<div class="form-group">
				                <div class="col-sm-offset-3 col-sm-6">
				                    <button type="submit" class="btn btn-default">
				                        <i class="fa fa-plus"></i> Send Referral
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
