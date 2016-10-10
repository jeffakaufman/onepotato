<?php
?>

<form method="POST" action="/admin/user_details/{{ $user->id }}/edit_menus/{{$deliveryDate}}" accept-charset="UTF-8" class="meals">
    {{ csrf_field() }}
    <input type="hidden" name="user_id" value="{{$user->id}}" />

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Update user menus for {{$deliveryDate}}</h4>
    </div>
    <div class="modal-body">
@foreach($menus as $m)
    <div class="menu-item" style="border-bottom: solid 1px blue;padding:5px;">
        <div class="row">
            <div class="col-sm-1" style="vertical-align: middle;">
                <input type="checkbox" name="menu_id[]" value="{{$m->id}}" @if($m->mu_id)checked="checked"@endif />
            </div>
            <div class="col-sm-4">
                <div class="image-container">
                    <img src="{{$m->image}}" style="width:200px;"/>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="menu-name" style="padding-left:30px;">
                    <div>{{$m->menu_title}}</div>
                    <div><i>{{$m->menu_description}}</i></div>
                </div>
            </div>
        </div>

    </div>
@endforeach
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="color: #666666;">Cancel</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>

