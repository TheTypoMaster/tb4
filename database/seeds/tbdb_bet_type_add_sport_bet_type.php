<?php

use Illuminate\Database\Seeder;

class tbdb_bet_type_add_sport_bet_type extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tbdb_bet_type_add_sport_bet_type = array(
            array(
                "id"           => 17,
                "name"         => "sport",
                "description"  => "Sports",
                "status_flag"  => "1",
                "created_date" => \Carbon\Carbon::now(),
                "updated_date"  => \Carbon\Carbon::now(),
            )
        );

        // Uncomment the below to run the seeder
        DB::table('tbdb_bet_type')->insert($tbdb_bet_type_add_sport_bet_type);

        $tbdb_bet_limit_type_update_sport_limit = array(
            "value" => 17,
        );

        DB::table('tbdb_bet_limit_types')->where('id', 15)->update($tbdb_bet_limit_type_update_sport_limit);
    }
}
