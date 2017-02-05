<?php

namespace Controller\Admin\Content;
use HTTP, Session, View, Mime, ID3;

/**
 * Fill out and organize.
 */
class Fill extends \Controller\Admin
{
	protected $required_roles = ['editor'];

	public function post()
	{
		var_dump($_POST);exit;

		Model::freshness()->add($_POST);
		parent::get();
	}
}
