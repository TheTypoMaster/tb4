<?php

class Tbdb_components_add_tournament_grouping_recordsTableSeeder extends Seeder {

    public function run()
    {
    	// Uncomment the below to wipe the table clean before populating
    	// DB::table('tbdb_components_add_tournament_grouping_records')->delete();

        $tbdb_components_add_tournament_grouping_records = array(

            array (
                'id' => '101',
                'name' => 'Tournament Labels',
                'link' => '',
                'menuid' => '0',
                'parent' => '68',
                'admin_menu_link' => 'option=com_tournament&controller=tournamentlabels',
                'admin_menu_alt' => 'Tournament Label Management',
                'option' => 'com_tournament',
                'ordering' => '0',
                'admin_menu_img' => 'js/ThemeOffice/component.png',
                'iscore' => '0',
                'params' => '',
                'enabled' => '1'
            ),
            array (
                'id' => '102',
                'name' => 'Tournament Label Groups',
                'link' => '',
                'menuid' => '0',
                'parent' => '68',
                'admin_menu_link' => 'option=com_tournament&controller=tournamentgroups',
                'admin_menu_alt' => 'Tournament Label Group Management',
                'option' => 'com_tournament',
                'ordering' => '0',
                'admin_menu_img' => 'js/ThemeOffice/component.png',
                'iscore' => '0',
                'params' => '',
                'enabled' => '1'
            )

        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_components')->insert($tbdb_components_add_tournament_grouping_records);
    }

}