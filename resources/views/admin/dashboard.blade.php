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
      		<div class="col-sm-5"> 
      			<div class="row">
        			<div class="panel panel-default">
            			<div class="panel-heading"><strong>{{ $thisTuesday }} Subscribers</strong></div>
                		<div class="panel-body">
	    					<div class="row">
            					<div class="col-sm-10">
            					Active
                				</div>
               					<div class="col-sm-2 text-right">
            					{{  $activeThisWeek  }}
                				</div>
            				</div>
	    					<div class="row">
            					<div class="col-sm-10">
            					Skips
                				</div>
               					<div class="col-sm-2 text-right">
            					{{  $skipsThisWeek }}
                				</div>
            				</div>
            			</div>	
      				</div>  
      			</div> 
      			<div class="row">
        			<div class="panel panel-default">
            			<div class="panel-heading"><strong>{{ date('F d',strtotime($thisTuesday . '+7 days')) }} Subscribers</strong></div>
                		<div class="panel-body">
	    					<div class="row">
            					<div class="col-sm-10">
            					Active
                				</div>
               					<div class="col-sm-2 text-right">
            					{{  $activeNextWeek  }}
                				</div>
            				</div>
	    					<div class="row">
            					<div class="col-sm-10">
            					Skips
                				</div>
               					<div class="col-sm-2 text-right">
            					{{  $skipsNextWeek }}
                				</div>
            				</div>
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
               					<div class="col-sm-2 text-right">
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

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Menus for {{ $thisTuesday }}</strong></div>
                    <div class="panel-body">
                    @foreach ($menus as $menu)
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



		</div>
	</div>    
</div>               		

</home>
@endsection



