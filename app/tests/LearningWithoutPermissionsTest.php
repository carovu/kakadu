<?php

require_once 'TestCaseCourse.php';

class LearningWithoutPermissionsTest extends TestCaseCourse {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = NULL;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();

        //Login in
        $user = Sentry::findUserByLogin('georg@example.com');
        Sentry::login($user, false);
    }

    public function tearDown() {
        parent::tearDown();
    }



    /**
     * Test the view to learn a course
     */
    public function testCourseView() {
        $response = $this->call('GET', 'course/' . $this->course->id . '/learning');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }

    /**
     * Test the view to learn a catalog
     */
    public function testCatalogView() {
        $response = $this->call('GET', 'catalog/' . $this->course->catalog()->first()->id . '/learning');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }

    /**
     * Test the view to learn a favorite
     */
    public function testFavoriteView() {
        Sentry::logout();
        $response = $this->call('GET', 'favorites/learning');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }

    /**
     * Test ajax get next question of course
     */
    public function testAjaxCourseNextQuestion() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'answer'    => 'true',
            'section'   => 'course'
        );
        $response = $this->call('POST', 'api/v1/learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

    /**
     * Test ajax get next question of catalog
     */
    public function testAjaxCatalogNextQuestion() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'catalog'   => $catalog->id,
            'answer'    => 'true',
            'section'   => 'catalog'
        );
        $response = $this->call('POST', 'api/v1/learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

    /**
     * Test ajax get next question of favorites
     */
    public function testAjaxFavoritesNextQuestion() {
        Sentry::logout();
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'answer'    => 'true',
            'section'   => 'favorites'
        );
        $response = $this->call('POST', 'api/v1/learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

}