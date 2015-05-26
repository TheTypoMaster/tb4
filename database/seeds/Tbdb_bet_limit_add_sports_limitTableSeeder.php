<?php

use Illuminate\Database\Seeder;

class Tbdb_bet_limit_add_sports_limitTableSeeder extends Seeder
{

    public function run()
    {

        $tbdb_bet_limit_add_sports_limit = array(
            array(
                "name"           => "bet_sports",
                "nickname"       => "Sports",
                "value"          => "",
                "default_amount" => 100000,
                "notes"          => "Sports Bets",
            )
        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_bet_limit_types')->insert($tbdb_bet_limit_add_sports_limit);
    }

}
