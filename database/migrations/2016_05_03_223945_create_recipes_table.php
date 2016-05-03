<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecipesTable extends Migration {

	public function up()
	{
		Schema::create('recipes', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('recipe_title', 255);
			$table->integer('recipe_type');
			$table->string('photo_url', 1000)->nullable();
			$table->string('pdf_url', 1000)->nullable();
			$table->string('instructions', 3000)->nullable();
			$table->string('video_url', 1000)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('recipes');
	}
}