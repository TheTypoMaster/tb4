<?php

class Tb_data_provider_matchTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tb_data_provider_match')->delete();

        $tb_data_provider_match = ( array (
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '1',
        				'value' => 'Fine',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '1',
        				'value' => 'fine',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '2',
        				'value' => 'Showery',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '2',
        				'value' => 'showery',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '3',
        				'value' => 'Overcast',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '3',
        				'value' => 'overcast',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '3',
        				'value' => 'Ocast',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		
        		
        		
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '4',
        				'value' => 'Fast',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '4',
        				'value' => 'fast',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '4',
        				'value' => 'FAST',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		
        		
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '5',
        				'value' => 'Good',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '5',
        				'value' => 'good',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '5',
        				'value' => 'GOOD',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '6',
        				'value' => 'Dead',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '6',
        				'value' => 'dead',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '6',
        				'value' => 'DEAD',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '6',
        				'value' => 'dead4',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		
        		

        		array (
        				'provider_id' => '1',
        				'data_value_id' => '7',
        				'value' => 'Slow',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '7',
        				'value' => 'slow',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '7',
        				'value' => 'SLOW',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '8',
        				'value' => 'Heavy',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '8',
        				'value' => 'heavy',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'provider_id' => '1',
        				'data_value_id' => '8',
        				'value' => 'HEAVY',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		)
        		        		
        		
        		
        ) );

        // Uncomment the below to run the seeder
        DB::table('tb_data_provider_match')->insert($tb_data_provider_match);
    }

}