<?php

/**
 * Handles compression and serving of javascript files.
 *
 * @see https://developers.google.com/closure/compiler/docs/api-tutorial1
 */
class Controller_Javascript extends CachedController
{
	public function __construct()
	{
		$this->config = self::config();
	}

	public function before(array &$info)
	{
		// Check if known path
		if( ! array_key_exists($info['path'], $this->config->path))
			HTTP::exit_status(404, $info['path']);

		$this->files = $this->config->path[$info['path']];

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
				'language' => 'ECMASCRIPT5',
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
