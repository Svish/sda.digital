<?php

namespace Data;

class Slug extends Computed
{
	protected function _set(string $key, $value)
	{
		yield "{$key}_slug" => self::slug($value);
	}

	protected function _unset(string $key)
	{
		yield "{$key}_slug";
	}
	
	protected static function slug($txt)
	{
		$txt = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $txt);
		$txt = preg_replace('/\W+/', '-', $txt);
		$txt = trim($txt, '-');
		return strtolower($txt);
	}
}
