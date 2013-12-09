<?php

class Tbdb_selection_status_addSuspendedRecordTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tbdb_selection_status')->delete();

        $tbdb_selection_status_addsuspendedrecord = array(
        		
        		array (
        				'id' => '1',
        				'keyword' => 'not scratched',
        				'name' => 'Not Scratched',
        				'description' => 'The runner is still active an taking bets'
        		),
        		array (
        				'id' => '2',
        				'keyword' => 'scratched',
        				'name' => 'Scratched',
        				'description' => 'The runner has been scratched ahead of the race'
        		),
        		array (
        				'id' => '3',
        				'keyword' => 'late scratching',
        				'name' => 'Late Scratching',
        				'description' => 'The runner has been scratched late'
        		),
        		array (
        				'id' => '4',
        				'keyword' => 'suspended',
        				'name' => 'Suspended',
        				'description' => 'The Selection has been suspended'
        		)

        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_selection_status')->insert($tbdb_selection_status_addsuspendedrecord);
    }

}