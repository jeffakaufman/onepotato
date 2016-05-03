<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDietaryPreferencesTable extends Migration {

	public function up()
	{
		Schema::create('dietary_preferences', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('dietary_preference_description', 255);
		});
	}

	public function down()
	{
		Schema::drop('dietary_preferences');
	}
}