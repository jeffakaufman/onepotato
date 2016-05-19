<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu_recipe extends Model {

	protected $table = 'menu_recipes';
	public $timestamps = true;

	public function recipes()
	{
		return $this->hasMany('Recipe', 'id');
	}

	public function menu()
	{
		return $this->hasMany('Menu', 'id');
	}

}