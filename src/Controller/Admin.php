<?php

/**
 * Base controller for admin area.
 */
class Controller_Admin extends Controller_Page
{
	public function before(array &$info)
	{
		parent::before($info);

		if( ! $this->user)
			HTTP::redirect('user/login?url='.urlencode(ltrim($info['path'], '/')));
	}
}
