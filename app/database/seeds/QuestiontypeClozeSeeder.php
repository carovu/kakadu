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
            'question'  => 'Question Clozex',
            'texts'  => array('textbeforegap', 'textaftergap')
        ));
        $jsonQuestion2 = json_encode(array(
            'question'  => 'Question Clozey',
            'texts'  => array('textbeforegap', 'textaftergap')
        ));
        Question::where('question', 'LIKE', $jsonQuestion1)->orWhere('question', 'LIKE', $jsonQuestion2)->delete();

        $jsonQuestion = json_encode(array(
            'question'  => 'Question x'
        ));

        $jsonAnswer = json_encode(array(
            'answer'    => '1',
            'choices'   => array(
                'correct',
                'wrong2',
                'wrong1'
            )
        ));
        $this->question = new Question;
        $this->question->type = 'cloze';
        $this->question->question = $jsonQuestion1;
        $this->question->answer = $jsonAnswer;
        $this->question->save();
	}

}  