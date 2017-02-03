<?php

/**
 * Adding new content.
 */
class Controller_Admin_Content_Add extends Controller_Page
{
	protected $required_roles = ['editor'];

	public function get()
	{
		$files = Session::get('adding', []);

		if( ! $files)
			HTTP::redirect('admin/content/new');

		TemplateView::output(['adding' => $files]);
	}

	public function post()
	{
		var_dump($_POST);return;
		parent::get(null);
	}
}
