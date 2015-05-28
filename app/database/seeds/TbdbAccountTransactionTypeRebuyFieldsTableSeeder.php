<?php

class TbdbAccountTransactionTypeRebuyFieldsTableSeeder extends Seeder
{

    public function run()
    {
        // Uncomment the below to wipe the table clean before populating
        // DB::table('tbdbaccounttransactiontyperebuyfields')->truncate();

        $tbdbaccounttransactiontyperebuyfields = array(
            array(
                "keyword"      => "tournamentrebuybuyin",
                "name"        => "Tournament Rebuy Buyin",
                "description" => "Account Balance is being decreased because of a tournament rebuy buyin fee"
            ),

            array(
                "keyword"      => "tournamentrebuyentry",
                "name"        => "Tournament Rebuy Entry",
                "description" => "Account Balance is being decreased because of a tournament rebuy entry fee"
            ),

            array(
                "keyword"      => "tournamenttopupbuyin",
                "name"        => "Tournament Topup Buyin",
                "description" => "Account Balance is being decreased because of a tournament topup buyin"
            ),

            array(
                "keyword"      => "tournamenttopupentry",
                "name"        => "Tournament Topup Entry",
                "description" => "Account Balance is being decreased because of a tournament topup entry fee"
            ),


        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_account_transaction_type')->insert($tbdbaccounttransactiontyperebuyfields);
    }

}
