<?php


/**
 * Validator.
 */
class Valid
{
	public static function check($subject, array $rule_set)
	{
		$errors = [];

		foreach($rule_set as $property => $rules)
		{
			$value = $subject[$property];

			// If allowed empty, and value is empty, skip other rules
			if( ! in_array('not_empty', $rules) and ! self::not_empty($value))
				continue;

			foreach($rules as $rule)
			{
				// Get method and params
				if(is_array($rule))
				{
					$method = array_shift($rule);
					$params = $rule;
					array_unshift($params, $value);
				}
				else
				{
					$method = $rule;
					$params = [$value];
				}

				// Try self if string and not callable
				if(is_string($method) && ! is_callable($method))
					$method = [Valid::class, $method];

				// Call validation method
				if( ! $method(...$params))
				{
					// Add error text
					array_shift($params);
					if(is_array($method))
						$method = implode(is_object($method[0]) ? '->' : '::', $method);
					$errors[$property][$method] = Text::validation($method, $params);
					break;
				}
			}
		}
		if($errors)
			throw new Error\ValidationFailed($errors);
		
		return true;
	}



	public static function keys_exist(array $value, array $keys): bool
	{
		foreach($keys as $key)
			if( ! array_key_exists($key, $value))
				return false;
		return true;
	}



	public static function max_length(string $value, int $length): bool
	{
		return strlen($value) <= $length;
	}

	public static function min_length(string $value, int $length): bool
	{
		return strlen($value) >= $length;
	}



	public static function not_empty($value): bool
	{
		return ! in_array($value, [null, false, '', []], true);
	}



	public static function email(string $value): bool
	{
		return Swift_Validate::email($value);
	}

	public static function email_domain(string $value): bool
	{
		if (empty($value))
			return false; // Empty fields cause issues with checkdnsrr()

		// Check if the email domain has a valid MX record
		return (bool) checkdnsrr(preg_replace('/^[^@]++@/', '', $value), 'MX');
	}
}
