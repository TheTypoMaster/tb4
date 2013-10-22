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
		$this->call('Tb_data_providerTableSeeder');
		$this->call('Tb_data_provider_matchTableSeeder');
		$this->call('Tb_data_valuesTableSeeder');
		$this->call('Tb_data_typesTableSeeder');
		$this->call('Tb_data_ordering_orderTableSeeder');
		$this->call('Tb_data_ordering_provider_matchTableSeeder');
		$this->call('Tbdb_selection_status_addSuspendedRecordTableSeeder');
		$this->call('Tbdb_event_status_AddDeleteRecordEventStatusTableSeeder');
		
	}

}