<?php

require_once 'TestCaseCourse.php';

class QuestionWithoutPermissionsTest extends TestCaseCourse {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = NULL;
    private $question = NULL;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $this->question = Question::where('question', 'LIKE', '%This is question 1.2 of course 1 - group Test xy%')
                                    ->first();
    }

    public function tearDown() {
        parent::tearDown();
    }
    
    /**
     * Test the view to show a existing question
     */
    public function testQuestionViewExistingID() {
        $response = $this->call('GET', 'question/' . $this->question->id);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test the view to create a question
     */
    public function testQuestionCreateView() {
        $response = $this->call('GET', 'course/' . $this->course->id . '/question/create');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test create question with valid data
     */
    public function testQuestionCreatePostWithValidData() {
        //Select catalogs
        $catalog = $this->course->catalog()->first();
        $subcatalogs = $catalog->children()->get();
        $ids = array();

        foreach ($subcatalogs as $c) {
            $ids[] = $c->id;
        }

        $post_data = array(
            'course'    => $this->course->id,
            'type'      => 'Question y',
            'question'  => 'Question y',
            'answer'    => 'Question y',
            'catalogs'  => $ids
        );
        $response = $this->call('POST', 'question/create', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test the view to edit a existing question
     */
    public function testQuestionEditExistingID() {
        $response = $this->call('GET', 'question/' . $this->question->id . '/edit');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test edit question with valid data
     */
    public function testQuestionEditPostWithValidData() {
        //Select catalogs
        $catalog = $this->course->catalog()->first();
        $subcatalogs = $catalog->children()->get();
        $ids = array();
        
        foreach ($subcatalogs as $c) {
            $ids[] = $c->id;
        }


        $post_data = array(
            'course'    => $this->course->id,
            'id'        => $this->question->id,
            'type'      => 'Question z',
            'question'  => 'Question z',
            'answer'    => 'Question z',
            'catalogs'  => $ids
        );
        $response = $this->call('POST', 'question/edit', $post_data);
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }


    /**
     * Test delete question with valid data
     */
    public function testQuestionDeleteWithValidData() {
        $response = $this->call('GET', 'question/' . $this->question->id . '/delete');
        $this->assertEquals('200', $response->getStatusCode());
        //$this->assertEquals('general.permission', $response->getContent()->view);
    }

}