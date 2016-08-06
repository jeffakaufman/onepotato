<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsCookings extends Model
{
    protected $table = 'whats_cookings';
	public $timestamps = true;
	protected $fillable =  ['product_type', 'week_of', 'created_at', 'updated_at'];

	public function menus()
    {
        return $this->belongsToMany('App\Menus')->withTimestamps();
    }
}
