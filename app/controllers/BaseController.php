<?php

class BaseController extends Controller {

    protected $layout = 'layouts.master';
    protected $user;
    protected $role;


    public function __construct(){
        //csrf
        $this->beforeFilter('csrf', array('on' => 'post'));
        Asset::add('jquery', 'js/jquery-1.8.2.js');
        Asset::add('underscore', 'js/underscore-min.js');
        Asset::add('backbone', 'js/backbone-min.js');
        Asset::add('bootstrap', 'js/bootstrap.js');
        Asset::add('bootbox', 'js/bootbox.js');
        Asset::add('jquery-ui', 'js/jquery-ui-1.10.0.js');
        Asset::add('sidebar', 'js/sidebar.js');
        Asset::add('cutString', 'js/cutString.js');

    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if ( ! is_null($this->layout))
        {   
            $this->layout = View::make($this->layout);
        }
    }


    public function beforeFilter($filter, array $options = array())
    {   parent::beforeFilter($filter, $options);

        //User not logged in
        if(Sentry::check() === FALSE) {
            $this->user = NULL;
            $this->role = ConstRole::GUEST;
        } else {
            //Get user informations
            $user = Sentry::getUser();
            $this->user = array();
            $this->user['id'] = $user->getId();
            $this->user['email'] = $user->getLogin();
            View::share('displayname', DB::table('users_metadata')->where('user_id', $user->getId())->first()->displayname);
            //Get the role in the system
            if($user->hasAccess('admin')) {
                $this->role = ConstRole::ADMIN;
            } else {
                $this->role = ConstRole::USER;
            }
        }
        View::share('user', $this->user);
        View::share('roleSystem', $this->role);
        View::share('roleLearngroup', $this->role);

    }
    /**
     * Redirect to the destination route with an error message 
     * @param  string $destinationRoute
     * @param  array  $message
     * @param  array  $parameters
     * @return Response                   
     */
    protected function redirectWithErrors($destinationRoute, $messages, $parameters = array()) {
        return Redirect::route($destinationRoute, $parameters)->withErrors($messages)->withInput();
    }


    /**
     * Returns a json response with the ok messages
     * @return Response      Response
     */
    protected function getJsonOkResponse() {
        $response = array(
            'status'    => 'Ok'
            );

        return Response::json($response);
    }


    /**
     * Returns a json response with the given info messages
     * @param  array $infos Info messages
     * @return Response      Response
     */
    protected function getJsonInfoResponse($infos) {
        $response = array(
            'status'    => 'Info',
            'messages'  => $infos
            );

        return Response::json($response);
    }
    

    /**
     * Returns a json response with the given error messages
     * @param  array $errors Error messages
     * @return Response      Response
     */
    protected function getJsonErrorResponse($errors) {
        $response = array(
            'status'    => 'Error',
            'errors'    => $errors
            );

        return Response::json($response);
    }

}