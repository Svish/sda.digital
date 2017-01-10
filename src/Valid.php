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

		foreach($rule_set as $property => $rule)
		{
			$value = $subject[$property];

			foreach($rule as $func)
			{
				// If rule is array, use first item as function and the rest as parameters
				if(is_array($func))
				{
					$params = $func;
					$func = array_shift($params);
					array_unshift($params, $value);
				}
				else
					$params = [$value];

				// If $func has no ::, assume self
				if(strpos($func, '::') === false)
					$func = Valid::class.'::'.$func;

				// TODO: Allow validation methods on $subject

				// Call validation method
				if( ! call_user_func_array($func, $params))
				{
					// Add error text
					array_shift($params);
					$errors[$property][$func] = Text::error($func, $params);
					break;
				}
			}
		}
		if($errors)
			throw new ValidationException($errors);
		
		return true;
	}


	public static function keys_exist(array $array, array $keys)
	{
		foreach($keys as $key)
			if( ! array_key_exists($key, $array))
				return false;
		return true;
	}

	public static function min_length($value, $length)
	{
		return strlen($value >= $length);
	}


	public static function not_empty($value)
	{
		return ! in_array($value, [null, false, '', []], true);
	}

	public static function email($email)
	{
		return Swift_Validate::email($email);
	}

	public static function email_domain($email)
	{
		if ( ! Valid::not_empty($email))
			return FALSE; // Empty fields cause issues with checkdnsrr()

		// Check if the email domain has a valid MX record
		return (bool) checkdnsrr(preg_replace('/^[^@]++@/', '', $email), 'MX');
	}
}
