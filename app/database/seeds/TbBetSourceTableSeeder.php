<?php

class TbBetSourceTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('tb_bet_source')->truncate();
        
		\DB::table('tb_bet_source')->insert(array (
			0 => 
			array (
				'id' => '1',
				'keyword' => 'topbetta',
				'description' => 'TopBetta Website',
				'api_endpoint' => NULL,
				'shared_secret' => NULL,
				'created_at' => '2015-01-16 10:17:59',
				'updated_at' => '2015-01-16 10:17:59',
			),
			1 => 
			array (
				'id' => '2',
				'keyword' => 'fb-topbetta',
				'description' => 'TopBetta FastBet App',
				'api_endpoint' => NULL,
				'shared_secret' => NULL,
				'created_at' => '2015-01-16 10:18:19',
				'updated_at' => '2015-01-16 10:18:19',
			),
			2 => 
			array (
				'id' => '3',
				'keyword' => 'puntersclub',
				'description' => 'Punters Club App',
				'api_endpoint' => NULL,
				'shared_secret' => 's3cr3tp@ss',
				'created_at' => '2015-01-16 10:19:01',
				'updated_at' => '2015-01-16 10:19:01',
			),
		));
	}

}
