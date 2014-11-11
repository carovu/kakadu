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
        
        $iterations = array();
        $quantity = 0;
        $tmpQuestions = 0;
        $tmpCourse = $course;
        $maxIteration = 0;
        $zeroPercentage = 0;
        $onePercentage = 0;
        $threePercentage = 0;

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
        $quantity = (int) DB::table('catalog_questions')->whereIn('catalog_id', $catalogs)->count();
        
        if($quantity === 0){
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

        $questionsArray = DB::table('catalog_questions')
                ->join('favorites', 'favorites.catalog_id', '=', 'catalog_questions.catalog_id')
                ->whereIn('favorites.catalog_id', $catalogs)
                ->where('favorites.user_id', '=', $userId)
                ->lists('catalog_questions.question_id');

        $maxIteration = DB::table('flashcards')->whereIn('question_id', $questionsArray)->where('user_id', '=', $userId)->max('number_correct');

        //get informationsfield when clicking the i in the client
        for ($i = 0; $i <= $maxIteration; $i++) {
            $anwseredQuestion = 0;

            if($i === 0){
                //if question has not been answered yet, the question does not exist in the flashcard table
                foreach($questions as $question){
                    $anwseredQuestion += DB::table('flashcards')->where('user_id', '=', $userId)->where('question_id', '=', $question->question_id)->count();
                }
                $anwseredQuestion = $quantity - $anwseredQuestion;
                $zeroPercentage = ($anwseredQuestion/$quantity)*100;
            }else{
                foreach($questions as $question){
                    $anwseredQuestion += DB::table('flashcards')->where('user_id', '=', $userId)->where('question_id', '=', $question->question_id)->where('number_correct', '>=', $i)->count();
                }
                if($i === 1){
                    $onePercentage = ($anwseredQuestion/$quantity)*100;
                }elseif($i === 3){
                    $threePercentage = ($anwseredQuestion/$quantity)*100;
                }
            }
            
            $tmpArray = array(
                'iteration'         => $i,
                'anwseredQuestion'  => $anwseredQuestion,
                'percentage'        => ($anwseredQuestion/$quantity)*100,
            );
            array_push($iterations, $tmpArray);
        }

        $result = array(
            'iterations'        => $iterations,
            'quantity'          => $quantity,
            'zeroPercentage'    => $zeroPercentage,
            'onePercentage'     => $onePercentage,
            'threePercentage'   => $threePercentage
        );

        return $result;
    }     
    /**
     * Compute percentage of catalogs
     * 
     * @param  Array $catalogs 
     */
    public static function computePercentageCatalogs($userId, $catalogs) {
        
        $iterations = array();
        $quantity = 0;
        $tmpQuestions = 0;
        $maxIteration = 0;
        $zeroPercentage = 0;
        $onePercentage = 0;
        $threePercentage = 0;
        
        //Overall questions of course
        foreach($catalogs as $catalog){
            $quantity += (int) DB::table('catalog_questions')->where('catalog_id', $catalog)->count();
        }
        
        if($quantity === 0){
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

        $questionsArray = DB::table('catalog_questions')
                ->join('favorites', 'favorites.catalog_id', '=', 'catalog_questions.catalog_id')
                ->whereIn('favorites.catalog_id', $catalogs)
                ->where('favorites.user_id', '=', $userId)
                ->lists('catalog_questions.question_id');

        $maxIteration = DB::table('flashcards')->whereIn('question_id', $questionsArray)->where('user_id', '=', $userId)->max('number_correct');

        //get informationsfield when clicking the i in the client
        for ($i = 0; $i <= $maxIteration; $i++) {
            $anwseredQuestion = 0;

            if($i === 0){
                //if question has not been answered yet, the question does not exist in the flashcard table
                foreach($questions as $question){
                    $anwseredQuestion += DB::table('flashcards')->where('user_id', '=', $userId)->where('question_id', '=', $question->question_id)->count();
                }
                $anwseredQuestion = $quantity - $anwseredQuestion;
                $zeroPercentage = ($anwseredQuestion/$quantity)*100;
            }else{
                foreach($questions as $question){
                    $anwseredQuestion += DB::table('flashcards')->where('user_id', '=', $userId)->where('question_id', '=', $question->question_id)->where('number_correct', '>=', $i)->count();
                }
                if($i === 1){
                    $onePercentage = ($anwseredQuestion/$quantity)*100;
                }elseif($i === 3){
                    $threePercentage = ($anwseredQuestion/$quantity)*100;
                }
            }
  
            $tmpArray = array(
                'iteration'         => $i,
                'anwseredQuestion'  => $anwseredQuestion,
                'percentage'        => ($anwseredQuestion/$quantity)*100,
            );
            array_push($iterations, $tmpArray);
        }

        $result = array(
            'iterations'        => $iterations,
            'quantity'          => $quantity,
            'zeroPercentage'    => $zeroPercentage,
            'onePercentage'     => $onePercentage,
            'threePercentage'   => $threePercentage
        );

        return $result;
    }  
}