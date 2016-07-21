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
		
	
						<a href="/user/new/">Create a New User</a><br/><br />
                        @foreach ($users as $user)
						    <div>
						    	<span class="user_name" style="display:inline-block;width:200px;"><a href="/admin/user/{{ $user->id }}">{{ $user->name }}</a></span><span  style="display:inline-block;width:200px;" class="user_name">{{ $user->email }}</span>
	                        </div>
						@endforeach
                    </div>
					


                </div>
            </div>
        </div>
</div>
@endsection