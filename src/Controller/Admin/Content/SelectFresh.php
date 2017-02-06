<?php

namespace Controller\Admin\Content;
use HTTP, Session, View, Model;

/**
 * View and select new, fresh content to add.
 */
class SelectFresh extends \Controller\Admin
{
	protected $required_roles = ['editor'];

	public function post()
	{
		Model::freshness()->store_adding($_POST['files'] ?? []);
		HTTP::redirect_self('/../fill-out');
	}
}
