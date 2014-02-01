<?php

use Illuminate\Database\Migrations\Migration;

class DefaultGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{		
		Sentry::getGroupProvider()->create(array(
	        'name'        => 'admin',
	        'permissions' => array(
	            'admin' => 1,
	        )));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('groups')->delete();
	}

}