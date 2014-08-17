<?php

class CourseController extends BaseKakaduController {

    private $rules = array(
                            'name'          => 'required|min:5|max:100',
                            'description'   => 'required|min:50|max:500'
                        );


    /**
     * Shows all courses with pagination.
     * 
     * On a ajax request just the list will be returned
     *
     * GET variables:
     * - sort: name, id, created_at (Sorting value)
     * - sort_dir: asc, desc (Sorting direction)
     * - per_page: number (20) (Items per page)
     * - page: number (Actuall page)
     */
    public function getCourses() {

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::ALL);

        if($permission === ConstPermission::DENIED) {
            return View::make('general.permission');
        }

        //Create view
        $view = $this->getListOfCourses();

        if(Request::ajax()) {
            return $view;
        } else {
            $this->layout->content = $view;
        }
        
    }

    /**
     * Shows all courses in JSON format.
     * 
     * On a ajax request just the list will be returned
     */
    public function getCoursesJSON() {

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
                                'courses.percentage',
                                'courses.created_at',
                                'courses.updated_at',
                                'courses.catalog',
                                'favorites.user_id',
                        )); 
     
        return Response::json($courses);    
    }

    /**
     * Shows all courses in JSON format.
     * 
     * On a ajax request just the list will be returned
     *
     * GET variables:
     * - sort: name, id, created_at (Sorting value)
     * - sort_dir: asc, desc (Sorting direction)
     * - per_page: number (20) (Items per page)
     * - page: number (Actuall page)
     */
    public function resetCoursePercentageJSON($id) {
        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::json(array(
                'code'      =>  404,
                'message'   =>  'Course not found.'
                ), 
            404);
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::LEARN);

        if($permission !== ConstPermission::ALLOWED) {
            return Response::json(array(
                'code'      =>  401,
                'message'   =>  'You dont have permission.'
                ), 
            401);
        }

        $catalog = $this->course->catalog()->first();
        //Get all catalogs
        $catalogs = HelperCourse::getSubCatalogIDsOfCatalog($catalog);

        //Get all questions
        $query = DB::table('questions')
              ->whereIn('catalogs.id', $catalogs)
              ->join('catalog_questions', 'catalog_questions.question_id', '=', 'questions.id')
              ->join('catalogs', 'catalog_questions.catalog_id', '=', 'catalogs.id')
              ->groupBy('questions.id');
        $questions = $query->get(array('questions.id as question_id','questions.learned as question_learned'));

        foreach($questions as $question){
            $changeQuestion = Question::find($question->question_id);
            $changeQuestion->learned = 'false';
            $changeQuestion->save();
        }

        //Get user data
        $userSentry = Sentry::getUser();
        $data = HelperFavorite::getFavorites($userSentry);
        $courses = array_fetch($data['courses'], 'id');
        //compute percentage
        foreach($courses as $id){
            HelperCourse::computePercentage(Course::find($id));
        }
        $data = HelperFavorite::getFavorites($userSentry);
        return Response::json($data['courses']);
        
    }
    /**
     * Search all courses with a given text
     */
    public function getSearch() {
    
        //Check permissions
        $permission = $this->checkPermissions(ConstAction::SEARCH);

        if($permission === ConstPermission::DENIED) {
            return View::make('general.permission');
        }

        //Validate
        if(Input::get('search') === '') {
            $search = false;
        } else {
            $search = Input::get('search');
        }

        //Create view
        $view = $this->getListOfCourses($search);

        if(Request::ajax()) {
            return $view;
        } else {
            $this->layout->content = $view;
        }
    }

    /**
     * Search all courses with a given text for AJS client
     */
    public function getSearchJSON() {
    
        //Validate
        if(Input::get('search') === '') {
            $search = false;
        } else {
            $search = Input::get('search');
        }

        //Get user id
        $userID = null;

        if($this->user !== null) {
            $userID = $this->user['id'];
        }

        //Query
        $table = DB::table('courses');

        if($search !== false) {
            $text = '%' . $search . '%';
            $table->where('courses.name', 'LIKE', $text)
                  ->orWhere('courses.description', 'LIKE', $text);
        }

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
                        ->paginate(20, array(
                                'courses.id',
                                'courses.name',
                                'courses.description',
                                'courses.percentage',
                                'courses.created_at',
                                'courses.updated_at',
                                'courses.catalog',
                                'favorites.user_id',
                        ));


        return Response::json($courses);
        
    }

    private function getListOfCourses($search = false) {
        
        //Set up the options
        $append = array();

        if(Input::has('sort')) {
            $sort = Input::get('sort');
            $append['sort'] = $sort;
        } else {
            $sort = 'name';
        }

        if(Input::has('sort_dir')) {
            $sort_dir = Input::get('sort_dir');
            $append['sort_dir'] = $sort_dir;
        } else {
            $sort_dir = 'asc';
        }

        if(Input::has('per_page')) {
            $per_page = Input::get('per_page');
            $append['per_page'] = $per_page;
        } else {
            $per_page = 20;
        }

        if($search !== false) {
            $append['search'] = $search;
        }


        //Get user id
        $userID = null;

        if($this->user !== null) {
            $userID = $this->user['id'];
        }

        //Query
        $table = DB::table('courses');

        if($search !== false) {
            $text = '%' . $search . '%';
            $table->where('courses.name', 'LIKE', $text)
                  ->orWhere('courses.description', 'LIKE', $text);
        }

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

        //Create view
        $tmp = array();

        foreach($courses as $course) {
            $favorite = ($course->user_id !== null);
            $tmp[] = $this->getCourseArray($course, $favorite);
        }


        if(Request::ajax()) {
            $view = View::make('course.list');
        } else {
            $view = View::make('course.courses');
        }


        $view->courses = $tmp;
        $view->links = $courses->appends($append)->links();

        return $view;
    }


    /**
     * Shows a course
     */
    public function getCourse($id) {

        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::view('error.404', array(), 404);
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::SHOW);

        if($permission === ConstPermission::DENIED) {
            return View::make('general.permission');
        }

        //Get groups
        $groups = $this->course->learngroups()->orderBy('name', 'ASC')->get();
        $learngroups = array();

        foreach($groups as $group) {
            $learngroups[] = $this->getGroupArray($group);
        }

        //Check favorite
        $favorite = false;
        $catalog = $this->course->catalog()->first();

        if($this->user !== null) {
            $userSentry = Sentry::getUser();
            $favorite = HelperFavorite::isCatalogFavoriteOfUser($catalog, $userSentry);
        }
        
        //Create view
        $view = View::make('course.course');
        $this->layout->content = $view;
        $view->course = $this->getCourseArray($this->course, $favorite);
        $view->groups = $learngroups;

        //Catalog tree structure
        $view->catalogs = HelperCourse::getTreeStructureOfCatalog($catalog);        
    }



    /**
     * Shows the view to create a course
     */
    public function getCreate() {

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::CREATE);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Create view
        $this->layout->content = View::make('course.create');
    }


    /**
     * Create a course
     */
    public function postCreate() {

        $redirect_success = 'course';
        $redirect_error = 'course/create';

        //Validate input
        $response = $this->validateInput($this->rules);

        if ($response !== true) {
            return $this->redirectWithErrors($redirect_error, $response);
        }

        //Get groups
        $groupIDs = array();
        $groups = array();

        if(is_array(Input::get('groups'))) {
            foreach(Input::get('groups') as $g) {
                $group = Learngroup::find($g);

                if($group === null) {
                    $messages = array(trans('group.group_not_found'));
                    return $this->redirectWithErrors($redirect_error, $messages);
                }

                $groupIDs[] = $group->id;
                $groups[] = $group;
            }
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::CREATE);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Chech if user is system admin or admin of learngroups
        if($this->role !== ConstRole::ADMIN) {
            $role = Role::where('name', 'LIKE', 'admin')->first();

            foreach($groups as $group) {
                $pivot = $group->users();
                $allocation = $pivot->where('user_id', '=', $this->user['id'])->first();

                if($allocation === null || $allocation->role_id !== $role->id) {
                    $messages = array(trans('group.user_not_admin'));
                    return $this->redirectWithErrors($redirect_error, $messages);
                }
            }
        }

        //Create the catalog
        $catalog = new Catalog;
        $catalog->name = Input::get('name');
        $catalog->save();

        //Create the course
        $course = new Course;
        $course->name = Input::get('name');
        $course->description = Input::get('description');
        $course->catalog = $catalog->id;
        $course->save();

        $course->learngroups()->sync($groupIDs);

        return Redirect::route($redirect_success, array($course->id));
    }


    /**
     * Shows the view to edit a course
     */
    public function getEdit($id) {
        
        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::view('error.404', array(), 404);
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::EDIT);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Get groups
        $groups = $this->course->learngroups()->orderBy('name', 'ASC')->get();
        $learngroups = array();

        foreach($groups as $group) {
            $learngroups[] = array(
                'id'            => $group->id,
                'name'          => $group->name,
                'description'   => $group->description,
                'created_at'    => $group->created_at,
                'updated_at'    => $group->updated_at
            );
        }

        //Create view
        $view = View::make('course.edit');
        $this->layout->content = $view;
        $view->course = $this->getCourseArray($this->course);
        $view->groups = $learngroups;
    }


    /**
     * Edit a course
     */
    public function postEdit() {

        $redirect_success = 'course';
        $redirect_error = 'course/edit';

        //Validate input
        $this->rules['id'] = 'required|integer|min:0';
        $response = $this->validateInput($this->rules);

        if ($response !== true) {
            $parameters = array(Input::get('id'));
            return $this->redirectWithErrors($redirect_error, $response, $parameters);
        }

        //Get course
        $this->course = Course::find(Input::get('id'));

        if($this->course === null) {
            return Response::view('error.404', array(), 404);
        }

        //Get groups
        $groupIDs = array();
        $groups = array();

        if(is_array(Input::get('groups'))) {
            foreach(Input::get('groups') as $g) {
                $group = Learngroup::find($g);

                if($group === null) {
                    $messages = array(trans('group.group_not_found'));
                    $parameters = array($this->course->id);
                    return $this->redirectWithErrors($redirect_error, $messages, $parameters);
                }

                $groupIDs[] = $group->id;
                $groups[] = $group;
            }
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::EDIT);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Chech if user is system admin or admin of new learngroups
        if($this->role !== ConstRole::ADMIN) {
            $role = Role::where('name', 'LIKE', 'admin')->first();
            $currentGroupIDs = DB::table('learngroup_courses')->lists('id');
            foreach($groups as $group) {
                if(in_array($group->id, $currentGroupIDs)) {
                    continue;
                }
                
                $pivot = $group->users();
                $allocation = $pivot->where('user_id', '=', $this->user['id'])
                                    ->first();

                if($allocation === null || $allocation->role_id !== $role->id) {
                    $messages = array(trans('group.user_not_admin'));
                    $parameters = array($this->course->id);
                    return $this->redirectWithErrors($redirect_error, $messages, $parameters);
                }
            }
        }

        //Delete favorites of old groups
        if(count($groupIDs) > 0) {
            $oldGroups = $this->course->learngroups()->whereNotIn('learngroups.id', $groupIDs)->get();
        } else {
            $oldGroups = $this->course->learngroups()->get();
        }

        HelperGroup::deleteFavoritesOfLearngroupMembers($this->course, $groups, $oldGroups);

        //Edit the course
        $this->course->name = Input::get('name');
        $this->course->description = Input::get('description');
        $this->course->save();

        $this->course->learngroups()->sync($groupIDs);

        //Edit the description of the catalog
        $catalog = $this->course->catalog()->first();
        $catalog->name = Input::get('name');
        $catalog->save();


        return Redirect::route($redirect_success, array($this->course->id));
    }
    

    /**
     * Delete a course
     */
    public function getDelete($id) {

        $redirect_success = 'courses';

        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::view('error.404', array(), 404);
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::DELETE);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Delete all questions
        $catalog = $this->course->catalog()->first();
        HelperCourse::removeQuestionsOfSubCatalogs($catalog);
        
        //Delete course and catalog
        $this->course->delete();
        $catalog->delete();

        return Redirect::route($redirect_success);
    }


    /**
     * Show the view that the course was deleted
     */
    public function getDeleted() {
        return View::make('course.deleted');
    }


    /**
     * Validate input with the given rules
     * 
     * @return array|boolean Returns a error array when there is validation error or true on a valid validation
     */
    private function validateInput($rules) {
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            return $validation->messages();
        }

        return true;
    }
}