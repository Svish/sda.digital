<?php

namespace Controller\User;
use HTTP, Model, View, Message;

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
			$x = $this->me
				->set($_POST)
				->save();
			Message::ok($x ? 'saved' : 'no-changes');
			HTTP::redirect_self();
		}
		catch(\Error\HttpException $e)
		{
			return parent::error($e, ['me' => $this->me]);
		}
	}
}
