<?php

class Tb_data_ordering_provider_matchTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tb_data_ordering_provider_match')->delete();

        $tb_data_ordering_provider_match = array(
        		array (
        				'provider_id' => '1',
        				'topbetta_keyword' => 'englishpremierleague',
        				'provider_value' => 'ENGLISH PREMIER LEAGUE'
        		),
        		array (
        				'provider_id' => '1',
        				'topbetta_keyword' => 'englishpremierleaguefutures',
        				'provider_value' => 'English Premier League Futures'
        		),
        );

        // Uncomment the below to run the seeder
        DB::table('tb_data_ordering_provider_match')->insert($tb_data_ordering_provider_match);
    }

}