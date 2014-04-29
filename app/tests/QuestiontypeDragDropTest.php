<?php

class QuestiontypeDragDropTest extends TestCase {

    private $question = null;
    
    public function setUp() {
        parent::setUp();
        $jsonQuestion = json_encode(array(
            'question'  => 'Question x'
        ));

        $jsonAnswer = json_encode(array(
            'answer'    => 'Answer 1',
            'choices'   => array(
                'Answer 1',
                'Answer 2',
                'Answer 3'
            )
        ));

        $this->question = new Question;
        $this->question->type = 'dragdrop';
        $this->question->question = $jsonQuestion;
        $this->question->answer = $jsonAnswer;
        $this->question->save();
    }

    public function tearDown() {
        $jsonQuestion1 = json_encode(array(
            'question'  => 'Question x'
        ));

        $jsonQuestion2 = json_encode(array(
            'question'  => 'Question y'
        ));

        Question::where('question', 'LIKE', $jsonQuestion1)->orWhere('question', 'LIKE', $jsonQuestion2)->delete();
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

        $this->question->type = 'dragdrop';
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertNotNull($questionType);
        $this->assertInstanceOf('DragDrop', $questionType);

        $this->assertEquals($this->question->id, $questionType->getId());
        $this->assertEquals('dragdrop', $questionType->getType());
        $this->assertEquals('Question x', $questionType->getQuestion());
        
        $answer = $questionType->getAnswer();
        $this->assertContains('1', $answer);

        $this->assertNotNull($questionType->getCreatedAt());
        $this->assertNotNull($questionType->getUpdatedAt());

        $choices = $questionType->getChoices();
        $this->assertCount(3, $choices);
        $this->assertContains('Answer 1', $choices);
        $this->assertContains('Answer 2', $choices);
        $this->assertContains('Answer 3', $choices);
    }

    public function testGetQuestionFromDatabase() {
        $questionType = QuestionType::getQuestionFromDatabase(null);
        $this->assertNull($questionType);

        $questionType = QuestionType::getQuestionFromDatabase($this->question->id);
        $this->assertNotNull($questionType);
        $this->assertInstanceOf('DragDrop', $questionType);

        $this->assertEquals($this->question->id, $questionType->getId());
        $this->assertEquals('dragdrop', $questionType->getType());
        $this->assertEquals('Question x', $questionType->getQuestion());
        
        $answer = $questionType->getAnswer();
        $this->assertContains('1', $answer);

        $this->assertNotNull($questionType->getCreatedAt());
        $this->assertNotNull($questionType->getUpdatedAt());

        $choices = $questionType->getChoices();
        $this->assertCount(3, $choices);
        $this->assertContains('Answer 1', $choices);
        $this->assertContains('Answer 2', $choices);
        $this->assertContains('Answer 3', $choices);
    }

    public function testGetQuestionType() {
        $questionType = QuestionType::getQuestionType('dragdrop');
        $this->assertNotNull($questionType);
        $this->assertInstanceOf('DragDrop', $questionType);
    }



    /**
     * Non-static functions
     */
    public function testCreate() {
        $jsonQuestion = json_encode(array(
            'question'  => 'Question y'
        ));

        $jsonAnswer = json_encode(array(
            'answer'    => 'Answer 2',

            'choices'   => array(
                'Answer 1',
                'Answer 2',
                'Answer 3',
                'Answer 4'
            )
        ));

        $question = new Question;
        $question->type = 'dragdrop';
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
            'question'  => 'Question y'
        ));

        $jsonAnswer = json_encode(array(
            'answer'    => 'Answer 3',

            'choices'   => array(
                'Answer 1',
                'Answer 2',
                'Answer 3',
                'Answer 4'
            )
        ));


        $question = $this->question;
        $question->type = 'dragdrop';
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
        $this->assertArrayHasKey('choices', $viewElement);
        $this->assertArrayHasKey('created_at', $viewElement);
        $this->assertArrayHasKey('updated_at', $viewElement);

        $this->assertEquals($questionType->getId(), $viewElement['id']);
        $this->assertEquals($questionType->getType(), $viewElement['type']);
        $this->assertEquals($questionType->getQuestion(), $viewElement['question']);
        $this->assertEquals($questionType->getAnswer(), $viewElement['answer']);
        $this->assertEquals($questionType->getCreatedAt(), $viewElement['created_at']);
        $this->assertEquals($questionType->getUpdatedAt(), $viewElement['updated_at']);

        $choices = $viewElement['choices'];
        $this->assertCount(3, $choices);
        $this->assertContains('Answer 1', $choices);
        $this->assertContains('Answer 2', $choices);
        $this->assertContains('Answer 3', $choices);
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
        $this->assertEquals('dragdrop', $questionType->getType());
    }

    public function testGetQuestion() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertEquals('Question x', $questionType->getQuestion());
    }

    public function testGetAnswer() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $answer = $questionType->getAnswer();
        $this->assertNotNull($answer);
        $this->assertContains('1', $answer);
    }

    public function testGetCreatedAt() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertNotNull($questionType->getCreatedAt());
    }

    public function testGetUpdatedAt() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $this->assertNotNull($questionType->getUpdatedAt());
    }

    public function testGetChoices() {
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $choices = $questionType->getChoices();
        $this->assertNotNull($choices);
        $this->assertCount(3, $choices);
        $this->assertContains('Answer 1', $choices);
        $this->assertContains('Answer 2', $choices);
        $this->assertContains('Answer 3', $choices);
    }

}