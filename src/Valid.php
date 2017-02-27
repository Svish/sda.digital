<?php


/**
 * Validator.
 */
class Valid
{
	public static function check(Data $subject, array $rule_set)
	{
		$errors = [];
		foreach($rule_set as $property => $rules)
		{
			$value = $subject->$property;

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


	public static function between($value, $min, $max): bool
	{
		return $value >= $min && $value <= $max;
	}


	public static function flexi_time($value): bool
	{
		$valid = preg_match('/^(?<year>\d{4})(?:-(?<month>\d{2})(?:-(?<day>\d{2})(?: (?<hour>\d{2}):(?<min>\d{2})(?::(?<sec>\d{2}))?)?)?)?$/', $value, $x);
		
		if( ! $valid)
			return false;

		extract($x);

		// Check month
		if($month ?? null AND ! self::between($month, 1, 12))
			return false;

		// Check date
		if($day ?? null AND ! checkdate($month, $day, $year))
			return false;

		// Check hour
		if($hour ?? null AND ! self::between($hour, 0, 23))
			return false;

		// Check minute
		if($min ?? null AND ! self::between($min, 0, 59))
			return false;

		// Check second
		if($sec ?? null AND ! self::between($sec, 0, 59))
			return false;

		return true;
	}
}
