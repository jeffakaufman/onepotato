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
	    	<div class="col-md-8">
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
									<th></th>
									<th></th>
								</tr> 
							</thead>
							<tbody>
							@foreach ($weeksMenus as $weekMenus)
							<?php
								$hold_status = isset($weekMenus->skipStatus->hold_status) ? $weekMenus->skipStatus->hold_status : "";
							?>
								<tr>
									<td class="text-center"><strong>{{ date('n/j',strtotime($weekMenus->delivery_date)) }}</strong></td>
									@if ($hold_status == 'held') 
										<td class="skip-status bg-danger text-danger text-center">SKIPPED <br/><span style="font-size:x-small">set {{ date('n/j',strtotime($weekMenus->skipStatus->updated_at)) }}</span></td>
									@elseif ($hold_status == 'hold') 
										<td class="skip-status bg-warning text-warning text-center">SKIP<br/><span style="font-size:x-small">set {{ date('n/j',strtotime($weekMenus->skipStatus->updated_at)) }}</span></td>
									@elseif ($hold_status == 'released') 
										<td class="skip-status bg-success text-success text-center">RELEASED<br/><span style="font-size:x-small">set {{ date('n/j',strtotime($weekMenus->skipStatus->updated_at)) }}</span></td>
									@else
										<td class="skip-status text-center"></td>
									@endif
									@foreach($weekMenus->weekMenu as $weekMenu)
										<td>{{ $weekMenu }}</strong></td>
									@endforeach
									@if ($hold_status == 'hold') 
										<td><button type="button" class="btn btn-success unskip-delivery" value="{{$weekMenus->delivery_date}}">UNSKIP</button></strong></td>
									@elseif ($hold_status == 'released' || $hold_status == NULL) 
										<td><button type="button" class="btn btn-danger skip-delivery" value="{{$weekMenus->delivery_date}}">SKIP</button></strong></td>
									@else
										<td></td>
									@endif
									<td><button type="button" class="btn btn-primary change-menu" value="{{$weekMenus->delivery_date}}">Change Menus</button></strong></td>
								</tr>
							@endforeach
							@foreach ($upcomingSkipsNoMenu as $upcomingSkipNoMenu)
           						<tr>
           							<td class="text-center"><strong>{{ date('n/j',strtotime($upcomingSkipNoMenu->date_to_hold)) }}</strong></td>
									@if ($upcomingSkipNoMenu->hold_status == 'held') 
										<td class="skip-status bg-danger text-danger text-center">SKIPPED<br/><span style="font-size:x-small">set {{ date('n/j',strtotime($upcomingSkipNoMenu->updated_at)) }}</span></td>
									@elseif ($upcomingSkipNoMenu->hold_status == 'hold') 
										<td class="skip-status bg-warning text-warning text-center">SKIP<br/><span style="font-size:x-small">set {{ date('n/j',strtotime($upcomingSkipNoMenu->updated_at)) }}</span></td>
									@elseif ($upcomingSkipNoMenu->hold_status == 'released') 
										<td class="skip-status bg-success text-success text-center">RELEASED<br/><span style="font-size:x-small">set {{ date('n/j',strtotime($upcomingSkipNoMenu->updated_at)) }}</span></td>
									@else
										<td class="skip-status text-center"></td>
									@endif
           							<td></td>
           							<td></td>
           							<td></td>
           							@if ($upcomingSkipNoMenu->hold_status == 'hold') 
										<td><button type="button" class="btn btn-success unskip-delivery" value="{{$upcomingSkipNoMenu->date_to_hold}}">UNSKIP</button></strong></td>
									@elseif ($upcomingSkipNoMenu->hold_status == 'released') 
										<td><button type="button" class="btn btn-danger skip-delivery" value="{{$upcomingSkipNoMenu->date_to_hold}}">SKIP</button></strong></td>
									@else
										<td></td>
									@endif
									<td></td>
           						</tr>
           					@endforeach
           				</table>
           				
        			</div>
        		</div> 
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
</div> 
<!-- Row 1 End -->
<!-- Row 2 -->
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default ">
			<div class="panel-heading"><h4>Past Deliveries</h4></div>
			<div class="panel-body">
				<div class="col-sm-12">
				</div>										
			</div>	
		</div>
	</div>
</div>
<!-- Row 2 -->
<div class="row">
	<div class="col-md-6">
		<!-- credits panel-->
		@include('admin.users.credits')
	</div>
    <div class="col-md-6">
    	<div class="row">
			@include('admin.users.csr-notes')
		</div>
	</div>
</div>

</home>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Modal title</h4>
			</div>
			<div class="modal-body">
				...
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save changes</button>
			</div>
		</div>
	</div>
</div>

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

		$.get(
			"/admin/user_details/"+_userId+"/edit_product",
			function(data) {
				$("#myModal .modal-content").html(data);
				$("#myModal").modal('show');
			}
		);
	});

	$("button.change-menu").click(function(e) {
		var _date = $(this).val();
		$.get(
				"/admin/user_details/"+_userId+"/edit_menus/"+_date,
				function(data) {
					$("#myModal .modal-content").html(data);
					$("#myModal").modal('show');
				}
		);
	});

	$("#users").on("click", "button.skip-delivery", function(e) {
		if(confirm("Are you sure ?")) {
			var _date = $(this).val();
			var _this = $(this);
			$.get(
					"/admin/user_details/"+_userId+"/skip_delivery/"+_date,
					function(data) {
						if(data.ok == true) {
							_this.removeClass('skip-delivery').removeClass('btn-danger').addClass('btn-success').addClass('unskip-delivery').html('UNSKIP');
							_this.off('click');

							var _statusTd = _this.parent().parent().find("td.skip-status");
							var _now = new Date();
							var _nowText = (1+_now.getMonth()) + '/' + _now.getDate();
							_statusTd.removeClass('bg-success')
									.removeClass('text-success')
									.removeClass('bg-danger')
									.removeClass('text-danger')
									.addClass('bg-warning')
									.addClass('text-warning')
									.html('SKIP<br/><span style="font-size:x-small">set '+_nowText+'</span>');
						} else {
							alert("Something wrong");
						}
					},
					'json'
			);

		}
	});

	$("#users").on("click", "button.unskip-delivery", function(e) {
		if(confirm("Are you sure ?")) {
			var _date = $(this).val();
			var _this = $(this);
			$.get(
					"/admin/user_details/"+_userId+"/unskip_delivery/"+_date,
					function(data) {
						if(data.ok == true) {
							_this.removeClass('unskip-delivery').removeClass('btn-success').addClass('btn-danger').addClass('skip-delivery').html('SKIP');
							_this.off('click');
							var _statusTd = _this.parent().parent().find("td.skip-status");
							var _now = new Date();
							var _nowText = (1+_now.getMonth()) + '/' + _now.getDate();
							_statusTd.removeClass('bg-warning')
									.removeClass('text-warning')
									.removeClass('bg-danger')
									.removeClass('text-danger')
									.addClass('bg-success')
									.addClass('text-success')
									.html('RELEASED<br/><span style="font-size:x-small">set '+_nowText+'</span>');

						} else {
							alert("Something wrong");
						}
					},
					'json'
			);

		}
	});
});
</script>
@endsection
