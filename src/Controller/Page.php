<?php

/**
 * Handles normal pages.
 */
class Controller_Page extends SecureController
{
	public function get()
	{
		return TemplateView::output();
	}


	protected function error(HttpException $e, array $context = [])
	{
		HTTP::set_status($e->getHttpStatus());

		$context += Msg::exception($e);
		if($e instanceof ValidationException)
			$context += ['errors' => array_map('array_values', $e->getErrors())];

		return TemplateView::output($context);
	}
}
