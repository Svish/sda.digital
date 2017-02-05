<?php

namespace Error;
use View, HTTP, Message;

/**
 * Global error handler.
 */
class Handler
{
	public function __invoke(\Throwable $e = null)
	{
		// Wrap in HttpException if not already one
		if( ! $e instanceof HttpException)
			$e = new Internal($e);

		Message::exception($e);
		HTTP::set_status($e);
		View::template([
			'status' => $e->getHttpStatus(),
			'title' => $e->getHttpTitle(),
			'debug' => self::collect_xdebug($e),
			], 'error')
			->output();
	}

	private static function collect_xdebug(\Throwable $e = null)
	{
		if( ! $e) return null;

		$msg = isset($e->xdebug_message)
			? '<table class="xdebug">'.$e->xdebug_message.'</table>'
			: '<pre>'
				.'<b>'.$e->getMessage().'</b>'
				."\r\n\r\n"
				.$e->getTraceAsString()
				.'</pre>';

		return self::collect_xdebug($e->getPrevious()) . $msg;
	}

}
