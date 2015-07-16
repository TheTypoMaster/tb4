<?php

use Illuminate\Database\Seeder;

class TbSportTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('tb_sports')->truncate();
        
		\DB::table('tb_sports')->insert(array (
			0 => 
			array (
				'id' => '1',
				'name' => 'galloping',
                'short_name' => 'galloping',
                'default_name' => 'galloping',
				'description' => 'gallop racing',
				'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
				'created_at' => '2015-03-09 13:30:00',
				'updated_at' => '2015-03-09 13:30:00',
			),
			1 => 
			array (
				'id' => '2',
				'short_name' => 'harness',
                'name' => 'harness',
                'default_name' => 'harness',
				'description' => 'harness racing',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			2 => 
			array (
				'id' => '3',
				'short_name' => 'greyhounds',
                'name' => 'greyhounds',
                'default_name' => 'greyhounds',
				'description' => 'greyhound racing',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			3 => 
			array (
				'id' => '4',
				'short_name' => 'AFL',
                'name' => 'AFL',
                'default_name' => 'AFL',
				'description' => 'AFL',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			4 => 
			array (
				'id' => '5',
				'short_name' => 'Rugby League',
                'name' => 'Rugby League',
                'default_name' => 'Rugby League',
				'description' => 'Rugby League',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			5 => 
			array (
				'id' => '6',
				'short_name' => 'Rugby Union',
                'name' => 'Rugby Union',
                'default_name' => 'Rugby Union',
				'description' => 'Rugby Union',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			6 => 
			array (
				'id' => '7',
				'short_name' => 'Football',
                'name' => 'Football',
                'default_name' => 'Football',
				'description' => 'Soccer',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			7 => 
			array (
				'id' => '8',
				'short_name' => 'Cricket',
                'name' => 'Cricket',
                'default_name' => 'Cricket',
				'description' => 'Cricket',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			8 => 
			array (
				'id' => '9',
				'short_name' => 'Mixed Martial Arts',
                'name' => 'Mixed Martial Arts',
                'default_name' => 'Mixed Martial Arts',
				'description' => 'Mixed Martial Arts',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			9 => 
			array (
				'id' => '10',
				'short_name' => 'Baseball',
                'name' => 'Baseball',
                'default_name' => 'Baseball',
				'description' => 'Baseball',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			10 => 
			array (
				'id' => '11',
				'short_name' => 'Tennis',
                'name' => 'Tennis',
                'default_name' => 'Tennis',
				'description' => 'Tennis',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			11 => 
			array (
				'id' => '12',
				'short_name' => 'American Football',
                'name' => 'American Football',
                'default_name' => 'American Football',
				'description' => 'American Football',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			12 => 
			array (
				'id' => '13',
				'short_name' => 'Basketball',
                'name' => 'Basketball',
                'default_name' => 'Basketball',
				'description' => 'Basketball',
                'display_flag' => '0',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			13 => 
			array (
				'id' => '14',
				'short_name' => 'Olympics',
                'name' => 'Olympics',
                'default_name' => 'Olympics',
				'description' => 'Olympics',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			14 => 
			array (
				'id' => '15',
				'short_name' => 'Aussie Rules',
                'name' => 'Aussie Rules',
                'default_name' => 'Aussie Rules',
				'description' => '',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			15 => 
			array (
				'id' => '17',
				'short_name' => 'Womens Basketball',
                'name' => 'Womens Basketball',
                'default_name' => 'Womens Basketball',
				'description' => '',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			16 => 
			array (
				'id' => '19',
				'short_name' => 'Soccer',
                'name' => 'Soccer',
                'default_name' => 'Soccer',
				'description' => '',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			17 => 
			array (
				'id' => '21',
				'short_name' => 'Darts',
                'name' => 'Darts',
                'default_name' => 'Darts',
				'description' => '',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			18 => 
			array (
				'id' => '23',
				'short_name' => 'Boxing',
                'name' => 'Boxing',
                'default_name' => 'Boxing',
				'description' => '',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			19 => 
			array (
				'id' => '25',
				'short_name' => 'Motor Sport',
                'name' => 'Motor Sport',
                'default_name' => 'Motor Sport',
				'description' => '',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			20 => 
			array (
				'id' => '27',
				'short_name' => 'Golf',
                'name' => 'Golf',
                'default_name' => 'Golf',
				'description' => '',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			21 => 
			array (
				'id' => '29',
				'short_name' => 'Ice Hockey',
                'name' => 'Ice Hockey',
                'default_name' => 'Ice Hockey',
				'description' => '',
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			22 => 
			array (
				'id' => '31',
				'short_name' => 'Motorcycling',
                'name' => 'Motorcycling',
                'default_name' => 'Motorcycling',
				'description' => NULL,
                'display_flag' => '0',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			23 => 
			array (
				'id' => '33',
				'short_name' => 'Snooker',
                'name' => 'Snooker',
                'default_name' => 'Snooker',
				'description' => NULL,
                'display_flag' => '0',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
			24 => 
			array (
				'id' => '35',
				'short_name' => 'Australian Rules',
                'name' => 'Australian Rules',
                'default_name' => 'Australian Rules',
				'description' => NULL,
                'display_flag' => '1',
                'icon_id' => 0,
                'default_competition_icon_id' => 0,
                'created_at' => '2015-03-09 13:30:00',
                'updated_at' => '2015-03-09 13:30:00',
			),
		));
	}

}
