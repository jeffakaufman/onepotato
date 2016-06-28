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
								 
							        <!-- New Menu Form -->
							        {!! Form::open(
							            array(
							                'url' => 'menufile', 
							                'class' => 'form-horizontal', 
							                'files' => true)) !!}

							        <div class="form-group">
							            {!! Form::label('Menu Title', null,array('class'=>'col-sm-3 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::text('menu_title', null, array('placeholder'=>'Menu Title','class'=>'form-control')) !!}
							        	</div>
							        </div>

							        <div class="form-group">
							            {!! Form::label('Menu Description', null,array('class'=>'col-sm-3 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::text('menu_description', null, array('placeholder'=>'Menu Description','class'=>'form-control')) !!}
							        	</div>
							        </div>

							        <div class="form-group">
							            {!! Form::label('Product Image', null,array('class'=>'col-sm-3 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::file('image', null, array('class'=>'form-control')) !!}
							        	</div>
							        </div>

							        <div class="form-group">
							        	<div class="col-sm-offset-3 col-sm-6"><button type="submit" class="btn btn-default">
							                        <i class="fa fa-plus"></i> Add Menu
							        							                    </button>
							        </div>
							        	</div>
							                   
							        {!! Form::close() !!}
							        </div>
							       
							    </div>


                    </div>
					


                </div>
            </div>
        </div>
		

    </div>
</home>
@endsection



