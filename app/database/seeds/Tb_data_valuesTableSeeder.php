<?php

class Tb_data_valuesTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tb_data_values')->delete();

        $tb_data_values = ( array (
        		array (
        				'id' => '1',
        				'data_type_id' => '1',
        				'value' => 'FINE',
        		),
        		array (
        				'id' => '2',
        				'data_type_id' => '1',
        				'value' => 'SHOWERY',
        		),
        		array (
        				'id' => '3',
        				'data_type_id' => '1',
        				'value' => 'OVERCAST',
        		),
        		array (
        				'id' => '4',
        				'data_type_id' => '2',
        				'value' => 'FAST',
        		),
        		array (
        				'id' => '5',
        				'data_type_id' => '2',
        				'value' => 'GOOD',
        		),
        		array (
        				'id' => '6',
        				'data_type_id' => '2',
        				'value' => 'DEAD',
        		),
        		array (
        				'id' => '7',
        				'data_type_id' => '2',
        				'value' => 'SLOW',
        		),
        		array (
        				'id' => '8',
        				'data_type_id' => '2',
        				'value' => 'HEAVY',
        		),
        ) );

        // Uncomment the below to run the seeder
        DB::table('tb_data_values')->insert($tb_data_values);
    }

}