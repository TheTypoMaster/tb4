<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');
		$this->call('Tb_api_usersTableSeeder');
		$this->call('Tb_product_defaultTableSeeder');
		$this->call('Tb_product_provider_matchTableSeeder');
		$this->call('Tb_product_providerTableSeeder');
	}

}