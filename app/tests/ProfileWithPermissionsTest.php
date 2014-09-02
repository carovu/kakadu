<?php

require_once 'TestCaseUser.php';

class ProfileWithPermissionsTest extends TestCaseUser {

    public function setUp() {
        parent::setUp();
        $user = Sentry::findUserByLogin('georg@example.com');
        Sentry::login($user, false);
    }

    public function tearDown() {
        parent::tearDown();
    }
    
    /**
     * Test the profile edit view.
     */
    public function testProfileEditView() {
        $response = $this->call('GET', 'profile/edit');
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test profile edit with no data.
     *
     * @depends testProfileEditView
     */
    public function testProfileEditWithNoData() {
        $response = $this->call('POST', 'profile/edit');
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('profile/edit', $response);
        ////$this->checkIfErrorsExist();
    }

    /**
     * Test profile edit with no data.
     *
     * @depends testProfileEditView
     */
    public function testProfileEditWithNoDataJSON() {
        $response = $this->call('POST', 'api/spa/profile/edit');
        $this->assertEquals('401', $response->getStatusCode());
    }

    /**
     * Test profile edit with not valid email.
     *
     * @depends testProfileEditWithNoData
     */
    public function testProfileEditWithNotValidEmail() {
        $post_data = array(
            'displayname'   => 'Alex',
            'email'         => 'email',
            'language'      => 'en'
        );
        $response = $this->call('POST', 'profile/edit', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('profile/edit', $response);
        ////$this->checkIfErrorsExist();
    }

    /**
     * Test profile edit with not valid email.
     *
     * @depends testProfileEditWithNoData
     */
    public function testProfileEditWithNotValidEmailJSON() {
        $post_data = array(
            'displayname'   => 'Alex',
            'email'         => 'email',
            'language'      => 'en'
        );
        $response = $this->call('POST', 'api/spa/profile/edit', $post_data);
        $this->assertEquals('401', $response->getStatusCode());
    }
    /**
     * Test profile edit with existing email.
     *
     * @depends testProfileEditWithNoData
     */
    public function testProfileEditWithExistingEmail() {
        $post_data = array(
            'displayname'   => 'Alex',
            'email'         => 'georg@example.com',
            'language'      => 'en'
        );
        $response = $this->call('POST', 'profile/edit', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('profile/edit', $response);
        ////$this->checkIfErrorsExist();
    }

    /**
     * Test profile edit with existing email.
     *
     * @depends testProfileEditWithNoData
     */
    public function testProfileEditWithExistingEmailJSON() {
        $post_data = array(
            'displayname'   => 'Alex',
            'email'         => 'georg@example.com',
            'language'      => 'en'
        );
        $response = $this->call('POST', 'api/spa/profile/edit', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertEquals('"Change of user information successful."', $content);
    }

    /**
     * Test profile edit with valid data.
     *
     * @depends testProfileEditWithExistingEmail
     */
    public function testProfileEditWithValidData() {
        $post_data = array(
            'displayname'   => 'Georg',
            'email'         => 'georg@example.com',
            'language'      => 'de'
        );
        $response = $this->call('POST', 'profile/edit', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('profile/edit', $response);
        ////$this->checkIfNoErrorsExist();
    }

    /**
     * Test profile edit with valid data.
     *
     * @depends testProfileEditWithExistingEmail
     */
    public function testProfileEditWithValidDataJSON() {
        $post_data = array(
            'displayname'   => 'Georg',
            'email'         => 'georg@example.com',
            'language'      => 'de'
        );
        $response = $this->call('POST', 'api/spa/profile/edit', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertEquals('"Change of user information successful."', $content);
    }

    /**
     * Test profile change password with no data.
     *
     * @depends testProfileEditWithValidData
     */
    public function testProfileChangePasswordWithNoData() {
        $response = $this->call('POST', 'profile/changepassword');
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('profile/edit', $response);
        ////$this->checkIfErrorsExist();
    }

    /**
     * Test profile change password with no data.
     *
     * @depends testProfileEditWithValidData
     */
    public function testProfileChangePasswordWithNoDataJSON() {
        $response = $this->call('POST', 'api/spa/profile/changepassword');
        $this->assertEquals('401', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertEquals('{"code":401,"message":"Validation failed."}', $content);
    }

    /**
     * Test profile change password with wrong old password.
     *
     * @depends testProfileChangePasswordWithNoData
     */
    public function testProfileChangePasswordWithWrongOldPassword() {
        $post_data = array(
            'password_old' => 'pass',
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        );
        $response = $this->call('POST', 'profile/changepassword', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('profile/edit', $response);
        ////$this->checkIfErrorsExist();
    }

    /**
     * Test profile change password with wrong old password.
     *
     * @depends testProfileChangePasswordWithNoData
     */
    public function testProfileChangePasswordWithWrongOldPasswordJSON() {
        $post_data = array(
            'password_old' => 'pass',
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        );
        $response = $this->call('POST', 'api/spa/profile/changepassword', $post_data);
        $this->assertEquals('401', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertEquals('{"code":401,"message":"Old password does not match with your current password."}', $content);
    }

    /**
     * Test profile change password with wrong confirmation password.
     *
     * @depends testProfileChangePasswordWithWrongOldPassword
     */
    public function testProfileChangePasswordWithWrongConfirmationPassword() {
        $post_data = array(
            'password_old' => 'password',
            'password' => 'new_password',
            'password_confirmation' => 'new_pass'
        );
        $response = $this->call('POST', 'profile/changepassword', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('profile/edit', $response);
        ////$this->checkIfErrorsExist();
    }

    /**
     * Test profile change password with wrong confirmation password.
     *
     * @depends testProfileChangePasswordWithWrongOldPassword
     */
    public function testProfileChangePasswordWithWrongConfirmationPasswordJSON() {
        $post_data = array(
            'password_old' => 'password',
            'password' => 'new_password',
            'password_confirmation' => 'new_pass'
        );
        $response = $this->call('POST', 'api/spa/profile/changepassword', $post_data);
        $this->assertEquals('401', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertEquals('{"code":401,"message":"Validation failed."}', $content);
    }

    /**
     * Test profile change password with valid data.
     *
     * @depends testProfileChangePasswordWithWrongConfirmationPassword
     */
    public function testProfileChangePasswordWithValidData() {
        $post_data = array(
            'password_old' => 'password1',
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        );
        $response = $this->call('POST', 'profile/changepassword', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('profile/edit', $response);
        ////$this->checkIfNoErrorsExist();
    }

    /**
     * Test profile change password with valid data.
     *
     * @depends testProfileChangePasswordWithWrongConfirmationPassword
     */
    public function testProfileChangePasswordWithValidDataJSON() {

        $post_data = array(
            'password_old' => 'password1',
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        );
        $response = $this->call('POST', 'api/spa/profile/changepassword', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertEquals('"Change of password successful."', $content);    
    }

    /**
     * Test the profile delete view.
     */
    public function testProfileDeleteView() {
        $response = $this->call('GET', 'profile/delete');
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test the profile delete.
     *
     * @depends testProfileDeleteView
     */
    public function testProfileDelete() {
        $response = $this->call('DELETE', 'profile/delete');
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('/', $response);
        ////$this->checkIfNoErrorsExist();

        //Check if user is logged in and exists
        $this->assertFalse(Sentry::check());

        //recreate user georg, so in parentteardown, he can be deleted again
        $user = Sentry::createUser(array(
            'email'     => 'georg@example.com',
            'password'  => 'password1',
            'activated' => true
            ));
        DB::table('users_metadata')->insert(array(
            'user_id'       => $user->getId(),
            'displayname'   => 'Georg',
            'language'      => 'en'
            ));
    }

    /**
     * Test the profile delete.
     *
     * @depends testProfileDeleteView
     */
    public function testProfileDeleteJSON() {
        $response = $this->call('POST', 'api/spa/profile/delete');
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertEquals('"Delete profile successful."', $content);

        //Check if user is logged in and exists
        $this->assertFalse(Sentry::check());

        //recreate user georg, so in parentteardown, he can be deleted again
        $user = Sentry::createUser(array(
            'email'     => 'georg@example.com',
            'password'  => 'password1',
            'activated' => true
            ));
        DB::table('users_metadata')->insert(array(
            'user_id'       => $user->getId(),
            'displayname'   => 'Georg',
            'language'      => 'en'
            ));
    }
}