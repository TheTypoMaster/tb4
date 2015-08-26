<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiKeyToAffiliate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_affiliates', function (Blueprint $table) {
            $table->string('affiliate_api_key')->after('affiliate_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_affiliates', function (Blueprint $table) {
            $table->dropColumn('affiliate_api_key');
        });
    }
}
