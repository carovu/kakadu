<?php

class ProfileController extends BaseController {


    /**
     * Show the site to edit the profile
     */
    public function getEdit() {
        $user = Sentry::getUser();
        $view = View::make('authentification.edit_profile');        

        $view->language = DB::table('users_metadata')->where('user_id', $user->getId())->first()->language;      

        //Get all accepted languages
        $view->languages = Config::get('app.languages_accepted');    

        $this->layout->content = $view;
    }



    /**
     * Edit the profile
     */
    public function postEdit() {

        $redirect_success = 'profile/edit';
        $redirect_error = 'profile/edit';


        //Validate input
        $rules = array(
            'displayname'   => 'required',
            'email'         => 'required|email',
            'language'      => 'required|max:2'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->messages();
            return $this->redirectWithErrors($redirect_error, $messages);
        }


        //Try to change profile settings
        try
        {
            $user = Sentry::getUser();
            $user->email = trim(Input::get('email'));
            $update = $user->save();
            DB::table('users_metadata')->where('user_id', $user->getId())->update(array('displayname' => trim(Input::get('displayname'))));
            DB::table('users_metadata')->where('user_id', $user->getId())->update(array('language' => Input::get('language')));

            if ($update) {
                Session::put('my.locale', Input::get('language'));
                return Redirect::route($redirect_success)->with('info', trans('profile.profile_change_success'));
            } else {
                $messages = array(trans('profile.profile_change_failure'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }

    /**
     * Edit the profile for AJS client
     */
    public function postEditJSON() {

        //Validate input
        $rules = array(
            'displayname'   => 'required',
            'email'         => 'required|email',
            'language'      => 'required|max:2'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            return Response::json(array(
                'code'      =>  401,
                'message'   =>  'Validation failed.'
                ), 
            401);   
        }


        //Try to change profile settings
        try
        {
            $user = Sentry::getUser();
            $user->email = trim(Input::get('email'));
            $update = $user->save();
            DB::table('users_metadata')->where('user_id', $user->getId())->update(array('displayname' => trim(Input::get('displayname'))));
            DB::table('users_metadata')->where('user_id', $user->getId())->update(array('language' => Input::get('language')));

            if ($update) {
                Session::put('my.locale', Input::get('language'));
                return Response::json('Change of user information successful.'); 
            } else {
                return Response::json(array(
                    'code'      =>  401,
                    'message'   =>  'Update failed.'
                    ), 
                401);   
            }
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            return Response::json(array(
            'code'      =>  404,
            'message'   =>  'User not found.'
            ), 
            404);
        }
    }


    /**
     * Change the password
     */
    public function postChangepassword() {

        $redirect_success = 'profile/edit';
        $redirect_error = 'profile/edit';


        //Validate input
        $rules = array(
            'password_old'  => 'required',
            'password'      => 'required|confirmed'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->messages();
            return $this->redirectWithErrors($redirect_error, $messages);
        }


        //Try to change the password
        try
        {
            $user = Sentry::getUser();
            $user->password = Input::get('password');
            if ($user->save()) {
                return Redirect::route($redirect_success)->with('info', trans('profile.password_change_success'));
            } else {
                $messages = array(trans('profile.password_change_failure'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }

    /**
     * Change the password for AJS client
     */
    public function postChangepasswordJSON() {

        //Validate input
        $rules = array(
            'password_old'  => 'required',
            'password'      => 'required|confirmed'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            return Response::json(array(
                'code'      =>  401,
                'message'   =>  'Validation failed.'
                ), 
            401);   
        }

        $user = Sentry::getUser();
        if($user->checkPassword(Input::get('password_old'))){
            //Try to change the password
            try
            {    
                $user->password = Input::get('password');
                if ($user->save()) {
                    return Response::json('Change of password successful.');
                } else {
                    return Response::json(array(
                        'code'      =>  401,
                        'message'   =>  'Update failed.'
                        ), 
                    401);   
                }
            }
            catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
            { 
                return Response::json(array(
                'code'      =>  404,
                'message'   =>  'User not found.'
                ), 
                404);
            }
        }else{
            return Response::json(array(
                'code'      =>  401,
                'message'   =>  'Old password does not match with your current password.'
                ), 
            401);  
        }

    }
    /**
     * Show question if really delete the profile
     */
    public function getDelete() {
        $this->layout->content = View::make('authentification.delete_profile');
    }


    /**
     * Delete the user with all his data
     */
    public function deleteDelete() {
        
        $redirect_success = 'home';
        $redirect_error = 'profile/edit';

        $userSentry = Sentry::getUser();
        $userKakadu = User::find($userSentry->getId());

        //Delete all related data
        DB::table('favorites')->where('id', $userKakadu)->delete(); 
        DB::table('flashcards')->where('id', $userKakadu)->delete(); 

        //Delete all learngroups, where user is the only admin
        $role = Role::where('name', 'LIKE', 'admin')->first();

        foreach($userKakadu->learngroups()->get() as $group) {
            $pivot = $group->users();
            $number = $pivot->where('role_id', '=', $role->id)->count();

            if($number !== null && $number > 1) {
                //Delete learngroup with courses
                HelperGroup::deleteGroupAndCheckRelatedCourses($group);
            }
        }

        //Try to delete the user
        try
        {
            if ($userSentry->delete()) {
                Sentry::logout();
                return Redirect::route($redirect_success)->with('info', trans('profile.profile_delete_success'));
            } else {
                $messages = array(trans('profile.profile_delete_failure'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }

    /**
     * Delete the user with all his data for AJS client
     */
    public function postDeleteJSON() {

        $userSentry = Sentry::getUser();
        $userKakadu = User::find($userSentry->getId());

        //Delete all related data
        DB::table('favorites')->where('user_id', $userSentry->id)->delete(); 
        DB::table('favorite_questions')->where('user_id', $userSentry->id)->delete(); 
        DB::table('users_metadata')->where('user_id', $userSentry->id)->delete(); 
        DB::table('flashcards')->where('user_id', $userSentry->id)->delete(); 

        //Delete all learngroups, where user is the only admin
        $role = Role::where('name', 'LIKE', 'admin')->first();

        foreach($userKakadu->learngroups()->get() as $group) {
            $pivot = $group->users();
            $number = $pivot->where('role_id', '=', $role->id)->count();

            if($number !== null && $number > 1) {
                //Delete learngroup with courses
                HelperGroup::deleteGroupAndCheckRelatedCourses($group);
            }
        }

        //Try to delete the user
        try
        {
            if ($userSentry->delete()) {
                Sentry::logout();
                return Response::json('Delete profile successful.');
            } else {
                return Response::json(array(
                    'code'      =>  401,
                    'message'   =>  'User delete failed.'
                    ), 
                401);   
            }
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            return Response::json(array(
            'code'      =>  404,
            'message'   =>  'User not found.'
            ), 
            404);
        }
    }
}
