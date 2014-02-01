<?php

require_once 'TestCaseCourse.php';

class CourseWithoutPermissionsTest extends TestCaseCourse {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = null;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        
        $user = Sentry::createUser(array(
            'email'     => 'coursetest@example.com',
            'password'  => 'password',
            'permissions' => array('admin' => 0),
            'activated' => true
            ));
        DB::table('users_metadata')->insert(array(
            'user_id'       => $user->getId(),
            'displayname'   => 'Coursetest',
            'language'      => 'en'
            ));
        $user = Sentry::findUserByLogin('coursetest@example.com');
        Sentry::login($user, false);
 
    }

    public function tearDown() {
        Sentry::logout();
        parent::tearDown();
        $user_sentry = Sentry::findUserByLogin('coursetest@example.com');
        DB::table('users_metadata')->where('user_id', $user_sentry->getId())->delete(); 
        User::find($user_sentry->getId())->delete();
        //Delete the course with no group
        $course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();

        if($course !== NULL) {
            $catalog = $course->catalog()->first();
            HelperCourse::removeQuestionsOfSubCatalogs($catalog);

            $course->delete();
            $catalog->delete();
        }
    }



    /**
     * Test the view to create a course
     */
    public function testCourseCreateView() {
        Sentry::logout();
        $response = $this->call('GET', 'api/v1/course/create');
        $this->assertEquals('200', $response->getStatusCode());
    }


    /**
     * Test create course with valid data and no group
     */
    public function testCourseCreatePostWithValidDataAndNoGroup() {
        Sentry::logout();
        $post_data = array(
            'name'        => 'Test yz',
            'description' => 'This is a testcourse that shows the right functionality of the controller.'
        );
        $response = $this->call('POST', 'api/v1/course/create', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test create course with valid data and group
     */
    public function testCourseCreatePostWithValidDataAndGroup() {
        $group = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();

        $post_data = array(
            'name'        => 'Test yz',
            'description' => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'      => array($group->id)
        );
        $response = $this->call('POST', 'api/v1/course/create', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        ////$this->checkResponseLocation('course/create', $response); 
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test the view to edit a existing course
     */
    public function testCourseEditExistingID() {
        $response = $this->call('GET', 'api/v1/course/' . $this->course->id . '/edit');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test edit course with valid data
     */
    public function testCourseEditPostWithValidData() {
        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.'
        );
        $response = $this->call('POST', 'api/v1/course/edit', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test edit course with valid data and same group
     */
    public function testCourseEditPostWithValidDataAndSameGroup() {
        $group = $this->course->learngroups()->first();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'        => array($group->id)
        );
        $response = $this->call('POST', 'api/v1/course/edit', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test edit course with valid data and from group to group
     */
    public function testCourseEditPostWithValidDataAndFromGroupToGroup() {
        $group_new = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'        => array($group_new->id)
        );
        $response = $this->call('POST', 'api/v1/course/edit', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test edit course with valid data and from group to no group
     */
    public function testCourseEditPostWithValidDataAndFromGroupToNoGroup() {
        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'        => ''
        );
        $response = $this->call('POST', 'api/v1/course/edit', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test edit course with valid data and from no group to group
     */
    public function testCourseEditPostWithValidDataAndFromNoGroupToGroup() {
        $group_new = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();
        DB::table('learngroup_courses')->where('course_id', 'LIKE', $this->course->id)->delete();
        //$this->course->learngroups()->delete();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'        => array($group_new->id)
        );
        $response = $this->call('POST', 'api/v1/course/edit', $post_data);
        $this->assertEquals('302', $response->getStatusCode());
        //$this->checkResponseLocation('course/' . $this->course->id . '/edit', $response);
        ////$this->checkIfErrorsExist();
    }


    /**
     * Test delete course with valid data
     */
    public function testCourseDeleteWithValidData() {
        $response = $this->call('GET', 'api/v1/course/' . $this->course->id . '/delete');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }

}