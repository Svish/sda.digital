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
		$cache = new Cache(__CLASS__, [$path]);

		return $cache->get($path, function($path)
			{
				return $this->_read($path);
			});
	}

	public function _read($path)
	{
		// Analyze
		Log::trace("Analyzing", $path);
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
		yield 'length' => [
			'string' => $info['playtime_string'],
			'seconds' => $info['playtime_seconds'],
			];


		// Yield tags of interest
		if(isset($info['tags']))
		yield 'tags' => iterator_to_array($this->tags($info['tags']));

		// Yield audio info
		if(isset($info['audio']))
		yield 'audio' => iterator_to_array($this->media($info['audio']));
		

		// Yield video info
		if(isset($info['video']))
		yield 'video' => iterator_to_array($this->media($info['video']));
	}



	private function tags(array $tags)
	{
		foreach($tags as $version => $values)
		foreach($values as $key => $value)
		{
			if( ! in_array($key, self::OF_INTEREST))
				continue;

			switch($key)
			{
				case 'artist':
					yield self::name($key) => array_flatten(array_map(function($item)
						{
							return explode('/', $item);
						}, $value));
					break;

				case 'comment':
					if(is_array($value))
						$value = trim(implode(PHP_EOL.PHP_EOL, $value));

					if(preg_match('/\s*\b(?<t>'.Valid::FLEXI_TIME.')\b\s*/', $value, $x, PREG_OFFSET_CAPTURE))
					{
						yield 'time' => $x['t'][0];
						$value = substr_replace($value, '', $x['t'][1], strlen($x[0][0]));
					}

					yield self::name($key) => trim($value) ?: null;

				default:
					yield self::name($key) => is_array($value)
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
					yield self::name($key) => [
						'string' => Format::si($value, 'bps'),
						'raw' => $value,
					];
					break;

				case 'sample_rate':
					yield self::name($key) => [
						'string' => Format::si($value, 'Hz', 1),
						'raw' => $value,
					];
					break;

				case 'encoder':
					// HACK: Remove garbage from... something...
					$value = explode("\u{4}", $value)[0];

				default:
					yield self::name($key) => $value;
					break;
			}
		}
	}


	private static function name(string $key):string
	{
		return self::NAMES[$key] ?? $key;
	}

	const NAMES = [
		'creation_date' => 'date',
		'band' => 'album_artist',
	];

	const OF_INTEREST = [
		'band',
		'album',
		'artist',
		'comment',
		'title',
		'track',
		'year',
		'creation_date',
		];
}
