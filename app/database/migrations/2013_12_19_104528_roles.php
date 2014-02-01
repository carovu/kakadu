<?php

use Illuminate\Database\Migrations\Migration;

class Roles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{	
		$role1 = new Role();
        $role1->name = 'member';
        $role1->description = 'Member';
        $role1->save();

        $role2 = new Role();
        $role2->name = 'admin';
        $role2->description = 'Administrator';
        $role2->save();
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::delete('DELETE FROM roles WHERE name LIKE "admin"');
        DB::delete('DELETE FROM roles WHERE name LIKE "member"');
	}

}