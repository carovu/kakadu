<?php

class QuestiontypeMultipleSeeder extends Seeder {

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

        $jsonAnswer = json_encode(array(
            'answer'    => array(
                '1',
                '2'
            ),
            'choices'   => array(
                'Answer 1',
                'Answer 2',
                'Answer 3'
            )
        ));
        $this->question = new Question;
        $this->question->type = 'multiple';
        $this->question->question = $jsonQuestion;
        $this->question->answer = $jsonAnswer;
        $this->question->save();
	}

}  