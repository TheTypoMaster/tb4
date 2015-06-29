<?php

use Illuminate\Database\Seeder;

class tb_bet_sourceTableSeeder extends Seeder {

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
				'api_username' => NULL,
				'api_password' => NULL,
				'shared_secret' => NULL,
				'created_at' => '2015-01-20 08:43:10',
				'updated_at' => '2015-01-20 08:43:10',
			),
			1 => 
			array (
				'id' => '2',
				'keyword' => 'fastbet',
				'description' => 'TopBetta Fast Bet App',
				'api_endpoint' => NULL,
				'api_username' => NULL,
				'api_password' => NULL,
				'shared_secret' => NULL,
				'created_at' => '2015-01-20 08:43:29',
				'updated_at' => '2015-01-20 08:43:29',
			),
			2 => 
			array (
				'id' => '3',
				'keyword' => 'puntersclub',
				'description' => 'TopBetta Punters Club Application',
				'api_endpoint' => 'http://puntersclub.mugbookie.com/api/v1/betnotification',
				'api_username' => 'topbetta',
				'api_password' => 't0pb3tt@',
				'shared_secret' => 's3cr3tp@ss',
				'created_at' => '2015-01-20 08:43:54',
				'updated_at' => '2015-01-20 08:43:54',
			),
		));
	}

}
