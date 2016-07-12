@extends('spark::layouts.app')

@section('content')
<home :recipes="recipes" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

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



