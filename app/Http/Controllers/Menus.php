<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    protected $table = 'menus';
	public $timestamps = true;
	
	public function whatscookings()
    {
        return $this->belongsToMany('App\Http\Controllers\WhatsCookings');
    }
}
