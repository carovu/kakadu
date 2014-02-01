<?php

class UserSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		//deletes created test data - by first db:seed you must comment it out
		//$user_sentry = Sentry::findUserByLogin('alex@example.com');
       // User::find($user_sentry->getId())->delete();
       //	$user_sentry = Sentry::findUserByLogin('georg@example.com');
      // 	User::find($user_sentry->getId())->delete();
		//$user_sentry = Sentry::findUserByLogin('a@example.com');
	 //   User::find($user_sentry->getId())->delete();

        //create test data
      // Create the user
		$user = Sentry::createUser(array(
			'email'     => 'alex@example.com',
			'password'  => 'password',
			'permissions' => array('admin' => 1),
            'activated' => true
			));
		DB::table('users_metadata')->insert(array(
			'user_id'		=> $user->getId(),
			'displayname'   => 'Alex',
			'language'      => 'en'
			));
		$group = Sentry::findGroupByName('admin');
	    $user->addGroup($group);

		$user = Sentry::createUser(array(
			'email'     => 'georg@example.com',
			'password'  => 'password1',
			'permissions' => array('admin' => 0),
            'activated' => true
			));
		DB::table('users_metadata')->insert(array(
			'user_id'		=> $user->getId(),
			'displayname'   => 'Georg',
			'language'      => 'en'
			));
		$group = Sentry::findGroupByName('admin');
	    $user->addGroup($group);
	    
		$user = Sentry::createUser(array(
			'email'     => 'a@example.com',
			'password'  => 'password1',
			'permissions' => array('admin' => 1),
            'activated' => true
			));
		DB::table('users_metadata')->insert(array(
			'user_id'		=> $user->getId(),
			'displayname'   => 'A',
			'language'      => 'en'
			));
		$group = Sentry::findGroupByName('admin');
	    $user->addGroup($group);
	}

}                