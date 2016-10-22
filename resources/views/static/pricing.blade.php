@extends('spark::layouts.app')

@section('content')
<div id="static" class="container">
	<div class="row">
		<div class="col-xs-12">
			<h1>Pricing</h1>
		</div>
	</div>
  <div class="row">
    <div class="col-sm-4 col-sm-offset-1">
      <p>
      <h2>Meals per week: <span style="color:black">3</h2>
      <h2>Price per Adult Meal: <span style="color:black"> $11.99*</h2>
      <h2>Price per Child Meal: <span style="color:black"> $4.50</span></h2>
      </p>
      *Gluten Free pricing of $1.50 per meal per adult
      <h2 style="text-align: center;"></h2>
      <div style="text-align: center; height: 65px;"><a class="btn btn-primary" style="font-size:x-large" href="/register">Get Started</a></div>
      	<div style="text-align: center;">
 	   		<div style="text-align: center;"><em>Customize to your family. Shipping is always free.</em></div>
 	   		<div style="text-align: center;"><em>Skip any week. Cancel any time.</em></div>
    	</div>
    </div>
	<div class="col-sm-5 col-sm-offset-1">
    	<img src="/img/pricing1.jpg" alt="" />
    </div>
  </div>
</div>
@endsection