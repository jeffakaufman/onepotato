@extends('spark::layouts.app-admin', ['menuitem' => 'users'])



@section('page_header')

    <h1>
        {{ $user->name }}
	@if ($user->status == "inactive" || $user->status == "active" )
		<span class="bg-success" style="color:darkgreen;font-size:14px">ACTIVE</span>
	@elseif ($user->status == 'inactive-cancelled')
		<span class="bg-danger text-center" style="color:darkred;font-size:14px">CANCELLED</span>
	@endif
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection
@section('content')
<home :menu="menu" inline-template>
<strong>{{ $userProduct->product_description }} {{ $userSubscription->dietary_preferences }}</strong>
<span style="padding-left:30px;"><a href="#" id="productEditLink">Edit</a></span>
<br>	
		<div class="row">
		<!-- Row 1 -->
	    	<div class="col-md-7">
		    	<div class="panel panel-default ">
		       		<div class="panel-heading"><h4>Upcoming Deliveries</h4></div>
					<div class="panel-body">
					
						<table id="users" class="table table-striped table-hover table-order-column" width="100%" cellspacing="0">
							<thead> 
								<tr>
        	    					<th class="text-center">Date</th>
									<th class="text-center">Status</th>
									<th>Menu #1</th>
									<th>Menu #2</th>
									<th>Menu #3</th>
								</tr> 
							</thead>
							<tbody> 
								<tr>
								@foreach ( $weeksMenus as $i => $weeksMenu)
								<?php
									if ($i == 0) {
										$delivery_date = $weeksMenu->delivery_date;
								?>
								<td class="text-center"><strong>{{ date('n/j',strtotime($weeksMenu->delivery_date)) }}</strong></td>
								@if ($weeksMenu->hold_status == 'held') 
								<td class="bg-danger text-danger text-center">SKIPPED</td>
								@elseif ($weeksMenu->hold_status == 'hold') 
								<td class="bg-warning text-warning text-center">SKIP</td>  
								@elseif ($weeksMenu->hold_status == 'released') 
								<td class="bg-success text-success text-center">RELEASED</td>
								@else
								<td ></td> 
								@endif	
							<?php	} ?>
							<?php	if ($delivery_date != $weeksMenu->delivery_date) {
							?>
							</tr>
							<tr> 
								<td class="text-center"><strong>{{ date('n/j',strtotime($weeksMenu->delivery_date)) }}</strong></td>
								@if ($weeksMenu->hold_status == 'held') 
								<td class="bg-danger text-danger text-center">SKIPPED</td>
								@elseif ($weeksMenu->hold_status == 'hold') 
								<td class="bg-warning text-warning text-center">SKIP</td> 
								@elseif ($weeksMenu->hold_status == 'released') 
								<td class="bg-success text-success text-center">RELEASED</td>
								@else
								<td></td>
								@endif	
							<?php	} ?>	
								<td >{{ $weeksMenu->menu_title }}	</td>						
							<?php $delivery_date = $weeksMenu->delivery_date ?>						
							@endforeach
           					</tr>
           					@foreach ($upcomingSkipsNoMenu as $upcomingSkipNoMenu)
           					<tr>
           						<td class="text-center"><strong>{{ date('n/j',strtotime($upcomingSkipNoMenu->date_to_hold)) }}</strong></td>
								@if ($upcomingSkipNoMenu->hold_status == 'held') 
								<td class="bg-danger text-danger text-center">SKIPPED</td>
								@elseif ($upcomingSkipNoMenu->hold_status == 'hold') 
								<td class="bg-warning text-warning text-center">SKIP</td>  
								@elseif ($upcomingSkipNoMenu->hold_status == 'released') 
								<td class="bg-success text-success text-center">RELEASED</td>
								@else
								<td ></td> 
								@endif
           						<td></td>
           						<td></td>
           						<td></td>
           					</tr>
           					@endforeach
           				</table>
           				
        			</div>
        		</div> 

					<!-- credits panel-->
					@include('admin.users.credits')
					

        	</div> 
			
		
		    
			<div class="col-md-3"><!-- Shipping Address -->
		       	<div class="panel panel-default ">
		           	<div class="panel-heading">
						<div style="float:left;"><strong>Shipping Address</strong></div>
						<div style="float:right;" id="shippingAddressEditLinkContainer"><a href="#" id="editShippingAddressLink">Edit</a></div>
						<div style="float:right;display:none;" id="shippingAddressEditCancelLinkContainer" ><a href="#" id="cancelEditShippingAddressLink">Cancel</a></div>
						<div style="clear:both"></div>
					</div>
					<div class="panel-body">
						<div class="col-sm-12" id="shippingAddressContainer">
					    	<address>
					        	{{ $shippingAddress->shipping_address }}<br />
					            @if ($shippingAddress->shipping_address_2)
					            	{{ $shippingAddress->shipping_address_2 }}<br />
					            @endif
								{{ $shippingAddress->shipping_city }}, {{ $shippingAddress->shipping_state }} {{ $shippingAddress->shipping_zip}}<br />
					            <a href="mailto:{{ $user->email}}">{{ $user->email }}</a>
							</address>
						</div>

						<div class="col-sm-12" id="shippingAddressEditContainer" style="display:none;">
							Yo
						</div>

					</div>	
				</div>
			@if ($user->status == "inactive" || $user->status == "active" )
				<div class="row">
					<div class="col-md-10 ">
					<form action="{{ url('admin/user') }}/send_cancel_link/{{ $user->id }}" method="POST" class="form-horizontal" onsubmit="return confirm('Are you sure you would like to send a cancellation link?');">
						{{ csrf_field() }}
						<input type="hidden" name="user_id" value="{{ $user->id }}" />
					<div>
					<div class="form-group">
						<div class="col-sm-3">
							<button type="submit" class="btn btn-danger">
								<i class="fa fa-mail"></i> Send Cancellation Link
							</button>
						</div>
					</div>
					</form>
				</div>
			@elseif ($user->status == 'inactive-cancelled')
			
				<div class="row">
					<div class="col-md-10 ">
					<form action="{{ url('admin/user') }}/cancel/restart/{{ $user->id }}" method="POST" class="form-horizontal" onsubmit="return confirm('Are you sure you would like to restart this subscription?');">
						{{ csrf_field() }}
						<input type="hidden" name="user_id" value="{{ $user->id }}" />
					<div>
					<div class="form-group">
						<div class="col-sm-3">
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-mail"></i> Restart Subscription
							</button>
						</div>
					</div>
					</form>
				</div>
			@endif
			
			
		</div>
	</div>
</div> 
			<!-- Row 1 End -->
			
	
		
			<!-- Row 2 -->
			<div class="row">
           		<div class="col-md-6"><!--Referrals -->
           			<div class="row">
					@include('admin.users.csr-notes')
					</div>
				</div>
			</div>

</home>

<script type="text/javascript">
var _userId = '{{$user->id}}';
var _shippingAddressId = '{{$shippingAddress->id}}'

$(document).ready(function() {
	$("#editShippingAddressLink").click(function(e) {
		e.preventDefault();

		$.get(
			"/admin/user_details/"+_userId+"/edit_shipping_address/"+_shippingAddressId,
			function(data) {
				var $_token = "{{ csrf_token() }}";

				$("#shippingAddressEditContainer").html(data).show();
				$("#shippingAddressContainer").hide();

				$("#shippingAddressEditLinkContainer").hide();
				$("#shippingAddressEditCancelLinkContainer").show();

				$(".shipping-address-form .save-button").click(function() {
					$.post(
						"/admin/user_details/"+_userId+"/edit_shipping_address/"+_shippingAddressId,
						{
							_token: $_token,
							address1: $(".shipping-address-form input[name=address1]").val(),
							address2: $(".shipping-address-form input[name=address2]").val(),
							city: $(".shipping-address-form input[name=city]").val(),
							state: $(".shipping-address-form select[name=state]").val(),
							zip: $(".shipping-address-form input[name=zip]").val(),
						},
						function(data) {
							$("#shippingAddressContainer").html(data).show();
							$("#shippingAddressEditContainer").hide();

							$("#shippingAddressEditLinkContainer").show();
							$("#shippingAddressEditCancelLinkContainer").hide();
						}
					);
				});
			}
		);

	});

	$("#cancelEditShippingAddressLink").click(function(e) {
		e.preventDefault();

		$("#shippingAddressEditContainer").hide();
		$("#shippingAddressContainer").show();

		$("#shippingAddressEditLinkContainer").show();
		$("#shippingAddressEditCancelLinkContainer").hide();

	});


	$("#productEditLink").click(function(e) {
		e.preventDefault();
		alert("TBD");
	});
});
</script>
@endsection
