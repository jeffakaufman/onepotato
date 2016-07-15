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
{{--                        @foreach ($recipes as $recipe)--}}
						    {{--<div><span class="recipe_id">{{ $recipe->id }}</span>--}}
{{--	                        <span class="recipe_title"><a href="/recipe/{{ $recipe->id }}">{{ $recipe->recipe_title }}</a></span>--}}
						{{--@endforeach--}}
                    </div>
					


                </div>
            </div>
        </div>




    </div>
</home>
@endsection



