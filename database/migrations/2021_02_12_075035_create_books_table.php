<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
	/**
	 * Run the migrations.
	 * 
	 * @return void
	 */
	public function up()
	{
		Schema::create('books', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('title', 255);
			$table->string('isbn', 10);
			$table->date('published_at');
			$table->enum('status', ['CHECKED_OUT', 'AVAILABLE']);
			$table->timestamps();
            $table->softDeletes();

		});
	}

	/**
	 * Reverse the migrations.
	 * 
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('books');

        Schema::table('books', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
	}

}