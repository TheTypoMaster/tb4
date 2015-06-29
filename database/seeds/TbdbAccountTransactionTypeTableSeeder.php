<?php

use Illuminate\Database\Seeder;

class TbdbAccountTransactionTypeTableSeeder extends Seeder {

	public function run()
	{

		$tbdb_account_transaction_type = array(
			array(
				"keyword" => "dormantcharge",
				"name" => "Dormant Account Charge",
				"description" => "Account balance is being decreased because of dormant account charge"
			)
		);

		// Uncomment the below to run the seeder
		DB::table('tbdb_account_transaction_type')->insert($tbdb_account_transaction_type);
	}

}
