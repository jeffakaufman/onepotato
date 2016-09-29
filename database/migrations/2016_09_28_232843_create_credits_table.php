<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		 Schema::create('credits', function (Blueprint $table) {
	            $table->increments('id');
	            $table->timestamps();
				$table->integer('user_id')->nullable();
				$table->integer('credit_amount')->nullable();;
				$table->integer('credit_percent')->nullable();
				$table->datetime('date_applied')->nullable();
				$table->string('credit_description', 255)->nullable();
				$table->string('credit_status', 1000)->nullable();
				$table->string('stripe_xml', 5000)->nullable();
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
