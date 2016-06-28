@extends('spark::layouts.app')

@section('content')
<home :menu="menu" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Menu</div>

                    <div class="panel-body">
						<span class="menu_id">{{ $menu->id }}</span>
                        <span class="menu_title">{{ $menu->menu_title }}</span>
						<span style="padding-left:10px;">{{ $menu->menu_description}}</span>
						<span style="padding-left:10px;">{{ $menu->menu_delivery_date}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
