@extends('spark::layouts.app', ['menuitem' => 'recipes'])

@section('content')
<home :recipes="recipes" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Recipes</div>

                    <div class="panel-body">
                        @foreach ($recipes as $recipe)
						    <div><span class="recipe_id">{{ $recipe->id }}</span>
	                        <span class="recipe_title"><a href="/recipe/{{ $recipe->id }}">{{ $recipe->recipe_title }}</a></span>
						@endforeach
                    </div>
					


                </div>
            </div>
        </div>



		
		<div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">recipes</div>

                    <div class="panel-body">
                        

							<div class="panel-body">
							        
							        <!-- Display Validation Errors -->
								        @include('errors.errors')
								 

							        <!-- New Task Form -->
							        <form action="{{ url('recipes') }}" method="POST" class="form-horizontal">
							            {{ csrf_field() }}

							            <!-- Task Name -->
							            <div class="form-group">
							                <label for="recipe_title" class="col-sm-3 control-label">Recipe Title</label>

							                <div class="col-sm-6">
							                    <input type="text" name="recipe_title" id="recipe_title" class="form-control">
							                </div>
										</div>
										<div class="form-group">
											<label for="recipe_type" class="col-sm-3 control-label">Recipe Type</label>

							                <div class="col-sm-6">
							                    <input type="text" name="recipe_type" id="recipe_description" class="form-control">
							                </div>
							            </div>
										<div class="form-group">
											<label for="photo_url" class="col-sm-3 control-label">Photo</label>

							                <div class="col-sm-6">
							                    <input type="text" name="photo_url" id="photo_url" class="form-control">
							                </div>
							            </div>
										<div class="form-group">
											<label for="'pdf_url', 1000" class="col-sm-3 control-label">PDF</label>

							                <div class="col-sm-6">
							                    <input type="text" name="pdf_url" id="pdf_url" class="form-control">
							                </div>
							            </div>

										<div class="form-group">
											<label for="video_url" class="col-sm-3 control-label">Video URL</label>

							                <div class="col-sm-6">
							                    <input type="text" name="video_url" id="instructions" class="form-control">
							                </div>
							            </div>

							            <!-- Add Task Button -->
							            <div class="form-group">
							                <div class="col-sm-offset-3 col-sm-6">
							                    <button type="submit" class="btn btn-default">
							                        <i class="fa fa-plus"></i> Add Recipe
							                    </button>
							                </div>
							            </div>
							        </form>
							    </div>


                    </div>
					


                </div>
            </div>
        </div>
		

    </div>
</home>
@endsection



