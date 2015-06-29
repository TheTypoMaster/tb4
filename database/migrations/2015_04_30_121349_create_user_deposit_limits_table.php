<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDepositLimitsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_user_deposit_limit', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('user_id')->unsigned();

            $table->integer('amount');

            $table->text('notes');

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
		Schema::drop('tb_user_deposit_limit');
	}

}
