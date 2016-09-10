@extends('spark::layouts.app-admin', ['menuitem' => 'users'])

@section('page_header')

    <h1>
        Users
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection
@section('content')

	<div class="container">
		<!-- Application Dashboard -->
		{{--<div class="row">--}}
			{{--<div class="col-md-10 col-md-offset-1">--}}
				<div class="panel panel-default">
					<div class="">
						<div style="text-align: right;">
							<input type="text" id="filterText" value="{{$params['filterText']}}" />
							<button onclick="document.location.href='/admin/users/updateListParams/filterText/'+$('#filterText').val();">Filter</button>
						</div>
					</div>
					<div class="panel-body">

	<div class="row col-offset-1">
		<div class="user_name col-sm-3"><strong><a href="/admin/users/updateListParams/orderBy/userName">User Name</a></strong></div>
		<div class="user_name col-sm-2"><strong><a href="/admin/users/updateListParams/orderBy/email">Email Address</a></strong></div>
		<div class="user_name col-sm-2 text-center"><strong><a href="/admin/users/updateListParams/orderBy/startDate">First Delivery Date</a></strong></div>
		<div class="user_name col-sm-1 text-center"><strong><a href="/admin/users/updateListParams/orderBy/revenue">Revenue</a></strong></div>
		<div class="user_name col-sm-2 text-center"><strong><a href="/admin/users/updateListParams/orderBy/status">Status</a></strong></div>
	</div>
	@foreach ($users as $user)
	<div class="row">
		{{--<div class="user_name col-sm-3" ><a href="/admin/user/{{ $user->id }}">{{ $user->name }}</a></div>--}}
		<div class="user_name col-sm-3" ><a href="/admin/user_details/{{ $user->id }}">{{ $user->name }}</a></div>
		<div class="user_name col-sm-2">{{ $user->email }}</div>
		<div class="user_name col-sm-2 text-center">{{ date('m/d/y', strtotime($user->start_date)) }}</div>
		<div class="user_name col-sm-1 text-right">${{ $user->revenue }}</div>
		<div class="user_name col-sm-2 text-center">
			@if ($user->status == 'active')
			<strong><span class="label label-success">Active</span></strong>
			@elseif ($user->status == 'cancelled')
			<strong><span class="label label-danger">Cancelled</span></strong>
			@endif
		</div>
	</div>
	@endforeach

					</div>
				{{--</div>--}}
			{{--</div>--}}
		</div>
	</div>
@endsection