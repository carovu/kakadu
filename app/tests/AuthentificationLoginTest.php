<?php

require_once 'TestCaseUser.php';

class AuthentificationLoginTest extends TestCaseUser {

    public function setUp() {
        parent::setUp();
    }


    public function tearDown() {
        parent::tearDown();
    }

    /**
     * Test the register view
     */
    public function testRegisterView() {
        $response = $this->call('GET', 'auth/register');
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test register with no data.
     *
     * @depends testRegisterView
     */
    public function testRegisterWithNoData()
    {
        $post_data = array();

        $response = $this->call('POST', 'auth/register', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        //////$this->checkResponseLocation('auth/register', $response);
        
    }


    /**
     * Test register with displayname.
     *
     * @depends testRegisterWithNoData
     */
    public function testRegisterWithDisplayname()
    {
        $post_data = array(
            'displayname' => 'Georg'
        );
        $response = $this->call('POST', 'auth/register', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('auth/register', $response);
        
    }

    
    /**
     * Test register with email.
     *
     * @depends testRegisterWithDisplayname
     */
    public function testRegisterWithEmail()
    {
        $post_data = array(
            'email' => 'georg@example.com'
        );
        $response = $this->call('POST', 'auth/register', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        //////$this->checkResponseLocation('auth/register', $response);
        
    }


    /**
     * Test register with password.
     *
     * @depends testRegisterWithEmail
     */
    public function testRegisterWithPassword()
    {
        $post_data = array(
            'password'              => 'password',
            'password_confirmation' => 'password'
        );
        $response = $this->call('POST', 'auth/register', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('auth/register', $response);
        
    }


    /**
     * Test register with valid data.
     *
     * @depends testRegisterWithPassword
     */
    public function testRegisterWithValidDataAndActivation()
    {
        $post_data = array(
            'displayname'           => 'Georg',
            'email'                 => 'georg@example.com',
            'password'              => 'password',
        );
        $response = $this->call('POST', 'auth/register', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('auth/confirmemail', $response);
        
        //Clear the old request
        Request::flush();
        Session::flush();

        //Activate user
        $mailer = new PHPMailer;
                
        //no comfigurationfile in package -> set config here
        $mailer->setFrom('no-reply@uibk.ac.at','Kakadu');
        $mailer->isSMTP();                                   
        $mailer->Host = 'localhost';  

        $link = explode('auth/activate', $mailer->Body, 2);
        //$response = $this->call('GET', 'auth/activate' . $link[1]);
        //$this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('auth/activate', $response);
        
    }


    /**
     * Test the confirmemail view
     */
    public function testConfirmemailView() {
        $response = $this->call('GET', 'auth/confirmemail');
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test the forgot password view
     */
    public function testForgotPasswordView() {
        $response = $this->call('GET', 'auth/forgotpassword');
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test forgot password with no data.
     *
     * @depends testForgotPasswordView
     */
    public function testForgotPasswordWithNoData()
    {
        $post_data = array();
        $response = $this->call('POST', 'auth/forgotpassword', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('auth/forgotpassword', $response);
        
    }


    /**
     * Test forgot password with valid data.
     *
     * @depends testForgotPasswordWithNoData
     */
    public function testForgotPasswordWithValidData()
    {
        $post_data = array(
            'email' => 'alex@example.com'
        );
        $response = $this->call('POST', 'auth/forgotpassword', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('auth/forgotpassword', $response);
    }

}