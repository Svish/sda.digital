<?php

namespace Controller\Admin\Content;
use HTTP, Session, View, Mime;

/**
 * Adding new content.
 */
class Add extends \Controller\Admin
{
	protected $required_roles = ['editor'];


	public function get()
	{
		$files = Session::get('adding', []);
		if( ! $files)
			HTTP::redirect('admin/content/fresh');

		$files = array_map(function($path)
		{
			return pathinfo($path) + [
			'path' => $path,
			'mime' => Mime::get(self::pathfix($path)),
			];
		}, $files);


		var_dump($files);


		// TODO: Json view how? 
		View::template(['adding' => $files])
			->output();
	}

	public function post()
	{
		Model::content()->add($_POST);
		parent::get(null);
	}


	private static function pathfix($path)
	{
		return IS_WIN ? utf8_decode($path) : $path;
	}
}
