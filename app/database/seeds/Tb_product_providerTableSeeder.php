<?php

class Tb_product_providerTableSeeder extends Seeder {

    public function run()
    {
    	// wipe the table clean before populating
    	DB::table('tb_product_provider')->truncate();

        $tb_product_provider = ( array (
        		array (
        				'id' => '1',
        				'provider_name' => 'igas',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		)
         ) );
        
         // run the seeder
        DB::table('tb_product_provider')->insert($tb_product_provider);
    }

}