<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenusUsers extends Model
{
    public function users()
    {
        return $this->hasMany('App\User','id','users_id');;
    }
    public function menu()
    {
        return $this->belongsTo('App\Menus', 'menus_id');
    }
}
