<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;

class WhatsCookings extends Model
{
    protected $table = 'whats_cookings';
	public $timestamps = true;
	
	public function menus()
    {
        return $this->belongsToMany('App\Menus')->withTimestamps();
    }
}
