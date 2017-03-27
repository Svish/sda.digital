<?php
namespace View;
use Config, Model, View, Mustache;

/**
 * Views using Mustache templates.
 */
class Front extends Template
{
	public function __construct()
	{
		$latest_added = Model::front()->latest_added();
		$latest_added = $this->r('list/content',
			['content_list' => $latest_added]);

		$latest_recorded = Model::front()->latest_recorded();
		$latest_recorded = $this->r('list/content',
			['content_list' => $latest_recorded]);

		parent::__construct(get_defined_vars());
	}

	private function r($template, $data)
	{
		return View::template($data, $template)
			->render('text/html');
	}
}
