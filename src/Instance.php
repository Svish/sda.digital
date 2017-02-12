<?php

/**
 * Instance.
 */
trait Instance
{
	private static $_instance;
	public static function instance()
	{
		if( ! self::$_instance)
			self::$_instance = new self;
		return self::$_instance;
	}
}
