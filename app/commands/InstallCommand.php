<?php

use Illuminate\Console\Command;

class InstallCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'kakadu:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Set application key and create tables for kakadu';

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
		//key:generate
		$this->call('key:generate');
	}
}