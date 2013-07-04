<?php

class Tb_api_usersTableSeeder extends Seeder {

    public function run()
    {
    	// wipe the table clean before populating
    	DB::table('tb_api_users')->delete();
    	
        $tb_api_users = array(
        	'username' => 'test_api_user',
        	'password' => Hash::make('p@ssw0rd!'),
        	'created_at' => new DateTime,
        	'updated_at' => new DateTime

        );

        // run the seeder
        DB::table('tb_api_users')->insert($tb_api_users);
    }

}