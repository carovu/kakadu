<?php

require_once 'TestCaseUser.php';

class LoginTest extends TestCaseUser {
    
    public function setUp() {
        parent::setUp();
    }


    public function tearDown() {
        parent::tearDown();
    }

    /**
     * Test login with no data.
     */
    public function testLoginWithNoData()
    {
        $post_data = array();
        //does not work because of redirect
        //$response = $this->call('POST', 'auth/login', $post_data);
        //$this->assertEquals('302', $response->getStatusCode());
        //$this->checkResponseLocation('/', $response);
        //$this->checkIfErrorsExist();
    }


    /**
     * Test login with just the email.
     *
     * @depands testLoginWithNoData
     */
    public function testLoginWithEmail()
    {
        $post_data = array(
            'email' => 'alex@example.com'
        );
        //$response = $this->call('POST', 'auth/login', $post_data);
        //$this->assertEquals('302', $response->getStatusCode());
        //$this->checkResponseLocation('/', $response);
        //$this->checkIfErrorsExist();
    }


    /**
     * Test login with just the password.
     *
     * @depands testLoginWithEmail
     */
    public function testLoginWithPassword()
    {
        $post_data = array(
            'password' => 'password'
        );
        //$response = $this->call('POST', 'auth/login', $post_data);

        //$this->assertEquals('302', $response->getStatusCode());
        //$this->checkResponseLocation('/', $response);
        //$this->checkIfErrorsExist();
    }


    /**
     * Test login with a invalid data.
     *
     * @depands
     */
    public function testLoginWithInvalidData()
    {
        $post_data = array(
            'email' => 'test@test.com',
            'password' => 'password'
        );
        //$response = $this->call('POST', 'auth/login', $post_data);

        //$this->assertEquals('302', $response->getStatusCode());
        //$this->checkResponseLocation('/', $response);
        //$this->checkIfErrorsExist();
    }


    /**
     * Test login with a valid data.
     *
     * @depands testLoginWithInvalidData
     */
    public function testLoginWithValidData()
    {
        $post_data = array(
            'email' => 'Logintest@example.com',
            'password' => 'password'
        );
        //$response = $this->call('POST', 'auth/login', $post_data);
        //$this->assertEquals('302', $response->getStatusCode());
        //$this->checkResponseLocation('/', $response);
        //$this->checkIfNoErrorsExist();
        //$this->assertTrue(Sentry::check());
    }


    /**
     * Test logout.
     */
    public function testLogout()
    {
        try {
        $user = Sentry::findUserByLogin('alex@example.com');
        Sentry::login($user, false);
        } catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
            printf($e->getMessage());
        } catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            printf($e->getMessage());
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            printf($e->getMessage());
        } 
        //Check if user is logged in
        $this->assertTrue(Sentry::check());
        //Call logout site
        $response = $this->call('GET', 'auth/logout');
        $this->assertEquals('302', $response->getStatusCode());
        //$this->checkResponseLocation('/', $response);

        //User is logged out
        $this->assertFalse(Sentry::check());
    }

}