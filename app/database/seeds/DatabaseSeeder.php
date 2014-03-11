<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		$this->call('UserSeeder');
		$this->call('DefaultGroupSeeder');
		$this->call('LearngroupSeeder');
		$this->call('QuestiontypeClozeSeeder');
		$this->call('QuestiontypeImageSeeder');
		$this->call('QuestiontypeMatchSeeder');
		$this->call('QuestiontypeMultipleSeeder');
		$this->call('QuestiontypeSimpleSeeder');
		$this->call('CourseSeeder');
		$this->call('FavoriteSeeder');

	}

}