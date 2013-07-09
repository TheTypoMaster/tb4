<?php

class Tb_data_typesTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tb_data_types')->delete();

        $tb_data_types = ( array (
        		array (
        				'data_type' => 'weather_condition',
        		),
        		array (
        				'data_type' => 'track_condition',
        		),
        		
        		
        ) );

        // Uncomment the below to run the seeder
         DB::table('tb_data_types')->insert($tb_data_types);
    }

}