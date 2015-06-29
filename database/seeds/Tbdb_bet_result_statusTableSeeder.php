<?php

use Illuminate\Database\Seeder;

class Tbdb_bet_result_statusTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tbdb_bet_result_status')->delete();

        $tbdb_bet_result_status = array(

        		array (
        				'id' => '1',
        				'name' => 'unresulted',
        				'description' => 'unresulted',
        				'status_flag' => '1',
        				'created_date' => new DateTime,
        				'updated_date' => new DateTime
        		),
        		array (
        				'id' => '2',
        				'name' => 'paid',
        				'description' => 'paid',
        				'status_flag' => '1',
        				'created_date' => new DateTime,
        				'updated_date' => new DateTime
        		),
        		array (
        				'id' => '3',
        				'name' => 'partially-refunded',
        				'description' => 'partially-refunded',
        				'status_flag' => '1',
        				'created_date' => new DateTime,
        				'updated_date' => new DateTime
        		),
        		array (
        				'id' => '4',
        				'name' => 'fully-refunded',
        				'description' => 'fully-refunded',
        				'status_flag' => '1',
        				'created_date' => new DateTime,
        				'updated_date' => new DateTime
        		),
        		array (
        				'id' => '5',
        				'name' => 'pending',
        				'description' => 'pending',
        				'status_flag' => '1',
        				'created_date' => new DateTime,
        				'updated_date' => new DateTime
        		),
        		array (
        				'id' => '6',
        				'name' => 'processing',
        				'description' => 'This status is set when the bet record is placed and is then updated to another afterwards',
        				'status_flag' => '1',
        				'created_date' => new DateTime,
        				'updated_date' => new DateTime
        		),
        		array (
        				'id' => '7',
        				'name' => 'failed',
        				'description' => 'This status is set when the bet placement has failed. Any bet record with this status should not be displayed to the user',
        				'status_flag' => '1',
        				'created_date' => new DateTime,
        				'updated_date' => new DateTime
        		),
                array (
                        'id' => '8',
                        'name' => 'cancelled',
                        'description' => 'This bet was cancelled by an admin user',
                        'status_flag' => '1',
                        'created_date' => new DateTime,
                        'updated_date' => new DateTime
                ),
        		
        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_bet_result_status')->insert($tbdb_bet_result_status);
    }

}