<?php

class Tbdb_event_status_AddDeleteRecordEventStatusTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tbdb_event_status')->delete();

        $tbdb_event_status_adddeleterecordeventstatus = array(

	        		array (
	        				'id' => '1',
	        				'keyword' => 'selling',
	        				'name' => 'Selling',
	        				'description' => 'Open for bets'
	        		),
	        		array (
	        				'id' => '2',
	        				'keyword' => 'paying',
	        				'name' => 'Paying',
	        				'description' => 'All Paying'
	        		),
	        		array (
	        				'id' => '3',
	        				'keyword' => 'abandoned',
	        				'name' => 'Abandoned',
	        				'description' => 'Event Abandoned'
	        		),
	        		array (
	        				'id' => '4',
	        				'keyword' => 'paid',
	        				'name' => 'Paid',
	        				'description' => 'All bets paid'
	        		),
	        		array (
	        				'id' => '5',
	        				'keyword' => 'closed',
	        				'name' => 'Closed',
	        				'description' => 'Event is closed to betting'
	        		),
	        		array (
	        				'id' => '6',
	        				'keyword' => 'interim',
	        				'name' => 'Interim',
	        				'description' => 'Interim event results'
	        		),
        			array (
        					'id' => '7',
        					'keyword' => 'deleted',
        					'name' => 'Deleted',
        					'description' => 'Removed'
        			)
        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_event_status')->insert($tbdb_event_status_adddeleterecordeventstatus);
    }

}