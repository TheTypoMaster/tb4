<?php

use Illuminate\Database\Seeder;

class Tbdb_user_create_super_userTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('tbdb_user_create_super_user')->truncate();

		$tbdb_user_create_super_user = array(
           "permissions" => json_encode(array("superuser" => 1)),
		);

		// Uncomment the below to run the seeder
		DB::table('tbdb_users')->where('id', 64)->update($tbdb_user_create_super_user);
	}

}
