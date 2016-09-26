@extends('spark::layouts.app-admin', ['menuitem' => 'users'])


@include('admin.users.previous-shipping')
@section('page_header')

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

<div class="container">
	<form action="{{ url('user') }}/{{ $user->id }}" method="POST" class="form-horizontal">
		<div class="row">
		<!-- Row 1 ---!>
       		<div class="row"> 
	        	<div class="col-md-12">
		       		<div class="panel panel-default ">
		       		<div class="panel-heading"><strong>Personal Information</strong></div>
					<div class="panel-body">
		    			<!-- Display Validation Errors -->
			   			@include('errors.errors')
		       			<!-- User -->
		       			{{ csrf_field() }}
							<input type="hidden" name="user_id" value="{{ $user->id }}" />
							<div class="row">
								<div class="col-sm-2">
	        						<input type="text" name="first_name" id="first_name" class="form-control" value="{{ $user->first_name }}" placeholder="First Name">
	           					</div>
								<div class="col-sm-2">
        							<input type="text" name="last_name" id="last_name" class="form-control" value="{{ $user->last_name }}" placeholder="Last Name">
            					</div>
								<div class="col-sm-3">
									<input type="email" name="user_email" id="user_email" class="form-control" value="{{ $user->email}}" placeholder="Email">
           						</div>
								<div class="col-sm-3">
									{{ $userProduct->product_description }}<br> {{ $userSubscription->dietary_preferences }}</i>
           						</div>
								<div class="col-sm-1">
   					    			<button type="submit" class="btn btn-default">
	       		    	    			<i class="fa fa-plus"></i> Update
	       							</button>
	            				</div>
							</div>
            			</div>
        			</div>
				</div>
			</div> 
			<!-- Row 1 End ---!>
			<!-- Row 2 ---!>
			<div class="row"><!--Current Addresses ---!>
    			<div class="col-md-6"><!-- Billing Address ---!>
		    		<div class="panel panel-default ">
		            	<div class="panel-heading"><strong>Billing Address</strong></div>
						<div class="panel-body">
							<div class="form-group">
								<div class="col-sm-10 col-sm-offset-1">
									<input type="text" name="billing_address" id="billing_address" class="form-control" value="{{ $user->billing_address }}" placeholder="Billing Address">
				    			</div>
							</div>
							<div class="form-group">
								<div class="col-sm-10 col-sm-offset-1">
									<input type="text" name="billing_address_line_2" id="billing_address" class="form-control" value="{{ $user->billing_address_line_2}}" placeholder="Billing Address 2">
								</div>
							</div>
							<div class="form-group">
		    					<div class="col-sm-10 col-sm-offset-1">
    		    					<input type="text" name="billing_city" id="billing_city" class="form-control" value="{{ $user->billing_city}}" placeholder="Billing City">
		            			</div>
    	        			</div>
    	        			<div class="form-group">
								<div class="col-sm-3 col-sm-offset-1">
									{{ Form::select('billing_state', $states, $user->billing_state, ['class' => 'form-control col-sm-3']) }}
								</div>
				    		<div class="col-sm-4">
				        		<input type="text" name="shipping_zip" id="shipping_zip" class="form-control" value="{{ $user->billing_zip }}" placeholder="Billing Zip">
							</div>
				       	</div>
					</div>
		       	</div><!-- Billing Address ---!>
			</div>
		    	<div class="col-md-6"><!-- Shipping Address ---!>
		        	<div class="panel panel-default ">
		            	<div class="panel-heading"><strong>Shipping Address</strong></div>
							@foreach ($shippingAddresses as $shippingAddress)
								@if ($shippingAddress->is_current === 1)	
						<div class="panel-body">
							<input type="hidden" name="shipping_address_id" value="{{ $shippingAddress->id }}" />
							<div class="form-group">
					           	<div class="col-sm-10 col-sm-offset-1">
					               	<input type="text" name="shipping_address" id="shipping_address" class="form-control" value="{{ $shippingAddress->shipping_address }}" placeholder="Shipping Address">
					           	</div>
							</div>
							<div class="form-group">
								<div class="col-sm-10 col-sm-offset-1">
					               	<input type="text" name="shipping_address_line_2" id="shipping_address_line_2" class="form-control" value="{{ $shippingAddress->shipping_address_2}}" placeholder="Shipping Address 2">
					           	</div>
					        </div>
							<div class="form-group">
								<div class="col-sm-10 col-sm-offset-1">
					               	<input type="text" name="shipping_city" id="shipping_city" class="form-control" value="{{ $shippingAddress->shipping_city}}" placeholder="Shipping City">
					            </div>
					        </div>
					        <div class="form-group">
								<div class="col-sm-3 col-sm-offset-1">
									{{ Form::select('shipping_state', $states, $shippingAddress->shipping_state, ['class' => 'form-control col-sm-3']) }}
					               </div>
								<div class="col-sm-4">
						        	<input type="text" name="shipping_zip" id="shipping_zip" class="form-control" value="{{ $shippingAddress->shipping_zip}}" placeholder="Shipping Zip">
						    	</div>
								<div class="col-sm-1">
								@if (count($shippingAddresses) > 1)
								<div class="btn btn-primary" data-toggle="modal" data-whatscooking="" data-menu="" data-target="#previousAddressModal"><i class="fa fa-archive "></i> Previous</div></div>
								@endif

	            				</div>
						    </div>										
						</div>	
					</div>
								@endif
							@endforeach
				</div>
			</div>
			<!-- Row 2 End ---!>
			<!-- Row 3 ---!>
			<div class="row">
				<div class="col-md-6"><!--Credit Card ---!>
		        	<div class="panel panel-default ">
		            	<div class="panel-heading"><strong>Credit Card Info</strong></div>
						<div class="panel-body">
							<input type="hidden" name="user_id" value="{{ $user->id }}" />
							<div class="form-group">
   	    	   					<div class="col-sm-10 col-sm-offset-1">
        	        				<select name="card_type" type="select" class="form-control">
        	        					<option>Visa</option>
        	        					<option>Mastercard</option></select>
        	      				</div>
							</div>
							<div class="form-group">
            					<div class="col-sm-10 col-sm-offset-1">
            						<input type="text" name="card" id="user_name" class="form-control" value="" placeholder="Card Number">
        	    				</div>
							</div>
							<div class="form-group">
								<div class="col-sm-3 col-sm-offset-1">
				       				<select type="select" class="form-control"  name="card_month"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option><option>11</option><option>12</option></select>
			        			</div>
								<div class="col-sm-2">
				    				<select type="select" class="form-control"  name="card_month"><option>2016</option><option>2017</option><option>2018</option><option>2019</option><option>2020</option><option>2021</option><option>2022</option></select>
				    			</div>
				    			<div class="col-sm-2">
		    		   				<input type="text" name="cvv" id="user_name" class="form-control" value="" placeholder="CVV">
		    	    			</div>
							</div>
						</div>
					</div>
				</div><!--Credit Card ---!>
           		<div class="col-md-6"><!--Referrals ---!>
           			<div class="row">
           				<div class="col-md-12">
                				<div class="panel panel-default">
                 					<div class="panel-heading"><strong>Referrals</strong></div>
									<?PHP		
										$subsrcribeCount = 0;
									?>
									<div class="panel-body">
										@foreach ($referrals as $referral)
											<?PHP if ($referral->did_subscribe==1) { 
													$subsrcribeCount += 1;
													?><strong><?php } ?>
											<?php if ($referral->did_subscribe==1) {$hasSubscribed=" did subscribe.";}else{$hasSubscribed=" did not subscribe.";}?>
										<div class="row">
											<div class="col-sm-10 col-sm-offset-1">
												{{ $referral->referral_email }} was sent on {{ $referral->created_at }} and {{ $hasSubscribed }}
		               	        			</div>
		               	        		</div>
										<?php if ($referral->did_subscribe==1) { ?></strong><br /><?php } ?>
										@endforeach
										
										<div class="row">
											<div class="col-sm-10 col-sm-offset-1">
												@if ($subsrcribeCount > 0)
													<strong><?PHP echo $subsrcribeCount ?> referrals</strong>
												@else
													No referrals
												@endif
											</div>
										</div>
										<div class="row">
											<div class="col-sm-10 col-sm-offset-1">
												<h5 style="border-bottom-style: solid;"><strong>Send Referral for {{ $user->name }}</strong></h5>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
											<!-- Display Validation Errors -->
					       					@include('errors.errors')
						        			<!-- New Task Form -->
					            			{{ csrf_field() }}
											<input type="hidden" name="user_id" value="{{ $user->id }}" />
											<div class="form-group">
												<div class="col-sm-10 col-sm-offset-1">
			                    						<input type="email" name="send_email" id="send_email" class="form-control" placeholder="Email Address">
			                					</div>
			            					</div>
			            					<div class="form-group">
		                						<div class="col-sm-10 col-sm-offset-1">
		                    						<textarea name="custom_message" id="custom_message" class="form-control" placeholder="Referral Message"></textarea>
		                						</div>
		            						</div>
												<div class="form-group">
		    	            						<div class="col-sm-offset-8 col-sm-3">
		        	            						<button type="submit" class="btn btn-default">
		            	            						<i class="fa fa-plus"></i> Send Referral
		                	    						</button>
		                							</div>
		            							</div>
		            						</div>
		        						</div>
               						</div>
               					</div>
							</div>
						</div>
					</div>
				@include('admin.users.csr-notes')
			</form>
<hr />
<br />
<br />
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<form action="{{ url('admin/user') }}/send_cancel_link/{{ $user->id }}" method="POST" class="form-horizontal" onsubmit="return confirm('Are you sure??');">
				{{ csrf_field() }}
				<input type="hidden" name="user_id" value="{{ $user->id }}" />
				<div>
					<div class="form-group">
						<div class="col-sm-3">
							<button type="submit" class="btn btn-default">
								<i class="fa fa-mail"></i> Send Cancellation Link
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</home>
@endsection
