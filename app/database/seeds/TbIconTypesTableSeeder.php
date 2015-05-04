<?php

class TbIconTypesTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('tb_icon_types')->truncate();
        
		\DB::table('tb_icon_types')->insert(array (
			0 => 
			array (
				'id' => '1',
				'name' => 'sport',
				'created_at' => '2015-03-19 16:18:40',
				'updated_at' => '2015-03-19 16:18:40',
			),
			1 => 
			array (
				'id' => '2',
				'name' => 'base_competition',
				'created_at' => '2015-03-19 16:19:02',
				'updated_at' => '2015-03-19 16:19:05',
			),
			2 => 
			array (
				'id' => '3',
				'name' => 'event_group',
				'created_at' => '2015-03-19 16:19:14',
				'updated_at' => '2015-03-19 16:19:15',
			),
			3 => 
			array (
				'id' => '4',
				'name' => 'event',
				'created_at' => '2015-03-19 16:19:21',
				'updated_at' => '2015-03-19 16:19:22',
			),
			4 => 
			array (
				'id' => '5',
				'name' => 'market_type',
				'created_at' => '2015-03-19 16:19:33',
				'updated_at' => '2015-03-19 16:19:36',
			),
			5 => 
			array (
				'id' => '6',
				'name' => 'team',
				'created_at' => '2015-03-19 16:19:45',
				'updated_at' => '2015-03-19 16:19:47',
			),
            6 => array (
                'id' => '7',
                'name' => 'player',
                'created_at' => '2015-03-19 16:19:45',
                'updated_at' => '2015-03-19 16:19:47',
            ),
            7 => array (
                'id' => '8',
                'name' => 'region',
                'created_at' => '2015-03-19 16:19:45',
                'updated_at' => '2015-03-19 16:19:47',
            ),
		));
	}

}
