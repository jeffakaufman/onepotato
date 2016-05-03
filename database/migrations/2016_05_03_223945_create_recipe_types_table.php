<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecipeTypesTable extends Migration {

	public function up()
	{
		Schema::create('recipe_types', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('recipe_type_description', 1000)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('recipe_types');
	}
}