<?php

class TbBetSourceUpdateTableSeeder extends Seeder {

	public function run()
	{
        $tbdbbetsourceupdate = array(
            "api_endpoint" => '{"bet_endpoint":"http:\/\/puntersclubapi.dev\/api\/v1\/externalbets","deposit_endpoint":"http:\/\/puntersclubapi.dev\/api\/v1\/payments"}'
        );

        DB::table('tb_bet_source')
            ->where('keyword', 'puntersclub')
            ->update($tbdbbetsourceupdate);
	}

}
