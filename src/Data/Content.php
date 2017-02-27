<?php

namespace Data;

class Content extends RelationalSql
{
	const SERIALIZE = true;


	protected $rules = [
			'time' => ['flexi_time'],
		];

	public function __construct()
	{
		parent::__construct();

		$this->computed( new Slug('title') );
	}
}
