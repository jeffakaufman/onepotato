
<home :menus="menus" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
                        @foreach ($menus as $menu)
						    <div class="row">
						    	<div class="col-md-3">Menus for the Week of {{ date('F d, Y',strtotime($whatscooking->week_of)) }}</div>
	                        	<div class="col-md-3"><a href="/menu/{{ $menu->id }}">{{ $menu->menu_title }}</a></div>
								<div class="col-md-3">{{ $menu->menu_description}}</div>
								<div class="col-md-3"><img height="100px" src="{{ $menu->image }}"/></div>
							</div>
						@endforeach
        </div>
    </div>
</home>


@include('menus', ['whatscooking'=>$whatscooking,'menus'=>$whatscooking->menus()->get()])
