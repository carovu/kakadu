<?php

class QuestiontypeClozeSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{   
        $jsonQuestion = json_encode(array(
            'question'  => 'Bla bla blablabla bla bla alsdjfkölasd correct0 bla bla blablabla bla bla alsdjfkölasd correct1 bla bla blablabla bla bla alsdjfkölasd correct2',
        ));

        Question::where('question', 'LIKE', $jsonQuestion)->delete();

        $jsonAnswer = json_encode(array(
            'answer'   => array(
                'correct0',
                'correct1',
                'correct2'
            )
        ));
        $this->question = new Question;
        $this->question->type = 'cloze';
        $this->question->question = $jsonQuestion;
        $this->question->answer = $jsonAnswer;
        $this->question->learned = 'false';
        $this->question->save();
	}

}  