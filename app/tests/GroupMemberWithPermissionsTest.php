<?php

require_once 'TestCaseCourse.php';

class GroupMemberWithPermissionsTest extends TestCaseCourse {

    protected $names_create = array('Test xy', 'Test yz');
    protected $names_delete = array('Test xy', 'Test yz');

    private $group = null;


    public function setUp() {
        parent::setUp();
        $this->group = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();
        
        $user = Sentry::createUser(array(
            'email'     => 'groupmembertest@example.com',
            'password'  => 'password1',
            'permissions' => array('admin' => 1),
            'activated' => true
            ));
        DB::table('users_metadata')->insert(array(
            'user_id'       => $user->getId(),
            'displayname'   => 'Groupmembertest',        
            'language'      => 'en'
            ));
        //Login in
        $user = Sentry::findUserByLogin('groupmembertest@example.com');
        Sentry::login($user, false);
    }


    public function tearDown() {
                $user_sentry = Sentry::findUserByLogin('groupmembertest@example.com');
        DB::table('users_metadata')->where('user_id', $user_sentry->getId())->delete(); 
        User::find($user_sentry->getId())->delete();
        parent::tearDown();
    }


    /**
     * Test ajax user add to group with not valid data
     */
    public function testAjaxGroupUserAddWithNotValidData() {
        $post_data = array(
            'id'    => $this->group->id
        );
        $response = $this->call('POST', 'group/user/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user add to group with not existing group
     *
     * @depends testAjaxGroupUserAddWithNotValidData
     */
    public function testAjaxGroupUserAddWithNotExistingGroup() {
        $id = $this->getNotExistingID('Group');

        $post_data = array(
            'id'    => $id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'group/user/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user add to group with not existing user
     *
     * @depends testAjaxGroupUserAddWithNotExistingGroup
     */
    public function testAjaxGroupUserAddWithNotExistingUser() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'g@example.com'
        );
        $response = $this->call('POST', 'group/user/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user add to group with valid data
     *
     * @depends testAjaxGroupUserAddWithNotExistingUser
     */
    public function testAjaxGroupUserAddWithValidData() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'group/user/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);


        //Reset Sentry cache
        //$this->resetSentry();

        //Check if is in group
        $user = Sentry::findUserByLogin('georg@example.com');
        $role = Role::where('name', 'LIKE', 'member')->first();

        $allocation = DB::table('user_learngroups')
                            ->where('user_id', '=', $user->getId())
                            ->where('role_id', '=', $role->id)
                            ->first();

        $this->assertNotNull($allocation);
    }


    /**
     * Test ajax user remove to group with not valid data
     */
    public function testAjaxGroupUserRemoveWithNotValidData() {
        $post_data = array(
            'id'    => $this->group->id
        );
        $response = $this->call('POST', 'group/user/remove', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user remove to group with not existing group
     *
     * @depends testAjaxGroupUserRemoveWithNotValidData
     */
    public function testAjaxGroupUserRemoveWithNotExistingGroup() {
        $id = $this->getNotExistingID('Group');

        $post_data = array(
            'id'    => $id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'group/user/remove', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user remove to group with not existing user
     *
     * @depends testAjaxGroupUserRemoveWithNotExistingGroup
     */
    public function testAjaxGroupUserRemoveWithNotExistingUser() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'g@example.com'
        );
        $response = $this->call('POST', 'group/user/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user remove to group with valid data
     *
     * @depends testAjaxGroupUserRemoveWithNotExistingUser
     */
    public function testAjaxGroupUserRemovedWithValidData() {
        $user_sentry = Sentry::findUserByLogin('georg@example.com');
        $user = User::find($user_sentry->getId());
        $role = Role::where('name', 'LIKE', 'member')->first();
        $this->group->users()->attach($user, array('role_id' => $role->id));

        //Add favorite
        $course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $catalog = $course->catalog()->first();
        $catalog2 = $catalog->children()->first();
        $user->favorites()->attach($catalog2);

        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'group/user/remove', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);


        //Reset Sentry cache
        //$this->resetSentry();

        //Check if is in group
        $userSentry = Sentry::findUserByLogin('georg@example.com');
        $user = User::find($userSentry->getId());
        $allocation = DB::table('user_learngroups')->where('user_id', '=', $user->id)->first();
        $this->assertNull($allocation);

        //Check favorites
        $check = $this->isSavedAsFavorite($catalog2->id, $user);
        $this->assertFalse($check);
    }


    /**
     * Test ajax user remove to group with valid data and two related learngroups
     *
     * @depends testAjaxGroupUserRemovedWithValidData
     */
    public function testAjaxGroupUserRemovedWithValidDataAndTwoRelatedLearngroups() {
        $user_sentry = Sentry::findUserByLogin('georg@example.com');
        $user = User::find($user_sentry->getId());

        $role = Role::where('name', 'LIKE', 'member')->first();
        $this->group->users()->attach($user, array('role_id' => $role->id));

        $group2 = Learngroup::where('name', 'LIKE', 'Group Test yz')->first();
        $group2->users()->attach($user, array('role_id' => $role->id));

        //Add second learngroup
        $course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $course->learngroups()->attach($group2);

        //Add favorite
        $catalog = $course->catalog()->first();
        $catalog2 = $catalog->children()->first();
        $user->favorites()->attach($catalog2);
        
        $check = $this->isSavedAsFavorite($catalog2->id, $user);
        $this->assertTrue($check);

        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'group/user/remove', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);

        //Check favorites
        $check = $this->isSavedAsFavorite($catalog2->id, $user);
        $this->assertTrue($check);
    }


    /**
     * Test ajax admin add to group with not valid data
     */
    public function testAjaxGroupAdminAddWithNotValidData() {
        $post_data = array(
            'id'    => $this->group->id
        );
        $response = $this->call('POST', 'group/admin/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin add to group with not existing group
     *
     * @depends testAjaxGroupAdminAddWithNotValidData
     */
    public function testAjaxGroupAdminAddWithNotExistingGroup() {
        $id = $this->getNotExistingID('Group');

        $post_data = array(
            'id'    => $id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'group/admin/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin add to group with not existing user
     *
     * @depends testAjaxGroupAdminAddWithNotExistingGroup
     */
    public function testAjaxGroupAdminAddWithNotExistingUser() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'g@example.com'
        );
        $response = $this->call('POST', 'group/admin/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin add to group with valid data
     *
     * @depends testAjaxGroupAdminAddWithNotExistingUser
     */
    public function testAjaxGroupAdminAddWithValidData() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'group/admin/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);

        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);


        //Reset Sentry cache
        //$this->resetSentry();

        //Check if is in group
        $user = Sentry::findUserByLogin('georg@example.com');
        $role = Role::where('name', 'LIKE', 'admin')->first();

        $allocation = DB::table('user_learngroups')
                            ->where('user_id', '=', $user->getId())
                            ->where('role_id', '=', $role->id)
                            ->first();

        $this->assertNotNull($allocation);
    }


    /**
     * Test ajax admin remove to group with not valid data
     */
    public function testAjaxGroupAdminRemoveWithNotValidData() {
        $post_data = array(
            'id'    => $this->group->id
        );
        $response = $this->call('POST', 'group/admin/remove', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin remove to group with not existing group
     *
     * @depends testAjaxGroupAdminRemoveWithNotValidData
     */
    public function testAjaxGroupAdminRemoveWithNotExistingGroup() {
        $id = $this->getNotExistingID('Group');

        $post_data = array(
            'id'    => $id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'group/admin/remove', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin remove to group with not existing user
     *
     * @depends testAjaxGroupAdminRemoveWithNotExistingGroup
     */
    public function testAjaxGroupAdminRemoveWithNotExistingUser() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'g@example.com'
        );
        $response = $this->call('POST', 'group/admin/add', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin remove to group with valid data
     *
     * @depends testAjaxGroupAdminRemoveWithNotExistingUser
     */
    public function testAjaxGroupAdminRemovedWithValidData() {
        $user_sentry = Sentry::findUserByLogin('georg@example.com');
        $user_kakadu = User::find($user_sentry->getId());
        $role = Role::where('name', 'LIKE', 'admin')->first();
        $this->group->users()->attach($user_kakadu, array('role_id' => $role->id));

        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->call('POST', 'group/admin/remove', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);


        //Reset Sentry cache
        //$this->resetSentry();

        //Check if is in group
        $user = Sentry::findUserByLogin('georg@example.com');
        $allocation = DB::table('user_learngroups')->where('user_id', '=', $user->getId())->first();
        $role = Role::where('name', 'LIKE', 'admin')->first();
        $this->assertEquals($allocation->role_id, $role->id);
    }

}