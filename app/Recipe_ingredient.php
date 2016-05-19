<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe_ingredient extends Model {

	protected $table = 'recipe_ingredients';
	public $timestamps = true;

	public function ingredient()
	{
		return $this->hasMany('Ingredient', 'id');
	}

	public function recipe()
	{
		return $this->hasMany('Recipe', 'id');
	}

}