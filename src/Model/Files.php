<?php

namespace Model;
use Data\File;
use DB, Model;


class Files extends Model
{
	/**
	 * Get location by id or email.
	 */
	public function get($id = null): File
	{
		return File::get($id);
	}

	/**
	 * For file/$id.
	 */
	public function for_page($id): File
	{
		$x = self::get($id);
		$x->load_relations('content');
		$x->content_list = $x->content;

		return $x;
	}
}
