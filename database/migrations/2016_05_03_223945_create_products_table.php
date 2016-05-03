<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	public function up()
	{
		Schema::create('products', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->float('cost');
			$table->string('product_description', 1000)->nullable();
			$table->string('sku', 255)->nullable();
			$table->string('product_title', 255)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('products');
	}
}