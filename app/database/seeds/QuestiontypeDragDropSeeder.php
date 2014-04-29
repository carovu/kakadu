<?php

class QuestiontypeDragDropSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{  
        $jsonQuestion = json_encode(array(
            'question'  => 'Questiontest Drag&drop'
        ));

        Question::where('question', 'LIKE', $jsonQuestion)->delete();

        $jsonAnswer = json_encode(array(
            'answer'    => 'Answer x',
            'choices'   => array(
                'Answer x',
                'Answer y',
                'Answer z'
            )
        ));

        $this->question = new Question;
        $this->question->type = 'dragdrop';
        $this->question->question = $jsonQuestion;
        $this->question->answer = $jsonAnswer;
        $this->question->save();

	}

}  