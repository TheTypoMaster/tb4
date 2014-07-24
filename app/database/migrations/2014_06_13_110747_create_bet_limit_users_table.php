<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBetLimitUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbdb_bet_limit_users', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
			$table->integer('bet_limit_type_id')->index();
			$table->integer('amount');
			$table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbdb_bet_limit_users');
    }

}
