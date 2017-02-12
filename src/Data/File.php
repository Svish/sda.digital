<?php

namespace Data;

class File extends Sql
{
	const SERIALIZE = true;

	const DIR = ROOT.'_'.DIRECTORY_SEPARATOR;


	/**
	 * @return false if no changes; otherwise true
	 */
	public function save()
	{
		throw new Exception('Implement: '.__METHOD__);
		// If path is already DIR
		return parent::save();

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
