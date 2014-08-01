<?php

class Tbdb_bet_limit_typesTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tbdb_bet_limit_types')->delete();
		
        $betLimitTypes = array(
        		
        		array (
        				'id' => 1,
        				'name' => 'default',
        				'nickname' => 'global',
        				'value' => 'all',
        				'default_amount' => 100000,
        				'notes' => '**safety net** If no user set or default value is in this table, this is the limit applied.',
        		),
        		array (
        				'id' => 2,
        				'name' => 'default_flexi',
        				'nickname' => 'global flexi',
        				'value' => 'all',
        				'default_amount' => 100000,
        				'notes' => '**safety net** If no user set flexi limit or default value is in this table, this is the limit applied.',
        		),
        		array (
        				'id' => 3,
        				'name' => 'bet_type',
        				'nickname' => 'Win',
        				'value' => '1',
        				'default_amount' => 100000,
        				'notes' => 'Win bets',
        		),
        		array (
        				'id' => 4,
        				'name' => 'bet_type',
        				'nickname' => 'Place',
        				'value' => '2',
        				'default_amount' => 100000,
        				'notes' => 'Place bets',
        		),			
        		array (
        				'id' => 5,
        				'name' => 'bet_type',
        				'nickname' => 'Quinella',
        				'value' => '4',
        				'default_amount' => 100000,
        				'notes' => 'Quinella exotic bets',
        		),			
        		array (
        				'id' => 6,
        				'name' => 'bet_type',
        				'nickname' => 'Exacta',
        				'value' => '5',
        				'default_amount' => 100000,
        				'notes' => 'Exacta exotic bets',
        		),						
        		array (
        				'id' => 7,
        				'name' => 'bet_type',
        				'nickname' => 'Trifecta',
        				'value' => '6',
        				'default_amount' => 100000,
        				'notes' => 'Trifecta exotic bets',
        		),						
        		array (
        				'id' => 8,
        				'name' => 'bet_type',
        				'nickname' => 'First Four',
        				'value' => '7',
        				'default_amount' => 100000,
        				'notes' => 'First Four exotic bets',
        		),						
        		array (
        				'id' => 9,
        				'name' => 'bet_flexi',
        				'nickname' => 'Quinella Flexi',
        				'value' => '4',
        				'default_amount' => 100000,
        				'notes' => 'Quinella Flexi limit',
        		),						
        		array (
        				'id' => 10,
        				'name' => 'bet_flexi',
        				'nickname' => 'Exacta Flexi',
        				'value' => '5',
        				'default_amount' => 100000,
        				'notes' => 'Exacta Flexi limit',
        		),						
        		array (
        				'id' => 11,
        				'name' => 'bet_flexi',
        				'nickname' => 'Trifecta Flexi',
        				'value' => '6',
        				'default_amount' => 100000,
        				'notes' => 'Trifecta Flexi limit',
        		),							
        		array (
        				'id' => 12,
        				'name' => 'bet_flexi',
        				'nickname' => 'First Four Flexi',
        				'value' => '7',
        				'default_amount' => 100000,
        				'notes' => 'First Four Flexi limit',
        		),									
        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_bet_limit_types')->insert($betLimitTypes);
    }

}