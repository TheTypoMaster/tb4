<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduledPaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tb_scheduled_payments', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('amount');
            $table->string('recurring_period');
            $table->dateTime('next_payment');
            $table->tinyInteger('active');
            $table->integer('retries')->default(0);

            $table->integer('payment_token_id')->unsigned();
            $table->string('payment_token_type');

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
		Schema::drop('tb_scheduled_payments');
	}

}
