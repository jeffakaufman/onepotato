<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenusTable extends Migration {

	public function up()
	{
		Schema::create('menus', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('menu_description', 1000)->nullable();
			$table->string('menu_title', 255)->nullable();
			$table->datetime('menu_delivery_date')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('menus');
	}
}