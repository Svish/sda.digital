<?php

/**
 * ID3 tag helper.
 */
class ID3
{
	use Instance;


	private $lib;
	private function __construct()
	{
		$this->lib = new getID3;
	}


	public function read($path)
	{
		// Analyze
		$info = $this->lib->analyze($path);

		// Check encoding
		if($info['encoding'] != 'UTF-8')
			throw new Internal("ID3 encoding '{$info['encoding']}' != 'UTF-8'... so, guess we need to deal with that now...");

		// Yield info
		yield 'fileformat' => $info['fileformat'] ?? null;
		yield 'mime' => $info['mime_type'] ?? null;
		yield 'size' => [
			'string' => Format::bytes($info['filesize']),
			'raw' => $info['filesize']
			];


		if(isset($info['bitrate']))
		yield 'bitrate' => [
			'string' => Format::si($info['bitrate'], 'bps'),
			'raw' => $info['bitrate'],
		];


		if(isset($info['playtime_string']))
		yield 'time' => [
			'string' => $info['playtime_string'],
			'seconds' => $info['playtime_seconds'],
			];


		// Yield tags of interest
		foreach($info['tags'] ?? [] as $tags)
			yield from $this->tags($tags);


		// Yield audio info
		if(isset($info['audio']))
		yield 'audio' => iterator_to_array($this->media($info['audio']));
		

		// Yield video info
		if(isset($info['video']))
		yield 'video' => iterator_to_array($this->media($info['video']));
	}



	private function tags(array $tags)
	{
		foreach($tags as $key => $value)
		{
			if( ! in_array($key, self::OF_INTEREST))
				continue;

			switch($key)
			{
				case 'artist':
					yield $key => array_flatten(array_map(function($item)
						{
							return explode('/', $item);
						}, $value));
					break;

				default:
					yield $key => is_array($value)
						? implode('; ', $value)
						: $value;
					break;
			}
		}
	}



	private function media(array $info)
	{
		foreach($info as $key => $value)
		{
			if(is_array($value))
				continue;

			switch($key)
			{
				case 'bitrate':
					yield $key => [
						'string' => Format::si($value, 'bps'),
						'raw' => $value,
					];
					break;

				case 'sample_rate':
					yield $key => [
						'string' => Format::si($value, 'Hz', 1),
						'raw' => $value,
					];
					break;

				case 'encoder':
					// HACK: Remove garbage from... something...
					$value = explode("\u{4}", $value)[0];

				default:
					yield $key => $value;
					break;
			}
		}
	}

	const OF_INTEREST = [
		'band',
		'album',
		'artist',
		'title',
		'track',
		];
}
