<?php

namespace Data;

class Content extends UrlEntity
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
