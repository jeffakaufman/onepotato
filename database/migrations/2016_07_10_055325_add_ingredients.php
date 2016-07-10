<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIngredients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->boolean('hasBeef');
            $table->boolean('hasPoultry');
            $table->boolean('hasFish');
            $table->boolean('hasLamb');
            $table->boolean('hasPork');
            $table->boolean('hasShellfish');
            $table->boolean('hasNoGluten');
            $table->boolean('hasNuts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            //
        });
    }
}
