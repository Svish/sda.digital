<?php

/**
 * Handles user account.
 */
class Controller_User_Me extends Controller_Page
{
	protected $require_roles = ['login'];


	public function get($url = null, $context = [])
	{
		if( ! $this->user)
			HTTP::redirect('user/login?url='.urlencode(ltrim($info['path'], '/')));

		if(isset($_GET['saved']))
			return parent::get($this->path, Msg::ok('saved'));

		if(isset($_GET['reset']))
			return parent::get($this->path, Msg::ok('reset_done'));

		return parent::get($this->path, $context);
	}



	public function post()
	{
		if( ! $this->user)
			HTTP::redirect('user/login?url='.urlencode(ltrim($info['path'], '/')));


		try
		{
			$this->user
				->set($_POST)
				->save();
			HTTP::redirect('user/me?saved');
		}
		catch(ValidationException $e)
		{
			return parent::get($this->path, $this->error('save_fail', $e));
		}
	}
}
