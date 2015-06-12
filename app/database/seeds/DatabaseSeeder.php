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
		
		$this->call('Tbdb_bet_result_statusTableSeeder');
		$this->call('Tbdb_components_add_tournament_grouping_recordsTableSeeder');
		$this->call('Tbdb_bet_limit_typesTableSeeder');
		$this->call('tb_bet_sourceTableSeeder');
		$this->call('TbDataValuesTableSeeder');
		$this->call('TbDataProviderMatchTableSeeder');
		$this->call('TbProcessParamsTableSeeder');
		$this->call('TbdbAccountTransactionTypeTableSeeder');
		$this->call('Tbdb_bet_limit_add_sports_limitTableSeeder');
		$this->call('TbConfigurationTableSeeder');
		$this->call('TbdbTournamentBuyinTypeTableSeeder');
		$this->call('TbdbAccountTransactionTypeRebuyFieldsTableSeeder');
		$this->call('TbdbTournamentTransactionTypeRebuyFieldsTableSeeder');
		$this->call('TbdbBetSourceUpdateTableSeeder');
		$this->call('TbBetSourceUpdateTableSeeder');
	}

}