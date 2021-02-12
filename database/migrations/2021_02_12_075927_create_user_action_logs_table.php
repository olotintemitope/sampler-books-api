<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserActionLogsTable extends Migration
{
	/**
	 * Run the migrations.
	 * 
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_action_logs', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('book_id', 10);
			$table->integer('user_id', 10);
			$table->enum('action', ['CHECKIN','CHECKOUT']);
			$table->timestamps();

		});
	}

	/**
	 * Reverse the migrations.
	 * 
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_action_logs');
	}

}