<?php

/**
 * Base class for controller handling multiple special pages.
 *
 * First parameter is expected to be name of action, and method names
 * should be action_method, e.g. login_post.
 */
abstract class Controller_MultiPage extends Controller_Page
{
	public function before(array &$info)
	{
		$info['method'] = "{$info['params'][1]}_{$info['method']}";
		$info['params'][1] = 'user/'.$info['params'][1];
			parent::before($info);
	}

}
