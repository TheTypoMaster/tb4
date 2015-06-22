<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_trainers', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('external_trainer_id')->unsigned();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('state')->nullable();
            $table->integer('postcode')->nullable();
            $table->string('initials')->nullable();

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
        Schema::drop('tb_trainers');
    }
}
