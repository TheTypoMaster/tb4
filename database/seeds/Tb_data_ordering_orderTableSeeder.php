<?php

use Illuminate\Database\Seeder;

class Tb_data_ordering_orderTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tb_data_ordering_order')->delete();

        $tb_data_ordering_order = array(
        		
        		array (
        				'sport_keyword' => 'Soccer',
        				'topbetta_keyword' => 'englishpremierleague',
        				'order_number' => '1'
        		),
        		array (
        				'sport_keyword' => 'Soccer',
        				'topbetta_keyword' => 'englishpremierleaguefutures',
        				'order_number' => '2'
        		),
        		

        );

        // Uncomment the below to run the seeder
        DB::table('tb_data_ordering_order')->insert($tb_data_ordering_order);
    }

}