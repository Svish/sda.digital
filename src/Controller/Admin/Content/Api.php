<?php

namespace Controller\Admin\Content;
use Session, View, Model;

/**
 * Methods for content adding.
 */
class Api extends \Controller\Api
{
	protected $required_roles = ['editor'];

	public function get_fresh()
	{
		return Model::freshness()->get_fresh();
	}

	public function get_adding()
	{
		return Model::freshness()->get_adding();
	}
}
