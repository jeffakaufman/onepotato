<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenuRecipesTable extends Migration {

	public function up()
	{
		Schema::create('menu_recipes', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('recipe_id')->nullable();
			$table->integer('menu_id')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('menu_recipes');
	}
}