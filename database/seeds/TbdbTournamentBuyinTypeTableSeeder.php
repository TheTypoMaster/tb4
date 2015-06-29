<?php

use Illuminate\Database\Seeder;

class TbdbTournamentBuyinTypeTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('tbdb_tournament_buyin_type')->truncate();
        
		\DB::table('tbdb_tournament_buyin_type')->insert(array (
			0 => 
			array (
				'id' => '1',
				'keyword' => 'buyin',
				'name' => 'Buy in',
			),
			1 => 
			array (
				'id' => '2',
				'keyword' => 'rebuy',
				'name' => 'Rebuy',
			),
			2 => 
			array (
				'id' => '3',
				'keyword' => 'topup',
				'name' => 'Top Up',
			),
		));
	}

}
