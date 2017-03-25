<?php

namespace Controller;
use HTTP, Model, View;

/**
 * Handles Series pages.
 */
class Series extends Controller
{
	public function get($id, $slug = null)
	{
		if($id == 'new')
		{
			$template = 'series/new';
			$series = Model::series()->get(null);
		}
		else
		{
			$template = 'series/series';
			$series = Model::series()->for_page($id);

			if( ! $slug || $slug != $series->slug)
				HTTP::redirect($series->url);	
		}

		return View::template(get_defined_vars(), $template)
			->output();
	}
}
