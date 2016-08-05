<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus_users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('menus_id')->unsigned()->index();
            $table->integer('users_id')->unsigned()->index();
            $table->date('delivery_date');
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
