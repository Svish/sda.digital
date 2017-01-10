<?php

/**
 * Controller for protected member area.
 */
class Controller_Tools_MemberEditor extends Controller_Tools
{
	public function before(array &$info)
	{
		parent::before($info);
		var_dump($info);
	}

	public function get($id = null, $context = [])
	{
		var_dump(get_defined_vars());exit;
	}
}
