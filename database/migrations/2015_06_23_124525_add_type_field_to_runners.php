<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeFieldToRunners extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_runners', function (Blueprint $table) {
            $table->string('type')->after('dam');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_runners', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
