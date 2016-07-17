@extends('spark::layouts.app-admin', ['menuitem' => 'users'])

@section('page_header')

@include('menu-edit')
    <h1>
        {{ $user->name }}
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
	$('#'+'userinformation').removeClass("active");
	$('#'+BoxClass).className += "active";
	$('.'+'userinformation').hide();
	$('.'+'billingaddress').hide();
	$('.'+'shippingaddress').hide();
	$('.'+BoxClass).show();
}
</script>
<!--end temp CSS-->
    <div class="container">
	
		<!--page sub nav-->
		@include('admin-menu',['submenu' => 'accountInfo'])
		
		<!--page nav-->
		<div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                   
                    <div class="panel-body">
                    	<ul class="nav nav-tabs">
							<li class="nav_link active" id="userinformation"><a href="javascript:void(0);" onclick="ToggleBoxes('userinformation');">User Information</a></li>
    	                    <li class="nav_link" id="billingaddress"><a href="javascript:void(0);" onclick="ToggleBoxes('billingaddress');">Billing Information</a></li>
							<li style="nav_link" id="shippingaddress"><a href="javascript:void(0);" onclick="ToggleBoxes('shippingaddress');">Shipping Addresses</a></li>
						</ul>
                    </div>
		
	<form action="{{ url('user') }}/{{ $user->id }}" method="POST" class="form-horizontal">
	
	 	<div class="row userinformation">
            <div class="col-md-8">
		        <!-- Display Validation Errors -->
			        @include('errors.errors')
		        <!-- User -->
		        
		            {{ csrf_field() }}
				<input type="hidden" name="user_id" value="{{ $user->id }}" />

		        <div class="form-group">
		        	<label for="menu_title" class="col-sm-3 control-label">Name</label>
	                <div class="col-sm-6">
	                    <input type="text" name="user_name" id="user_name" class="form-control" value="{{ $user->name }}">
	                </div>
				</div>
				<div class="form-group">
					<label for="user_email" class="col-sm-3 control-label">Email</label>
	                <div class="col-sm-6">
	                    <input type="text" name="user_email" id="user_email" class="form-control" value="{{ $user->email}}">
	                </div>
	            </div>

	            <div class="form-group">
	                <div class="col-sm-offset-6 col-sm-6">
		                <button type="submit" class="btn btn-default">
		                        <i class="fa fa-plus"></i> Update User Information
		                 </button>
		            </div>
		    	</div>
			</div>
		</div>
	

		<div class="row billingaddress">

    		<div class="col-md-8">
				<div class="form-group">
					<label for="billing_address" class="col-sm-3 control-label">Address</label>
					<div class="col-sm-6">
						<input type="text" name="billing_address" id="billing_address" class="form-control" value="{{ $user->billing_address }}">
				    </div>
				</div>
				<div class="form-group">
					<label for="billing_address_line_2" class="col-sm-3 control-label">Line 2</label>
	    	        <div class="col-sm-6">
			            <input type="text" name="billing_address_line_2" id="billing_address" class="form-control" value="{{ $user->billing_address_line_2}}">
				    </div>
				</div>
				<div class="form-group">
					<label for="billing_city" class="col-sm-3 control-label">City</label>
		                <div class="col-sm-6">
    		                <input type="text" name="billing_city" id="billing_city" class="form-control" value="{{ $user->billing_city}}">
		                </div>
    	        </div>
				<div class="form-group">
					<label for="billing_state" class="col-sm-3 control-label">State</label>
    	            <div class="col-sm-6">
    	                <input type="text" name="billing_state" id="billing_state" class="form-control" value="{{ $user->billing_state}}">
    	            </div>
    	        </div>
				<div class="form-group">
					<label for="billing_state" class="col-sm-3 control-label">Zip</label>
		            <div class="col-sm-6">
		               <input type="text" name="billing_zip" id="billing_zip" class="form-control" value="{{ $user->billing_zip}}">
		            </div>
		       </div>
				<hr />
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
   		         <div class="col-sm-offset-6 col-sm-6">
	    	         <button type="submit" class="btn btn-default">
		                <i class="fa fa-plus"></i> Update Payment Information
	    	         </button>
			     </div>
			 </div>
			</div>
		</div>
		
		
		<div class="row shippingaddress">
		    <div class="col-md-10 col-md-offset-1">
		        <div class="panel panel-default ">
		            <div class="panel-heading"><strong>Current Shipping Address</strong></div>
		
						<div class="panel-body">
						<!--Shipping Addresses-->
						
						@if (count($shippingAddresses) >= 1)
						
						@foreach ($shippingAddresses as $shippingAddress)
							    
						@if ($shippingAddress->is_current === 1)	
							<input type="hidden" name="shipping_address_id" value="{{ $shippingAddress->id }}" />
							<div class="form-group">
					                <label for="shipping_address" class="col-sm-3 control-label">Address</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_address" id="shipping_address" class="form-control" value="{{ $shippingAddress->shipping_address }}">
					                </div>
								</div>
								<div class="form-group">
									<label for="shipping_address_line_2" class="col-sm-3 control-label">Line 2</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_address_line_2" id="shipping_address_line_2" class="form-control" value="{{ $shippingAddress->shipping_address_2}}">
					                </div>
					            </div>



								<div class="form-group">
									<label for="shipping_city" class="col-sm-3 control-label">City</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_city" id="shipping_city" class="form-control" value="{{ $shippingAddress->shipping_city}}">
					                </div>
					            </div>

								<div class="form-group">
									<label for="shipping_state" class="col-sm-3 control-label">State</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_state" id="shipping_state" class="form-control" value="{{ $shippingAddress->shipping_state}}">
					                </div>
					            </div>

								<div class="form-group">
									<label for="billing_state" class="col-sm-3 control-label">Zip</label>

						            <div class="col-sm-6">
						               <input type="text" name="shipping_zip" id="shipping_zip" class="form-control" value="{{ $shippingAddress->shipping_zip}}">
						            </div>
						       </div>



					            <div class="form-group">
					                <div class="col-sm-offset-3 col-sm-6">
					                    <button type="submit" class="btn btn-default">
					                        <i class="fa fa-plus"></i> Update Shipping Address
					                    </button>
					                </div>
					            </div>
					    @endif
							
							

						@endforeach
						
						@else
						
							<div class="form-group">
					                <label for="shipping_address" class="col-sm-3 control-label">Shipping Address</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_address" id="shipping_address" class="form-control" value="">
					                </div>
								</div>
								<div class="form-group">
									<label for="shipping_address_line_2" class="col-sm-3 control-label">Shippping Address Line 2</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_address_line_2" id="shipping_address_line_2" class="form-control" value="">
					                </div>
					            </div>



								<div class="form-group">
									<label for="shipping_city" class="col-sm-3 control-label">Shipping City</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_city" id="shipping_city" class="form-control" value="">
					                </div>
					            </div>

								<div class="form-group">
									<label for="shipping_state" class="col-sm-3 control-label">Shipping State</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_state" id="shipping_state" class="form-control" value="">
					                </div>
					            </div>

								<div class="form-group">
									<label for="billing_state" class="col-sm-3 control-label">Shipping Zip</label>

						            <div class="col-sm-6">
						               <input type="text" name="shipping_zip" id="shipping_zip" class="form-control" value="">
						            </div>
						       </div>



					            <div class="form-group">
					                <div class="col-sm-offset-3 col-sm-6">
					                    <button type="submit" class="btn btn-default">
					                        <i class="fa fa-plus"></i> Add Shipping Address
					                    </button>
					                </div>
					            </div>
						
						@endif
				</div>
			</div>
		</div>
	</div>
							<div class="row">
							    <div class="col-md-8 col-md-offset-2">
							        <div class="panel panel-default shippingaddress">
							         	<div class="panel-heading"><strong>Inactive Shipping Addresses</strong></div>
									

											<div class="panel-body">
							
						@foreach ($shippingAddresses as $shippingAddress)

						@if ($shippingAddress->is_current === 0)
							<div class="col-sm-offset-3 col-sm-6">
								<span>{{ $shippingAddress->shipping_address }}</span>
								<span>{{ $shippingAddress->shipping_address_2 }}</span>
								<span>{{ $shippingAddress->shipping_city }}</span>
								<span>{{ $shippingAddress->shipping_state }}</span>
								<span>{{ $shippingAddress->shipping_zip }}</span>
							</div>
							
							@endif


							@endforeach
							
						</div>
					
					
						
						
						
						<!--End Shipping Addresses-->
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
