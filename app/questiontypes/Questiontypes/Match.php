<?php

class Match extends QuestionType {

    protected $type = 'match';

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
     * Reads the information of the question form the input or return an error message
     * 
     * @return boolean|string True if no error occured or false if there was an error
     */
    public function getQuestionFromInput() {
        
    }

    /**
     * Reads the information of the question form the import data or return an error message
     *
     * @param array    $data An array with all informations
     * @return boolean True if no error occured or false if there was an error
     */
    public function getQuestionFromImportData($data) {

    }

    /**
     * Sets the question informations from a Question instance
     * 
     * @param Question $question A Question instance
     */
    public function setInfosFromQuestion($question) {

    }

    /**
     * Converts the question informations to JSON.
     * The result can be stored in the database.
     * 
     * @return string The JSON response of the question.
     */
    protected function getJsonQuestion() {

    }

    /**
     * Converts the answer informations to JSON.
     * The result can be stored in the database.
     * 
     * @return string The JSON response of the answer.
     */
    protected function getJsonAnswer() {

    }

    /**
     * Returns the question in a valid format for the view
     * 
     * @return array An array with all the informations of the question
     */
    public function getViewElement() {

    }


    /**
     * Read all question informations form a cell iterator and return an array with the given data
     * 
     * @param  PHPExcel_Worksheet_CellIterator $cellIterator
     * @return array                           An array with all specific informations or false on a syntax error
     */
    public static function readCSVData($cellIterator) {

    }

}