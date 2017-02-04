<?php


/**
 * Exceptions for Valid::class.
 */
class ValidationException extends HttpException
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
