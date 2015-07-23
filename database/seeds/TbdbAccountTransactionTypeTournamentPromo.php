<?php

use Illuminate\Database\Seeder;

class TbdbAccountTransactionTypeTournamentPromo extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tbdb_account_transaction_type = array(
            array(
                "keyword" => "promotournamententry",
                "name" => "Promo Tournament Entry",
                "description" => "Account balance is being increased because of a promotional tournament entry"
            )
        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_account_transaction_type')->insert($tbdb_account_transaction_type);
    }
}
