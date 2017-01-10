<?php

/**
 * Tool for editing members.
 */
class Controller_Tools_Members extends Controller_Tools
{
	public function before(array &$info)
	{
		parent::before($info);
	}

	public function get($id = null, $context = [])
	{
		var_dump(get_defined_vars());exit;
	}
}
