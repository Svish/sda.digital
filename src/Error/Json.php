<?php
namespace Error;

/**
 * Error view as Json.
 */
class Json extends \View\Json
{

	public function __construct(HttpException $e)
	{
		$message = new \View\Helper\Messages;
		$data = [
			'status' => $e->getHttpStatus(),
			'title' => $e->getHttpTitle(),
			'message' => $message(),
		];

		if($e instanceof ValidationFailed)
			$data['errors'] = array_map('array_values', $e->getErrors());

		if(ENV == 'dev')
			$data['reason'] = Html::collect_xdebug($e);

		parent::__construct($data);
	}

}
