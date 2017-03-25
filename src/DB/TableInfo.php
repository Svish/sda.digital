<?php

namespace DB;

class TableInfo
{
	public $primary_keys;
	public $column_names;
	public $columns;
	public $query;
	public $rules;
	public $relations;



	public function __construct(array $data)
	{
		foreach($data as $k => $v)
			$this->$k = $v;
	}
}
