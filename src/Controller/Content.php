<?php

namespace Controller;
use HTTP, Model, View;

/**
 * Handles Content pages.
 */
class Content extends Controller
{
	const TEMPLATE = 'content/content';

	public function get($id, $slug = null)
	{
		$content = Model::content()->for_page($id);

		if( ! $slug || $slug != $content->slug)
			HTTP::redirect($content->url);

		return View::template(get_defined_vars(), self::TEMPLATE)
			->output();
	}
}
