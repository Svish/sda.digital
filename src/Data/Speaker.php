<?php

namespace Data;

class Speaker extends RelationalSql
{
	const SERIALIZE = true;

	public function __construct()
	{
		parent::__construct();

		$this->computed( new Slug('name') );
	}
}
