
<home :menus="menus" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">Menus for the Week of {{ date('F d, Y',strtotime($whatscooking->week_of)) }}</div>

                    <div class="panel-body">
						    <div class="row">
	                        	<div class="col-md-3"><h5>Menu Title</h5></div>
								<div class="col-md-6"><h5>Menu Description</h5></div>
								<div class="col-md-3"><h5>Recipe Card</h5></div>
							</div>
                        @foreach ($menus as $menu)
						    <div class="row">
	                        	<div class="col-md-3"><a href="/menu/{{ $menu->id }}">{{ $menu->menu_title }}</a></div>
								<div class="col-md-6">{{ $menu->menu_description}}</div>
								<div class="col-md-3"><img height="100px" src="{{ $menu->image }}"/></div>
							</div>
						@endforeach
                    </div>
                </div>
            </div>
        </div>
		
		<div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Menu for {{ date('F d, Y',strtotime($whatscooking->week_of)) }}</div>
                    <div class="panel-body">
								 
							        <!-- New Menu Form -->
							        {!! Form::open(
							            array(
							                'url' => 'menufile', 
							                'class' => 'form-horizontal', 
							                'files' => true)) !!}
							        {!! Form::hidden('whatscooking_id', $whatscooking->id) !!}
							        <div class="form-group">
							            {!! Form::label('Menu Title', null,array('class'=>'col-sm-3 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::text('menu_title', null, array('placeholder'=>'Menu Title','class'=>'form-control')) !!}
							        	</div>
							        </div>

							        <div class="form-group">
							            {!! Form::label('Menu Description', null,array('class'=>'col-sm-3 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::text('menu_description', null, array('placeholder'=>'Menu Description','class'=>'form-control')) !!}
							        	</div>
							        </div>

							        <div class="form-group">
							            {!! Form::label('Product Image', null,array('class'=>'col-sm-3 control-label')) !!}
							            <div class="col-sm-6">
							        	    {!! Form::file('image', null, array('class'=>'form-control')) !!}
							        	</div>
							        </div>

							        <div class="form-group">
							        	<div class="col-sm-offset-3 col-sm-6"><button type="submit" class="btn btn-default">
							                        <i class="fa fa-plus"></i> Add Menu
							        							                    </button>
							        </div>
							                   
							        {!! Form::close() !!}
							       
						</div>
                    </div>
                </div>
            </div>
        </div>
		

    </div>
</home>


