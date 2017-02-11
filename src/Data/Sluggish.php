<?php

namespace Data;

abstract class Sluggish extends Sql
{
	const SLUG_COLUMNS = ['name'];

	public function __set($key, $value)
	{
		parent::__set($key, $value);

		// Also set _slug (unless PDO is doing it)
		if($this->loaded && in_array($key, static::SLUG_COLUMNS))
			parent::__set("{$key}_slug", self::slug($value));
	}

	
	public function __unset($key)
	{
		parent::__unset($key);

		// Also unset _slug
		if(in_array($key, static::SLUG_COLUMNS))
			parent::__unset("{$key}_slug");
	}

	protected static function slug($txt)
	{
		$txt = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $txt);
		$txt = preg_replace('/\W+/', '-', $txt);
		$txt = trim($txt, '-');
		return strtolower($txt);
	}
}
