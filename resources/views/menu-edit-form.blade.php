<!-- New Menu Form -->
    {!! Form::open(
        array(
            'url' => 'whatscooking', 
            'class' => 'form-horizontal', 
            'files' => true)) !!}          
<div class="form-group">
    {!! Form::label('Type', null,array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-6">
   	    {!! Form::radio('product_type', 'Omnivore') !!} Omnivore<br />
       	{!! Form::radio('product_type', 'Vegetarian') !!} Vegetarian
    </div>     
</div>
<div class="form-group">
    {!! Form::label('Ingredients', null,array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-6">
    	<div class="row">
    		<div class="col-sm-4">
    		{!! Form::checkbox('hasBeef',1,false) !!} Beef<br />
    		{!! Form::checkbox('hasPoultry',1,false) !!} Poultry<br />
    		{!! Form::checkbox('hasFish',1,false) !!} Fish<br />
    		</div>
    		<div class="col-sm-4">
    		{!! Form::checkbox('hasLamb',1,false) !!} Lamb<br />
    		{!! Form::checkbox('hasPork',1,false) !!} Pork<br />
    		{!! Form::checkbox('hasShellfish',1,false) !!} Shellfish<br />
    		</div>
    		<div class="col-sm-4">
    		{!! Form::checkbox('hasNoGluten',1,false) !!} Gluten-Free<br />
    		{!! Form::checkbox('hasNuts',1,false) !!} Nuts<br />
    		</div>  
    	</div>  
    </div>     
</div>
<div class="form-group">
    {!! Form::label('Week Of', null,array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-6">
  	    {!! Form::date('week_of', $last->week_of); !!}
   	</div>
</div>
        <div class="form-group">
            {!! Form::label('Title', null,array('class'=>'col-sm-2 control-label')) !!}
            <div class="col-sm-6">
        	    {!! Form::text('menu_title', null, array('placeholder'=>'Menu Title','class'=>'form-control')) !!}
        	</div>
        </div>

        <div class="form-group">
            {!! Form::label('Description', null,array('class'=>'col-sm-2 control-label')) !!}
            <div class="col-sm-6">
        	    {!! Form::text('menu_description', null, array('placeholder'=>'Menu Description','class'=>'form-control')) !!}
        	</div>
        </div>

        <div class="form-group">
            {!! Form::label('Image', null,array('class'=>'col-sm-2 control-label')) !!}
            <div class="col-sm-6">
        	    {!! Form::file('image', null, array('class'=>'form-control')) !!}
        	</div>
        </div>
					        <div class="form-group">
					        	<div class="col-sm-offset-3 col-sm-6"><button type="submit" class="btn btn-default">
			                        <i class="fa fa-plus"></i> Add Menu</button>
					        	</div>
					        </div>
    {!! Form::close() !!}