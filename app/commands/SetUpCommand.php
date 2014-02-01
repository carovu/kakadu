<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class SetUpCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'kakadu:setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create admin for kakadu in installprocess';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
        $displayname = $this->argument('displayname');
        $email = $this->argument('email');
        $password = $this->argument('password');

        //migrate --package=cartalyst/sentry
        $this->call('migrate', array('--package' => 'Cartalyst/Sentry'));
        //migrate
        $this->call('migrate');
        
        //Logout and reset sentry cache
        Sentry::logout();
		try
		{
	        $user = Sentry::createUser(array(
				'email'     => $email,
				'password'  => $password,
				'activated' => true,
				'permissions' => array('admin' => 1)
			));

			DB::table('users_metadata')->insert(array(
				'user_id'		=> $user->getId(),
				'displayname'   => $displayname,
				'language'      => 'en'
				));

			$group = Sentry::findGroupByName('admin');
	        $user->addGroup($group);
	    }
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
		{
		    echo 'Login field is required.';
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
		{
		    echo 'Password field is required.';
		}
		catch (Cartalyst\Sentry\Users\UserExistsException $e)
		{
		    echo 'User with this login already exists.';
		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
		    echo 'Group was not found.';
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('displayname', InputArgument::REQUIRED, 'Displayname of created Admin'),
			array('email', InputArgument::REQUIRED, 'Email of created Admin'),
			array('password', InputArgument::REQUIRED, 'Passwordd of created Admin'),
		);
	}

}