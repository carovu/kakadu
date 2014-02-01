<?php

require_once 'TestCaseCourse.php';

class GroupWithoutPermissionsTest extends TestCaseCourse {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $group = NULL;


    public function setUp() {
        parent::setUp();
        $this->group = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();

        $user = Sentry::findUserByLogin('georg@example.com');
        Sentry::login($user, false);
    }


    public function tearDown() {
        parent::tearDown();
    }
    


    /**
     * Test the view to create a group
     */
    public function testGroupCreateView() {
        Sentry::logout();
        $response = $this->call('GET', 'api/v1/group/create');
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test create group with valid data
     */
    public function testGroupCreatePostWithValidData() {
        Sentry::logout();
        $post_data = array(
            'name'        => 'Group Test yz',
            'description' => 'This is a testgroup that shows the right functionality of the controller.'
        );
        $response = $this->call('POST', 'api/v1/group/create', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test the view to edit a existing group
     */
    public function testGroupEditExistingID() {
        $response = $this->call('GET', 'api/v1/group/' . $this->group->id . '/edit');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test edit group with valid data
     */
    public function testGroupEditPostWithValidData() {
        $post_data = array(
            'id'            => $this->group->id,
            'name'          => 'Group Test yz',
            'description'   => 'This is a testgroup that shows the right functionality of the controller.'
        );
        $response = $this->call('POST', 'api/v1/group/edit', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test delete group with valid data
     */
    public function testGroupDeleteWithValidData() {
        $response = $this->call('GET', 'api/v1/group/' . $this->group->id . '/delete');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }

}