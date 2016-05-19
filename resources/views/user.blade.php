@extends('spark::layouts.app')

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
	
		<!--page nav-->
		<div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                   
                    <div class="panel-body">
						<span class="nav_link"><a href="javascript:void(0);" onclick="ToggleBoxes('.userinformation');">User Information</a> ||</span>
                        <span class="nav_link"><a href="javascript:void(0);" onclick="ToggleBoxes('.billingaddress');">Billing Address</a>  ||</span>
						<span style="nav_link"><a href="javascript:void(0);" onclick="ToggleBoxes('.shippingaddress');">Shipping Addresses</a></span>
                    </div>
                </div>
            </div>
        </div>
		<!--end page nave -->
		
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


		<!--edit form -->
		
	<form action="{{ url('user') }}/{{ $user->id }}" method="POST" class="form-horizontal">
	
	 	<div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default userinformation">
                    <div class="panel-heading">User Information</div>
		        <div class="panel-body">
		        <!-- Display Validation Errors -->
			        @include('errors.errors')
			 
					
		        <!-- New Task Form -->
		        
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
		                <div class="col-sm-offset-3 col-sm-6">
		                    <button type="submit" class="btn btn-default">
		                        <i class="fa fa-plus"></i> Update User Information
		                    </button>
		                </div>
		            </div>
			</div>
		</div>
	</div>
	
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default billingaddress">
            <div class="panel-heading">Billing Address</div>
				<div class="panel-body">
					
					 <div class="form-group">
			                <label for="billing_address" class="col-sm-3 control-label">Billing Address</label>

			                <div class="col-sm-6">
			                    <input type="text" name="billing_address" id="billing_address" class="form-control" value="{{ $user->billing_address }}">
			                </div>
						</div>
						<div class="form-group">
							<label for="billing_address_line_2" class="col-sm-3 control-label">Billing Address Line 2</label>

			                <div class="col-sm-6">
			                    <input type="text" name="billing_address_line_2" id="billing_address" class="form-control" value="{{ $user->billing_address_line_2}}">
			                </div>
			            </div>
			
						

						<div class="form-group">
							<label for="billing_city" class="col-sm-3 control-label">Billing City</label>

			                <div class="col-sm-6">
			                    <input type="text" name="billing_city" id="billing_city" class="form-control" value="{{ $user->billing_city}}">
			                </div>
			            </div>
			
						<div class="form-group">
							<label for="billing_state" class="col-sm-3 control-label">Billing State</label>

			                <div class="col-sm-6">
			                    <input type="text" name="billing_state" id="billing_state" class="form-control" value="{{ $user->billing_state}}">
			                </div>
			            </div>
			
						<div class="form-group">
							<label for="billing_state" class="col-sm-3 control-label">Billing Zip</label>

				            <div class="col-sm-6">
				               <input type="text" name="billing_zip" id="billing_zip" class="form-control" value="{{ $user->billing_zip}}">
				            </div>
				       </div>
						


			            <div class="form-group">
			                <div class="col-sm-offset-3 col-sm-6">
			                    <button type="submit" class="btn btn-default">
			                        <i class="fa fa-plus"></i> Update Billing Address
			                    </button>
			                </div>
			            </div>
			
					</div>
				</div>
			</div>

		</div>
		
		
		<div class="row">
		    <div class="col-md-8 col-md-offset-2">
		        <div class="panel panel-default shippingaddress">
		            <div class="panel-heading">Current Shipping Address</div>
		
						<div class="panel-body">
						<!--Shipping Addresses-->
						
						@if (count($shippingAddresses) >= 1)
						
						@foreach ($shippingAddresses as $shippingAddress)
							    
						@if ($shippingAddress->is_current === 1)	
							<input type="hidden" name="shipping_address_id" value="{{ $shippingAddress->id }}" />
							<div class="form-group">
					                <label for="shipping_address" class="col-sm-3 control-label">Shipping Address</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_address" id="shipping_address" class="form-control" value="{{ $shippingAddress->shipping_address }}">
					                </div>
								</div>
								<div class="form-group">
									<label for="shipping_address_line_2" class="col-sm-3 control-label">Shippping Address Line 2</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_address_line_2" id="shipping_address_line_2" class="form-control" value="{{ $shippingAddress->shipping_address_2}}">
					                </div>
					            </div>



								<div class="form-group">
									<label for="shipping_city" class="col-sm-3 control-label">Shipping City</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_city" id="shipping_city" class="form-control" value="{{ $shippingAddress->shipping_city}}">
					                </div>
					            </div>

								<div class="form-group">
									<label for="shipping_state" class="col-sm-3 control-label">Shipping State</label>

					                <div class="col-sm-6">
					                    <input type="text" name="shipping_state" id="shipping_state" class="form-control" value="{{ $shippingAddress->shipping_state}}">
					                </div>
					            </div>

								<div class="form-group">
									<label for="billing_state" class="col-sm-3 control-label">Shipping Zip</label>

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
							         	<div class="panel-heading">Inactive Shipping Addresses</div>
									

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

<div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default csrnotes">
                <div class="panel-heading">Notes</div>

                <div class="panel-body">
						<div class="form-group">
							<label for="note_text" class="col-sm-3 control-label">Note</label>

				            <div class="col-sm-6">
				               	<textarea rows="4" cols="50" name="note_text"></textarea>
				            </div>
				       </div>
						 <div class="form-group">
				                <div class="col-sm-offset-3 col-sm-6">
				                    <button type="submit" class="btn btn-default">
				                        <i class="fa fa-plus"></i> Add Note
				                    </button>
				                </div>
				            </div>
						
						<div class="old_notes">	
							@foreach ($csr_notes as $csr_note)
								<div class="noteline">
								<span style="width:50px;">{{$csr_note->created_at}}</span>
								<span>{{$csr_note->note_text}}</span>
								</div>
							@endforeach
						</div>
                </div>
            </div>
        </div>
    </div>

</div>

	
		        </form>
		
			
		    </div>

			 

		
		<!-- end edit form -->

    </div>
</home>
@endsection
