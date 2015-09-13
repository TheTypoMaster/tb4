<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateEndpointTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_affiliate_endpoint_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('affiliate_endpoint_type_name');
            $table->text('affiliate_endpoint_type_description');
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
        Schema::drop('tb_affiliate_endpoint_types');
    }
}
