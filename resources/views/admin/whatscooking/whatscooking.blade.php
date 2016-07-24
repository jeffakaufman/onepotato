@extends('spark::layouts.app-admin', ['menuitem' => 'whatscooking'])

@section('page_header')

@include('admin.whatscooking.menu-edit')
    <h1>
        What's Cooking
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection
@section('content')
<whatscookings :whatscookings="whatscookings" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
			<!-- Display Validation Errors -->
			@include('errors.errors')
		
		<div class="row">
            <div class="col-md-9">
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
						        <div class="col-sm-2">
						        @if( $last)
                   					{!! Form::select('week_of', $upcomingDates,$last->week_of,array('class'=>'form-control')); !!}
						        @else
                   					{!! Form::select('week_of', $upcomingDates,null,array('class'=>'form-control')); !!}
                   					
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
            <div class="col-md-9">
                <div class="panel panel-default">
                 	<div class="panel-heading">What's Cooking!</div>
					<div class="panel-body">
					<div class="container">
                    	<div class="row">
							<div class="container col-md-9">
	                        	<div class="col-md-1"><strong>Week</strong></div>
								<div class="col-md-2"><strong>Type</strong></div>
	                        	<div class="col-md-2"><strong>Title</strong></div>
	                        	<div class="col-md-2"><strong>Ingredients</strong></div>
								<div class="col-md-2 text-center"><strong>Image</strong></div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-9">
								<hr style="border-top: 1px solid #d3e0e9;position: relative;left: -30px;margin-top:3px;margin-bottom:0px">
							</div>
						</div>
				        <div class="row">
							<div class="container col-md-9">
    	                	    @foreach ($whatscookings as $whatscooking)
		                	        @foreach ($whatscooking->menus()->orderBy('id','desc')->get() as $menu)
								    <div class="row" style="margin-top:20px;margin-bottom:20px">
								    	<div class="col-md-1">{{ date('m/d/y',strtotime($whatscooking->week_of)) }}</div>
								    	<div class="col-md-2">{{ $whatscooking->product_type }}</div>
	            	    	        	<div class="col-md-2">{{ $menu->menu_title }}</div>
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
    
    Date.prototype.addDays = function(days) {
    var dat = new Date(this.valueOf())
    	dat.setDate(dat.getDate() + days);
    	return dat;
	}

	function getAllDays() {
	    var s = new Date();
	    var e = new Date(s);
	    var a = [];
    
    	e.setDate(e.getDay() +126);

    	while(s < e) {
    	    if (s.getDay() == 1 ) {a.push(s)};
    	    s = new Date(s.setDate(
    	        s.getDate() + 1
    	    ))
        
    	}

    	return a;
	};

    var menu = $(e.relatedTarget).data('menu');
    var whatscooking = $(e.relatedTarget).data('whatscooking');
    
	var weekOfCompare = new Date (whatscooking.week_of)
    var weekOfDiv = document.getElementById("dateSelect");

	//Create array of options to be added
	var array = getAllDays();

	//Create and append select list
	var week_of = document.createElement("select");
	
    weekOfCompare = weekOfCompare.getFullYear()+"-"+(weekOfCompare.getMonth()+1)+"-"+(weekOfCompare.getDate()+1);
	week_of.id = "week_of";
	week_of.className += "form-control" 
	week_of.setAttribute("name", "week_of");
	
	if ( weekOfDiv.hasChildNodes() ) { 
		weekOfDiv.removeChild( weekOfDiv.childNodes[0] );
	}
	weekOfDiv.appendChild(week_of);

	//Create and append the options
	for (var i = 0; i < array.length; i++) {
	    var option = document.createElement("option");
	    option.value = array[i].getFullYear()+"-"+(array[i].getMonth()+1)+"-"+array[i].getDate();
	    option.text = (array[i].getMonth()+1)+"/"+array[i].getDate()+"/"+array[i].getFullYear();
	    week_of.appendChild(option);
	}
	

    $("#menuEditModal #week_of").val( weekOfCompare );
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



