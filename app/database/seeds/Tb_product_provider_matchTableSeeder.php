<?php

class Tb_product_provider_matchTableSeeder extends Seeder {

    public function run()
    {
    	// wipe the table clean before populating
    	DB::table('tb_product_provider_match')->delete();

        $tb_product_provider_match = ( array (
        		array (
        				'tb_product_id' => '7',
        				'provider_id' => '1',
        				'provider_product_name' => 'BO4',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'tb_product_id' => '6',
        				'provider_id' => '1',
        				'provider_product_name' => 'MID',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'tb_product_id' => '4',
        				'provider_id' => '1',
        				'provider_product_name' => 'SUP',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		),
        		array (
        				'tb_product_id' => '5',
        				'provider_id' => '1',
        				'provider_product_name' => 'TOP',
        				'created_at' => new DateTime,
        				'updated_at' => new DateTime
        		)
        ) );

        // run the seeder
        DB::table('tb_product_provider_match')->insert($tb_product_provider_match);
    }

}