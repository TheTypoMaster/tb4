<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddProcessedAmountToTbdbWithdrawalRequestTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tbdb_withdrawal_request', function(Blueprint $table) {
			$table->integer('processed_amount')->nullable()->after('amount');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tbdb_withdrawal_request', function(Blueprint $table) {
			$table->dropColumn('processed_amount');
		});
	}

}
