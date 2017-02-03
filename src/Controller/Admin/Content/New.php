<?php

/**
 * View and select new content to add.
 */
class Controller_Admin_Content_New extends Controller_Page
{
	protected $required_roles = ['editor'];


	public function get()
	{
		$files = Model::newFiles()->all();
		TemplateView::output(['groups' => $files]);
	}

	public function post()
	{
		Session::set('adding', $_POST['files'] ?? []);
		HTTP::redirect('admin/content/add');
	}
}
