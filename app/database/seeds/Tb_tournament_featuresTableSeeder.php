<?php

class Tb_tournament_featuresTableSeeder extends Seeder {
	
	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		//DB::table('tb_tournament_features')->delete();

		$tb_tournament_features = ( array (
				array (
						'keyword' => 'atp2013',
						'description' => '2013 ATP Tournaments',
				),


		) );

		// Uncomment the below to run the seeder
		//DB::table('tb_tournament_features')->insert($tb_tournament_features);
	}

}