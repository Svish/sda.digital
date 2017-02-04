<?php

namespace Controller\Admin\Content;
use HTTP, Session, View, Model;

/**
 * View and select new content to add.
 */
class Fresh extends \Controller\Admin
{
	protected $required_roles = ['editor'];


	public function get()
	{
		$files = Model::newFiles()->all();
		View::template(['groups' => $files])->output();
	}

	public function post()
	{
		Session::set('adding', $_POST['files'] ?? []);
		HTTP::redirect('admin/content/add');
	}
}
