<?php

namespace View\Helper;
use Session, Message, Mustache;

/**
 * Returns HTML for current messages.
 */
class Messages
{
	public function __invoke()
	{
		$m = Session::unget(Message::SESSION_KEY, []);
		return Mustache::engine()
			->render('messages', ['list' => $m]);
	}
}
