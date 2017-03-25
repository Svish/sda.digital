<?php

namespace Data;

use Model;

class Person extends UrlEntity
{
	const SERIALIZE = true;

	public function __construct()
	{
		parent::__construct();

		$this->computed( new Slug('name') );
	}


	public static function from($data)
	{
		// NOTE: Make sure we reuse existing persons.
		$p = Model::persons()->find($data['name'] ?? null);
		$p->set($data);
		return $p;
	}
}
