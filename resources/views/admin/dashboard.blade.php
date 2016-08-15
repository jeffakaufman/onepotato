@extends('spark::layouts.app-admin', ['menuitem' => 'dashboard'])

@section('page_header')
    <h1>
        Dashboard
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection

@section('content')
<home :recipes="recipes" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>
                    <div class="panel-body">
                    
                    @foreach ($menus as $menu)
                    	@if ( ($oldDate !=  $menu->delivery_date))
                    	<div class="row">
                    		<div class="col-sm-5"> 
                    		<strong>{{ $menu->delivery_date }}</strong>
                    		</div>
                    	</div>
                    	@endif
                    	<div class="row">
                    		<div class="col-sm-5">
                    			{{ $menu->menu_title }}
		            		@if($menu->hasBeef)
		            			(Beef)
							@endif
	        	    		@if($menu->hasPoultry)
	            			    (Poultry)
							@endif
	            	    	@if($menu->hasFish)
	            	    	    (Fish)
							@endif
	            	    	@if($menu->hasLamb)
	            	    	    (Lamb)
							@endif
		            		@if($menu->hasPork)
	    	        			(Pork)
							@endif
	            			@if($menu->hasShellfish)
	            				(Shellfish)
							@endif
                    		</div>
                    		<div class="col-sm-2">
                    			{{ $menu->total }}
                    		</div>
                    	</div>                  	
                    	
                    	<?php 
                    		$oldDate = $menu->delivery_date;
                    	?>
                    @endforeach
                    	<div class="row">
                    		<div class="col-sm-5"> 
                    			<strong>Omnivore Meals</strong>
                    		</div>
                    	</div>
                    	<div class="row">
                    		<div class="col-sm-5"> 
                    			Beef: {{$meat->beef}}<br />
                    			Poultry: {{$meat->poultry}}<br />
                    			Fish: {{$meat->fish}}<br />
                    			Lamb: {{$meat->lamb}}<br />
                    			Pork: {{$meat->pork}}<br />
                    			Shellfish: {{$meat->shellfish}}<br />
                    		</div>
                    	</div>
                    
                    	<div class="row">
                    		<div class="col-sm-5"> 
                    			<strong>Upcoming New User Start Dates</strong>
                    		</div>	     
                    	</div>	        
                    @foreach ($newSubs as $newSub)
	                   	<div class="row">
                    		<div class="col-sm-2">
                    		{{ date('F dS', strtotime($newSub->start_date)) }}
                    	    </div>
                    		<div class="col-sm-1">
                    		{{$newSub->total}}
                    	    </div>
                    	</div>	
                    @endforeach

                    </div>
					


                </div>
            </div>
        </div>




    </div>
</home>
@endsection



