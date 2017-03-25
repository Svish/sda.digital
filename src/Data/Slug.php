<?php

namespace Data;

class Slug extends Computed
{
	private $column_slug;

	public function __construct(string $column)
	{
		parent::__construct($column);
		$this->column_slug = "{$column}_slug";
	}

	public function column()
	{
		return $this->column_slug;
	}

	protected function _set(string $key, $value)
	{
		yield $this->column_slug => self::slug($value);
	}

	protected function _unset(string $key)
	{
		yield $this->column_slug;
	}
	
	private static function slug($txt)
	{
		$txt = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $txt);
		$txt = preg_replace('/\W+/', '-', $txt);
		$txt = trim($txt, '-');
		return strtolower($txt);
	}
}
