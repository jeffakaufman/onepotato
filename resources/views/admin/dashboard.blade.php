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
                    	<div class="row" style="background-color:black; color:white;border-bottom: black;border-width: 2px;border-style: solid;border-right: white;border-left: white;border-top: white;">
                    		<div class="col-sm-5"> 
                    			<strong>{{ $menu->delivery_date }}</strong>
                    		</div>
                    	</div>
                    	@endif
                    	@if ( ($oldMenu !=  $menu->menu_title))
                    	<?php $i = 0; ?>
                    	<div class="row" style="border-bottom: black;border-width: 1px;border-style: solid;border-right: white;border-left: white;">
                    		<div class="col-sm-10">
                    			<strong>{{ $menu->menu_title }}                 			
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
							</strong>  
                    </div>
                    		</div>
                    	@endif
                    	<div class="row" @if  ($i%2 == 0) style="background-color:lightblue" @endif   >
                    		<div class="col-sm-10"> 
                    		{{ $menu->product_title }} 
                    		</div> 
                    		<div class="col-sm-2 text-right">
                    			{{ $menu->total }}
                    		</div>
                    	</div>                 	
                         			              	
                    	<?php 
                    		$i++;
                    		$oldDate = $menu->delivery_date;
                    		$oldMenu = $menu->menu_title;
                    	?>
                    @endforeach
                </div>
            </div>
            </div>


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
      		<div class="col-sm-5"> 
      			<div class="row">
        			<div class="panel panel-default">
            			<div class="panel-heading"><strong>Current Users</strong></div>
                		<div class="panel-body">
                		<?php 
                	    		$i = 0;
                	    ?>
        				@foreach ($totalSubs as $totalSub)
	    					<div class="row" @if  ($i%2 == 0) style="background-color:lightblue" @endif   >
            					<div class="col-sm-10">
                					{{ $totalSub->product_description }}
                				</div>
               					<div class="col-sm-1">
                					{{$totalSub->total}}
                				</div>
            				</div>	
            				<?php
            					$i++; 
                    		?>
            				@endforeach
						</div>
      				</div>  
      			</div>
      			<div class="row">
        			<div class="panel panel-default">
            			<div class="panel-heading"><strong>New Users</strong></div>
                		<div class="panel-body">
                		<?php 
                	    		$oldDate = "";
                	    ?>
        				@foreach ($newSubs as $newSub)
        					@if ( ($oldDate !=  $newSub->start_date))
        					<?php $i = 0; ?>
                	    	<div class="row" style="background-color:black; color:white;border-bottom: black;border-width: 2px;border-style: solid;border-right: white;border-left: white;border-top: white;">
                	    		<div class="col-sm-5"> 
                	    			<strong>{{ date('F dS', strtotime($newSub->start_date)) }}</strong>
                	    		</div>
                	    	</div>
                	    	@endif
	    					<div class="row" @if  ($i%2 != 0) style="background-color:lightblue" @endif   >
            					<div class="col-sm-10">
                					{{ $newSub->product_description }}
                				</div>
               					<div class="col-sm-1">
                					{{$newSub->total}}
                				</div>
            				</div>	
            				<?php
            					$i++; 
                    			$oldDate =$newSub->start_date;
                    		?>
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



