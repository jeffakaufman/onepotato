@extends('spark::layouts.app-admin', ['menuitem' => 'coupons'])

@section('page_header')
    <h1>
        Coupons
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
                    <div class="panel-heading">Coupons List</div>

                    <div class="panel-body">
                       @foreach ($coupons as $coupon)
						    <div><span class="couponCode">{{ $coupon->couponCode }}</span>
	                        <span class="percentDiscount">{{ $coupon->percentDiscount }}%</span>
						@endforeach
                    </div>
					


                </div>
            </div>
        </div>
<div class="row">
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Coupon</div>
                    	<div class="panel-body">
							<!-- New Menu Form -->
							    {!! Form::open(
							        array(
							            'url' => 'coupon', 
							            'class' => 'form-horizontal')) !!}          
							        <div class="form-group">
							            {!! Form::label('couponCode', null,array('class'=>'col-sm-2 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::text('couponCode', null, array('placeholder'=>'Coupon Code','class'=>'form-control')) !!}
							        	</div>
							        </div>

							        <div class="form-group">
							            {!! Form::label('Description', null,array('class'=>'col-sm-2 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::text('percentDiscount', null, array('class'=>'form-control')) !!}
							        	</div>
							        </div>

					        <div class="form-group">
					        	<div class="col-sm-offset-3 col-sm-6"><button type="submit" class="btn btn-default">
			                        <i class="fa fa-plus"></i> Coupon</button>
					        	</div>
					        </div>
						        {!! Form::close() !!}
						</div>
                </div>
            </div>
        </div>



    </div>
</home>
@endsection



