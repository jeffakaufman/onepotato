<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_menu extends Model {

	protected $table = 'product_menus';
	public $timestamps = true;

	public function product()
	{
		return $this->hasMany('Product', 'id');
	}

	public function menu()
	{
		return $this->hasMany('Menu', 'id');
	}

}