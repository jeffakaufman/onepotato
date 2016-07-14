@extends('spark::layouts.app-admin')

@section('page_header')

@include('menu-edit')
    <h1>
        New User
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection
<home :menu="menu" inline-template>

<div class="container">
		
		<form method="POST"  class="form-horizontal">
			
			<div class="row">
	            <div class="col-md-8 col-md-offset-2">
	                <div class="panel panel-default">
	                    <div class="panel-heading">New User</div>

	                    <div class="panel-body">
							<!-- Display Validation Errors -->
						        @include('errors.errors')


					        <!-- New Task Form -->

					            {{ csrf_field() }}
							 	<div class="form-group">
					                <label for="user_name" class="col-sm-3 control-label">Name</label>

					                <div class="col-sm-6">
					                    <input type="text" name="user_name" id="user_name" class="form-control" value="">
					                </div>
								</div>
								
								<div class="form-group">
									<label for="user_email" class="col-sm-3 control-label">Email</label>

					                <div class="col-sm-6">
					                    <input type="text" name="user_email" id="user_email" class="form-control" value="">
					                </div>
					            </div>
					
									<div class="form-group">
										<label for="user_password" class="col-sm-3 control-label">Password</label>

						                <div class="col-sm-6">
						                    <input type="password" name="user_password" id="user_password" class="form-control" value="">
						                </div>
						            </div>



					            <div class="form-group">
					                <div class="col-sm-offset-3 col-sm-6">
					                    <button type="submit" class="btn btn-default">
					                        <i class="fa fa-plus"></i> Save New User
					                    </button>
					                </div>
					            </div>
							
	                    </div>
	                </div>
	            </div>
	        </div>
	
		</form>

<!--End Container -->
</div>
</home>
@endsection
