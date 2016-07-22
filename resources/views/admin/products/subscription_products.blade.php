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
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
	                       	<div class="product_title col-md-5"><strong>Name</strong></div>
	                       	<div class="product_cost col-md-2 text-center"><strong>Cost</strong></div>
	                       	<div class="product_sku col-md-3 text-center"><strong>Sku</strong></div>
	                    </div>
                        @foreach ($products as $product)
                        <div class="row">
{!! Form::open(
							        array(
							            'url' => 'whatscooking', 
							            'class' => 'form-horizontal', 
							            'files' => true)) !!}   
							<div class="col-md-5">
	                        	 {!! Form::text('product_title', $product->product_description , array('placeholder'=>'Product Description','class'=>'form-control product_title')) !!}
							</div>
							<div class="col-md-2">
	                        	 {!! Form::number('product_cost', number_format($product->cost, 2) , array('placeholder'=>'Product Description','class'=>'form-control product_cost text-right')) !!}
							</div>

	                        	<div class="product_sku col-md-3 text-center">{{ $product->sku }}</div>
{!! Form::close() !!}
	                        </div>
                        <br>
						@endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection



