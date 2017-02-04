<?php

namespace Controller;
use Session as S;

/**
 * Takes care of session stuff.
 */
abstract class Session extends Controller
{
	public function before(array &$info)
	{
		parent::before($info);
		S::start();
	}
}
