<?php
namespace View;
use Config, Model, View, Mustache;

/**
 * Views using Mustache templates.
 */
class Front extends Layout
{
	public function __construct()
	{
		$latest_recorded = Model::front()->latest_recorded();
		$latest_recorded = $this->r('list/content',
			['content_list' => $latest_recorded]);

		parent::__construct(get_defined_vars());
	}

	private function r($template, $data)
	{
		return View::layout($data, $template)
			->render('text/html');
	}
}
