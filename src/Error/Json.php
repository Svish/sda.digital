<?php
namespace Error;
use Security;

/**
 * Error view as Json.
 */
class Json extends \View\Json
{
	use \WinPathFix;

	public function __construct(HttpException $e)
	{
		$message = new \View\Helper\Messages;
		$data = [
			'status' => $e->getHttpStatus(),
			'title' => $e->getHttpTitle(),
			'message' => $message(),
		];

		if($e instanceof ValidationFailed)
			$data['errors'] = $e->errors;

		if(Security::check('admin'))
			$data['reason'] = self::from_win(Html::collect_xdebug($e));

		parent::__construct($data);
	}

}


