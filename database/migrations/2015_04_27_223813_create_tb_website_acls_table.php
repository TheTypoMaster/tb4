<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTbWebsiteAclsTable extends Migration {

	public function up()
	{
		Schema::create('tb_website_acls', function(Blueprint $table) {
			$table->increments('acl_id');
			$table->string('acl_filter');
			$table->string('acl_code');
			$table->string('acl_description');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tb_website_acls');
	}
}