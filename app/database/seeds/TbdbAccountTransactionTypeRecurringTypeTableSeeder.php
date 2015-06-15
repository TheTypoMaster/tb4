<?php

class TbdbAccountTransactionTypeRecurringTypeTableSeeder extends Seeder {

	public function run()
	{
		$tbdbaccounttransactiontyperecurringtype = array(
            array(
                "keyword"      => "ewayrecurringdeposit",
                "name"        => "Eway Recurring Deposit",
                "description" => "Account Balance is being increased because of an Eway recurring deposit"
            ),

        );

		DB::table('tbdb_account_transaction_type')->insert($tbdbaccounttransactiontyperecurringtype);
	}

}
