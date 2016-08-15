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
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Upcoming Meals</strong></div>
                    <div class="panel-body">
                    @foreach ($menus as $menu)
                    	@if ( ($oldDate !=  $menu->delivery_date))
                    	@if ($menu != reset($menus)) <br /> @endif
                    	<div class="row">
                    		<div class="col-sm-5"> 
                    			<strong>{{ $menu->delivery_date }}</strong>
                    		</div>
                    	</div>
                    	@endif
                    	<div class="row">
                    		<div class="col-sm-10">
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
                    		<div class="col-sm-2 text-right">
                    			{{ $menu->total }}
                    		</div>
                    	</div>                  	
                    	
                    	<?php 
                    		$oldDate = $menu->delivery_date;
                    	?>
                    @endforeach
                    </div>
                </div>
            </div>
               <div class="row">  
            <div class="col-sm-2"> 
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Omnivore Meals</strong></div>
                    <div class="panel-body">
                    	<div class="row">
                    		<div class="col-sm-5"> 
                    			Beef
                    		</div>	
                    		<div class="col-sm-5 text-right"> 
                    			{{$meat->beef}}
                    		</div>	
                    	</div>
                    	<div class="row">	
                    		<div class="col-sm-5">
                    			Poultry
                    		</div>
                    		<div class="col-sm-5 text-right"> 
                    			{{$meat->poultry}}
                    		</div>	
                    	</div>
                    	<div class="row">
                    		<div class="col-sm-5">
                    			Fish
                    		</div>
                    		<div class="col-sm-5 text-right"> 
                    			{{$meat->fish}}
                    		</div>	
                    	</div>
                    	<div class="row">
                    		<div class="col-sm-5">
                    			Lamb
                    		</div>
                    		<div class="col-sm-5 text-right"> 
                    			{{$meat->lamb}}
                    		</div>
                    	</div>
                    	<div class="row">	
                    		<div class="col-sm-5">
                    			Pork
                    		</div>
                    		<div class="col-sm-5 text-right"> 
                    			{{$meat->pork}}
                    		</div>	
                    	</div>
                    	<div class="row">
                    		<div class="col-sm-5">
                    			Shellfish
                    		</div>
                    		<div class="col-sm-5 text-right"> 
                    			{{$meat->shellfish}}
                    		</div>
                    	</div>
                   	</div>
           		</div>
           	</div>
           	</div>
           	
           	
           	<div class="row">
      		<div class="col-sm-2"> 
        		<div class="panel panel-default">
            		<div class="panel-heading"><strong>New Users</strong></div>
                	<div class="panel-body">
        			@foreach ($newSubs as $newSub)
	    				<div class="row">
            				<div class="col-sm-9">
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
	</div>               		

</home>
@endsection



