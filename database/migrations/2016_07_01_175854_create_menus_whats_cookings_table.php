<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusWhatsCookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus_whats_cookings', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('menus_id')->unsigned()->index();
            $table->foreign('menus_id')->references('id')->on('menus');
            $table->integer('whats_cookings_id')->unsigned()->index();
            $table->foreign('whats_cookings_id')->references('id')->on('whats_cookings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('menus_whats_cookings');
    }
}
