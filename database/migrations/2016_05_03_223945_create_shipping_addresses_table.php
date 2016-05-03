<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShippingAddressesTable extends Migration {

	public function up()
	{
		Schema::create('shipping_addresses', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('user_id');
			$table->boolean('is_current');
			$table->string('shipping_address', 255)->nullable();
			$table->string('shipping_address_2', 255)->nullable();
			$table->string('shipping_city', 255)->nullable();
			$table->string('shipping_state', 255)->nullable();
			$table->string('shipping_zip', 255)->nullable();
			$table->string('shipping_country', 255)->nullable();
			$table->string('phone1', 255)->nullable();
			$table->string('phone2', 255)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('shipping_addresses');
	}
}