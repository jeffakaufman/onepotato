<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingholdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shippingholds', function(Blueprint $table) {
            //
				$table->increments('id');
				$table->integer('user_id')->nullable();
				$table->datetime('date_to_hold')->nullable();
				$table->string('hold_status', 255)->nullable();
				$table->timestamps();
			
				
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shippingholds', function (Blueprint $table) {
            //
        });
    }
}
