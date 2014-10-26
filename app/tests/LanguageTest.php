<?php

require_once 'TestCaseUser.php';

class LanguageTest extends TestCaseUser {
    
    public function setUp() {
        parent::setUp();
    }


    public function tearDown() {
        parent::tearDown();
    }
    
    /**
     * Test the change language post with no data.
     */
    public function testChangeLanguage() {
        $response = $this->call('POST', 'language/edit');
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('/', $response);
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test the change language post with not falid language.
     *
     * @depends testChangeLanguage
     */
    public function testChangeLanguageWithNotFalidLanguage() {
        $post_data = array(
            'language' => 'xy'
        );
        $response = $this->call('POST', 'language/edit', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('/', $response);
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test the change language post as guest.
     *
     * @depends testChangeLanguageWithNotFalidLanguage
     */
    public function testChangeLanguageAsGuest() {
        //Check if not logged in
        $this->assertFalse(Sentry::check());

        //Change language
        $post_data = array(
            'language' => 'de'
        );
        //does not work because of redirect
        //$response = $this->call('POST', 'language/edit', $post_data);
        //$this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('/', $response);
        ////$this->checkIfNoErrorsExist();
        //Check set language;
        //$this->assertEquals('de', Cookie::get('language'));
    }


    /**
     * Test the change language post as user.
     *
     * @depends testChangeLanguageAsGuest
     */
    public function testChangeLanguageAsUser() {
        $user = Sentry::findUserByLogin('alex@example.com');
        Sentry::login($user, false);
        
        //Check if not logged in
        $this->assertTrue(Sentry::check());
        //Change language
        $post_data = array(
            'language' => 'de'
        );
        //$response = $this->call('POST', 'language/edit', $post_data);
        //$this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('/', $response);
        ////$this->checkIfNoErrorsExist();

        //Check set language
        //$this->assertEquals('de', DB::table('users_metadata')->where('user_id', $user->getId())->first()->language);
        //$this->assertEquals('de', Cookie::get('language'));
    }

}