<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecipeIngredientsTable extends Migration {

	public function up()
	{
		Schema::create('recipe_ingredients', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('ingredient_id')->nullable();
			$table->integer('recipe_id')->nullable();
			$table->string('ingredient_instructions', 2000)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('recipe_ingredients');
	}
}