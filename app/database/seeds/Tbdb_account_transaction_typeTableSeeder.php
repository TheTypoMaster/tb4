<?php

class Tbdb_account_transaction_typeTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
//    	DB::table('Tbdb_account_transaction_type')->delete();

        $tbdb_account_transaction_type = array(
                array (
                        'id' => '18',
                        'keyword' => 'betwincancelled',
                        'name' => 'Bet Win Cancelled',
                        'description' => 'Account Balance is being decreased because of a bet win refund/cancel'
                ),
        		
        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_account_transaction_type')->insert($tbdb_account_transaction_type);
    }

}