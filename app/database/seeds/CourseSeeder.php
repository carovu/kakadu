<?php

class CourseSeeder extends Seeder {
    
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        $this->createCourseSeeder('Test 1');
        $this->createCourseSeeder('Test 2');
	}


    private function createCourseSeeder($name) {
        $user_sentry = Sentry::findUserByLogin('alex@example.com');
        $user_kakadu = User::find($user_sentry->getId());
        $role = Role::where('name', 'LIKE', 'admin')->first();
            $group = new Learngroup();
            $group->name = 'Group ' . $name;
            $group->description = 'This is the description of group ' . $name . '. It has to be very long.';
            $group->save();

            //Add admin
            $group->users()->attach($user_kakadu, array('role_id' => $role->id));

            for($i = 1; $i <= 2; $i++) {
                $catalog = new Catalog();
                $catalog->name = 'Course ' . $i . ' of group ' . $name;
                $catalog->number = 1;
                $catalog->save();

                $question1 = $this->createSimpleQuestion($i, $name, '1');
                $question2 = $this->createMultipleQuestion($i, $name, '2');
                $question3 = $this->createClozeQuestion($i, $name, '3');
                $question4 = $this->createDragDropQuestion($i, $name, '4');

                $catalog->questions()->save($question1);
                $catalog->questions()->save($question2);
                $catalog->questions()->save($question3);
                $catalog->questions()->save($question4);

                //Chapter 1
                $catalog1 = new Catalog();
                $catalog1->name = 'Catalog of course ' . $i . ' -  group ' . $name . ' - chapter 1';
                $catalog1->number = 1;
                $catalog1->parent = $catalog->id;
                $catalog1->save();

                $question11 = $this->createSimpleQuestion($i, $name, '1.1');
                $question12 = $this->createMultipleQuestion($i, $name, '1.2');
                $question13 = $this->createClozeQuestion($i, $name, '1.3');
                $question14 = $this->createDragDropQuestion($i, $name, '1.4');

                $catalog1->questions()->save($question11);
                $catalog1->questions()->save($question12);
                $catalog1->questions()->save($question13);
                $catalog1->questions()->save($question14);

                $catalog->questions()->save($question12);

                //Chapter 2
                $catalog2 = new Catalog();
                $catalog2->name = 'Catalog of course ' . $i . ' -  group ' . $name . ' - chapter 2';
                $catalog2->number = 2;
                $catalog2->parent = $catalog->id;
                $catalog2->save();

                $question21 = $this->createSimpleQuestion($i, $name, '2.1');
                $question22 = $this->createMultipleQuestion($i, $name, '2.2');
                $question23 = $this->createClozeQuestion($i, $name, '2.3');
                $question24 = $this->createDragDropQuestion($i, $name, '2.4');

                $catalog2->questions()->save($question21);
                $catalog2->questions()->save($question22);
                $catalog2->questions()->save($question23);
                $catalog2->questions()->save($question24);

                $catalog1->save();
                $catalog2->save();
                
                //Kurs 1
                $description = 'This is the description of course ' . $i . ' - group '
                                . $name . '. It has to be very long.';

                $course = new Course();
                $course->name = 'Course ' . $i . ' of group ' . $name;
                $course->description = $description;
                $course->catalog = $catalog->id;
                $course->save();

                $course->learngroups()->save($group);
            }
        return;
    }

        /**
     * Returns an id of a not exiting element in the database
     * @param  string  $type The type of the element (modelname)
     * @return integer       ID
     */
    protected function getNotExistingID($type) {
        for($i = 500;; $i++) {
            switch($type) {
                case 'Group':
                    $instance = Learngroup::find($i);
                    break;
                case 'Course':
                    $instance = Course::find($i);
                    break;
                case 'Catalog':
                    $instance = Catalog::find($i);
                    break;
                case 'Question':
                    $instance = Question::find($i);
                    break;
            }

            if($instance === null) {
                return $i;
            }
        }
    }


    /**
     * Checks if the catalog is saved as favorite
     *
     * @param  integer $catalogId The catalog id
     * @param  User    $user      The user instance
     * @return boolean
     */
    protected function isSavedAsFavorite($catalogId, $user = null) {
        if($user === null) {
            $userID = Sentry::getUser()->getId();
            $user = User::find($userID);
        }

        foreach($user->favorites()->get() as $favorite) {
            if($favorite->id === $catalogId) {
                return true;
            }
        }

        return false;
    }


    /**
     * Create a simple question with example question and answer
     * 
     * @param  string  $courseNumber   A course number to show the referenced course
     * @param  string  $groupNumber    A name of a group to show the referenced groups
     * @param  string  $questionNumber A course number to show the referenced course
     * @return Question                A Question instance
     */
    private function createSimpleQuestion($courseNumber, $groupName, $questionNumber) {
        $q = array(
            'question' => 'This is question ' . $questionNumber . ' of course ' . $courseNumber . ' - group ' . $groupName
        );

        $a = array(
            'answer'    => 'This is answer ' . $questionNumber . ' of course ' . $courseNumber . ' - group ' . $groupName
        );

        $question = new Question();
        $question->type = 'simple';
        $question->question = json_encode($q);
        $question->answer = json_encode($a);
        $question->learned = 'true';
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
    private function createMultipleQuestion($courseNumber, $groupName, $questionNumber) {
        $q = array(
            'question' => 'This is question ' . $questionNumber . ' of course ' . $courseNumber . ' - group ' . $groupName
        );
        //answer - choose from 0-3 
        $a = array(
            'answer'    => array(
                '2',
                '3'
            ),
            'choices'   => array(
                'This is answer 1',
                'This is answer 2',
                'This is answer 3',
                'This is answer 4'
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
    private function createClozeQuestion($courseNumber, $groupName, $questionNumber) {
        $q = array(
            'question'  => 'This is question ' . $questionNumber . ' of course ' . $courseNumber . ' - group ' . $groupName
        );

        $a = array(
            'answer'   => array(
                $questionNumber,
                'course',
                $groupName
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
    private function createDragDropQuestion($courseNumber, $groupName, $questionNumber) {
        $q = array(
            'question'  => 'This is question ' . $questionNumber . ' of course ' . $courseNumber . ' - group ' . $groupName
        );

        $a = array(
            'answer'    => 'This is answer 2',
            'choices'   => array(
                'This is answer 1',
                'This is answer 2',
                'This is answer 3',
                'This is answer 4'
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