<?php

namespace Controller;
use Model, View;

/**
 * Handles index.
 */
class Index extends Controller
{
	public function get()
	{
		return View::front()->output();
	}
}
