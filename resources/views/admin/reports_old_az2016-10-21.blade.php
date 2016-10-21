@extends('spark::layouts.app-admin', ['menuitem' => 'dashboard'])

@section('page_header')
    <h1>
        What's Shipping {{ $thisTuesday }}
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
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Standard Omnivore</strong></div>
                    <div class="panel-body">
                    	<div class="row" style="font-size:small;color:white;background-color:black;padding-left: 10px">
                    	<strong>
                    	
                    	@foreach ($standardOmnivoreBoxes->names as $i => $name)
                    		{{$name}}@if ($i != 2),@endif
                    	@endforeach
                    	</strong>
                    	</div>
                    	<table id="boxes" class="table table-striped table-hover table-order-column" width="80%" cellspacing="0">
                    	@foreach ($standardOmnivoreBoxes->counts as $count)
                    		<tr>
                    			<td>{{$count->product_title}}</td>
                    			<td class="text-right">{{$count->total}}</td>
                    		</tr>
                    	@endforeach
                    	</table>
                	</div>
            	</div>
        	</div>
		</div>
        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Other Omnivore Boxes</strong></div>
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
                    			<td class="text-right">{{$count->total}}</td>
                    		</tr>
                    	@endforeach
                    	</table>
                    @endforeach
                	</div>
            	</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Standard Vegetarian</strong></div>
                    <div class="panel-body">
                    	<div class="row" style="font-size:small;color:white;background-color:black;padding-left: 10px">
                    	<strong>
                    	
                    	@foreach ($vegetarianBoxes->names as $i => $name)
                    		{{$name}}@if ($i != 2),@endif
                    	@endforeach
                    	</strong>
                    	</div>
                    	<table id="boxes" class="table table-striped table-hover table-order-column" width="100%" cellspacing="0">
                    	@foreach ($vegetarianBoxes->counts as $count)
                    		<tr>
                    			<td>{{$count->product_title}}</td>
                    			<td class="text-right">{{$count->total}}</td>
                    		</tr>
                    	@endforeach
                    	</table>
                	</div>
            	</div>
        	</div>
		</div>
	</div>    
</div>               		

</home>
@endsection



