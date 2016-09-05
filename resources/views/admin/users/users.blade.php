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
	<div class="row  col-offset-1">
		<div class="user_name col-sm-3"><strong>User Name</strong></div>
		<div class="user_name col-sm-2"><strong>Email Address</strong></div>
		<div class="user_name col-sm-2 text-center"><strong>First Delivery Date</strong></div>
		<div class="user_name col-sm-2 text-center"><strong>Status</strong></div>
	</div>
	@foreach ($users as $user)
	<div class="row">
		<div class="user_name col-sm-3" ><a href="/admin/user/{{ $user->id }}">{{ $user->name }}</a></div>
		<div class="user_name col-sm-2">{{ $user->email }}</div>
		<div class="user_name col-sm-2 text-center">{{ date('m/d/y', strtotime($user->start_date)) }}</div>
		<div class="user_name col-sm-2 text-center"><strong>
			@if ($user->userSubscription->status == 'active')
			<span class="label label-success">Active</span></strong></div>
			@elseif ($user->userSubscription->status == 'cancelled')
			<span class="label label-danger">Cancelled</span></strong></div>
			@endif
	</div>
	@endforeach
@endsection