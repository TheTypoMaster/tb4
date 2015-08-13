<?php

use Illuminate\Database\Seeder;

class tbdb_bet_limit_types_add_exposure_limits extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tbdb_bet_limit_add_exposure_limits = array(
            array(
                "name"           => "exposure_racing",
                "nickname"       => "Win Exposure",
                "value"          => "1",
                "default_amount" => 100000,
                "notes"          => "Win bets exposure limit",
            ),
            array(
                "name"           => "exposure_racing",
                "nickname"       => "Place Exposure",
                "value"          => "2",
                "default_amount" => 100000,
                "notes"          => "Place bets exposure limit",
            ),
            array(
                "name"           => "exposure_sport",
                "nickname"       => "Sport Exposure",
                "value"          => 17,
                "default_amount" => 100000,
                "notes"          => "Sports bets exposure limit",
            )
        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_bet_limit_types')->insert($tbdb_bet_limit_add_exposure_limits);
    }
}
