@extends('spark::layouts.app-admin', ['menuitem' => 'users'])



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
		<div class="row">
		<!-- Row 1 ---!>
	    	<div class="col-md-10">
		    	<div class="panel panel-default ">
		       		<div class="panel-heading"><strong>{{ $userProduct->product_description }}<br />{{ $userSubscription->dietary_preferences }}</strong></div>
					<div class="panel-body">
						<h3>Upcoming Deliveries</h3>
						<div class="row">
							@foreach ( $weeksMenus as $i => $weeksMenu)
							<?php
								if ($i == 0) {
									$delivery_date = $weeksMenu->delivery_date;
							?>
								<div class="col-md-1 text-center"><strong>{{ date('n/j',strtotime($weeksMenu->delivery_date)) }}</strong></div>
								@if ($weeksMenu->hold_status == 'held') 
								<div class="col-md-1 bg-danger text-danger text-center">SKIPPED</div> 
								@elseif ($weeksMenu->hold_status == 'hold') 
								<div class="col-md-1 bg-warning text-warning text-center">SKIP</div>  
								@elseif ($weeksMenu->hold_status == 'released') 
								<div class="col-md-1 bg-success text-success text-center">RELEASED</div> 
								@else
								<div class="col-md-1"></div> 
								@endif	
							<?php	} ?>
							<?php	if ($delivery_date != $weeksMenu->delivery_date) {
							?>
							</div>	<div class="row"> 
								<div class="col-md-1 text-center"><strong>{{ date('n/j',strtotime($weeksMenu->delivery_date)) }}</strong></div>
								@if ($weeksMenu->hold_status == 'held') 
								<div class="col-md-1 bg-danger text-danger text-center">SKIPPED</div> 
								@elseif ($weeksMenu->hold_status == 'hold') 
								<div class="col-md-1 bg-warning text-warning text-center">SKIP</div>  
								@elseif ($weeksMenu->hold_status == 'released') 
								<div class="col-md-1 bg-success text-success text-center">RELEASED</div> 
								@else
								<div class="col-md-1"></div> 
								@endif	
							<?php	} ?>	
								<div class="col-md-3">{{ $weeksMenu->menu_title }}	</div>						
							<?php $delivery_date = $weeksMenu->delivery_date ?>							
							@endforeach
           					</div>
						</div>
        			</div>
        		</div> 
        	</div> 
		    <div class="col-md-3"><!-- Shipping Address ---!>
		       	<div class="panel panel-default ">
		           	<div class="panel-heading"><strong>Shipping Address</strong></div>
					<div class="panel-body">
						<div class="col-sm-12">
					    	<address>
					        	{{ $shippingAddress->shipping_address }}<br />
					            @if ($shippingAddress->shipping_address_2)
					            {{ $shippingAddress->shipping_address_2 }}<br />
					            @endif
					            {{ $shippingAddress->shipping_city }}, {{ $shippingAddress->shipping_state }} {{ $shippingAddress->shipping_zip}}<br />
					            <a href="mailto:{{ $user->email}}">{{ $user->email }}</a>
					            </address>
						</div>										
					</div>	
				</div>
			</div>
		</div> 
			<!-- Row 1 End ---!>

			<!-- Row 3 ---!>
			<div class="row">
           		<div class="col-md-6"><!--Referrals ---!>
           			<div class="row">
					@include('admin.users.csr-notes')
					</div>
				</div>
			</div>


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
