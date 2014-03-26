<?php

class QuestiontypeImageSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{  
        $jsonQuestion1 = json_encode(array(
            'question'  => 'Question x'
        ));
        $jsonQuestion2 = json_encode(array(
            'question'  => 'Question y'
        ));
        Question::where('question', 'LIKE', $jsonQuestion1)->orWhere('question', 'LIKE', $jsonQuestion2)->delete();

        $jsonQuestion = json_encode(array(
            'question'  => 'Question x'
        ));

        //TODO
        $jsonAnswer = json_encode(array());
        $this->question = new Question;
        $this->question->type = 'cloze';
        $this->question->question = $jsonQuestion;
        $this->question->answer = $jsonAnswer;
        $this->question->save();
	}

}  