@extends('spark::layouts.app')

@section('content')

<whatscookings :whatscookings="whatscookings" inline-template>

    <div class="container">
        <!-- Application Dashboard -->
			<!-- Display Validation Errors -->
			@include('errors.errors')
		
		<div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Week</div>
                    	<div class="panel-body">
							 
							<!-- New Menu Form -->
							    {!! Form::open(
							        array(
							            'url' => 'whatscooking', 
							            'class' => 'form-horizontal', 
							            'files' => true)) !!}          
						    <div class="form-group">
						        {!! Form::label('Type', null,array('class'=>'col-sm-2 control-label')) !!}
						        <div class="col-sm-6">
						        @if( $last)
						        	{!! Form::hidden('last_id',$last->id); !!}  
						    	    {!! Form::radio('product_type', 'Omnivore',$last->product_type=='Omnivore') !!} Omnivore<br />
						           	{!! Form::radio('product_type', 'Vegetarian',$last->product_type=='Vegetarian') !!} Vegetarian
						        @else
						       	    {!! Form::radio('product_type', 'Omnivore') !!} Omnivore<br />
						           	{!! Form::radio('product_type', 'Vegetarian') !!} Vegetarian
						        @endif
						        </div>
						    </div>
						    <div class="form-group">
						        {!! Form::label('Week Of', null,array('class'=>'col-sm-2 control-label')) !!}
						        <div class="col-sm-6">
						        @if( $last)
						      	    {!! Form::date('week_of', $last->week_of); !!}
						        @else
						      	    {!! Form::date('week_of', \Carbon\Carbon::now()); !!}
						        @endif
						       	</div>
						    </div>
							        <div class="form-group">
							            {!! Form::label('Title', null,array('class'=>'col-sm-2 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::text('menu_title', null, array('placeholder'=>'Menu Title','class'=>'form-control')) !!}
							        	</div>
							        </div>

							        <div class="form-group">
							            {!! Form::label('Description', null,array('class'=>'col-sm-2 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::text('menu_description', null, array('placeholder'=>'Menu Description','class'=>'form-control')) !!}
							        	</div>
							        </div>

							        <div class="form-group">
							            {!! Form::label('Recipe Card', null,array('class'=>'col-sm-2 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::file('image', null, array('class'=>'form-control')) !!}
							        	</div>
							        </div>
					        <div class="form-group">
					        	<div class="col-sm-offset-3 col-sm-6"><button type="submit" class="btn btn-default">
			                        <i class="fa fa-plus"></i> Add Menu</button>
					        	</div>
					        </div>
						        {!! Form::close() !!}
						</div>
                </div>
            </div>
        </div>
        
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                 	<div class="panel-heading">What's Cooking!</div>
					<div class="panel-body">
					<div class="container">
                    	<div class="row">
	                        	<div class="col-md-1"><h5>Week</h5></div>
								<div class="col-md-1"><h5>Type</h5></div>
	                        	<div class="col-md-2"><h5>Menu Title</h5></div>
								<div class="col-md-2"><h5>Image</h5></div>
						</div>
				        <div class="row">
    	                    @foreach ($whatscookings as $whatscooking)
		                        @foreach ($whatscooking->menus()->orderBy('id','desc')->get() as $menu)
							    <div class="row" style="margin-bottom:20px">
							    	<div class="col-md-1">{{ date('m/d/y',strtotime($whatscooking->week_of)) }}</div>
							    	<div class="col-md-1">{{ $whatscooking->product_type }}</div>
	            	            	<div class="col-md-2"><a href="/menu/{{ $menu->id }}">{{ $menu->menu_title }}</a></div>
	            	            	@if($menu->image )
									<div class="col-md-2"><img height="100px" src="{{ $menu->image }}"/></div>
									@else
									<div class="col-md-2"><img height="100px" src="/img/foodpot.jpg"/></div>
									@endif
	            	            	<div class="col-md-2"><div class="btn btn-default"">Edit</div></div>
	            	            	
								</div>
								@endforeach
    	                    @endforeach
                    	</div>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</whatscookings>
@endsection



