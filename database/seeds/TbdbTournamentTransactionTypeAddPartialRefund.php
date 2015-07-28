<?php

use Illuminate\Database\Seeder;

class TbdbTournamentTransactionTypeAddPartialRefund extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tbdbtournamenttransactiontypepartialrefund = array(
            array(
                "keyword"     => "freebetpartialrefund",
                "name"        => "Free Bet Partial Refund",
                "description" => "A runner has been scratched in an exotic bet and a partial refund has been awarded"
            )
        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_tournament_transaction_type')->insert($tbdbtournamenttransactiontypepartialrefund);
    }
}
