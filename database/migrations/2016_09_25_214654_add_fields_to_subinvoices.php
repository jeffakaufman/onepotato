<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToSubinvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subinvoices', function (Blueprint $table) {
            //
				$table->datetime('ship_date')->nullable();
				$table->string('ship_carrier',255)->nullable();
				$table->string('ship_service',255)->nullable();
				$table->string('tracking_number',255)->nullable();
				$table->string('ship_station_xml',3000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subinvoices', function (Blueprint $table) {
            //
        });
    }
}
