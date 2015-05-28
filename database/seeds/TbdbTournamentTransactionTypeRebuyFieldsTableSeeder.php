<?php

use Illuminate\Database\Seeder;

class TbdbTournamentTransactionTypeRebuyFieldsTableSeeder extends Seeder
{

    public function run()
    {
        // Uncomment the below to wipe the table clean before populating
        // DB::table('tbdbtournamenttransactiontyperebuyfields')->truncate();

        $tbdbtournamenttransactiontyperebuyfields = array(
            array(
                "keyword"      => "rebuybuyin",
                "name"        => "Rebuy buyin",
                "description" => "The user is spending Tournament Dollars as part of their tournament rebuy buy-in"
            ),

            array(
                "keyword"      => "rebuyentry",
                "name"        => "Rebuy Entry",
                "description" => "The user is spending Tournament Dollars as an entry fee for a tournament rebuy"
            ),

            array(
                "keyword"      => "topupbuyin",
                "name"        => "Topup Buyin",
                "description" => "The user is spending Tournament Dollars as part of their tournament topup buy-in"
            ),

            array(
                "keyword"      => "topupentry",
                "name"        => "Topup Entry",
                "description" => "The user is spending Tournament Dollars as an entry fee for a tournament topup"
            ),

        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_tournament_transaction_type')->insert($tbdbtournamenttransactiontyperebuyfields);
    }

}
