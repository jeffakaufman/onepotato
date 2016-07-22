<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::table('shipping_addresses', function (Blueprint $table) {
			$table->string('address_type',255)->nullable();
			$table->string('delivery_instructions',1000)->nullable();
			$table->string('shipping_first_name',255)->nullable();
			$table->string('shipping_last_name',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
