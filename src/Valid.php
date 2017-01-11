<?php


/**
 * Validator.
 *
 * $errors = Valid::check($obj, ['prop' => [
 		'rule1',
 		['rule2', 'param1', 'param2'],
 		'rule3',
 		]]);
 */
class Valid
{
	public static function check($subject, array $rule_set)
	{
		$errors = [];

		foreach($rule_set as $property => $rules)
		{
			$value = $subject[$property];

			// If allowed empty, and value is empty, skip rules
			if( ! in_array('not_empty', $rules) and ! self::not_empty($value))
				continue;

			foreach($rules as $rule)
			{
				// Get method and parameters
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

				// If $method has no ::, assume self
				if(strpos($method, '::') === false)
					$method = Valid::class.'::'.$method;

				// TODO: Allow validation methods on $subject (if starts $this->?)

				// Call validation method
				if( ! call_user_func_array($method, $params))
				{
					// Add error text
					array_shift($params);
					$errors[$property][$method] = Text::error($method, $params);
					break;
				}
			}
		}
		if($errors)
			throw new ValidationException($errors);
		
		return true;
	}



	public static function keys_exist(array $value, array $keys)
	{
		foreach($keys as $key)
			if( ! array_key_exists($key, $value))
				return false;
		return true;
	}



	public static function max_length($value, $length)
	{
		return strlen($value) <= $length;
	}

	public static function min_length($value, $length)
	{
		return strlen($value) >= $length;
	}



	public static function not_empty($value)
	{
		return ! in_array($value, [null, false, '', []], true);
	}



	public static function email($value)
	{
		return Swift_Validate::email($value);
	}

	public static function email_domain($value)
	{
		if (empty($value))
			return false; // Empty fields cause issues with checkdnsrr()

		// Check if the email domain has a valid MX record
		return (bool) checkdnsrr(preg_replace('/^[^@]++@/', '', $value), 'MX');
	}



	public static function db_type($value, $column_type)
	{
		// Ignore unmatching types
		if( ! preg_match('/(?<type>\w+)\((?<m>[^)]+)\)/m', $column_type, $column_type))
			return true;
		extract($column_type);

		switch($type)
		{
			// varchar(max_length)
			case 'varchar':
				return self::max_length($value, $m);

			// set('allowed','values')
			case 'set':
				$value = explode(',', $value);
				$allowed = explode(',', str_replace('\'', '', $m));
				return $value == array_intersect($value, $allowed);

			default:
				return true;
		}
	}
}
