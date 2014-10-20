<?php 

class FavoriteController extends BaseKakaduController {


    private $catalog = null;

    private $rules = array(
                            'id'    => 'required|integer',
                            'type'  => 'required|in:course,catalog'
                        );



    /**
     * Shows the view with all favorites of a user
     */
    public function getFavorites() {
        //Get user data
        $userSentry = Sentry::getUser();
        $data = HelperFavorite::getFavorites($userSentry);

        //Create view
        $view = View::make('favorite.favorites');
        $this->layout->content = $view;
        $view->courses = $data['courses'];
        $view->catalogs = $data['catalogs'];
    }

    /**
     * Shows the response with all favorites of a user 
     */
    public function getFavoritesJSON() {
        //Get user data
        $userSentry = Sentry::getUser();
        $data = HelperFavorite::getFavoritesJSON($userSentry);
        return Response::json($data['courses']);
    }

    /**
     * Adds a course or a catalog to the favorite list of a user
     */
    public function postAdd() {
        $response = $this->checkInputAndPermissions();

        if($response !== true) {
            return $response;
        }

        //Get the user and the favorites
        $user = User::find($this->user['id']);
        $favorites = $user->favorites()->get();

        //Check if catalog is already a favorite
        foreach($favorites as $favorite) {
            if($this->catalog->id === $favorite->id) {
                return $this->getJsonInfoResponse(array(trans('profile.allready_favorite')));
            }
        }
        
        //Save catalog as favorite
        $user->favorites()->attach($this->catalog);

        return $this->getJsonOkResponse();
    }

    /**
     * Adds a course or a catalog to the favorite list of a user for AJSclient
     */
    public function postAddJSON() {
        $response = $this->checkInputAndPermissions();

        if($response !== true) {
            return $response;
        }

        //Get the user and the favorites
        $user = User::find($this->user['id']);
        $favorites = $user->favorites()->get();

        //save favoritequestions for percentage
        //Save catalog as favorite
        $user->favorites()->attach($this->catalog);

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::ALL);

        if($permission === ConstPermission::DENIED) {
            return Response::json(array(
                'code'      =>  401,
                'message'   =>  'Courses not allowed to see.'
                ), 
            401);      
        }
        
        //Get user id
        $userID = null;

        if($this->user !== null) {
            $userID = $this->user['id'];
        }

        if(Input::has('sort')) {
            $sort = Input::get('sort');
        } else {
            $sort = 'name';
        }

        if(Input::has('sort_dir')) {
            $sort_dir = Input::get('sort_dir');
        } else {
            $sort_dir = 'asc';
        }

        if(Input::has('per_page')) {
            $per_page = Input::get('per_page');
        } else {
            $per_page = 20;
        }

        //Query
        $table = DB::table('courses');

        $courses = $table->join('catalogs', 'courses.catalog', '=', 'catalogs.id')
                        ->leftJoin('favorites', function($join) use($userID) {
                            $join->on('catalogs.id', '=', 'favorites.catalog_id');

                            if($userID === null) {
                                $parameter = DB::raw('null');
                            } else {
                                $parameter = DB::raw($userID);
                            }

                            $join->on('favorites.user_id', '=', $parameter);
                        })
                        ->orderBy('courses.' . $sort, $sort_dir)
                        ->paginate($per_page, array(
                                'courses.id',
                                'courses.name',
                                'courses.description',
                                'courses.created_at',
                                'courses.updated_at',
                                'courses.catalog',
                                'favorites.user_id',
                        ));
        return Response::json($courses);  
    }
    /**
     * Removes a course or a catalog to the favorite list of a user
     */
    public function postRemove() {
        $response = $this->checkInputAndPermissions();

        if($response !== true) {
            return $response;
        }

        //Get the user and the favorites
        $user = User::find($this->user['id']);

        //Remove catalog as favorite
        $user->favorites()->detach($this->catalog);

        return $this->getJsonOkResponse();
    }

    /**
     * Removes a course or a catalog to the favorite list of a user for AJS client
     */
    public function postRemoveJSON() {
        $response = $this->checkInputAndPermissions();

        if($response !== true) {
            return $response;
        }

        //Get the user and the favorites
        $user = User::find($this->user['id']);

        //Remove catalog as favorite
        $user->favorites()->detach($this->catalog);

        //Get user data
        $userSentry = Sentry::getUser();
        $data = HelperFavorite::getFavoritesJSON($userSentry);
        return Response::json($data['courses']);
    }

    /**
     * Check the input and the permissions
     * 
     * @return Response|boolean Returns a error response with the given message or true on a valid check
     */
    private function checkInputAndPermissions() {
        //Validate input
        $validation = $this->validateInput($this->rules);

        if($validation !== true) {
            return $validation;
        }

        //Get catalog
        $id = Input::get('id');
        $type = Input::get('type');

        if($type === 'course') {
            $this->course = Course::find($id);

            if($this->course === null) {
                return $this->getJsonErrorResponse(array(trans('catalog.course_not_found')));
            }

            $this->catalog = $this->course->catalog()->first();
        } else {
            $this->catalog = Catalog::find($id);

            if($this->catalog === null) {
                return $this->getJsonErrorResponse(array(trans('catalog.catalog_not_found')));
            }

            $this->course = HelperCourse::getCourseOfCatalog($this->catalog);
        }


        //Check permissions
        $permission = $this->checkPermissions(ConstAction::FAVORITE);

        if($permission !== ConstPermission::ALLOWED) {
            return $this->getJsonErrorResponse(array(trans('general.permission_denied')));
        }

        return true;
    }


    /**
     * Validate input with the given rules
     * 
     * @return Response|boolean Returns a error response when there is a validation error or true on a valid validation
     */
    private function validateInput($rules) {
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $errors = $validation->messages();
            return $this->getJsonErrorResponse($errors);
        } else {
            return true;
        }
    }

}
