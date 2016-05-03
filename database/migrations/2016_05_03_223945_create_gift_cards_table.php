<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGiftCardsTable extends Migration {

	public function up()
	{
		Schema::create('gift_cards', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('gift_card_number', 255);
			$table->integer('used_by_id');
			$table->datetime('used_by_date');
			$table->datetime('expiration_date')->nullable();
			$table->float('card_value');
			$table->integer('giver_id');
		});
	}

	public function down()
	{
		Schema::drop('gift_cards');
	}
}