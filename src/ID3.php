<?php

/**
 * ID3 tag helper.
 */
class ID3
{
	private static $id3;

	public static function read($path)
	{
		// Init getID3
		if( ! self::$id3)
			self::$id3 = new getID3;

		// Analyze
		$tags = self::$id3->analyze($path);
		getid3_lib::CopyTagsToComments($tags);

		$tags = [
			'artist' => self::c($tags, 'artist'),
			'title' => self::c($tags, 'title'),
			'track' => self::c($tags, 'track_number'),

			'time' => $tags['playtime_string'],
		];
		return array_filter($tags);
	}

	private static function c(array $tags, $comment)
	{
		return implode(';', $tags['comments'][$comment] ?? []);
	}
}
