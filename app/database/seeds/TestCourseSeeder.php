<?php

class TestCourseSeeder extends Seeder {
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::disableQueryLog();
        $this->createCourseSeeder('Cities');
        $this->createCourseSeeder('English');
        $this->createCourseSeeder('Geography');
        $this->createCourseSeeder('Physics');
        $this->createCourseSeeder('Biology');
        $this->createCourseSeeder('Computer Science');
        $this->createCourseSeeder('Mathematics');
        $this->createCourseSeeder('Spanish');
        $this->createCourseSeeder('German');
        $this->createCourseSeeder('Music');
        $this->createCourseSeeder('Latin');


    }


    private function createCourseSeeder($name) {

        $catalog = new Catalog();
        $catalog->name = $name;
        $catalog->number = 1;
        $catalog->save();
        $i = 0;
        $question1 = $this->createSimpleQuestion($name, ++$i);
        $question2 = $this->createMultipleQuestion($i, $name, ++$i);
        $question3 = $this->createClozeQuestion($i, $name, ++$i);
        $question4 = $this->createDragDropQuestion($i, $name, ++$i);

        $catalog->questions()->save($question1);
        $catalog->questions()->save($question2);
        $catalog->questions()->save($question3);
        $catalog->questions()->save($question4);

        //Kurs 1
        $description = 'This is the description of ' . $name . '. It is a filler course, to give the tester a better impression how Kakadu will be, if there are many courses and therefore contains just generic filler questions.';

        $course = new Course();
        $course->name = $name;
        $course->description = $description;
        $course->catalog = $catalog->id;
        $course->save();

        return;
    }

    /**
     * Create a simple question with example question and answer
     * 
     * @param  string  $courseNumber   A course number to show the referenced course
     * @param  string  $groupNumber    A name of a group to show the referenced groups
     * @param  string  $questionNumber A course number to show the referenced course
     * @return Question                A Question instance
     */
    private function createSimpleQuestion($groupName, $questionNumber) {
        $q = array(
            'question' => 'This is a test question.'
        );

        $a = array(
            'answer'    => 'This is a test answer.'
        );

        $question = new Question();
        $question->type = 'simple';
        $question->question = json_encode($q);
        $question->answer = json_encode($a);
        $question->save();
        return $question;
    }



    /**
     * Create a multiple choice question with example question and answer
     * 
     * @param  string  $courseNumber   A course number to show the referenced course
     * @param  string  $groupNumber    A name of a group to show the referenced groups
     * @param  string  $questionNumber A course number to show the referenced course
     * @return Question                A Question instance
     */
    private function createMultipleQuestion($groupName, $questionNumber) {
        $q = array(
            'question' => 'This is a test question.'
        );
        //answer - choose from 0-3 
        $a = array(
            'answer'    => array(
                '2',
                '3'
            ),
            'choices'   => array(
                'This is a test answer 1',
                'This is a test answer 2',
                'This is the correct test answer 3',
                'This is the correct test answer 4'
            )
        );

        $question = new Question();
        $question->type = 'multiple';
        $question->question = json_encode($q);
        $question->answer = json_encode($a);
        $question->save();
        return $question;
    }

    /**
     * Create a cloze question with example question and answer
     * 
     * @param  string  $courseNumber   A course number to show the referenced course
     * @param  string  $groupNumber    A name of a group to show the referenced groups
     * @param  string  $questionNumber A course number to show the referenced course
     * @return Question                A Question instance
     */
    private function createClozeQuestion($groupName, $questionNumber) {
        $q = array(
            'question' => 'This is a test question.'
        );

        $a = array(
            'answer'   => array(
                'test'
            )
        );

        $question = new Question();
        $question->type = 'cloze';
        $question->question = json_encode($q);
        $question->answer = json_encode($a);
        $question->save();
        return $question;
    }

    /**
     * Create a drag and drop question with example question and answer
     * 
     * @param  string  $courseNumber   A course number to show the referenced course
     * @param  string  $groupNumber    A name of a group to show the referenced groups
     * @param  string  $questionNumber A course number to show the referenced course
     * @return Question                A Question instance
     */
    private function createDragDropQuestion($groupName, $questionNumber) {
        $q = array(
            'question' => 'This is a test question.'
        );

        $a = array(
            'answer'    => 'This is the correct test answer',
            'choices'   => array(
                'This is a test answer 1',
                'This is a test answer 2',
                'This is the correct test answer',
                'This is a test answer 4'
            )
        );

        $question = new Question();
        $question->type = 'dragdrop';
        $question->question = json_encode($q);
        $question->answer = json_encode($a);
        $question->save();
        return $question;
    }

}  