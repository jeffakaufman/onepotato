<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    protected $table = 'menus';
	public $timestamps = true;
	
		public function getDietaryPreferencesNumber()
    {
    	if ($this->attributes['hasBeef']) {
    		return 1;
    	}
    	if ($this->attributes['hasPoultry']) {
    		return 2;
    	}
    	if ($this->attributes['hasFish']) {
    		return 3;
    	}
    	if ($this->attributes['hasLamb']) {
    		return 4;
    	}
    	if ($this->attributes['hasPork']) {
    		return 5;
    	}
    	if ($this->attributes['hasShellfish']) {
    		return 6;
    	}
	}

	
    public function menus_users()
    {
        return $this->hasMany('App\MenusUsers');
    }
	
	
	public function whatscookings()
    {
        return $this->belongsToMany('App\WhatsCookings');
    }
    
    public function users()
    {
        return $this->belongsToMany('App\User','menus_users','menus_id','users_id');
    }
    
}
