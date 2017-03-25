<?php

namespace Error;

/**
 * 400 Validation failed
 */
class ValidationFailed extends UserError
{
	public $errors;
	public $subject;

	public function __construct(array $errors, $subject)
	{
		$this->errors = $errors;
		$this->subject = $subject;
		parent::__construct(400, [count($errors)]);
	}
}
