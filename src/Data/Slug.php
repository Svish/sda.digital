<?php

namespace Data;
use Format;

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
		yield $this->column_slug => Format::slug($value);
	}

	protected function _unset(string $key)
	{
		yield $this->column_slug;
	}
}
