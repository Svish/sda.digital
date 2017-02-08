<?php

namespace Controller\Admin\Content;
use View, Model;

/**
 * Methods for content adding.
 */
class Api extends \Controller\Api
{
	protected $required_roles = ['editor'];

	public function get_fresh_files()
	{
		return Model::freshness()->get_fresh();
	}

	public function get_selected_files()
	{
		return Model::freshness()->get_selected();
	}
}
