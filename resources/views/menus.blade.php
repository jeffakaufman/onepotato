@extends('spark::layouts.app')

@section('content')
<home :menus="menus" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Menus</div>

                    <div class="panel-body">
                        @foreach ($menus as $menu)
						    <div><span class="menu_id">{{ $menu->id }}</span>
	                        <span class="menu_title"><a href="/menu/{{ $menu->id }}">{{ $menu->menu_title }}</a></span>
							<span style="padding-left:10px;">{{ $menu->menu_description}}</span>
							<span style="padding-left:10px;">{{ $menu->menu_delivery_date}}</span></div>
						@endforeach
                    </div>
					


                </div>
            </div>
        </div>



		
		<div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Menus</div>

                    <div class="panel-body">
                        

							<div class="panel-body">
							        
							        <!-- Display Validation Errors -->
								        @include('errors.errors')
								 

							        <!-- New Task Form -->
							        <form action="{{ url('menus') }}" method="POST" class="form-horizontal">
							            {{ csrf_field() }}

							            <!-- Task Name -->
							            <div class="form-group">
							                <label for="menu_title" class="col-sm-3 control-label">Menu Title</label>

							                <div class="col-sm-6">
							                    <input type="text" name="menu_title" id="menu_title" class="form-control">
							                </div>
										</div>
										<div class="form-group">
											<label for="menu_description" class="col-sm-3 control-label">Menu Description</label>

							                <div class="col-sm-6">
							                    <input type="text" name="menu_description" id="menu_description" class="form-control">
							                </div>
							            </div>

							            <!-- Add Task Button -->
							            <div class="form-group">
							                <div class="col-sm-offset-3 col-sm-6">
							                    <button type="submit" class="btn btn-default">
							                        <i class="fa fa-plus"></i> Add Menu
							                    </button>
							                </div>
							            </div>
							        </form>
							    </div>


                    </div>
					


                </div>
            </div>
        </div>
		

    </div>
</home>
@endsection



