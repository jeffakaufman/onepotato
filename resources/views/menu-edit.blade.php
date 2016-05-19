<!-- app/views/user-edit.blade.php -->

    {{ Form::model($menu, array('route' => 'menu.edit', $menu->id)) }}    

        <!-- name -->
        {{ Form::label('menu_title', 'Menu Title') }}
        {{ Form::text('menu_title') }}

        <!-- email -->
        {{ Form::label('menu_description', 'Menu Description') }}
        {{ Form::email('menu_description') }}      

        {{ Form::submit('Update Menu!') }}

    {{ Form::close() }}
