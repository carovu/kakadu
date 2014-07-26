<?php

class LearningController extends BaseKakaduController {


    /**
     * Shows the learning view for a course with the first question
     */
    public function getCourse($id) {
        
        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::view('error.404', array(), 404);
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::LEARN);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Check favorites
        $userSentry = Sentry::getUser();
        $catalog = $this->course->catalog()->first();
        if(!HelperFavorite::isCatalogFavoriteOfUser($catalog, $userSentry)) {
            return View::make('general.permission');
        }

        //Get all catalogs
        $catalogs = HelperCourse::getSubCatalogIDsOfCatalog($catalog);
        
        //Create view
        $this->layout->content = $this->getLearningView('course', $catalogs);
    }

    /**
     * Gives JSON data of a course with the first question back
     */
    public function getCourseJSON($id) {
        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::json(array(
                'code'      =>  404,
                'message'   =>  'Course not found'
                ), 
            404);
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::LEARN);

        if($permission !== ConstPermission::ALLOWED) {
            return Response::json(array(
                'code'      =>  404,
                'message'   =>  'You dont have permission'
                ), 
            404);
        }

        //Check favorites
        $userSentry = Sentry::getUser();
        $catalog = $this->course->catalog()->first();

        if(!HelperFavorite::isCatalogFavoriteOfUser($catalog, $userSentry)) {
            //Get the user and the favorites
            $user = User::find($this->user['id']);
            $favorites = $user->favorites()->get();

            //Save catalog as favorite
            $user->favorites()->attach($catalog);
        }

        //Get all catalogs
        $catalogs = HelperCourse::getSubCatalogIDsOfCatalog($catalog);
        
        //Get the next question
        $data = HelperFlashcard::getNextQuestion($userSentry, $catalogs);

        //No questions found
        if($data === false) {
            return Response::json(array(
                'code'      =>  404,
                'message'   =>  'question not found'
                ), 
            404);
        }

        $question = $data['question'];
        $questionType = QuestionType::getQuestionFromQuestion($question);

        $catalog = $data['catalog'];
        $course = HelperCourse::getCourseOfCatalog($catalog);

        //can send all this, but need just question for AngularJSClient
        //$response = array(
       //     'question' => $questionType->getViewElement(), 
       //     'course' => $this->getCourseArray($course), 
       //     'catalog' => $this->getCatalogArray($catalog),
       //     'section' => 'course'
      //  );

        $response = array(
            'status'    => '',
            'catalog'   => $catalog->id,
            'course'    => $course->id,
            'section'   => 'course'
        );
        $response = array_merge($response, $questionType->getViewElement());
        return Response::json($response);
    }

    /**
     * Shows the learning view for a catalog with the first question
     */
    public function getCatalog($id) {
        
        //Get catalog
        $catalog = Catalog::find($id);

        if($catalog === null) {
            return Response::view('error.404', array(), 404);
        }

        //Get course
        $this->course = HelperCourse::getCourseOfCatalog($catalog);

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::LEARN);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Check favorites
        $userSentry = Sentry::getUser();
        if(!HelperFavorite::isCatalogFavoriteOfUser($catalog, $userSentry)) {
            if(!HelperFavorite::isParentCatalogFavoriteOfUser($catalog, $userSentry)) {
                return View::make('general.permission');
            }
        }

        //Get all catalogs
        $catalogs = HelperCourse::getSubCatalogIDsOfCatalog($catalog);

        //Create view
        $this->layout->content = $this->getLearningView('catalog', $catalogs);
    }


    /**
     * Shows the learning view for all favorites
     */
    public function getFavorites() {
        
        //Check permissions
        $permission = $this->checkPermissions(ConstAction::LEARN);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }


        //Get user
        $user = User::find($this->user['id']);

        //Get favorite catalogs
        $catalogs = array();

        foreach ($user->favorites()->get() as $favorite) {
            $fav = HelperCourse::getSubCatalogIDsOfCatalog($favorite);
            $catalogs = array_merge($catalogs, $fav);
            $catalogs = array_unique($catalogs);
        }

        //Create view
        $this->layout->content = $this->getLearningView('favorites', $catalogs);
    }


    /**
     * Saves the answer of the last question and returns the next question as a JSON response
     */
    public function postNext() {
        //Validate input
        $rules = array(
            'question'          => 'required|integer',
            'course'            => 'integer',
            'catalog'           => 'integer',
            'answer'            => 'required|in:true,false',
            'section'           => 'required|in:course,catalog,favorites',         
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $errors = $validation->messages();
            return $this->getJsonErrorResponse($errors);
        }


        //Get question
        $question = Question::find(Input::get('question'));

        if($question === null) {
            return $this->getJsonErrorResponse(array(trans('question.question_not_found')));
        }


        //Get catalog and course
        $section = Input::get('section');

        if($section === 'course') {
            $course = Course::find(Input::get('course'));

            if($course === null) {
                return $this->getJsonErrorResponse(array(trans('question.course_not_found')));
            }

            $catalog = $course->catalog()->first();
            $check = HelperCourse::isQuestionPartOfCatalog($question, $catalog);
            
            if($check === false) {
                return $this->getJsonErrorResponse(array(trans('question.catalog_not_valid')));
            }

        } else if($section === 'catalog') {
            $catalog = Catalog::find(Input::get('catalog'));

            if($catalog === null) {
                return $this->getJsonErrorResponse(array(trans('question.catalog_not_found')));
            }

            $check = HelperCourse::isQuestionPartOfCatalog($question, $catalog);
            
            if($check === false) {
                return $this->getJsonErrorResponse(array(trans('question.catalog_not_valid')));
            }

            $this->course = HelperCourse::getCourseOfCatalog($catalog);
        }


        //Check permissions
        $permission = $this->checkPermissions(ConstAction::LEARN);

        if($permission !== ConstPermission::ALLOWED) {
            return $this->getJsonErrorResponse(array(trans('general.permission_denied')));
        }


        //Check favorites
        $userSentry = Sentry::getUser();

        if($section === 'course' || $section === 'catalog') {
            if(!HelperFavorite::isCatalogFavoriteOfUser($catalog, $userSentry)) {
                if($section === 'course' || !HelperFavorite::isParentCatalogFavoriteOfUser($catalog, $userSentry)) {
                    return $this->getJsonErrorResponse(array(trans('general.permission_denied')));
                }
            }
        }


        //Get all catalogs
        if($section === 'course' || $section === 'catalog') {
            $catalogs = HelperCourse::getSubCatalogIDsOfCatalog($catalog);

        } else if($section === 'favorites') {
            $user = User::find($this->user['id']);

            $catalogs = array();
            foreach ($user->favorites()->get() as $favorite) {
                $fav = HelperCourse::getSubCatalogIDsOfCatalog($favorite);
                $catalogs = array_merge($catalogs, $fav);
                $catalogs = array_unique($catalogs);
            }

            if(count($catalogs) <= 0) {
                return $this->getJsonErrorResponse(array(trans('question.no_question_found')));
            }
        }
        

        //Save the answer of the last question
        HelperFlashcard::saveFlashcard($userSentry, $question, Input::get('answer'), $catalogs);

        //Get a new question
        $data = HelperFlashcard::getNextQuestion($userSentry, $catalogs);

        if($data === false) {
            return $this->getJsonErrorResponse(array(trans('question.no_question_found')));
        }

        $question = $data['question'];
        $questionType = QuestionType::getQuestionFromQuestion($question);

        $catalog = $data['catalog'];
        $course = HelperCourse::getCourseOfCatalog($catalog);

        $response = array(
            'status'    => 'Ok',
            'catalog'   => $catalog->id,
            'course'    => $course->id
        );

        $response = array_merge($response, $questionType->getViewElement());

        return Response::json($response);
    }

    /**
     * Gets the learning view with the first question
     * @param  array $catalogs An array with all valid catalogs
     * @return View            The learning view with the first question
     */
    private function getLearningView($section, $catalogs) {
        //Get the next question
        $data = HelperFlashcard::getNextQuestion(Sentry::getUser(), $catalogs);

        //Create view
        $view = View::make('learning.question');

        //No questions found
        if($data === false) {
            $view->error = array(trans('question.no_question_found'));
            return $view;
        }

        $question = $data['question'];
        $questionType = QuestionType::getQuestionFromQuestion($question);

        $catalog = $data['catalog'];
        $course = HelperCourse::getCourseOfCatalog($catalog);

        $view->section = $section;
        $view->question = $questionType->getViewElement();
        $view->catalog = $this->getCatalogArray($catalog);
        $view->course = $this->getCourseArray($course);

        return $view;
    }

}