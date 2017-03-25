<?php

namespace Controller;

/**
 * Base controller for handling requests.
 *
 * NOTE: These methods are defined here so sub-classes 
 * can always safely call parent::* without error.
 */
abstract class Controller
{
	public function __construct() {}
	public function before(array &$info){}
	public function after(array &$info){}
}
