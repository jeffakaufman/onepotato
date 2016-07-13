<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Orders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
			$table->int('order_id');
            $table->timestamps();
			$table->datetime('ship_date');
			$table->string('ship_carrier',255);
			$table->string('ship_service',255);
			$table->string('tracking_number',255);
			$table->string('ship_station_xml',3000);
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
