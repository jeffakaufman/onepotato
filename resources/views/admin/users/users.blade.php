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
<home :menus="menus" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Users</div>

                    <div class="panel-body">
						    <div class="row">
						    	<div class="user_name col-sm-3"><strong>User Name</strong></div>
						    	<div class="user_name col-sm-5"><strong>Email Address</strong></div>
						    	<div class="user_name col-sm-3 text-center"><strong>First Delivery Date</strong></div>
	                        </div>
		
	                  @foreach ($users as $user)
						    <div class="row">
						    	<div class="user_name col-sm-3" ><a href="/admin/user/{{ $user->id }}">{{ $user->name }}</a></div>
						    	<div class="user_name col-sm-5">{{ $user->email }}</div>
						    	<div class="user_name col-sm-3 text-center">{{ date('m/d/y', strtotime($user->start_date)) }}</div>
	                        </div>
						@endforeach
                    </div>
					


                </div>
            </div>
        </div>
</div>
@endsection