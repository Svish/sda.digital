<?php

namespace Controller\Series;
use View, Model;


/**
 * Location index.
 */
class Index extends \Controller\Page
{
	public function get()
	{
		$series_list = Model::series()->for_index();

		return View::layout(get_defined_vars())
			->output();;
	}
}
