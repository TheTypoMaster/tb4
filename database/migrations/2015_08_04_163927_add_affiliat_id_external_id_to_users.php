<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAffiliatIdExternalIdToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbdb_users', function (Blueprint $table) {
            $table->integer('affiliate_id')->unsigned()->after('permissions')->nullable();
            $table->string('external_user_id')->after('affiliate_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbdb_users', function (Blueprint $table) {
            $table->dropColumn('affiliate_id');
            $table->dropColumn('external_user_id');
        });
    }
}
