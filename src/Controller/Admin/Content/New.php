<?php

/**
 * Handles user account.
 */
class Controller_Admin_Content_New extends Controller_Page
{
	protected $required_roles = ['editor'];


	public function get($url = null, $context = [])
	{
		$files = Model::newFiles()->all();
		//var_dump($files);return;

		parent::get($url, $context + ['groups' => $files]);
	}

	public function post()
	{
		Session::set('adding', $_POST['files'] ?? []);
		HTTP::redirect('admin/content/add');
	}
}
