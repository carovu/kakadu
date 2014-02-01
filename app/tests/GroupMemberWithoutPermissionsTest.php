<?php

require_once 'TestCaseCourse.php';

class GroupMemberWithoutPermissionsTest extends TestCaseCourse {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $group = null;


    public function setUp() {
        parent::setUp();
        $this->group = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();

        //Login in
        $user = Sentry::findUserByLogin('georg@example.com');
        Sentry::login($user, false);
    }


    public function tearDown() {
        parent::tearDown();
    }
    

    /**
     * Test ajax user add to group with valid data
     */
    public function testAjaxGroupUserAddWithValidData() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'api/v1/group/user/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user remove to group with valid data
     */
    public function testAjaxGroupUserRemovedWithValidData() {
        $user_sentry = Sentry::findUserByLogin('georg@example.com');
        $user_kakadu = User::find($user_sentry->getId());
        $role = Role::where('name', 'LIKE', 'member')->first();
        $this->group->users()->attach($user_kakadu, array('role_id' => $role->id));
        

        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'api/v1/group/user/remove', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

    /**
     * Test ajax admin add to group with valid data
     */
    public function testAjaxGroupAdminAddWithValidData() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'api/v1/group/admin/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin remove to group with valid data
     */
    public function testAjaxGroupAdminRemovedWithValidData() {
        $user_sentry = Sentry::findUserByLogin('georg@example.com');
        $user_kakadu = User::find($user_sentry->getId());
        $role = Role::where('name', 'LIKE', 'member')->first();
        $this->group->users()->attach($user_kakadu, array('role_id' => $role->id));
        

        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'api/v1/group/admin/remove', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

}