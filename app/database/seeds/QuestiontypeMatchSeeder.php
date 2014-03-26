<?php

class QuestiontypeMatchSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{  
        $jsonQuestion1 = json_encode(array(
            'question'  => ''
        ));
        $jsonQuestion2 = json_encode(array(
            'question'  => ''
        ));
        Question::where('question', 'LIKE', $jsonQuestion1)->orWhere('question', 'LIKE', $jsonQuestion2)->delete();

        $jsonQuestion = json_encode(array(
            'question'  => 'Question x'
        ));

        $jsonAnswer = json_encode(array(
            'answer'    => 'Answer x',
            'choices'   => array(
                'Answer x',
                'Answer y',
                'Answer z'
            )
        ));
        $this->question = new Question;
        $this->question->type = 'match';
        $this->question->question = $jsonQuestion;
        $this->question->answer = $jsonAnswer;
        $this->question->save();

	}

}  