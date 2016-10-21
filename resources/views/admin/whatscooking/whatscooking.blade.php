@extends('spark::layouts.app-admin', ['menuitem' => 'whatscooking'])

@section('page_header')

@include('admin.whatscooking.menu-edit')
    <h1>
        What's Cooking
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a>
        <li><a href="#">UI</a>
        <li class="active">Buttons
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
						        <div class="col-sm-2">
						        @if( $last)
						        	{!! Form::hidden('last_id',$last->id); !!}
						       	    {!! Form::checkbox('isOmnivore') !!} Omnivore<br />
						           	{!! Form::checkbox('isVegetarian') !!} Vegetarian
						        @else
						       	    {!! Form::checkbox('isOmnivore',1,true) !!} Omnivore<br />
						           	{!! Form::checkbox('isVegetarian') !!} Vegetarian
						        @endif
						        </div>
							    <div class="col-sm-5" style="padding-left: 5px;">
							    	{!! Form::checkbox('vegetarianBackup',1,false) !!}  Vegetarian Backup<br />
						           	{!! Form::checkbox('isNotAvailable') !!} <strong>NOT AVAILABLE ONLINE</strong>
							    </div>   
						    </div>
						    <div class="form-group">
						        {!! Form::label('Preferences', null,array('class'=>'col-sm-2 control-label')) !!}
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
						        		{!! Form::checkbox('hasNuts',1,false) !!} Nut-Free<br />
						        		</div>  
						        	</div>  
						        </div>     
						    </div>
						    <div class="form-group">
						        {!! Form::label('Ingredients', null,array('class'=>'col-sm-2 control-label')) !!}
						        <div class="col-sm-6">
						        	<div class="row">
						        		<div class="col-sm-4">
						        		{!! Form::checkbox('noDairy',1,false) !!} No Dairy<br />
						        		{!! Form::checkbox('noEgg',1,false) !!} No Egg<br />
						        		{!! Form::checkbox('noSoy',1,false) !!} No Soy<br />
						        		</div>
						        		<div class="col-sm-3">
						        			{!! Form::label('Techniques', null) !!}
						        		</div>
						        		<div class="col-sm-4">
						        		{!! Form::checkbox('oven',1,false) !!} Oven<br />
						        		{!! Form::checkbox('stovetop',1,false) !!} Stovetop<br />
						        		{!! Form::checkbox('slowcooker',1,false) !!} Slowcooker<br />
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
    	                	    @foreach ($whatscookings as $whatscooking)
		                	        @foreach ($whatscooking->menus()->orderBy('id','desc')->get() as $menu)
								    	<div class="row" style="margin-top:0px;margin-bottom:10px">

								    	<div class="col-md-7">
								    		<div class="row">
									    		<div class="col-md-2">{{ date('m/d/y',strtotime($whatscooking->week_of)) }}</div>
									    		<div class="col-md-2">
									    		@if ( $menu->isOmnivore && !$menu->isVegetarian )
									    			Omnivore
									    		@elseif ( !$menu->isOmnivore && $menu->isVegetarian )
									    			Vegetarian
									    		@else
									    			Both
									    		@endif
									    		</div>
	    	        	    	        		<div class="col-md-8">{{ $menu->menu_title }}<br/><em>{{ $menu->menu_description }}</em></div>
	        	    	        	    													</div>
											<div class="row">
												<div class="col-md-8" style="margin-top: 10px;">
		            		            			@if($menu->hasBeef)
		            		            				<img src='/img/beef.png'>
													@endif
	        	    		            			@if($menu->hasPoultry)
	            			            				<img src='/img/chicken.png'>
													@endif
	            	    		        			@if($menu->hasFish)
	            	    	    	    				<img src='/img/fish.png'>
													@endif
	            	    	        				@if($menu->hasLamb)
	            	    	        					Lamb
													@endif
		            		           		 		@if($menu->hasPork)
	    	        		          			  		<img src='/img/pork.png'>
													@endif
	            			          			  	@if($menu->hasShellfish)
	            			         			   		Shellfish
													@endif
	            	    	    	 			   	@if($menu->hasNoGluten)
	            	    	        					<img src='/img/no_wheat.png'>
													@endif
	            	    	       		 			@if($menu->hasNuts)
	            	    	        					<img src='/img/no_nuts.png'>
													@endif	 
	            	    	        				@if($menu->noDairy)
	            	    	        					<img src='/img/no_dairy.jpg');">
													@endif	 
	            	    	        				@if($menu->noEgg)
	            	    	        					<img src='/img/no_eggs.png'>
													@endif	 
	            	    	        				@if($menu->noSoy)
	            	    	        					<img src='/img/no_soy.png'>
													@endif	 	 
	            	    	        				@if($menu->oven)
	            	    	        					<img src='/img/oven.png'> 
													@endif	   
	            	    	        				@if($menu->stovetop)
	            	    	        					<img src='/img/fry_pan.png'>
													@endif	   
	            	    	        				@if($menu->slowcooker)
	            	    	        					<img src='/img/dutch_oven.png'>
													@endif	
			                	        @if ($menu->vegetarianBackup)
			                	        <div class="row" style="margin-top:5px">
											<div class="col-md-12 ">
								    			<strong>Vegetarian Replacement</strong>
								    		</div>
								    	</div>
								    	@endif 
			                	        @if ($menu->isNotAvailable)
			                	        <div class="row" style="margin-top:5px">
											<div class="col-md-12 ">
								    			<strong>NOT AVAILABLE ONLINE</strong>
								    		</div>
								    	</div>
								    	@endif  
												</div>
											</div>
										</div>	
										
								    	<div class="col-md-4">     	        
												@if($menu->image)
												<div class="col-md-6 text-center"><img height="100px" src="{{ $menu->image }}"/></div>
												@else
												<div class="col-md-6 text-center"><img height="100px" src="/img/foodpot.jpg"/></div>
												@endif
	            	        		    		<div class="col-md-4 col-md-offset-2">
	            	        		    			<div class="btn btn-primary" data-toggle="modal" data-whatscooking="{{ $whatscooking }}" data-menu="{{ $menu }}" data-target="#menuEditModal">Edit</div>
	            	        		    		</div>
	            	        		    </div>
	            	        		    </div>

								    
									
								    
									<div class="row">
										<div class="col-md-12 ">
											<hr style="border-top: 1px solid #d3e0e9;position: relative;left: -30px;margin-top:10px;margin-bottom:10px">
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
<script type="text/javascript">

	Date.prototype.addDays = function(days) {
		var dat = new Date(this.valueOf())
		dat.setDate(dat.getDate() + days);
		return dat;
	};


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
	}

$('#menuEditModal').on('show.bs.modal', function(e) {

    var menu = $(e.relatedTarget).data('menu');
    var whatscooking = $(e.relatedTarget).data('whatscooking');

	var _dp = whatscooking.week_of.match(/(\d+)/g);

	var weekOfCompare = new Date(_dp[0], _dp[1]-1, _dp[2]);

//console.log("Week Of Compare :: ");
//console.log(weekOfCompare);

    var weekOfDiv = document.getElementById("dateSelect");

	//Create array of options to be added
	var array = getAllDays();

	//Create and append select list
	var week_of = document.createElement("select");
	
//    weekOfCompare = weekOfCompare.getFullYear()+"-"+(weekOfCompare.getMonth()+1)+"-"+(weekOfCompare.getDate()+1);
    weekOfCompare = weekOfCompare.getFullYear()+"-"+(weekOfCompare.getMonth()+1)+"-"+(weekOfCompare.getDate());

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
//console.log(option.value);
	}

//console.log(weekOfCompare);
	
    $("#menuEditModal #week_of").val( weekOfCompare );
    $("#menuEditModal #whatscooking_id").val( whatscooking.id );
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
    $("#menuEditModal #noDairy").prop( "checked", menu.noDairy );
    $("#menuEditModal #noEgg").prop( "checked", menu.noEgg );
    $("#menuEditModal #noSoy").prop( "checked", menu.noSoy );
    $("#menuEditModal #noSoy").prop( "checked", menu.noSoy );
    $("#menuEditModal #oven").prop( "checked", menu.oven );
    $("#menuEditModal #stovetop").prop( "checked", menu.stovetop );
    $("#menuEditModal #slowcooker").prop( "checked", menu.slowcooker );
    $("#menuEditModal #isVegetarian").prop( "checked", menu.isVegetarian );
    $("#menuEditModal #isOmnivore").prop( "checked", menu.isOmnivore );
    $("#menuEditModal #isNotAvailable").prop( "checked", menu.isNotAvailable );
    $("#menuEditModal #vegetarianBackup").prop( "checked", menu.vegetarianBackup );
});
</script>
</whatscookings>
@endsection



