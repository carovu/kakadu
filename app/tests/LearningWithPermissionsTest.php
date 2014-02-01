<?php

require_once 'TestCaseCourse.php';

class LearningWithPermissionsTest extends TestCaseCourse {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = null;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
       
        $user = Sentry::createUser(array(
            'email'     => 'learningtest@example.com',
            'password'  => 'password1',
            'permissions' => array('admin' => 1),
            'activated' => true
            ));
        DB::table('users_metadata')->insert(array(
            'user_id'       => $user->getId(),
            'displayname'   => 'Learningtest',        
            'language'      => 'en'
            ));
        //Login in
        $user = Sentry::findUserByLogin('learningtest@example.com');
        Sentry::login($user, false);

        //Add course as favorite
        $userID = Sentry::getUser()->getId();
        $user = User::find($userID);
        $catalog = $this->course->catalog()->first();
        $user->favorites()->attach($catalog);

        //Generate flashcard
        $question = Question::where('question', 'LIKE', '%This is question 1.1 of course 1 - group Test xy%')
                            ->first();

        $flashcard = new Flashcard;
        $flashcard->question_id = $question->id;
        $flashcard->user_id = Sentry::getUser()->getId();
        $flashcard->index = 3;
        $flashcard->number_correct = 4;
        $flashcard->number_incorrect = 1;
        $flashcard->save();
    }

    public function tearDown() {
        parent::tearDown();
        $user_sentry = Sentry::findUserByLogin('learningtest@example.com');
        DB::table('users_metadata')->where('user_id', $user_sentry->getId())->delete(); 
        User::find($user_sentry->getId())->delete();
    }

    /**
     * Test the view to learn a course
     */
    public function testCoursesView() {
        $response = $this->call('GET', 'course/' . $this->course->id . '/learning');
        $this->assertEquals('200', $response->getStatusCode());
    }

    /**
     * Test the view to learn a catalog
     */
    public function testCatalogView() {
        $response = $this->call('GET', 'catalog/' . $this->course->catalog()->first()->id . '/learning');
        $this->assertEquals('200', $response->getStatusCode());
    }

    /**
     * Test the view to learn favorites
     */
    public function testFavoriteView() {
        $response = $this->call('GET', 'favorites/learning');
        $this->assertEquals('200', $response->getStatusCode());
    }

    /**
     * Test ajax get next question with not set answer
     */
    public function testAjaxNextQuestionWithNoAnswer() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'section'   => 'course'
        );
        $response = $this->call('POST', 'learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax get next question with not valid data
     *
     * @depends testAjaxNextQuestionWithNoAnswer
     */
    public function testAjaxNextQuestionWithNotValidData() {
        $id = $this->getNotExistingID('Question');
        $catalog = $this->course->catalog()->first();

        $post_data = array(
            'question'  => $id,
            'course'    => $this->course->id,
            'answer'    => 'true',
            'section'   => 'course'
        );
        $response = $this->call('POST', 'learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax get next question with valid data and right answer
     *
     * @depends testAjaxNextQuestionWithNotValidData
     */
    public function testAjaxNextQuestionWithValidDataAndRightAnswer() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'answer'    => 'true',
            'section'   => 'course'
        );
        $response = $this->call('POST', 'learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                                ->where('user_id', '=', Sentry::getUser()->getId())
                                ->first();

        $this->assertEquals(1, $flashcard->index);
        $this->assertEquals(1, $flashcard->number_correct);
        $this->assertEquals(0, $flashcard->number_incorrect);
    }


    /**
     * Test ajax get next question with valid data and wrong answer
     *
     * @depends testAjaxNextQuestionWithValidDataAndRightAnswer
     */
    public function testAjaxNextQuestionWithValidDataAndWrongAnswer() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'answer'    => 'false',
            'section'   => 'course'
        );
        $response = $this->call('POST', 'learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                                ->where('user_id', '=', Sentry::getUser()->getId())
                             ->first();

        $this->assertEquals(0, $flashcard->index);
        $this->assertEquals(0, $flashcard->number_correct);
       $this->assertEquals(1, $flashcard->number_incorrect);
    }


    /**
     * Test ajax get next question with valid data, existing flashcard and right answer
     *
     * @depends testAjaxNextQuestionWithValidDataAndWrongAnswer
     */
    public function testAjaxNextQuestionWithValidDataExistingFlashcardAndRightAnswer() {
        $question = Question::where('question', 'LIKE', '%This is question 1.1 of course 1 - group Test xy%')
                            ->first();
        $catalog = $this->course->catalog()->first();
        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'answer'    => 'true',
            'section'   => 'course'
        );
        $response = $this->call('POST', 'learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode()); 
        $this->assertContains('"status":"Ok"', $response->getContent());

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                               ->where('user_id', '=', Sentry::getUser()->getId())
                                ->first();

        $this->assertEquals(4, $flashcard->index);
        $this->assertEquals(5, $flashcard->number_correct);
        $this->assertEquals(1, $flashcard->number_incorrect);
    }


    /**
     * Test ajax get next question with valid data, existing flashcard and wrong answer
     *
     * @depends testAjaxNextQuestionWithValidDataExistingFlashcardAndRightAnswer
     */
    public function testAjaxNextQuestionWithValidDataExistingFlashcardAndWrongAnswer() {
        $question = Question::where('question', 'LIKE', '%This is question 1.1 of course 1 - group Test xy%')
                            ->first();
        $catalog = $this->course->catalog()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'answer'    => 'false',
            'section'   => 'course'
        );
        $response = $this->call('POST', 'learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                                ->where('user_id', '=', Sentry::getUser()->getId())
                                ->first();

        $this->assertEquals(0, $flashcard->index);
        $this->assertEquals(4, $flashcard->number_correct);
        $this->assertEquals(2, $flashcard->number_incorrect);
    }


    /**
     * Test ajax get next question of catalog with valid data and right answer
     *
     * @depends testAjaxNextQuestionWithValidDataExistingFlashcardAndWrongAnswer
     */
    public function testAjaxNextQuestionOfCatalogWithValidDataAndRightAnswer() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'catalog'   => $catalog->id,
            'answer'    => 'true',
            'section'   => 'catalog'
        );
        $response = $this->call('POST', 'learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                                ->where('user_id', '=', Sentry::getUser()->getId())
                                ->first();

        $this->assertEquals(1, $flashcard->index);
        $this->assertEquals(1, $flashcard->number_correct);
        $this->assertEquals(0, $flashcard->number_incorrect);
    }


    /**
     * Test ajax get next question of favorites with valid data, right answer and favorites
     *
     * @depends testAjaxNextQuestionOfCatalogWithValidDataAndRightAnswer
     */
    public function testAjaxNextQuestionOfFavoritesWithValidDataAndRightAnswerAndNoFavorites() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $userID = Sentry::getUser()->getId();
        $user = User::find($userID);
        $user->favorites()->detach($catalog->id);        

        $post_data = array(
            'question'  => $question->id,
            'answer'    => 'true',
            'section'   => 'favorites'
        );
        $response = $this->call('POST', 'learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Error"', $content);
    }


    /**
     * Test ajax get next question of favorites with valid data, right answer and favorites
     *
     * @depends testAjaxNextQuestionOfFavoritesWithValidDataAndRightAnswerAndNoFavorites
     */
    public function testAjaxNextQuestionOfFavoritesWithValidDataAndRightAnswerAndFavorites() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'answer'    => 'true',
            'section'   => 'favorites'
        );
        $response = $this->call('POST', 'learning/next', $post_data, [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertEquals('200', $response->getStatusCode());
        $content = $response->getContent();
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                               ->where('user_id', '=', Sentry::getUser()->getId())
                                ->first();

        $this->assertEquals(1, $flashcard->index);
        $this->assertEquals(1, $flashcard->number_correct);
        $this->assertEquals(0, $flashcard->number_incorrect);
    }

}