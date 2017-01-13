<?php


/**
 * PHP Function wrapper for Mustache templates.
 */
class Helper_Function
{
	private $function;

	public function __construct($function)
	{
		$this->function = new ReflectionFunction($function);

		// Check required parameter count
		if(1 != $this->function->getNumberOfRequiredParameters())
			throw new Exception("'$function' not usable via ".__CLASS__.". Has $rp required parameters, needs exactly 1.");
	}


	public function __invoke($text, Mustache_LambdaHelper $render = null)
	{
		$text = $this->function->invokeArgs([$text]);
		return $render ? $render($text) : $text;
	}


	/**
	 * Only use for certain whitelisted functions.
	 */
	private static $whitelist = ['ucfirst', 'strtolower'];
	public static function exists($name)
	{
		return function_exists($name)
		   and in_array($name, self::$whitelist);
	}
}
