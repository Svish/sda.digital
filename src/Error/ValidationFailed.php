<?php

namespace Error;

/**
 * 400 Validation failed
 */
class ValidationFailed extends HttpException
{
	private $errors;

	public function __construct(array $errors)
	{
		$this->errors = $errors;
		parent::__construct([count($errors)], 400);
	}

	public function getErrors()
	{
		return $this->errors;
	}
}
