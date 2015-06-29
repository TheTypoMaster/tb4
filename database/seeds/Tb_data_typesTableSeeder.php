<?php

use Illuminate\Database\Seeder;

class Tb_data_typesTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tb_data_types')->delete();

        $tb_data_types = ( array (
        		array (
        				'id' => '1',
        				'data_type' => 'weather_condition',
        		),
        		array (
        				'id' => '2',
        				'data_type' => 'track_condition',
        		),
        		array (
        				'id' => '3',
        				'data_type' => 'bet_error_codes',
        		),
        		
        		
        ) );

        // Uncomment the below to run the seeder
         DB::table('tb_data_types')->insert($tb_data_types);
    }

}