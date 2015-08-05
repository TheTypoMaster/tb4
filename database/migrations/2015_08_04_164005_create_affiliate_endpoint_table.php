<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateEndpointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_affiliate_endpoints', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('affiliate_id')->unsigned();
            $table->text('affiliate_api_endpoint');
            $table->string('affiliate_endpoint_username');
            $table->string('affiliate_endpoint_password');

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
        Schema::drop('tb_affiliate_endpoints');
    }
}
