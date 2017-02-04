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

	public function before(array &$info)
	{
		parent::before($info);

		//TODO: Allow Admin edit other users.
		// $_GET['email'] ?? current user?

		$this->me = $this->user;
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
