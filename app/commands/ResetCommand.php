<?php

use Illuminate\Console\Command;

class ResetCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'kakadu:reset';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrate:reset for kakadu';

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
        //migrate:reset
        $this->call('migrate:reset');
	}

}