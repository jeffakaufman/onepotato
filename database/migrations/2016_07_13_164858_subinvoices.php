<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Subinvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
			Schema::create('subinvoices', function (Blueprint $table) {
				
	            $table->increments('id');
	            $table->timestamps();
				$table->string('stripe_event_id',255);
				$table->string('stripe_customer_id',255);
				$table->string('stripe_sub_id',255);
				$table->string('stripe_invoice_type',255);
				$table->datetime('charge_date');
				$table->string('charge_amount',255);
				$table->string('plan_id',255);
				$table->integer('user_id');
				$table->string('raw_json',3000);
				$table->string('invoice_status',255);
				
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
