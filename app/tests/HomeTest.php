<?php
require_once 'TestCaseUser.php';

class HomeTest extends TestCaseUser {
    
    public function setUp() {
        parent::setUp();
    }


    public function tearDown() {
        parent::tearDown();
    }

    /**
     * Test the home view
     */
    public function testHomeView() {
        $response = $this->call('GET', '/');
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test the help view
     */
    public function testHelpView() {
        $response = $this->call('GET', 'help');
        $this->assertEquals('200', $response->getStatusCode());
    }

}