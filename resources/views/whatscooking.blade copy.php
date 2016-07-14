@extends('spark::layouts.app')

@section('content')

@include('menu-edit')

<whatscookings :whatscookings="whatscookings" inline-template>

    <div class="container">
        <!-- Application Dashboard -->
			<!-- Display Validation Errors -->
			@include('errors.errors')
		
		<div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Menu</div>
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
						        {!! Form::label('Ingredients', null,array('class'=>'col-sm-2 control-label')) !!}
						        <div class="col-sm-6">
						        	<div class="row">
						        		<div class="col-sm-4">
						        		{!! Form::checkbox('hasBeef',1,false) !!} Beef<br />
						        		{!! Form::checkbox('hasPoultry',1,false) !!} Poultry<br />
						        		{!! Form::checkbox('hasFish',1,false) !!} Fish<br />
						        		</div>
						        		<div class="col-sm-4">
						        		{!! Form::checkbox('hasLamb',1,false) !!} Lamb<br />
						        		{!! Form::checkbox('hasPork',1,false) !!} Pork<br />
						        		{!! Form::checkbox('hasShellfish',1,false) !!} Shellfish<br />
						        		</div>
						        		<div class="col-sm-4">
						        		{!! Form::checkbox('hasNoGluten',1,false) !!} Gluten-Free<br />
						        		{!! Form::checkbox('hasNuts',1,false) !!} Nuts<br />
						        		</div>  
						        	</div>  
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
							            {!! Form::label('Image', null,array('class'=>'col-sm-2 control-label')) !!}
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
								<div class="col-md-2"><h5>Type</h5></div>
	                        	<div class="col-md-2"><h5>Menu Title</h5></div>
	                        	<div class="col-md-2"><h5>Ingredients</h5></div>
								<div class="col-md-2 text-center"><h5>Image</h5></div>
						</div>
								<div class="row">
									<div class="col-md-12 ">
										<hr style="border-top: 1px solid #d3e0e9;position: relative;left: -30px;">
									</div>
								</div>
				        <div class="row">
							<div class="container">
    	                	    @foreach ($whatscookings as $whatscooking)
		                	        @foreach ($whatscooking->menus()->orderBy('id','desc')->get() as $menu)
								    <div class="row" style="margin-top:20px;margin-bottom:20px">
								    	<div class="col-md-1">{{ date('m/d/y',strtotime($whatscooking->week_of)) }}</div>
								    	<div class="col-md-2">{{ $whatscooking->product_type }}</div>
	            	    	        	<div class="col-md-2"><a href="/menu/{{ $menu->id }}">{{ $menu->menu_title }}</a></div>
	            	    	        	<div class="col-md-2">
	            	    	        		<ul style="list-style-image: url('/img/pot.png');">
		            		            	@if($menu->hasBeef)
		            		            		<li>Beef</li>
											@endif
	        	    		            	@if($menu->hasPoultry)
	            			            		<li>Chicken</li>
											@endif
	            	    		        	@if($menu->hasFish)
	            	    	    	    		<li>Fish</li>
											@endif
	            	    	        		@if($menu->hasLamb)
	            	    	        			<li>Lamb</li>
											@endif
		            		            	@if($menu->hasPork)
	    	        		            		<li>Pork</li>
											@endif
	            			            	@if($menu->hasShellfish)
	            			            		<li>Shellfish</li>
											@endif
	            	    	    	    	@if($menu->hasNoGluten)
	            	    	        			<li>Gluten Free</li>
											@endif
	            	    	        		@if($menu->hasNuts)
	            	    	        			<li>Nuts</li>
											@endif
											<ul>
										</div>
	            	        	    	@if($menu->image)
										<div class="col-md-2 text-center"><img height="100px" src="{{ $menu->image }}"/></div>
										@else
										<div class="col-md-2 text-center"><img height="100px" src="/img/foodpot.jpg"/></div>
										@endif
	            	        	    	<div class="col-md-2 col-md-offset-1"><div class="btn btn-primary" data-toggle="modal" data-whatscooking="{{ $whatscooking }}" data-menu="{{ $menu }}" data-target="#menuEditModal">Edit</div></div>
									</div>
									<div class="row">
										<div class="col-md-12 ">
											<hr style="border-top: 1px solid #d3e0e9;position: relative;left: -30px;">
										</div>
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
</div>
<script>
$('#menuEditModal').on('show.bs.modal', function(e) {
console.log($(e.relatedTarget).data('whatscooking'));
    //get data-id attribute of the clicked element
    var menu = $(e.relatedTarget).data('menu');
    var whatscooking = $(e.relatedTarget).data('whatscooking');

    $("#menuEditModal #week_of").val( whatscooking.week_of );
    $("#menuEditModal #whatscooking_id").val( whatscooking.id );
    $("#menuEditModal #vegetarian_type").prop( "checked",whatscooking.product_type == 'Vegetarian' );
    $("#menuEditModal #omnivore_type").prop( "checked",whatscooking.product_type == 'Omnivore' );
    $("#menuEditModal #menu_title").val( menu.menu_title );
    $("#menuEditModal #menu_id").val( menu.id );
    if (menu.image) {$("#menuEditModal #image").attr("src", menu.image ); }
    $("#menuEditModal #menu_description").val( menu.menu_description );
    $("#menuEditModal #hasBeef").prop( "checked", menu.hasBeef );
    $("#menuEditModal #hasFish").prop( "checked", menu.hasFish );
    $("#menuEditModal #hasLamb").prop( "checked", menu.hasLamb );
    $("#menuEditModal #hasNoGluten").prop( "checked", menu.hasNoGluten );
    $("#menuEditModal #hasNuts").prop( "checked", menu.hasNuts );
    $("#menuEditModal #hasPork").prop( "checked", menu.hasPork );
    $("#menuEditModal #hasPoultry").prop( "checked", menu.hasPoultry );
    $("#menuEditModal #hasShellfish").prop( "checked", menu.hasShellfish );
   // $("#delForm").attr('action', 'put your action here with productId');//e.g. 'domainname/products/' + productId
});
</script>
</whatscookings>
@endsection



