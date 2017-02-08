<?php

namespace Error;

/**
 * 400 Validation failed
 */
class ValidationFailed extends UserError
{
	private $errors;

	public function __construct(array $errors)
	{
		$this->errors = $errors;
		parent::__construct(400, [count($errors)]);
	}

	public function getErrors()
	{
		return $this->errors;
	}
}
