<?php

use Illuminate\Database\Seeder;

class TbdbAccountTransactionTypeAddPartialRefund extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tbdbaccounttransactiontypepartialrefund = array(
            array(
                "keyword"      => "betpartialrefund",
                "name"        => "Bet Partial Refund",
                "description" => "Account Balance is being increased because of a partial refund"
            )
        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_account_transaction_type')->insert($tbdbaccounttransactiontypepartialrefund);
    }
}
