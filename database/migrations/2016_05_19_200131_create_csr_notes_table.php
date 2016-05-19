<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCsrNotesTable extends Migration {

	public function up()
	{
		Schema::create('csr_notes', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('note_text', 2000);
			$table->integer('user_id');
			$table->integer('csr_id');
		});
	}

	public function down()
	{
		Schema::drop('csr_notes');
	}
}