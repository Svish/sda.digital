<?php

namespace Controller;
use HTTP, Model, View;

/**
 * Handles Content pages.
 */
class Content extends Controller
{

	public function get($id, $slug = null)
	{
		switch($id)
		{
			case 'my-fresh':
				$template = 'content/my-fresh';
				$content_list = Model::fresh()->mine();
				break;

			case 'new':
				throw new \Error\PageNotFound();

			default:
				$template = 'content/content';
				$content = Model::content()->for_page($id);

				if( ! $slug || $slug != $content->slug)
					HTTP::redirect($content->url);
		}

		return View::template(get_defined_vars(), $template)
			->output();
	}

	public function post()
	{
		switch($_POST['action'] ?? null)
		{
			case 'delete':
				Model::fresh()->forget_mine();
				HTTP::redirect_self();

			default:
				throw new PageNotFound();
		}
	}
}
