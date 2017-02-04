<?php

abstract class Data_Sluggish extends SqlData
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

	protected function slug($value)
	{
		return Util::slug($value);
	}
}
