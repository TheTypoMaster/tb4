<?php

class Tb_product_defaultTableSeeder extends Seeder {

    public function run()
    {
    	// wipe the table clean before populating
    	DB::table('tb_product_default')->truncate();
    	
		$tb_product_default = ( array (
				/*
				 * Australia - Gallops
				 */
				
				// METRO
				array (
						'tb_product_id' => '7',
						'bet_type' => 'W',
						'country' => 'AU',
						'region' => 'METRO',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '5',
						'bet_type' => 'P',
						'country' => 'AU',
						'region' => 'METRO',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'Q',
						'country' => 'AU',
						'region' => 'METRO',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'E',
						'country' => 'AU',
						'region' => 'METRO',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'T',
						'country' => 'AU',
						'region' => 'METRO',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'FF',
						'country' => 'AU',
						'region' => 'METRO',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				
				// PROVINCIAL
				array (
						'tb_product_id' => '7',
						'bet_type' => 'W',
						'country' => 'AU',
						'region' => 'PROVINCIAL',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '6',
						'bet_type' => 'P',
						'country' => 'AU',
						'region' => 'PROVINCIAL',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'Q',
						'country' => 'AU',
						'region' => 'PROVINCIAL',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'E',
						'country' => 'AU',
						'region' => 'PROVINCIAL',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'T',
						'country' => 'AU',
						'region' => 'PROVINCIAL',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'FF',
						'country' => 'AU',
						'region' => 'PROVINCIAL',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				
				
				// COUNTRY
				array (
						'tb_product_id' => '7',
						'bet_type' => 'W',
						'country' => 'AU',
						'region' => 'COUNTRY',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '6',
						'bet_type' => 'P',
						'country' => 'AU',
						'region' => 'COUNTRY',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'Q',
						'country' => 'AU',
						'region' => 'COUNTRY',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'E',
						'country' => 'AU',
						'region' => 'COUNTRY',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'T',
						'country' => 'AU',
						'region' => 'COUNTRY',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'FF',
						'country' => 'AU',
						'region' => 'COUNTRY',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				
				// OTHER - Racing - No Region
				array (
						'tb_product_id' => '6',
						'bet_type' => 'W',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '6',
						'bet_type' => 'P',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'Q',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'E',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'T',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'FF',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
					
				/*
				 * Australia - Dogs
				 */
				// Win/Place
				array (
						'tb_product_id' => '6',
						'bet_type' => 'W',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '6',
						'bet_type' => 'P',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				// Exotics - Dogs
				array (
						'tb_product_id' => '4',
						'bet_type' => 'Q',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'E',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'T',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'FF',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				
				
				/*
				 * Australia - Harness
				 */
				// Win / Place
				array (
						'tb_product_id' => '6',
						'bet_type' => 'W',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
        				'updated_at' => new DateTime
						
				),
				array (
						'tb_product_id' => '6',
						'bet_type' => 'P',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				
				// Exotics
				array (
						'tb_product_id' => '4',
						'bet_type' => 'Q',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'E',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'T',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'FF',
						'country' => 'AU',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				
				
				/*
				 * New Zealand
				 */
				
				// Win and Place - Racing
				array (
						'tb_product_id' => '6',
						'bet_type' => 'W',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '6',
						'bet_type' => 'P',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				// Win and Place - Harness
				array (
						'tb_product_id' => '6',
						'bet_type' => 'W',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '6',
						'bet_type' => 'P',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				// Win and Place - Dogs
				array (
						'tb_product_id' => '6',
						'bet_type' => 'W',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '6',
						'bet_type' => 'P',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				
				// Exotics - Racing
				array (
						'tb_product_id' => '4',
						'bet_type' => 'Q',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'E',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'T',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'FF',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'R',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				// Exotics - Harness
				array (
						'tb_product_id' => '4',
						'bet_type' => 'Q',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'E',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'T',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'FF',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'H',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				// Exotics - Dogs
				array (
						'tb_product_id' => '4',
						'bet_type' => 'Q',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'E',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'T',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				),
				array (
						'tb_product_id' => '4',
						'bet_type' => 'FF',
						'country' => 'NZ',
						'region' => '',
						'type_code' => 'G',
						'created_at' => new DateTime,
						'updated_at' => new DateTime
				)
				
				
		) );
    	

        // run the seeder
        DB::table('tb_product_default')->insert($tb_product_default);
    }

}