<?php
namespace Error;

/**
 * Error view as Html.
 */
class Html extends \View\Template
{

	public function __construct(HttpException $e)
	{
		parent::__construct([
				'status' => $e->getHttpStatus(),
				'title' => $e->getHttpTitle(),
				'debug' => self::collect_xdebug($e),
			], 'error');
	}
	

	public static function collect_xdebug(\Throwable $e = null)
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
