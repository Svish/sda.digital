<?php
namespace Error;

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

		if(ENV == 'dev')
			$data['reason'] = self::from_win(Html::collect_xdebug($e));

		parent::__construct($data);
	}

}


