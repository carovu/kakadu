<?php

class DefaultGroupSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('groups')->delete();

		Sentry::getGroupProvider()->create(array(
	        'name'        => 'admin',
	        'permissions' => array(
	            'admin' => 1,
	        )));
	}

}  