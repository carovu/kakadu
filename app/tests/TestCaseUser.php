<?php

require_once 'TestCaseController.php';

abstract class TestCaseUser extends TestCaseController
{

    public function setUp() {
        parent::setUp();
        //create test data
        $user = Sentry::createUser(array(
            'email'     => 'alex@example.com',
            'password'  => 'password',
            'permissions' => array('admin' => 1),
            'activated' => true
            ));
        DB::table('users_metadata')->insert(array(
            'user_id'       => $user->getId(),
            'displayname'   => 'Alex',
            'language'      => 'en'
            ));
        $user = Sentry::createUser(array(
            'email'     => 'georg@example.com',
            'password'  => 'password1',
            'permissions' => array('admin' => 0),
            'activated' => true
            ));
        DB::table('users_metadata')->insert(array(
            'user_id'       => $user->getId(),
            'displayname'   => 'Georg',        
            'language'      => 'en'
            ));
    }


    public function tearDown() {
        parent::tearDown();
        $user_sentry = Sentry::findUserByLogin('alex@example.com');
        DB::table('users_metadata')->where('user_id', $user_sentry->getId())->delete(); 
        User::find($user_sentry->getId())->delete();
        $user_sentry = Sentry::findUserByLogin('georg@example.com');
        DB::table('users_metadata')->where('user_id', $user_sentry->getId())->delete(); 
        User::find($user_sentry->getId())->delete();
    }

}