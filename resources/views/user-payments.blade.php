@extends('spark::layouts.app-admin', ['menuitem' => 'users'])

@section('page_header')

@include('menu-edit')
    <h1>
        {{ $user->name }}'s Payments
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection
@section('content')
<home :menu="menu" inline-template>

<!--temp CSS-->
<style>
	.shippingaddress {display:none;}
	.billingaddress {display:none;}
</style>
<script>
function ToggleBoxes(BoxClass) {
	
	$('.userinformation').hide();
	$('.billingaddress').hide();
	$('.shippingaddress').hide();
	$(BoxClass).show();
}
</script>
<!--end temp CSS-->
    <div class="container">
	
		<!--page sub nav-->
		@include('admin-menu',['submenu' => 'accountInfo'])
		
		
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">User Information</div>

                    <div class="panel-body">
						<span class="menu_id">{{ $user->id }}</span>
                        <span class="menu_title">{{ $user->name }}</span>
						<span style="padding-left:10px;">{{ $user->email}}</span>
						
                    </div>
                </div>
            </div>
        </div>

		 <div class="row">
	            <div class="col-md-8 col-md-offset-2">
	                <div class="panel panel-default">
	                    <div class="panel-heading">Current Payment Information</div>

	                    <div class="panel-body">
							<span class="menu_id">{{ $user->card_brand }} ending in </span>
	                        <span class="menu_title">{{ $user->card_last_four }}</span>
						
	                    </div>
	                </div>
	            </div>
	        </div>


		<!--edit form -->
		
	<form action="" method="POST" class="form-horizontal">
	
	 	<div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default userinformation">
                    <div class="panel-heading">Update Payment Information</div>
		        <div class="panel-body">
		        <!-- Display Validation Errors -->
			        @include('errors.errors')
			 
					
		        <!-- New Task Form -->
		        
		            {{ csrf_field() }}
					<input type="hidden" name="user_id" value="{{ $user->id }}" />
					<div class="form-group">
		                <label for="menu_title" class="col-sm-3 control-label">Card Type</label>

		                <div class="col-sm-6">
		                  	<select name="card_type" type="select" class="form-control"><option>Visa</option><option>Mastercard</option></select>
		                </div>
					</div>
		            
		            <div class="form-group">
		                <label for="menu_title" class="col-sm-3 control-label">Card Number</label>

		                <div class="col-sm-6">
		                    <input type="text" name="card" id="user_name" class="form-control" value="">
		                </div>
					</div>
					<div class="form-group">
		                <label for="menu_title" class="col-sm-3 control-label">Exp Month</label>

		                <div class="col-sm-6">
		                   <select type="select" class="form-control"  name="card_month"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option><option>11</option><option>12</option></select>
		                </div>
					</div>
						<div class="form-group">
			                <label for="menu_title" class="col-sm-3 control-label">Exp Year</label>

			                <div class="col-sm-6">
			                    <select type="select" class="form-control"  name="card_month"><option>2016</option><option>2017</option><option>2018</option><option>2019</option><option>2020</option><option>2021</option><option>2022</option></select>
			                </div>
						</div>
					
					<div class="form-group">
		                <label for="menu_title" class="col-sm-3 control-label">CVV</label>

		                <div class="col-sm-6">
		                    <input type="text" name="cvv" id="user_name" class="form-control" value="">
		                </div>
					</div>
					

		           
		            <div class="form-group">
		                <div class="col-sm-offset-3 col-sm-6">
		                    <button type="submit" class="btn btn-default">
		                        <i class="fa fa-plus"></i> Update Payment Information
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
