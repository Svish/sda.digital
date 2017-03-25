<?php

namespace Controller\Person;
use View, Model;


/**
 * Person index.
 */
class Index extends \Controller\Page
{
	public function get()
	{
		$person_list = Model::persons()->for_index();

		return View::template(get_defined_vars())
			->output();;
	}
}
