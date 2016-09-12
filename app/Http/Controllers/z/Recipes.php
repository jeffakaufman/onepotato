<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;

class Recipes extends Model
{
    protected $table = 'recipes';
	public $timestamps = true;
}
