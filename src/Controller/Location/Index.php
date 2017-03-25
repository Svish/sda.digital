<?php

namespace Controller\Location;
use View, Model;


/**
 * Location index.
 */
class Index extends \Controller\Page
{
	public function get()
	{
		$location_list = Model::locations()->for_index();

		return View::template(get_defined_vars())
			->output();
	}
}
