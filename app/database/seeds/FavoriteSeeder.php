<?php

class FavoriteSeeder extends Seeder {


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        DB::table('favorites')->delete();
        $user = Sentry::findUserByLogin('alex@example.com');
        $course1 = Catalog::find(1);
        $course2 = Catalog::find(2);
        $course3 = Catalog::find(3);
        $course4 = Catalog::find(4);
        $course5 = Catalog::find(5);
        $course6 = Catalog::find(6);

        DB::table('favorites')->insert(array('catalog_id' => $course1->id, 'user_id' => $user->getId()));
        DB::table('favorites')->insert(array('catalog_id' => $course2->id, 'user_id' => $user->getId()));
        DB::table('favorites')->insert(array('catalog_id' => $course3->id, 'user_id' => $user->getId()));
        DB::table('favorites')->insert(array('catalog_id' => $course4->id, 'user_id' => $user->getId()));
        DB::table('favorites')->insert(array('catalog_id' => $course5->id, 'user_id' => $user->getId()));
        DB::table('favorites')->insert(array('catalog_id' => $course6->id, 'user_id' => $user->getId()));


    }

}  