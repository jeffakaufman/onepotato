<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomRegisterPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_register_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('route', 40)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('subtitle', 500)->nullable();
            $table->tinyInteger('status');
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
