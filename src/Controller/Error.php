<?php

/**
 * Global error handler.
 */
class Controller_Error extends Controller_Page
{
	public function __invoke(Throwable $e = null)
	{
		if( ! $e instanceof HttpException)
			$e = new HttpException('Internal Server Error', 500, $e);

		HTTP::set_status($e->getHttpStatus());
		$this->get('error', [
			'status' => $e->getHttpStatus(),
			'title' => $e->getHttpTitle(),
			'message' => [
				'type' => 'error',
				'text' => $e->getMessage(),
				],
			'debug' => self::collect_xdebug($e),
			]);
	}

	private static function collect_xdebug(Throwable $e = null)
	{
		if( ! $e)
			return null;

		$msg = isset($e->xdebug_message) ? $e->xdebug_message : '<pre>'.$e->getMessage().'</pre>';

		return self::collect_xdebug($e->getPrevious()) . $msg;
	}

}
