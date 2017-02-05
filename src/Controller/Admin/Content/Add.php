<?php

namespace Controller\Admin\Content;
use HTTP, Session, View, Mime, ID3;

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

		$files = array_map([$this, 'enrich'], $files);
		$files = array_group_by('filename', $files);
		var_dump($files);return;

		View::template(['adding' => $files])
			->output();
	}

	public function post()
	{
		Model::content()->add($_POST);
		parent::get();
	}


	private function enrich($path)
	{
		$path = self::to_win($path);

		$info['mime'] = Mime::get($path);

		// NOTE: Works for video, but seems to be suuuper slow...
		if(starts_with('audio/', $info['mime']['type']))
			$info += ID3::read($path);

		$info += [
			'path' => self::from_win($path),
			'title' => self::pathinfo($path, PATHINFO_FILENAME),
			'filename' => self::pathinfo($path, PATHINFO_FILENAME),
			'fileext' => self::pathinfo($path, PATHINFO_EXTENSION),
			];

		return $info;
	}

	use \WinPathFix;
	private static function pathinfo($path, $options)
	{
		return self::from_win(pathinfo($path, $options));
	}
}
