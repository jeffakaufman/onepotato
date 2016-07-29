<div class="modal fade" id="menuEditModal" 
     tabindex="-1" role="dialog" 
     aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" 
          data-dismiss="modal" 
          aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" 
        id="favoritesModalLabel">Edit Menu</h4>
      </div>
      <div class="modal-body">
     <!-- Edit Menu Form -->
     {!! Form::open(
			        array(
			            'url' => 'whatscooking', 
			            'class' => 'form-horizontal',
			            'method' => 'put',
			            'files' => true)) !!}    
    <form method="POST" action="/whatscooking" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
    <input type="hidden" name="_method" value="PUT">
        	    <input name="whatscooking_id" id="whatscooking_id" type="hidden">
        	    <input name="menu_id" id="menu_id" type="hidden">      
		<div class="form-group">
	    	<label for="Type" class="col-sm-2 control-label">Type</label>
	    	<div class="col-sm-3">
	   	    	<input name="product_type" id="omnivore_type" type="radio" value="Omnivore"> Omnivore<br />
	       		<input name="product_type" id="vegetarian_type" type="radio" value="Vegetarian"> Vegetarian
	    	</div>    
	    	<div class="col-sm-4 text-right" style="padding-right: 0;">
	   	    	Vegetarian Replacement
	    	</div>  
	    	<div class="col-sm-1" style="padding-left: 5px;">
	   	    	<input name="vegetarianBackup" id="vegetarianBackup" type="checkbox" value="1">
	    	</div>     
		</div>
		<div class="form-group">
	    	<label for="Preferences" class="col-sm-2 control-label">Preferences</label>
	    	<div class="col-sm-7">
	    		<div class="row">
 		   			<div class="col-sm-4">
	    				<input name="hasBeef" id="hasBeef" type="checkbox" value="1"> Beef<br />
   		 				<input name="hasPoultry" id="hasPoultry" type="checkbox" value="1"> Poultry<br />
    					<input name="hasLamb" id="hasLamb" type="checkbox" value="1"> Lamb<br />
    				</div>
    				<div class="col-sm-3">
    					<input name="hasFish" id="hasFish" type="checkbox" value="1"> Fish<br />
    					<input name="hasPork" id="hasPork" type="checkbox" value="1"> Pork<br />
    					<input name="hasNuts" id="hasNuts" type="checkbox" value="1"> Nuts<br />
    				</div>
    				<div class="col-sm-5">
    					<input name="hasShellfish" id="hasShellfish" type="checkbox" value="1"> Shellfish<br />
    					<input name="hasNoGluten" id="hasNoGluten" type="checkbox" value="1"> Gluten-Free<br />
    				</div>  
    			</div>  
    		</div>     
		</div>
		<div class="form-group">
   		 	<label for="Ingredients" class="col-sm-2 control-label">Ingredients</label>
			<div class="col-sm-8">
				<div class="row">
					<div class="col-sm-3">
			    		<input name="noDairy" id="noDairy" type="checkbox" value="1"> No Dairy<br />
			    		<input name="noEgg" id="noEgg" type="checkbox" value="1"> No Egg<br />
			    		<input name="noSoy" id="noSoy" type="checkbox" value="1"> No Soy<br />
					</div>
					<div class="col-sm-3">
						<label for="Ingredients">Techniques</label>
					</div>
					<div class="col-sm-4">
			    		<input name="technique" id="oven" type="radio" value="oven"> Oven<br />
			    		<input name="technique" id="stovetop" type="radio" value="stovetop"> Stovetop<br />
			    		<input name="technique" id="slowcooker" type="radio" value="slowcooker"> Slowcooker<br />
					</div>
				</div>  
			</div>     
		</div>
		<div class="form-group">
    		<label for="Week Of" class="col-sm-2 control-label">Week Of</label>
    		<div class="col-sm-6">
    			<div id="dateSelect"></div>
   			</div>
		</div>
        <div class="form-group">
            <label for="Title" class="col-sm-2 control-label">Title</label>
            <div class="col-sm-6">
        	    <input placeholder="Menu Title" class="form-control" name="menu_title" id="menu_title" type="text">
        	</div>
        </div>

        <div class="form-group">
            <label for="Description" class="col-sm-2 control-label">Description</label>
            <div class="col-sm-6">
        	    <input placeholder="Menu Description" class="form-control" name="menu_description" id="menu_description" type="text">
        	</div>
        </div>

        <div class="form-group">
            <label for="Image" class="col-sm-2 control-label">Image</label>
            <div class="col-sm-6">
            	<img height="100px" src="/img/foodpot.jpg" id="image" style="margin-bottom:15px" />
        	    <input name="image" type="file">
        	</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="margin-right:10px;">Close</button>
        <span class="pull-right">
          <button type="submit" class="btn btn-primary">
            Submit
          </button>
        </span>
        
    </form>
      </div>
    </div>
  </div>
</div>

