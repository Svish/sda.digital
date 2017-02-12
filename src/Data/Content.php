<?php

namespace Data;

class Content extends RelationalSql
{
	const SERIALIZE = true;

	public function __construct()
	{
		parent::__construct();

		$this->computed( new Slug('title') );
	}
}
