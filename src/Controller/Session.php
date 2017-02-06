<?php

namespace Controller;
use Session as S;

/**
 * Takes care of session stuff.
 */
abstract class Session extends Controller
{
	public function __construct()
	{
		parent::__construct();
		S::start();
	}
}
