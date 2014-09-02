<?php

class AuthentificationController extends BaseController {

    /**
     * Show the login site
     */
    public function getLogin() {
        return View::make('authentification.login');
    }



    /**
     * Log the user in
     */
    public function postLogin() {
        //Validate input
        $rules = array(
            'email'         => 'required|email',
            'password'      => 'required'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->messages();
            return Redirect::back()->withErrors($messages)->withInput();
        }

        //Try to log the user in
        try
        {   
            $user = Sentry::findUserByLogin(Input::get('email'));
            if(Input::get('email') === $user->getLogin() && $user->checkPassword(Input::get('password'))){
                if(Input::get('remember')){
                    Sentry::loginAndRemember($user);    
                } else {
                    Sentry::login($user, true);
                }

            }
            if (Sentry::check()) {
                //Set language
                $language = DB::table('users_metadata')
                        ->where('user_id', $user->getId())
                        ->first()->language;
                Cookie::forever('language', $language);
                return Redirect::back();
            } else {
                $messages = array(trans('authentification.email_or_password_not_correct'));
                return Redirect::back()->withErrors($messages)->withInput();
            }
        }
        catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            $messages = array($e->getMessage());
            return Redirect::back()->withErrors($messages)->withInput();
        }
        catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            $messages = array($e->getMessage());
            return Redirect::back()->withErrors($messages)->withInput();
        }
        catch (Cartalyst\Sentry\Users\UserExistsException $e)
        {
            $messages = array($e->getMessage());
            return Redirect::back()->withErrors($messages)->withInput();
        }
        catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            $messages = array($e->getMessage());
            return Redirect::back()->withErrors($messages)->withInput();
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $messages = array($e->getMessage());
            return Redirect::back()->withErrors($messages)->withInput();
        }
    }

    /**
     * Log the user in from your AngularJS Client
     */
    public function postLoginJSON() {
        //Validate input
        $rules = array(
            'email'         => 'required|email',
            'password'      => 'required'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            return Response::json(array(
            'code'      =>  400,
            'message'   =>  'Email required.'
            ), 
            400);
        }
        try 
        {
            $user = Sentry::findUserByLogin(Input::get('email'));
            if(Input::get('email') === $user->getLogin() && $user->checkPassword(Input::get('password'))){
                Sentry::loginAndRemember($user); 
            }
            if (Sentry::check()) {
                //send this information back for userprofile
                $array = array(
                    'id'            => Sentry::getUser()->id,
                    'displayname'   => DB::table('users_metadata')->where('user_id', Sentry::getUser()->id)->pluck('displayname'),
                    'email'         => Input::get('email'),
                    'language'      => DB::table('users_metadata')->where('user_id', Sentry::getUser()->id)->pluck('language')

                );
                return Response::json($array);
            } else {
                return Response::json(array(
                'code'      =>  400,
                'message'   =>  'You have given a wrong password.'
                ), 
                400);
            }
        }
        catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            return Response::json(array(
            'code'      =>  404,
            'message'   =>  'Login required.'
            ), 
            404);
        }
        catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            return Response::json(array(
            'code'      =>  404,
            'message'   =>  'Password required.'
            ), 
            404);
        }
        catch (Cartalyst\Sentry\Users\UserExistsException $e)
        {
            return Response::json(array(
            'code'      =>  404,
            'message'   =>  'User already exists.'
            ), 
            404);
        }
        catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            return Response::json(array(
            'code'      =>  404,
            'message'   =>  'User not activated.'
            ), 
            404);
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
     * Log the user out
     */
    public function getLogout() {
        Sentry::logout();
        return Redirect::route('home');
    }

    /**
     * Log the user out for AngularJS client
     */
    public function getLogoutJSON() {
        if(Sentry::check()){
            Sentry::logout();
        } else {
            return Response::json(array(
                'code'      =>  401,
                'message'   =>  'User not logged in.'
                ), 
            401);      
        }
      
    }

    /**
     * Show the registration site
     */
    public function getRegister() {
        $this->layout->content = View::make('authentification.register');
    }



    /**
     * Register the user
     */
    public function postRegister() {
        
        $redirect_success = 'auth/confirmemail';
        $redirect_error = 'auth/register';


        //Validate input
        $rules = array(
            'displayname'   => 'required',
            'email'         => 'required|email',
            'password'      => 'required|confirmed'
        );

        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            $messages = $validation->messages();
            return $this->redirectWithErrors($redirect_error, $messages);
        }


        //Try to register the user
        try
        {
            // create the user
            $user = Sentry::register(array(
                'email'     => trim(Input::get('email')),
                'password'  => Input::get('password'),
                'permissions' => array('admin' => 1),
            ));

            DB::table('users_metadata')->insert(array(
                'user_id'       => $user->getId(),
                'displayname'   => Input::get('displayname'),
                'language'      => 'en'
                ));

            $group = Sentry::findGroupByName('admin');
            $user->addGroup($group);

            if ($user) {
                //Sending activation link
                $link = URL::to('auth/activate/' . $user['hash']);
        
                $mailer = new PHPMailer;
                
                //no configurationfile in package -> set config here
                $mailer->setFrom('no-reply@uibk.ac.at','Kakadu');
                $mailer->isSMTP();                                   
                $mailer->Host = 'localhost';  
                

                $mailer->addAddress(Input::get('email'));
                $mailer->Subject  = 'Kakadu - ' . trans('authentification.activation_subject');
                $mailer->Body     = trans('authentification.activation_message') . $link;
                $mailer->send();
                
                return Redirect::route($redirect_success);
            } else {
                $messages = array(trans('authentification.registration_faild'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
        catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
        catch (Cartalyst\Sentry\Users\UserExistsException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
        catch (Exception $e)
        {
            $messages = array(trans('mail.message_not_send') . $e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }


    /**
     * Register the user for AJS, because sending activationemail does not work, we will leave it out completly
     */
    public function postRegisterJSON() {
        
        //Validate input
        $rules = array(
            'displayname'   => 'required',
            'email'         => 'required|email',
            'password'      => 'required|confirmed'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->messages();
            return Response::json(array(
                'code'      =>  400,
                'message'   =>  'Your password or email is wrong.'
                ), 
            400);
        }


        //Try to register the user
        try
        {
            // create the user
            $user = Sentry::createUser(array(
                'email'     => trim(Input::get('email')),
                'password'  => Input::get('password'),
                'permissions' => array('admin' => 1),
                'activated' => true,
            ));

            DB::table('users_metadata')->insert(array(
                'user_id'       => $user->getId(),
                'displayname'   => Input::get('displayname'),
                'language'      => 'en'
                ));

            $group = Sentry::findGroupByName('admin');
            $user->addGroup($group);
        }
        catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            return Response::json(array(
            'code'      =>  404,
            'message'   =>  'Login required.'
            ), 
            404);
        }
        catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            return Response::json(array(
            'code'      =>  404,
            'message'   =>  'Password required.'
            ), 
            404);
        }
        catch (Cartalyst\Sentry\Users\UserExistsException $e)
        {
            return Response::json(array(
            'code'      =>  404,
            'message'   =>  'User already exists.'
            ), 
            404);
        }
    }

    /**
     * Activate the user
     * @param  [type] $email Decoded email
     * @param  [type] $code  Activation code
     */
    public function getActivate($email = null, $code = null) {

        $redirect_success = 'auth/activate';
        $redirect_error = 'auth/activate';

        //Check if email and code exist
        if($email === null || $code === null) {
            $this->layout->content = View::make('authentification.activate');
            return;
        }

        //Try to activate the user
        try
        {   //$activate_user = Sentry::activate_user($email, $code);
            if ($$user->attemptActivation($code)) {
                return Redirect::route($redirect_success)->with('info', trans('authentification.activation_success'));
            } else {
                $messages = array(trans('authentification.activation_faild'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
        catch (Cartalyst\Sentry\Users\UserAlreadyActivatedException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }



    /**
     * Dispaly the message that an email was send to confirm the registration
     */
    public function getConfirmemail() {
        $this->layout->content = View::make('authentification.confirmemail');
    }



    /**
     * Show the site to reset the password if the user forgot it
     */
    public function getForgotpassword() {
        $this->layout->content = View::make('authentification.forgotpassword');
    }



    /**
     * Reset the password and send the new password to the email address
     */
    public function postForgotpassword() {
        
        $redirect_success = 'auth/forgotpassword';
        $redirect_error = 'auth/forgotpassword';

        //Validate input
        $rules = array(
            'email'         => 'required|email'
        );

        $validation = Validator::make(Input::all(), $rules);
        
        if ($validation->fails()) {
            $messages = $validation->messages();
            return $this->redirectWithErrors($redirect_error, $messages);
        }


        //Try to set a new password
        try
        {
            $new_password = Str::random(32);

            //Update user
            $user->email = Input::get('email');
            $user->password = $new_password;

            if ($user->save()) {
                $mailer = new PHPMailer;
                $mailer->addAddress(Input::get('email'));
                $mailer->Subject  = 'Kakadu - ' . trans('authentification.password_reset_subject');
                $mailer->Body     = trans('authentification.password_reset_message') . $new_password;
                $mailer->send();

                return Redirect::route($redirect_success)
                                ->with('info', trans('authentification.password_reset_success'));
            } else {
                $messages = array(trans('authentification.password_reset_faild'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Cartalyst\Sentry\Users\UserExistsException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
        catch (Exception $e)
        {
            $messages = array(trans('mail.message_not_send'));
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }

}