<?php

class QuestiontypeClozeTest extends TestCase {

    private $question = null;
    
    public function setUp() {
        parent::setUp();
        $jsonQuestion = json_encode(array(
            'question'  => 'Bla bla blablabla bla bla alsdjfkölasd Answer 1 bla bla blablabla bla bla alsdjfkölasd Answer 3 bla bla blablabla bla bla alsdjfkölasd  Answer 2',
        ));

        $jsonAnswer = json_encode(array(
            'answer'   => array(
                'Answer 1',
                'Answer 2',
                'Answer 3'
            )
        ));

        $this->question = new Question;
        $this->question->type = 'cloze';
        $this->question->question = $jsonQuestion;
        $this->question->answer = $jsonAnswer;
        $this->question->save();

    }

    public function tearDown() {
        $jsonQuestion1 = json_encode(array(
            'question'  => 'Bla bla blablabla bla bla alsdjfkölasd correct0 bla bla blablabla bla bla alsdjfkölasd correct1 bla bla blablabla bla bla alsdjfkölasd  correct2',
        ));

        Question::where('question', 'LIKE', $jsonQuestion1)->orWhere('question', 'LIKE', $jsonQuestion1)->delete();
        parent::tearDown();
    }
   /**
     * Static functions
     */
    public function testGetQuestionFromQuestion() {
        $questionType = QuestionType::getQuestionFromQuestion(null);
        $this->assertNull($questionType);

        $this->question->type = 'fail';
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertNull($questionType);

        $this->question->type = 'cloze';
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertNotNull($questionType);
        $this->assertInstanceOf('Cloze', $questionType);

        $this->assertEquals($this->question->id, $questionType->getId());
        $this->assertEquals('cloze', $questionType->getType());
        $this->assertEquals('Bla bla blablabla bla bla alsdjfkölasd Answer 1 bla bla blablabla bla bla alsdjfkölasd Answer 3 bla bla blablabla bla bla alsdjfkölasd  Answer 2', $questionType->getQuestion());
        
        $answer = $questionType->getAnswer();
        $this->assertCount(3, $answer);
        $this->assertContains('Answer 1', $answer);

        $this->assertNotNull($questionType->getCreatedAt());
        $this->assertNotNull($questionType->getUpdatedAt());
    }

    public function testGetQuestionFromDatabase() {
        $questionType = QuestionType::getQuestionFromDatabase(null);
        $this->assertNull($questionType);

        $questionType = QuestionType::getQuestionFromDatabase($this->question->id);
        $this->assertNotNull($questionType);
        $this->assertInstanceOf('Cloze', $questionType);

        $this->assertEquals($this->question->id, $questionType->getId());
        $this->assertEquals('cloze', $questionType->getType());
        $this->assertEquals('Bla bla blablabla bla bla alsdjfkölasd Answer 1 bla bla blablabla bla bla alsdjfkölasd Answer 3 bla bla blablabla bla bla alsdjfkölasd  Answer 2', $questionType->getQuestion());
        
        $answer = $questionType->getAnswer();
        $this->assertCount(3, $answer);
        $this->assertContains('Answer 1', $answer);

        $this->assertNotNull($questionType->getCreatedAt());
        $this->assertNotNull($questionType->getUpdatedAt());
    }

    public function testGetQuestionType() {
        $questionType = QuestionType::getQuestionType('cloze');
        $this->assertNotNull($questionType);
        $this->assertInstanceOf('Cloze', $questionType);
    }



    /**
     * Non-static functions
     */
    public function testCreate() {
        $jsonQuestion = json_encode(array(
            'question'  => 'Bla bla blablabla bla bla alsdjfkölasd Answer 1 bla bla blablabla bla bla alsdjfkölasd Answer 3 bla bla blablabla bla bla alsdjfkölasd  Answer 2',
        ));

        $jsonAnswer = json_encode(array(
            'answer'   => array(
                'Answer 1',
                'Answer 2',
                'Answer 3'
            )
        ));

        $question = new Question;
        $question->type = 'cloze';
        $question->question = $jsonQuestion;
        $question->answer = $jsonAnswer;
        
        $questionType = QuestionType::getQuestionFromQuestion($question);
        $resultQuestion = $questionType->save();

        $this->assertNotNull($resultQuestion);
        $this->assertNotNull($resultQuestion->id);
        $this->assertEquals($jsonQuestion, $resultQuestion->question);
        $this->assertEquals($jsonAnswer, $resultQuestion->answer);
    }

    public function testEdit() {
        $jsonQuestion = json_encode(array(
            'question'  => 'Bla bla blablabla bla bla alsdjfkölasd Answer 1 bla bla blablabla bla bla alsdjfkölasd Answer 3 bla bla blablabla bla bla alsdjfkölasd  Answer 2',
        ));

        $jsonAnswer = json_encode(array(
            'answer'   => array(
                'Answer 1',
                'Answer 2',
                'Answer 3'
            )
        ));


        $question = $this->question;
        $question->type = 'cloze';
        $question->question = $jsonQuestion;
        $question->answer = $jsonAnswer;
        
        $questionType = QuestionType::getQuestionFromQuestion($question);
        $resultQuestion = $questionType->save();

        $this->assertNotNull($resultQuestion);
        $this->assertNotNull($resultQuestion->id);
        $this->assertEquals($jsonQuestion, $resultQuestion->question);
        $this->assertEquals($jsonAnswer, $resultQuestion->answer);
    }

    public function testGetViewElement() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $viewElement = $questionType->getViewElement();

        $this->assertArrayHasKey('id', $viewElement);
        $this->assertArrayHasKey('type', $viewElement);
        $this->assertArrayHasKey('question', $viewElement);
        $this->assertArrayHasKey('answer', $viewElement);
        $this->assertArrayHasKey('created_at', $viewElement);
        $this->assertArrayHasKey('updated_at', $viewElement);

        $this->assertEquals($questionType->getId(), $viewElement['id']);
        $this->assertEquals($questionType->getType(), $viewElement['type']);
        $this->assertEquals($questionType->getQuestion(), $viewElement['question']);
        $this->assertEquals($questionType->getAnswer(), $viewElement['answer']);
        $this->assertEquals($questionType->getCreatedAt(), $viewElement['created_at']);
        $this->assertEquals($questionType->getUpdatedAt(), $viewElement['updated_at']);
    }



    /**
     * Getters
     */
    public function testGetID() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertEquals($this->question->id, $questionType->getId());
    }

    public function testGetType() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertEquals('cloze', $questionType->getType());
    }

    public function testGetQuestion() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertEquals('Bla bla blablabla bla bla alsdjfkölasd Answer 1 bla bla blablabla bla bla alsdjfkölasd Answer 3 bla bla blablabla bla bla alsdjfkölasd  Answer 2', $questionType->getQuestion());
    }

    public function testGetAnswer() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $answer = $questionType->getAnswer();
        $this->assertNotNull($answer);
        $this->assertCount(3, $answer);
        $this->assertContains('Answer 1', $answer);
    }

    public function testGetCreatedAt() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertNotNull($questionType->getCreatedAt());
    }

    public function testGetUpdatedAt() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertNotNull($questionType->getUpdatedAt());
    }

}