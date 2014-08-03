<?php

class QuestiontypeSimpleSeeder extends Seeder {
	
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        $jsonQuestion1 = json_encode(array(
            'question'  => 'Question Simplex'
        ));
        $jsonQuestion2 = json_encode(array(
            'question'  => 'Question Simpley'
        ));
        Question::where('question', 'LIKE', $jsonQuestion1)->orWhere('question', 'LIKE', $jsonQuestion2)->delete();
		
        $jsonQuestion = json_encode(array(
            'question'  => 'Question x'
        ));
        $jsonAnswer = json_encode(array(
            'answer'    => 'Answer x'
        ));
        $this->question = new Question;
        $this->question->type = 'simple';
        $this->question->question = $jsonQuestion1;
        $this->question->answer = $jsonAnswer;
        $this->question->learned = 'false';
        $this->question->save();
	}


}  