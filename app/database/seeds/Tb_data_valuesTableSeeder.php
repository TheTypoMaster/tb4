<?php

class Tb_data_valuesTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tb_data_values')->truncate();

        $tb_data_values = ( array (
        		array (
        				'data_type_id' => '1',
        				'value' => 'FINE',
        		),
        		array (
        				'data_type_id' => '1',
        				'value' => 'SHOWERY',
        		),
        		array (
        				'data_type_id' => '1',
        				'value' => 'OVERCAST',
        		),
        		array (
        				'data_type_id' => '2',
        				'value' => 'FAST',
        		),
        		array (
        				'data_type_id' => '2',
        				'value' => 'GOOD',
        		),
        		array (
        				'data_type_id' => '2',
        				'value' => 'DEAD',
        		),
        		array (
        				'data_type_id' => '2',
        				'value' => 'SLOW',
        		),
        		array (
        				'data_type_id' => '2',
        				'value' => 'HEAVY',
        		),
        		
        ) );

        // Uncomment the below to run the seeder
        DB::table('tb_data_values')->insert($tb_data_values);
    }

}