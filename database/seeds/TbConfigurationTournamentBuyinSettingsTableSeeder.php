<?php

use Illuminate\Database\Seeder;

class TbConfigurationTournamentBuyinSettingsTableSeeder extends Seeder {

	public function run()
	{

		$tbconfigurationtournamentbuyinsettings = array(
            array(
                "name" => "tournament_buyin_settings",
                "values" => '{"max_free_tournament":{"period":"month","number":"3"}}',
                "created_at" => DB::raw("NOW()"),
                "updated_at" => DB::raw("NOW()"),
            )
		);

		 DB::table('tb_configuration')->insert($tbconfigurationtournamentbuyinsettings);
	}

}
