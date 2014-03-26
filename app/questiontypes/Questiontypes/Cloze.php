<?php

class Cloze extends QuestionType {

    protected $type = 'cloze';
    protected $texts = array();
    protected $choices = array();

    /**
     * Returns the text of the question
     * 
     * @return string The text of the question
     */
    public function getQuestion() {
        return $this->question;
    }

    /**
     * Returns the answer of the question
     * 
     * @return string The answer of the question
     */
    public function getAnswer() {
        return $this->answer;
    }

    /**
     * Returns the text of the question
     * 
     * @return array The choices of the questions
     */
    public function getTexts() {
        return $this->texts;
    }

    /**
     * Returns the choices of the question
     * 
     * @return array The choices of the questions
     */
    public function getChoices() {
        return $this->choices;
    }




    /**
     * Reads the information of the question form the input or return an error message
     * 
     * @return boolean|string True if no error occured or false if there was an error
     */
    public function getQuestionFromInput() {
        if(parent::getQuestionFromInput() === false) {
            return false;
        }
        $this->texts = Input::get('text');
        $this->choices = Input::get('choices');

        if(!is_array($this->texts) || count($this->texts) < 2) {
            return trans('question.cloze_min_two_answers');
        }

        if(!is_array($this->choices) || count($this->choices) < 2) {
            return trans('question.multiple_min_two_answers');
        }

        if(!is_array($this->answer) || count($this->answer) === 1) {
            return trans('question.multiple_index_not_valid');
        }

        return true;
    }

    /**
     * Reads the information of the question form the import data or return an error message
     *
     * @param array    $data An array with all informations
     * @return boolean True if no error occured or false if there was an error
     */
    public function getQuestionFromImportData($data) {
        if(parent::getQuestionFromImportData($data) === false) {
            return false;
        }

        $this->texts = $data['texts'];
        $this->choices = $data['choices'];

        return true;
    }

    /**
     * Sets the question informations from a Question instance
     * 
     * @param Question $question A Question instance
     */
    public function setInfosFromQuestion($question) {
        parent::setInfosFromQuestion($question);

        $jsonQuestion = json_decode($question->question);
        $jsonAnswer = json_decode($question->answer);

        $this->question = $jsonQuestion->{'question'};
        
        foreach($jsonQuestion->{'texts'} as $text) {
            $this->texts[] = $text;
        }

        $this->answer = $jsonAnswer->{'answer'};

        foreach($jsonAnswer->{'choices'} as $choice) {
            $this->choices[] = $choice;
        }
    }

    /**
     * Converts the question informations to JSON.
     * The result can be stored in the database.
     * 
     * @return string The JSON response of the question.
     */
    protected function getJsonQuestion() {
        $jsonQuestion = array(
            'question' => $this->question,
            'texts'    => $this->texts
        );

        return json_encode($jsonQuestion);
    }

    /**
     * Converts the answer informations to JSON.
     * The result can be stored in the database.
     * 
     * @return string The JSON response of the answer.
     */
    protected function getJsonAnswer() {
        $jsonAnswer = array(
            'answer'    => $this->answer,
            'choices'   => $this->choices
        );

        return json_encode($jsonAnswer);
    }

    /**
     * Returns the question in a valid format for the view
     * 
     * @return array An array with all the informations of the question
     */
    public function getViewElement() {
        $element = parent::getViewElement();

        $element['question'] = $this->question;
        $element['answer'] = $this->answer;
        $element['texts'] = $this->texts;
        $element['choices'] = $this->choices;

        return $element;
    }


    /**
     * Read all question informations form a cell iterator and return an array with the given data
     * 
     * @param  PHPExcel_Worksheet_CellIterator $cellIterator
     * @return array                           An array with all specific informations or false on a syntax error
     */
    public static function readCSVData($cellIterator) {
        //Question - Question
        $cellIterator->next();

        if(!$cellIterator->valid()) {
            return false;
        }

        $cell = $cellIterator->current();
        $question = $cell->getValue();

        //Question - Texts
        $texts = array();
        $cellIterator->next();

        while($cellIterator->valid()) {
            $cell = $cellIterator->current();
            $texts[] = $cell->getValue();
            $cellIterator->next();
        }
        //Question - Answer
        $cellIterator->next();

        if(!$cellIterator->valid()) {
            return false;
        }

        $cell = $cellIterator->current();
        $answer = preg_split('/[ ]*,[ ]*/', $cell->getValue());

        if(count($answer) <= 0) {
            return false;
        }

        //Question - Choices
        $choices = array();
        $cellIterator->next();

        while($cellIterator->valid()) {
            $cell = $cellIterator->current();
            $choices[] = $cell->getValue();
            $cellIterator->next();
        }

        return array(
            'question'  => $question,
            'texts'     => $texts,
            'answer'    => $answer,
            'choices'   => $choices
        );
    }

}