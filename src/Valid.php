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

				// Append subject to parameters
				array_push($params, $property, $subject);

				// Call validation method
				if( ! $method(...$params))
				{
					// Add error text
					array_shift($params);
					if(is_array($method))
						$method = implode(is_object($method[0]) ? '->' : '::', $method);
					$errors[$property] = Text::validation($method, $params);
					break;
				}
			}
		}

		// Throw if any errors found
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



	public static function max_length($value, int $length): bool
	{
		return ! $value || strlen($value) <= $length;
	}

	public static function min_length($value, int $length): bool
	{
		return $value && strlen($value) >= $length;
	}



	public static function not_empty($value): bool
	{
		return ! in_array($value, [null, false, '', []], true);
	}



	public static function email($value): bool
	{
		return $value && Swift_Validate::email($value);
	}

	public static function email_domain($value): bool
	{
		// Check if the email domain has a valid MX record
		return $value && (bool) checkdnsrr(preg_replace('/^[^@]++@/', '', $value), 'MX');
	}
}
