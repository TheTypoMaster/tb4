<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAffiliateTypeRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_affiliate_types', function (Blueprint $table) {
            $table->renameColumn('affilaite_type_id', 'affiliate_type_id');
            $table->text('affiliate_user_rules')->nullable()->after('affiliate_type_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_affiliate_types', function (Blueprint $table) {
            $table->dropColumn('affiliate_user_rules');
        });
    }
}
