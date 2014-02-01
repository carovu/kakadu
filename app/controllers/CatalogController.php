<?php

class CatalogController extends BaseKakaduController {
    
    private $catalog;

    private $rules = array(
                        'course'        => 'required|integer|min:0',
                        'name'          => 'required|min:5|max:100',
                        'number'        => 'required|integer|min:0',
                        'parent'        => 'required|integer|min:0'
                    );


    /**
     * Shows a catalog
     */
    public function getCatalog($id) {
        
        //Get catalog
        $this->catalog = Catalog::find($id);

        if($this->catalog === null) {
            return Response::view('error.404', array(), 404);
        }

        //Get course
        $this->course = HelperCourse::getCourseOfCatalog($this->catalog);
        //Check permissions
        $permission = $this->checkPermissions(ConstAction::SHOW);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Check favorite
        $favorite = false;
        $learning = false;

        if($this->user !== null) {
            $userSentry = Sentry::getUser();
            $favorite = HelperFavorite::isCatalogFavoriteOfUser($this->catalog, $userSentry);

            if($favorite === true) {
                $learning = true;
            } else {
                $learning = HelperFavorite::isParentCatalogFavoriteOfUser($this->catalog, $userSentry);
            }
        }
        
        //Create view
        $view = View::make('catalog.catalog');
        $this->layout->content = $view;
        $view->course = $this->getCourseArray($this->course);
        $view->catalog = $this->getCatalogArray($this->catalog, $favorite, $learning);

        //Sub catalogs
        $tmp = array();
        $subcatalogs = $this->catalog->children()->orderBy('number', 'asc')->get();

        foreach($subcatalogs as $subcatalog) {
            $tmp[] = $this->getCatalogArray($subcatalog);
        }

        $view->subcatalogs = $tmp;

        //Questions
        $tmp = array();
        $questions = $this->catalog->questions()->get();

        foreach($questions as $question) {
            //Get catalogs
            $catalogIDs = array();

            foreach($question->catalogs as $catalog) {
                $catalogIDs[] = $catalog->id;
            }

            //QuestionType
            $questionType = QuestionType::getQuestionFromQuestion($question);
            $questionViewElement = $questionType->getViewElement();
            $questionViewElement['catalogs'] = $catalogIDs;

            $tmp[] = $questionViewElement;
        }

        $view->questions = $tmp;

        //All catalogs of course
        $catalog = $this->course->catalog()->first();
        $view->catalogs = HelperCourse::getSubCatalogsOfCatalogWithIndent($catalog);
    }


    /**
     * Show the view to create a catalog
     * @param integer $id Course id
     */
    public function getCreate($id) {
        
        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::view('error.404', array(), 404);
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::CREATE);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Create view
        $view = View::make('catalog.create');
        $this->layout->content = $view;
        $view->course = $this->getCourseArray($this->course);

        //All catalogs of course
        $catalog = $this->course->catalog()->first();
        $view->catalogs = HelperCourse::getSubCatalogsOfCatalogWithIndent($catalog);
    }


    /**
     * Create a catalog
     */
    public function postCreate() {
        $redirect_success = 'catalog';
        $redirect_error = 'catalog/create';

        //Validate input
        $response = $this->validateInput($this->rules);

        if ($response !== true) {
            $parameters = array(Input::get('course'));
            return $this->redirectWithErrors($redirect_error, $response, $parameters);
        }

        //Check if catalogs are part of the course
        $parent = Catalog::find(Input::get('parent'));

        if($parent === null) {
            $messages = array(trans('catalog.parent_not_found'));
            $parameters = array(Input::get('course'));
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }
    
        if(HelperCourse::isCatalogPartOfCourse($parent, $this->course) === false) {
            $messages = array(trans('catalog.parent_not_subcatalog_of_course'));
            $parameters = array(Input::get('course'));
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::CREATE);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }
        
        $number = Input::get('number');
        $max_number = Catalog::where('parent', '=', $parent->id)->max('number');
        if($number > $max_number) {
            $number = $max_number + 1;
        } else {
            $children = $parent->children()
                               ->where('number', '>=', $number)
                               ->orderBy('number', 'asc')
                               ->get();

            foreach($children as $child) {
                $child->number++;
                $child->save();
            }
        }
        
        //Save catalog
        $catalog = new Catalog();
        $catalog->name = Input::get('name');
        $catalog->number = Input::get('number');
        $catalog->parent = Input::get('parent');
        $catalog->save();

        return Redirect::route($redirect_success, array($catalog->id));
    }


    /**
     * Show the view to edit a catalog
     * @param  integer $id Catalog id
     */
    public function getEdit($id) {

        //Get catalog
        $this->catalog = Catalog::find($id);

        if($this->catalog === null) {
            return Response::view('error.404', array(), 404);
        }

        //Get course
        $this->course = HelperCourse::getCourseOfCatalog($this->catalog);

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::EDIT);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Create view
        $view = View::make('catalog.edit');
        $this->layout->content = $view;
        $view->course = $this->getCourseArray($this->course);
        $view->catalog = $this->getCatalogArray($this->catalog);

        //All catalogs of course
        $catalog = $this->course->catalog()->first();
        $view->catalogs = HelperCourse::getSubCatalogsOfCatalogWithIndent($catalog, $this->catalog);
    }


    /**
     * Edit a catalog
     */
    public function postEdit() {

        $redirect_success = 'catalog';
        $redirect_error = 'catalog/edit';


        //Validate input
        $this->rules['id'] = 'required|integer|min:0';
        $response = $this->validateInput($this->rules);

        if ($response !== true) {
            $parameters = array(Input::get('id'));
            return $this->redirectWithErrors($redirect_error, $response, $parameters);
        }

        //Get catalog
        $this->catalog = Catalog::find(Input::get('id'));

        if($this->catalog === null) {
            return Response::view('error.404', array(), 404);
        }

        //Check if catalogs are part of the course
        $parent = Catalog::find(Input::get('parent'));

        if($parent === null) {
            $messages = array(trans('catalog.parent_not_found'));
            $parameters = array(Input::get('course'));
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        if(HelperCourse::isCatalogPartOfCourse($parent, $this->course) === false) {
            $messages = array(trans('catalog.parent_not_subcatalog_of_course'));
            $parameters = array(Input::get('id'));
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        if(HelperCourse::isCatalogPartOfCourse($this->catalog, $this->course) === false) {
            $messages = array(trans('catalog.catalog_not_subcatalog_of_course'));
            $parameters = array(Input::get('id'));
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        //Check if catalog is not parent catalog
        if($this->course->catalog === $this->catalog->id) {
            $messages = array(trans('catalog.catalog_not_editable'));
            $parameters = array(Input::get('id'));
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::EDIT);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Close gab from old number
        $old_parent = $this->catalog->parent()->first();
        $old_number = $this->catalog->number;

        foreach($old_parent->children()->where('number', '>', $old_number)->get() as $children) {
            $children->number--;
            $children->save();
        }

        //Check number
        $number = Input::get('number');
        $max_number = Catalog::where('parent', '=', $parent->id)->max('number');

        if($number > $max_number) {
            $number = $max_number + 1;
        } else {
            $children = $parent->children()
                               ->where('number', '>=', $number)
                               ->orderBy('number', 'asc')
                               ->get();

            foreach($children as $child) {
                $child->number++;
                $child->save();
            }
        }

        //Save catalog
        $this->catalog->name = Input::get('name');
        $this->catalog->number = $number;
        $this->catalog->parent = Input::get('parent');
        $this->catalog->save();


        return Redirect::route($redirect_success, array($this->catalog->id));
    }


    /**
     * Delete a catalog
     * 
     * @param  [int] $id The id of the catalog
     */
    public function getDelete($id) {

        $redirect_success = 'course';
        $redirect_error = 'catalog';

        //Check catalog
        $this->catalog = Catalog::find($id);

        if($this->catalog === null) {
            return Response::view('error.404', array(), 404);
        }

        //Get course
        $this->course = HelperCourse::getCourseOfCatalog($this->catalog);

        //Check if catalog is not parent catalog
        if($this->course->catalog === $this->catalog->id) {
            $messages = array(trans('catalog.catalog_not_editable'));
            $parameters = array($id);
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        //Check permissions
        $permission = $this->checkPermissions(ConstAction::DELETE);

        if($permission !== ConstPermission::ALLOWED) {
            return View::make('general.permission');
        }

        //Move questions to parent catalog
        $parent = $this->catalog->parent()->first();
        $parent_questions = $parent->questions()->get();
        $questions = $this->catalog->questions()->get();

        foreach ($questions as $question) {
            $exists = false;

            foreach ($parent_questions as $parent_question) {
                if($question->id === $parent_question->id) {
                    $exists = true;
                    break;
                }
            }

            if($exists === false) {
                $question->catalogs()->attach($parent);
            }
        }

        //Delete catalog
        $this->catalog->delete();

        return Redirect::route($redirect_success, array($this->course->id));
    }


    /**
     * Show the view that the catalog was deleted
     */
    public function getDeleted() {
        $view = View::make('catalog.deleted');
        $this->layout->content = $view;
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

        //Check if course and catalogs exist
        $parent = Catalog::find(Input::get('parent'));

        if($parent === null) {
            $message = trans('catalog.parent_not_found');
            return array($message);
        }

        $this->course = Course::find(Input::get('course'));

        if($this->course === null) {
            $message = trans('catalog.course_not_found');
            return array($message);
        }

        return true;
    }

}
