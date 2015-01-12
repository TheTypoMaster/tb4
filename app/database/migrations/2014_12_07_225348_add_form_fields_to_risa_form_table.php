<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormFieldsToRisaFormTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('tb_data_risa_runner_form', function(Blueprint $table) {
            $table->string('firm_results')->after('good_results')->nullable();
            $table->string('soft_results')->nullable();
            $table->string('synthetic_results')->nullable();
            $table->string('wet_results')->nullable();
            $table->string('nonwet_results')->nullable();
            $table->string('night_results')->nullable();
            $table->string('jumps_results')->nullable();
            $table->string('season_results')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('tb_data_risa_runner_form', function(Blueprint $table) {
            $table->dropColumn('firm_results');
            $table->dropColumn('soft_results');
            $table->dropColumn('synthetic_results');
            $table->dropColumn('wet_results');
            $table->dropColumn('nonwet_results');
            $table->dropColumn('night_results');
            $table->dropColumn('jumps_results');
            $table->dropColumn('season_results');
        });
	}

}
