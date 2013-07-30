<?php

class Tb_data_providerTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	DB::table('tb_data_provider')->truncate();

        $tb_data_provider = ( array (
        		array (
        				'id' => '1',
        				'provider_name' => 'igas',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		)
        ) );
        
        // Uncomment the below to run the seeder
        DB::table('tb_data_provider')->insert($tb_data_provider);
    }

}