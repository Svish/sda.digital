<?php

/**
 * Handles user account.
 */
class Controller_Admin_Content_Add extends Controller_Page
{
	protected $required_roles = ['editor'];


	public function get($url = null, $context = [])
	{
		$adding = Session::get('adding', []);
		var_dump($adding);return;
		parent::get($url, $context + ['adding' => $adding]);
	}

	public function post()
	{
		var_dump($_POST);return;
		parent::get(null);
	}
}
