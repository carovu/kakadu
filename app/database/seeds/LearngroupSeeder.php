<?php

class LearngroupSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{	
        Learngroup::where('name', 'LIKE', 'Group x%')->delete();

		$group1 = new Learngroup;
        $group1->name = 'Group xy';
        $group1->description = 'Group xy';
        $group1->save();

        $group2 = new Learngroup;
        $group2->name = 'Group xz';
        $group2->description = 'Group xz';
        $group2->save();
	}

}  