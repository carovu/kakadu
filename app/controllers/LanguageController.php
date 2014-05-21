<?php

class LanguageController extends BaseController {


    /**
     * Save the language of the guest
     */
    public function postEdit() {
        $redirect_success = 'home';
        $redirect_error = 'home';

        //Validate input
        $rules = array(
            'language'      => 'required|max:2'
            );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->messages();
            return $this->redirectWithErrors($redirect_error, $messages);
        }

        //Check if language exists
        $language = Input::get('language');
        $accepted_languages = Config::get('app.languages_accepted');

        foreach($accepted_languages as $key => $value) {
            if($language === $key) {
                Session::put('my.locale', $language);
                if(Sentry::check()) {
                    //Change the language in the user profile
                    try {
                        $user = Sentry::getUser();
                        DB::table('users_metadata')
                        ->where('user_id', $user->getId())
                        ->update(array('language' => $language));
                    } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                        $messages = array($e->getMessage());
                        return $this->redirectWithErrors($redirect_error, $messages);
                    }
                }
                return Redirect::back();
            }
        }

        $messages = array(trans('language.language_not_found'));
        return $this->redirectWithErrors($redirect_error, $messages);
    }

}