<?php

class TbDataValuesTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('tb_data_values')->truncate();
        
		\DB::table('tb_data_values')->insert(array (
			0 => 
			array (
				'id' => '1',
				'data_type_id' => '1',
				'value' => 'FINE',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			1 => 
			array (
				'id' => '2',
				'data_type_id' => '1',
				'value' => 'SHOWERY',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			2 => 
			array (
				'id' => '3',
				'data_type_id' => '1',
				'value' => 'OVERCAST',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			3 => 
			array (
				'id' => '4',
				'data_type_id' => '2',
				'value' => 'FAST',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			4 => 
			array (
				'id' => '5',
				'data_type_id' => '2',
				'value' => 'GOOD',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			5 => 
			array (
				'id' => '6',
				'data_type_id' => '2',
				'value' => 'DEAD',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			6 => 
			array (
				'id' => '7',
				'data_type_id' => '2',
				'value' => 'SLOW',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			7 => 
			array (
				'id' => '8',
				'data_type_id' => '2',
				'value' => 'HEAVY',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			8 => 
			array (
				'id' => '9',
				'data_type_id' => '3',
				'value' => 'Betting Closed',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			9 => 
			array (
				'id' => '10',
				'data_type_id' => '3',
				'value' => 'Odds Changed',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			10 => 
			array (
				'id' => '11',
				'data_type_id' => '3',
				'value' => 'Line Changed',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			11 => 
			array (
				'id' => '12',
				'data_type_id' => '3',
				'value' => 'Selection Withdrawn',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			12 => 
			array (
				'id' => '13',
				'data_type_id' => '1',
				'value' => '-',
				'created_at' => '2014-03-05 05:41:04',
				'updated_at' => '0000-00-00 00:00:00',
			),
			13 => 
			array (
				'id' => '14',
				'data_type_id' => '2',
				'value' => '-',
				'created_at' => '0000-00-00 00:00:00',
				'updated_at' => '0000-00-00 00:00:00',
			),
			14 => 
			array (
				'id' => '15',
				'data_type_id' => '2',
				'value' => 'FAST 1',
				'created_at' => '2015-02-17 16:10:59',
				'updated_at' => '2015-02-17 16:11:00',
			),
			15 => 
			array (
				'id' => '16',
				'data_type_id' => '2',
				'value' => 'GOOD 2',
				'created_at' => '2015-02-17 16:11:13',
				'updated_at' => '2015-02-17 16:11:15',
			),
			16 => 
			array (
				'id' => '17',
				'data_type_id' => '2',
				'value' => 'GOOD 3',
				'created_at' => '2015-02-17 16:11:22',
				'updated_at' => '2015-02-17 16:11:23',
			),
			17 => 
			array (
				'id' => '18',
				'data_type_id' => '2',
				'value' => 'DEAD 4',
				'created_at' => '2015-02-17 16:11:34',
				'updated_at' => '2015-02-17 16:11:35',
			),
			18 => 
			array (
				'id' => '19',
				'data_type_id' => '2',
				'value' => 'DEAD 5',
				'created_at' => '2015-02-17 16:11:43',
				'updated_at' => '2015-02-17 16:11:44',
			),
			19 => 
			array (
				'id' => '20',
				'data_type_id' => '2',
				'value' => 'SLOW 6',
				'created_at' => '2015-02-17 16:11:54',
				'updated_at' => '2015-02-17 16:11:56',
			),
			20 => 
			array (
				'id' => '21',
				'data_type_id' => '2',
				'value' => 'SLOW 7',
				'created_at' => '2015-02-17 16:12:03',
				'updated_at' => '2015-02-17 16:12:04',
			),
			21 => 
			array (
				'id' => '22',
				'data_type_id' => '2',
				'value' => 'HEAVY 8',
				'created_at' => '2015-02-17 16:12:12',
				'updated_at' => '2015-02-17 16:12:13',
			),
			22 => 
			array (
				'id' => '23',
				'data_type_id' => '2',
				'value' => 'HEAVY 9',
				'created_at' => '2015-02-17 16:12:23',
				'updated_at' => '2015-02-17 16:12:24',
			),
			23 => 
			array (
				'id' => '24',
				'data_type_id' => '2',
				'value' => 'HEAVY 10',
				'created_at' => '2015-02-17 16:12:33',
				'updated_at' => '2015-02-17 16:12:34',
			),
		));
	}

}
