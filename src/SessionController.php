<?php

/**
 * Takes care of session stuff.
 */
abstract class SessionController extends Controller
{
	public function before(array &$info)
	{
		parent::before($info);
		Session::start();
	}
}
