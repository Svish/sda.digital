<?php

/**
 * Handles user account.
 */
class Controller_User_Me extends Controller_Admin
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
		$x = ['me' => $this->me];

		if(isset($_GET['saved']))
			$x += Msg::ok('saved');

		if(isset($_GET['reset']))
			$x += Msg::ok('reset_done');

		return TemplateView::output($x);
	}


	public function post()
	{
		try
		{
			$this->me
				->set($_POST)
				->save();
			HTTP::redirect_self('?saved');
		}
		catch(ValidationException $e)
		{
			return parent::error($e, ['me' => $this->me]);
		}
	}
}
