<?php

class TbProcessParamsTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('tb_process_params')->truncate();
        
		\DB::table('tb_process_params')->insert(array (
			0 => 
			array (
				'id' => '1',
				'process_name' => 'remove_free_credit_from_dormant_account',
				'process_params' => '{"last_run_date":"2014-06-06 00:00:00","last_run_days":60}',
				'is_running_flag' => '1',
				'created_at' => '2015-02-12 12:30:04',
				'updated_at' => '2015-02-17 10:32:59',
			),
		));
	}

}
