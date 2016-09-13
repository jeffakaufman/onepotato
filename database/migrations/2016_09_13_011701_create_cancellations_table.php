<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCancellationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancellations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
			$table->integer('user_id');
			$table->string('cancel_reason', 255)->nullable();
			$table->string('cancel_specify', 1000)->nullable();
			$table->string('cancel_suggestions', 1000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cancellations');
    }
}
