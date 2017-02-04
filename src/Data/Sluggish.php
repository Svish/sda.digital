<?php

namespace Data;

abstract class Sluggish extends Sql
{
	const SLUG_COLUMNS = ['name'];

	public function __set($key, $value)
	{
		parent::__set($key, $value);

		// Also set _slug
		if(in_array($key, static::SLUG_COLUMNS))
			parent::__set("$key_slug", $this->slug($value));
	}

	
	public function __unset($key)
	{
		parent::__unset($key);

		// Also unset _slug
		if(in_array($key, static::SLUG_COLUMNS))
			parent::__unset("$key_slug");
	}

	protected function slug($txt)
	{
		$txt = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $txt);
		$txt = preg_replace(['/\s+/', '/[^-\w.]+/'], ['-', ''], $txt);
		return strtolower($txt);
	}
}
