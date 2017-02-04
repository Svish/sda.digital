<?php

namespace Controller;
use HTTP, View, Message, HttpException, ValidationException;

/**
 * Handles normal pages.
 */
class Page extends Secure
{
	public function get()
	{
		return View::template()->output();
	}


	protected function error(HttpException $e, array $context = [])
	{
		HTTP::set_status($e);
		Message::exception($e);

		if($e instanceof ValidationException)
			$context += ['errors' => array_map('array_values', $e->getErrors())];

		return View::template($context)->output();;
	}
}
