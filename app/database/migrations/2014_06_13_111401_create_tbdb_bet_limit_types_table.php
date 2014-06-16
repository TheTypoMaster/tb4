<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbdbBetLimitTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbdb_bet_limit_types', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
			$table->string('value');
			$table->integer('default_amount');
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
        Schema::drop('tbdb_bet_limit_types');
    }

}
