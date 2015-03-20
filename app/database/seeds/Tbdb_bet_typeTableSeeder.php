<?php

class Tbdb_bet_typeTableSeeder extends Seeder
{

    public function run()
    {


        $tbdb_bet_type = array(

            array(
                "id"           => 8,
                "name"         => "two_leg_multi",
                "description"  => "2 Leg Multi Combo",
                "status_flag"  => 1,
                "created_date" => DB::raw("NOW()"),
                "updated_date" => DB::raw("NOW()")
            ),

            array(
                "id"           => 9,
                "name"         => "three_leg_multi",
                "description"  => "3 Leg Multi Combo",
                "status_flag"  => 1,
                "created_date" => DB::raw("NOW()"),
                "updated_date" => DB::raw("NOW()")
            ),

            array(
                "id"           => 10,
                "name"         => "four_leg_multi",
                "description"  => "4 Leg Multi Combo",
                "status_flag"  => 1,
                "created_date" => DB::raw("NOW()"),
                "updated_date" => DB::raw("NOW()")
            ),

            array(
                "id"           => 11,
                "name"         => "five_leg_multi",
                "description"  => "5 Leg Multi Combo",
                "status_flag"  => 1,
                "created_date" => DB::raw("NOW()"),
                "updated_date" => DB::raw("NOW()")
            ),

            array(
                "id"           => 12,
                "name"         => "six_leg_multi",
                "description"  => "6 Leg Multi Combo",
                "status_flag"  => 1,
                "created_date" => DB::raw("NOW()"),
                "updated_date" => DB::raw("NOW()")
            ),

            array(
                "id"           => 13,
                "name"         => "seven_leg_multi",
                "description"  => "7 Leg Multi Combo",
                "status_flag"  => 1,
                "created_date" => DB::raw("NOW()"),
                "updated_date" => DB::raw("NOW()")
            ),

            array(
                "id"           => 14,
                "name"         => "eight_leg_multi",
                "description"  => "8 Leg Multi Combo",
                "status_flag"  => 1,
                "created_date" => DB::raw("NOW()"),
                "updated_date" => DB::raw("NOW()")
            ),

            array(
                "id"           => 15,
                "name"         => "nine_leg_multi",
                "description"  => "9 Leg Multi Combo",
                "status_flag"  => 1,
                "created_date" => DB::raw("NOW()"),
                "updated_date" => DB::raw("NOW()")
            ),

            array(
                "id"           => 16,
                "name"         => "ten_leg_multi",
                "description"  => "10 Leg Multi Combo",
                "status_flag"  => 1,
                "created_date" => DB::raw("NOW()"),
                "updated_date" => DB::raw("NOW()")
            ),
        );

        DB::table('tbdb_bet_type')->insert($tbdb_bet_type);
    }

}
