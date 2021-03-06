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
            					<a role="button" data-toggle="collapse" href="#activeThisWeek" aria-expanded="false" aria-controls="collapseThisWeek">
            					<div class="col-sm-10">
            							Active
                					</div>
               						<div class="col-sm-2 text-right">
            							{{  count($activeThisWeek)  }}
                					</div>
                				</a>
            				</div>
            				<div class="collapse" id="activeThisWeek">
            					    @foreach ($activeThisWeek as $i => $subscriber)
            					    	<div class="row" style="background-color: {{ $i % 2 == 0 ? 'lightblue': '#ffffff' }};">
            					    		<div class="col-sm-10 col-sm-offset-1">
	            					    		<a style="color: {{ $i % 2 == 0 ? '#dd4b39': '#000000' }};" href="/admin/user_details/{{ $subscriber->id }} ">{{ $subscriber->name }}</a>
    	        					    	</div>
            					    	</div>
            					    @endforeach
            				</div>
	    					<div class="row" style="background-color:lightblue">
            					<a role="button" data-toggle="collapse" href="#skipsThisWeek" aria-expanded="false" aria-controls="collapseSkipsThisWeek">
            					<div class="col-sm-10">
            					Skips
                				</div>
               					<div class="col-sm-2 text-right">
            					{{  count($skipsThisWeek) }}
                				</div>
                				</a>
            				</div>
            				<div class="collapse" id="skipsThisWeek">
            					    @foreach ($skipsThisWeek as $i => $skipper)
            					    	<div class="row" style="background-color: {{ $i % 2 != 0 ? 'lightblue': '#ffffff' }};">
            					    		<div class="col-sm-10 col-sm-offset-1">
	            					    		<a style="color: {{ $i % 2 == 0 ? '#dd4b39': '#000000' }};" href="/admin/user_details/{{ $skipper->id }} ">{{ $skipper->name }}</a>
    	        					    	</div>
            					    	</div>
            					    @endforeach
            				</div>
            			</div>	
      				</div>  
      			</div> 
      			<div class="row">
        			<div class="panel panel-default">
            			<div class="panel-heading"><strong>{{ date('F d',strtotime($thisTuesday . '+7 days')) }} Subscribers</strong></div>
                		<div class="panel-body">
	    					<div class="row">
            					<a role="button" data-toggle="collapse" href="#activeNextWeek" aria-expanded="false" aria-controls="collapseNextWeek">
            					<div class="col-sm-10">
            					Active
                				</div>
               					<div class="col-sm-2 text-right">
            					{{  count($activeNextWeek)  }}
                				</div>
                				</a>
            				</div>
            				<div class="collapse" id="activeNextWeek">
            					    @foreach ($activeNextWeek as $i => $subscriber)
            					    	<div class="row" style="background-color: {{ $i % 2 == 0 ? 'lightblue': '#ffffff' }};">
            					    		<div class="col-sm-10 col-sm-offset-1">
	            					    		<a style="color: {{ $i % 2 == 0 ? '#dd4b39': '#000000' }};" href="/admin/user_details/{{ $subscriber->id }} ">{{ $subscriber->name }}</a>
    	        					    	</div>
            					    	</div>
            					    @endforeach
            				</div>
	    					<div class="row" style="background-color:lightblue">
            					<a role="button" data-toggle="collapse" href="#skipsNextWeek" aria-expanded="false" aria-controls="collapseNextWeek">
            					<div class="col-sm-10">
            					Skips
                				</div>
               					<div class="col-sm-2 text-right">
            					{{  count($skipsNextWeek) }}
                				</div>
                				</a>
            				</div>
            				<div class="collapse" id="skipsNextWeek">
            					    @foreach ($skipsNextWeek as $i => $skipper)
            					    	<div class="row" style="background-color: {{ $i % 2 != 0 ? 'lightblue': '#ffffff' }};">
            					    		<div class="col-sm-10 col-sm-offset-1">
	            					    		<a style="color: {{ $i % 2 == 0 ? '#dd4b39': '#000000' }};" href="/admin/user_details/{{ $skipper->id }} ">{{ $skipper->name }}</a>
    	        					    	</div>
            					    	</div>
            					    @endforeach
            				</div>
            			</div>	
      				</div>  
      			</div> 
      			<div class="row">
        			<div class="panel panel-default">
            			<div class="panel-heading"><strong>{{ date('F d',strtotime($thisTuesday . '-7 days')) }} Subscribers</strong></div>
                		<div class="panel-body">
	    					<div class="row">
            					<a role="button" data-toggle="collapse" href="#shippedLastWeek" aria-expanded="false" aria-controls="collapseLastWeek">
            					<div class="col-sm-10">
            					Shipped
                				</div>
               					<div class="col-sm-2 text-right">
            					{{  count($shippedLastWeek)  }}
                				</div>
                				</a>
            				</div>
            				<div class="collapse" id="shippedLastWeek">
            					    @foreach ($shippedLastWeek as $i => $subscriber)
            					    	<div class="row" style="background-color: {{ $i % 2 == 0 ? 'lightblue': '#ffffff' }};">
            					    		<div class="col-sm-10 col-sm-offset-1">
	            					    		<a style="color: {{ $i % 2 == 0 ? '#dd4b39': '#000000' }};" href="/admin/user_details/{{ $subscriber->id }} ">{{ $subscriber->name }}</a>
    	        					    	</div>
            					    	</div>
            					    @endforeach
            				</div>
	    					<div class="row" style="background-color:lightblue">
            					<a role="button" data-toggle="collapse" href="#skipslastWeek" aria-expanded="false" aria-controls="collapseLastWeek">
            					<div class="col-sm-10">
            					Skips
                				</div>
               					<div class="col-sm-2 text-right">
            					{{  count($skipsLastWeek) }}
                				</div>
                				</a>
            				</div>
            				<div class="collapse" id="skipslastWeek">
            					    @foreach ($skipsNextWeek as $i => $skipper)
            					    	<div class="row" style="background-color: {{ $i % 2 != 0 ? 'lightblue': '#ffffff' }};">
            					    		<div class="col-sm-10 col-sm-offset-1">
	            					    		<a style="color: {{ $i % 2 == 0 ? '#dd4b39': '#000000' }};" href="/admin/user_details/{{ $skipper->id }} ">{{ $skipper->name }}</a>
    	        					    	</div>
            					    	</div>
            					    @endforeach
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

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Menus for {{ $thisTuesday }}</strong></div>
                    <div class="panel-body">
                    @foreach ($otherBoxes as $otherBox)
                    	<div class="row" style="font-size:small;color:white;background-color:black;padding-left: 10px">
                    	<strong>
                    	@foreach ($otherBox->names as $i => $name)
                    		{{$name}}@if ($i != 2),@endif
                    	@endforeach
                    	</strong>
                    	</div>
                    	<table id="boxes" class="table table-striped table-hover table-order-column" width="100%" cellspacing="0">
                    	@foreach ($otherBox->counts as $count)
                    		<tr>
                    			<td>{{$count->product_title}}</td>
                    			<td>{{$count->total}}</td>
                    		</tr>
                    	@endforeach
                    	</table>
                    @endforeach
                </div>
            </div>
        </div>



		</div>
	</div>    
</div>               		

</home>
@endsection



