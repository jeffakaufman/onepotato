<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public function users()
    {
        return $this->hasMany('App\User');
    }
}
