<?php

namespace Controller\User;
use HTTP, View, Message;
use HttpException;

/**
 * Handles user account.
 */
class Me extends \Controller\Admin
{
	private $me;

	public function __construct()
	{
		parent::__construct();
		$this->me = Model::users()->logged_in();
	}
	

	public function get()
	{
		return View::template(['me' => $this->me])
			->output();;
	}


	public function post()
	{
		try
		{
			$this->me
				->set($_POST)
				->save();
			Message::ok('saved');
			HTTP::redirect_self();
		}
		catch(HttpException $e)
		{
			return parent::error($e, ['me' => $this->me]);
		}
	}
}
