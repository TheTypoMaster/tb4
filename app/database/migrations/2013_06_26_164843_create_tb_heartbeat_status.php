<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbHeartbeatStatus extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_heartbeat_status', function(Blueprint $table) {
            $table->increments('id');
            $table->string('heartbeat_endpoint');
            $table->string('last_status');
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
        Schema::drop('tb_heartbeat_status');
    }

}
