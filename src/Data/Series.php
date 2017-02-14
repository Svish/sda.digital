<?php

namespace Data;

class Series extends RelationalSql
{
	const SERIALIZE = true;

	public function __construct()
	{
		parent::__construct();

		$this->computed( new Slug('title') );
	}

	// TODO: How to deal with n in series_content?
}
