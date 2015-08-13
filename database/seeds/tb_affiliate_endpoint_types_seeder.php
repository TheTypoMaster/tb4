<?php

use Illuminate\Database\Seeder;

class tb_affiliate_endpoint_types_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Uncomment the below to wipe the table clean before populating
        DB::table('tb_affiliate_endpoint_types')->truncate();

        $tb_affiliate_endpoint_types = array(

            array (
                'affiliate_endpoint_type_name' => 'tournamententry',
                'affiliate_endpoint_type_descritpion' => 'Endpoint for tournament entry request',
            ),
            array (
                'affiliate_endpoint_type_name' => 'tournamentresults',
                'affiliate_endpoint_type_descritpion' => 'Endpoint for tournament results',
            ),

        );

        // Uncomment the below to run the seeder
        DB::table('tb_affiliate_endpoint_types')->insert($tb_affiliate_endpoint_types);
    }
}
