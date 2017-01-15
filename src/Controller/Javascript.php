<?php

/**
 * Handles compression and serving of javascript files.
 *
 * @see https://developers.google.com/closure/compiler/docs/api-ref
 * @see https://developers.google.com/closure/compiler/docs/api-tutorial1
 */
class Controller_Javascript extends CachedController
{
	const DIR = DOCROOT.'src'.DIRECTORY_SEPARATOR.'_js'.DIRECTORY_SEPARATOR;

	public function __construct()
	{
		$this->config = self::config();
		
		// Add full path to bundle files
		array_walk_recursive($this->config->bundles, function(&$value)
		{
			if(is_string($value))
				$value = self::DIR.$value;
		});

		// Add single files
		foreach(glob(self::DIR.'*.js') as $file)
			$this->config->bundles[basename($file)] = [$file];
	}

	public function before(array &$info)
	{
		$this->files = $this->config->bundles[$info['params'][1]] ?? null;

		if( ! $this->files)
			HTTP::exit_status(404, $info['path']);

		parent::before($info);
	}



	protected function cache_valid($cached_time)
	{
		$newest = array_reduce(array_map('filemtime', $this->files), 'max');
		return parent::cache_valid($cached_time)
		   and $cached_time >= $newest;
	}
	


	public function get()
	{
		header('Content-Type: text/javascript; charset=utf-8');
		
		// Gather contents of all input files into one string
		$js = array_map('file_get_contents', $this->files);
		$js = implode(PHP_EOL.PHP_EOL, $js);

		// Setup curl request
		$c = curl_init();
		curl_setopt_array($c, array
		(
			CURLOPT_URL => 'https://closure-compiler.appspot.com/compile',
			CURLOPT_POST => TRUE,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_POSTFIELDS => http_build_query([
				'language' => 'ECMASCRIPT6',
				'language_out' => 'ECMASCRIPT5',
				'output_info' => 'compiled_code',
				'output_format' => 'text',
				'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
				'js_code' => $js,
			]),
		));

		$resp = curl_exec($c);
		$info = curl_getinfo($c);

		if($resp === FALSE
			|| $info['http_code'] != 200 
			|| $info['download_content_length'] <= 1)
		{
			http_response_code(500);
			echo "// ERROR: ".curl_error($c)."\r\n";
			echo $js;
		}
		else
		{
			echo $resp;
		}

		curl_close($c);
	}

	

	public static function config()
	{
		return Config::javascript();
	}
}
