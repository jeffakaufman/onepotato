@extends('spark::layouts.app')

@section('content')
<home :menus="menus" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
			<!-- Display Validation Errors -->
			@include('errors.errors')
		
		<div class="row">
            <div class="col-md-9 col-md-offset-2">
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
						        {!! Form::label('Product Type', null,array('class'=>'col-sm-3 control-label')) !!}
						        <div class="col-sm-6">
						    	    {!! Form::radio('product_type', 'Omnivore') !!} Omnivore<br />
						           	{!! Form::radio('product_type', 'Vegetarian') !!} Vegetarian
						        </div>
						    </div>
						    <div class="form-group">
						        {!! Form::label('Week Of', null,array('class'=>'col-sm-3 control-label')) !!}
						        <div class="col-sm-6">
						      	    {!! Form::date('week_of', \Carbon\Carbon::now()); !!}
						       	</div>
						    </div>
					        <div class="form-group">
					        	<div class="col-sm-offset-3 col-sm-6"><button type="submit" class="btn btn-default">
			                        <i class="fa fa-plus"></i> Add What's Cooking</button>
					        	</div>
					        </div>
						        {!! Form::close() !!}
						</div>
                </div>
            </div>
        </div>
        
        
        <div class="row">
            <div class="col-md-9 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">What's Cooking!</div>

                    <div class="panel-body">
				        <div class="row">
            				<div class="col-md-9">
    	                    @foreach ($whatscookings as $whatscooking)
							    @include('menus', ['whatscooking'=>$whatscooking,'menus'=>$whatscooking->menus()->get()])
							@endforeach
                    		</div>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection



