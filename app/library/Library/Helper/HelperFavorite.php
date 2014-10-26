<?php

class HelperFavorite {


    /**
     * Returns an array with all favorites of a given user
     * 
     * @param  User $userSentry An instance of a Sentry user
     * @return array            An array with the favorite courses and catalogs of the user
     */
    public static function getFavorites($userSentry) {
        //Get the user
        $userID = $userSentry->getId();
        $user = User::find($userID);

        //Get all favorites
        $favorites = $user->favorites()->get();

        //Sort the favorites
        $courses = array();
        $catalogs = array();

        foreach($favorites as $favorite) {
            if($favorite->parent === null) {
                //Course
                $course = $favorite->course()->first();
                $courses[] = array(
                        'id'            => $course->id,
                        'name'          => $course->name,
                        'description'   => $course->description,
                        'created_at'    => $course->created_at,
                        'updated_at'    => $course->updated_at
                    );
            } else {
                $course = HelperCourse::getCourseOfCatalog($favorite);

                //Catalog
                $catalogs[] = array(
                        'id'            => $favorite->id,
                        'name'          => $favorite->name,
                        'number'        => $favorite->number,
                        'created_at'    => $favorite->created_at,
                        'updated_at'    => $favorite->updated_at,

                        'course'    => array(
                                'id'            => $course->id,
                                'name'          => $course->name,
                                'description'   => $course->description,
                                'created_at'    => $course->created_at,
                                'updated_at'    => $course->updated_at
                        )
                    );
            }
        }


        $result = array(
                'courses'   => $courses,
                'catalogs'  => $catalogs
            );


        return $result;
    }

    /**
     * Returns an array with all favorites of a given user
     * 
     * @param  User $userSentry An instance of a Sentry user
     * @return array            An array with the favorite courses and catalogs of the user
     */
    public static function getFavoritesJSON($userSentry) {
        //Get the user
        $userID = $userSentry->getId();
        $user = User::find($userID);

        //Get all favorites
        $favorites = $user->favorites()->get();

        //Sort the favorites
        $courses = array();

        foreach($favorites as $favorite) {
            if($favorite->parent === null) {
                //Course
                $course = $favorite->course()->first();
                $data = HelperFavorite::computePercentage($userID, $course);
                $courses[] = array(
                        'id'                => $course->id,
                        'name'              => $course->name,
                        'iteration'         => $data['iteration'],
                        'percentage'        => $data['percentage'],
                        'quantity'          => $data['quantity'],
                        'description'       => $course->description,
                        'created_at'        => $course->created_at,
                        'updated_at'        => $course->updated_at
                    );
            }
        }

        $result = array(
                'courses'   => $courses,
            );

        return $result;
    }
    /**
     * Checks if a catalog is a favorite of a given user
     * 
     * @param  Catalog $catalog     A catalog instance
     * @param  User    $userSentry  The Sentry user
     * @return boolean
     */
    public static function isCatalogFavoriteOfUser($catalog, $userSentry) {
        $user_id = $userSentry->getId();
        $user = User::find($user_id);

        $favorites = $user->favorites()->where('catalog_id', '=', $catalog->id)->get();

        if(count($favorites) > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Checks if a parent catalog is a favorite of a given user
     * 
     * @param  Catalog $catalog     A catalog instance
     * @param  User    $userSentry  The Sentry user
     * @return boolean
     */
    public static function isParentCatalogFavoriteOfUser($catalog, $userSentry) {
        $user_id = $userSentry->getId();
        $user = User::find($user_id);

        while(($parent = $catalog->parent()->first()) !== null) {

            $favorites = $user->favorites()->where('catalog_id', '=', $parent->id)->get();

            if(count($favorites) > 0) {
                return true;
            }

            $catalog = $parent;
        }

        return false;
    }

    /**
     * Compute percentage of course
     * 
     * @param  Course $course 
     */
    public static function computePercentage($userId, $course) {
        
        $correctQuestions = 0;
        $percentage = 0;
        $iteration = 0;
        $tmpCourse = $course;
        $questionsArray = array();

        if($tmpCourse === null) {
            return Response::json(array(
                'code'      =>  404,
                'message'   =>  'Course not found'
                ), 
            404);
        }


        $catalog = $tmpCourse->catalog()->first();
        //Get all catalogs
        $catalogs = HelperCourse::getSubCatalogIDsOfCatalog($catalog);
        
        //Overall questions of course
        $overallQuestions = DB::table('catalog_questions')->whereIn('catalog_id', $catalogs)->count();

        if($overallQuestions === 0){
            return Response::json(array(
                'code'      =>  404,
                'message'   =>  'Course questions not found.'
                ), 
            404);
        }

        //get all catalogquestions
        $query = DB::table('catalog_questions')
              ->join('favorites', 'favorites.catalog_id', '=', 'catalog_questions.catalog_id')
              ->whereIn('favorites.catalog_id', $catalogs)
              ->where('favorites.user_id', '=', $userId);
        $questions = $query->get(array('catalog_questions.question_id as question_id'));


        foreach ($questions as $question) {
            array_push($questionsArray, $question->question_id);
        }

        //get iteration
        $iteration = DB::table('flashcards')->where('user_id', '=', $userId)->whereIn('question_id', $questionsArray)->min('number_correct');
        $numCorrect = DB::table('flashcards')->where('user_id', '=', $userId)->whereIn('question_id', $questionsArray)->sum('number_correct');
        $numIncorrect =  DB::table('flashcards')->where('user_id', '=', $userId)->whereIn('question_id', $questionsArray)->sum('number_incorrect');
        $numAnswered = $numIncorrect + $numCorrect;
        //if iteration is null, course was added as favorite, but no question was answered yet
        if($iteration === null){
            $iteration = 1;
        } else {
            $tmpIteration = $iteration - 1;
            $correctQuestions = 0;
            foreach($questions as $question){
                $correctQuestions += DB::table('flashcards')->where('user_id', '=', $userId)->where('question_id', '=', $question->question_id)->where('number_correct', '>', $tmpIteration)->count();
            }
            if($correctQuestions === $overallQuestions){
                $correctQuestions = 0;
                $iteration++;
                $tmpIteration = $iteration - 1;
                foreach($questions as $question){
                    $correctQuestions += DB::table('flashcards')->where('user_id', '=', $userId)->where('question_id', '=', $question->question_id)->where('number_correct', '>', $tmpIteration)->count();
                }
            }
        } 
        $percentage = ($correctQuestions/$overallQuestions)*100;   
        
        $result = array(
            'percentage'    => $percentage,
            'iteration'     => $iteration,
            'quantity'      => $overallQuestions,
            'numCorrect'    => $numCorrect,
            'numIncorrect'  => $numIncorrect,
            'numAnswered'   => $numAnswered,
        );
        
        return $result;
    }
}