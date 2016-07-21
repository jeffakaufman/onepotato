@extends('spark::layouts.app-admin', ['menuitem' => 'subscription-products'])

@section('page_header')
    <h1>
        Subscription Products
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection

@section('content')
<home :products="products" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-7 col-md-offset-2">
                <div class="panel panel-default">

                    <div class="panel-body">
                        <div class="row">
	                       	<div class="product_title col-md-6"><strong>Name</strong></div>
	                       	<div class="product_cost col-md-2 text-right"><strong>Cost</strong></div>
	                       	<div class="product_sku col-md-3 text-center"><strong>Sku</strong></div>
	                    </div>
                        @foreach ($products as $product)
                        <div class="row">
	                        	<div class="product_title col-md-6">{{ $product->product_description }}</div>
	                        	<div class="product_cost col-md-2 text-right">${{ number_format($product->cost, 2) }}</div>
	                        	<div class="product_sku col-md-3 text-center">{{ $product->sku }}</div>
	                        </div>
						@endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection



