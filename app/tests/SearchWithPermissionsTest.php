<?php

require_once 'TestCaseUser.php';

class SearchWithPermissionsTest extends TestCaseUser {

    public function setUp() {
        parent::setUp();

        $group1 = new Learngroup;
        $group1->name = 'Group xy';
        $group1->description = 'Group xy';
        $group1->save();

        $group2 = new Learngroup;
        $group2->name = 'Group xz';
        $group2->description = 'Group xz';
        $group2->save();

        $user = Sentry::findUserByLogin('alex@example.com');
        Sentry::login($user, false);
    }


    public function tearDown() {
        Learngroup::where('name', 'LIKE', 'Group x%')->delete();
        parent::tearDown();
    }


    /**
     * Test ajax search user with no data
     */
    public function testUserAjaxWithNoData() {
        $response = $this->call('POST', 'api/v1/users/search', [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax search user with valid result
     */
    public function testUserAjaxWithValidResult() {
        $post_data = array(
            'search'    => 'lex'
        );
        $response = $this->call('POST', 'api/v1/users/search', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);
        $this->assertContains('users', $content);
        $this->assertEquals(1, substr_count($content, 'id'));
    }


    /**
     * Test ajax search group with no data
     */
    public function testGroupAjaxWithNoData() {
        $response = $this->call('POST', 'api/v1/groups/search', [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax search group with valid result
     */
    public function testGroupAjaxWithValidResult() {
        $post_data = array(
            'search'    => 'Group x'
        );
        $response = $this->call('POST', 'api/v1/groups/search', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);

        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);
        $this->assertContains('groups', $content);
        $this->assertEquals(2, substr_count($content, 'id'));
    }

}