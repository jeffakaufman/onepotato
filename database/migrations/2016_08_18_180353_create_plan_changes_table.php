<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_changes', function(Blueprint $table) {
			 $table->increments('id');
	            $table->timestamps();
	            $table->integer('user_id')->nullable();
	            $table->date('date_to_change')->nullable();
				$table->string('sku_to_change', 255)->nullable();
				$table->string('old_sku', 255)->nullable();
				$table->string('status', 255)->nullable();
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
