<?php

namespace Controller;
use HTTP, Model, View;

/**
 * Handles Person pages.
 */
class Person extends Controller
{
	public function get($id, $slug = null)
	{
		if($id == 'new')
		{
			$template = 'person/new';
			$person = Model::persons()->get(null);
		}
		else
		{
			$template = 'person/person';
			$person = Model::persons()->for_page($id);

			if( ! $slug || $slug != $person->slug)
				HTTP::redirect($person->url);	
		}

		return View::template(get_defined_vars(), $template)
			->output();
	}
}
