<?php

require_once 'TestCaseUser.php';

class SearchWithoutPermissionsTest extends TestCaseUser {
    
    public function setUp() {
        parent::setUp();
    }


    public function tearDown() {
        parent::tearDown();
    }

    /**
     * Test ajax search user
     */
    public function testUserAjax() {
        $post_data = array(
            'search'    => 'lex'
        );
        $response = $this->call('POST', 'api/v1/users/search', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax search groups
     */
    public function testGroupAjax() {
        $post_data = array(
            'search'    => 'group'
        );
        $response = $this->call('POST', 'api/v1/groups/search', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

}