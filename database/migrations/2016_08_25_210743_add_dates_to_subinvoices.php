<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatesToSubinvoices extends Migration
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
			$table->datetime('period_start_date')->nullable();
			$table->datetime('period_end_date')->nullable();
			$table->string('coupon_code',255)->nullable();
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
