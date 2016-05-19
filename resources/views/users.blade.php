@extends('spark::layouts.app')

@section('content')
<home :menus="menus" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Users</div>

                    <div class="panel-body">
                        @foreach ($users as $user)
						    <div><span style="display:inline-block;width:30px;" class="user_id">{{ $user->id }}</span><span class="user_name" style="display:inline-block;width:200px;"><a href="/user/{{ $user->id }}">{{ $user->name }}</a></span><span  style="display:inline-block;width:200px;" class="user_name">{{ $user->email }}</span>
	                        </div>
						@endforeach
                    </div>
					


                </div>
            </div>
        </div>
</div>