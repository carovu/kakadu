<?php

class QuestiontypeClozeSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{  
        $jsonQuestion1 = json_encode(array(
            'question'  => 'Bla bla blablabla bla bla alsdjfkölasd correct0 bla bla blablabla bla bla alsdjfkölasd correct1 bla bla blablabla bla bla alsdjfkölasd  correct2',
        ));
        $jsonQuestion2 = json_encode(array(
            'question'  => 'Question Clozey',
        ));
        Question::where('question', 'LIKE', $jsonQuestion1)->orWhere('question', 'LIKE', $jsonQuestion2)->delete();

        $jsonQuestion = json_encode(array(
            'question'  => 'Question x'
        ));

        $jsonAnswer = json_encode(array(
            'answer'   => array(
                'correct0',
                'correct1',
                'correct2'
            ),
            'choices'   => array(
                'correct0',
                'correct1',
                'correct2'
            )
        ));
        $this->question = new Question;
        $this->question->type = 'cloze';
        $this->question->question = $jsonQuestion1;
        $this->question->answer = $jsonAnswer;
        $this->question->save();
	}

}  