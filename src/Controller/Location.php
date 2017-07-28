<?php

namespace Controller;
use HTTP, Model, View;

/**
 * Handles Location pages.
 */
class Location extends Controller
{
	public function get($id, $slug = null)
	{
		if($id == 'new')
		{
			$template = 'location/new';
			$location = Model::locations()->get(null);
		}
		else
		{
			$template = 'location/location';
			$location = Model::locations()->for_page($id);

			if( ! $slug || $slug != $location->slug)
				HTTP::redirect($location->url);	
		}

		return View::layout(get_defined_vars(), $template)
			->output();
	}
}
