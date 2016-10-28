<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInsteadOfToMenuAssigments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menus_users', function (Blueprint $table) {
            $table->integer('instead_of')->nullable();
            $table->string('initial_comments', 255)->nullable();
            $table->string('change_comments', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menus_users', function (Blueprint $table) {
            //
        });
    }
}
