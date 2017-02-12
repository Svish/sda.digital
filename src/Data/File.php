<?php

namespace Data;

class File extends RelationalSql
{
	const SERIALIZE = true;

	const DIR = ROOT.'_'.DIRECTORY_SEPARATOR;

	public function __construct()
	{
		parent::__construct();

		$this->computed( new FileInfo('path') );
	}

	public function move()
	{
		throw new Exception('Implement: '.__METHOD__);

		if( ! $this->loaded)
			// Simply move the file


		// Begin transaction
		// $old = $this->path;
		// Save to get id, if not already got one

		// $this->path = ha/sh/hash_id.ext
		// Save with new path

		// Copy $old => $this->path
		// End transaction

		// Delete $old if ok, $new if not
		return true;
	}
}
