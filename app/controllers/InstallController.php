<?php

class InstallController extends Controller {

    protected $layout = 'layouts.install';


    public function __construct(){
        
        //csrf
        $this->beforeFilter('csrf', array('on' => 'post'));
    }


    /**
     * Display the install screen.
     */
    public function getIndex() {
         return View::make('install.index');
    }


    /**
     * Make the installation
     */
    public function postInstall() {

        $redirect_success = 'install/finished';
        $redirect_error = 'install';

        //Validate input
        $rules = array(
            'user_displayname'  => 'required',
            'user_email'        => 'required|email',
            'user_password'     => 'required|confirmed',

            'db_host'           => 'required',
            'db_database'       => 'required',
            'db_username'       => 'required',
            'db_password'       => 'required|confirmed',
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->messages();
            return Redirect::route($redirect_error)->withErrors($messages)->withInput();
        }

        //Get database settings
        $host = Input::get('db_host');
        $database = Input::get('db_database');
        $username = Input::get('db_username');
        $password = Input::get('db_password');

        //Set database runtime settings
        $connections = Config::get('database.connections'); //get array from application/config/kakadu/database.php

        $mysql = $connections['mysql']; //get array with mysql settings
        $mysql['host'] = $host; //rewrite settings for database.php 
        $mysql['database'] = $database;
        $mysql['username'] = $username;
        $mysql['password'] = $password;
        $connections['mysql'] = $mysql;

        Config::set('database.connections', $connections); //set configuration with new connections

        //Save database settings in file
        $content = '<?php return array(\'connections\' => ' . var_export($connections, true) . ');';
        $content = preg_replace('/array \(/', 'array(', $content);
        File::put(base_path(). '/app/config/database_kakadu.php', $content);

        //Run artisan commands
        $displayname = Input::get('user_displayname');
        $email = Input::get('user_email');
        $password = Input::get('user_password');

        //Suppress output
        ob_start();

        //Run commands
        Artisan::call('kakadu:install');
        Artisan::call('kakadu:setup', array('displayname' => $displayname, 'email' => $email, 'password' => $password));
        //Get suppressed output
        $output = ob_get_clean();

        return Redirect::route($redirect_success);
    }


    /**
     * Display the finished install screen.
     */
    public function getFinished() {
         return View::make('install.finished');
    }

}