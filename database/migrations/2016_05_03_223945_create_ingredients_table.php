<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIngredientsTable extends Migration {

	public function up()
	{
		Schema::create('ingredients', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('ingredient_name', 255)->nullable();
			$table->string('ingredient_description', 1000)->nullable();
			$table->string('ingredient_quantity', 255)->nullable();
			$table->string('ingredient_unit', 255)->nullable();
			$table->string('restrictions', 255)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('ingredients');
	}
}