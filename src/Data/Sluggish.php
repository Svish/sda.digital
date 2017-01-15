<?php

abstract class Data_Sluggish extends SqlData
{
	const COLUMN = 'name';

	public function __set($key, $value)
	{
		parent::__set($key, $value);
		switch($key)
		{
			case static::COLUMN:
				parent::__set(static::COLUMN.'_slug', $this->slug($value));
		}
	}

	
	public function __unset($key)
	{
		parent::__unset($key);
		switch($key)
		{
			case static::COLUMN:
				parent::__unset(static::COLUMN.'_slug');
				break;
		}
	}

	protected function slug($value)
	{
		return Util::slug($value);
	}
}
