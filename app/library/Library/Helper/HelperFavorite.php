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
                HelperFavorite::computePercentage($userID, $course);
                $percentage = DB::table('favorites')->where('user_id', $userID)->where('catalog_id', $course->id)->pluck('percentage');
                $quantity = DB::table('catalog_questions')->where('catalog_id', $course->id)->count();
                $courses[] = array(
                        'id'            => $course->id,
                        'percentage'    => $percentage,
                        'name'          => $course->name,
                        'quantity'      => $quantity,
                        'description'   => $course->description,
                        'created_at'    => $course->created_at,
                        'updated_at'    => $course->updated_at
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
        $overallQuestions = 0;
        $correctQuestions = 0;
        $tmpCourse = $course;

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

        //Get all questionsid of favorite catalog of user
        $query = DB::table('favorite_questions')
              ->whereIn('favorite_questions.catalog_id', $catalogs)
              ->where('favorite_questions.user_id', $userId);
        $questions = $query->get(array('favorite_questions.question_id as question_id', 'favorite_questions.learned as question_learned'));

        foreach($questions as $question){
            $overallQuestions += 1;
            if($question->question_learned === 'true'){
                $correctQuestions += 1;
            }
        }
       
        DB::table('favorites')->where('user_id', $userId)->where('catalog_id', $tmpCourse->id)->update(array('percentage' => ($correctQuestions/$overallQuestions)*100));

    }
}