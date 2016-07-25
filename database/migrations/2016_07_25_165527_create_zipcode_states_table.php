<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZipcodeStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zipcode_states', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
			$table->integer('zipcode')->nullable();
			$table->string('state', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('zipcode_states');
    }
}
