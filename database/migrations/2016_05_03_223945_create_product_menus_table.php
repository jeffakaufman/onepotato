<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductMenusTable extends Migration {

	public function up()
	{
		Schema::create('product_menus', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('product_id');
			$table->integer('menu_id');
		});
	}

	public function down()
	{
		Schema::drop('product_menus');
	}
}