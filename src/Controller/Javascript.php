<?php

namespace Controller;
use Config, HTTP, Log;

/**
 * Handles compression and serving of javascript files.
 *
 * @see https://developers.google.com/closure/compiler/docs/api-ref
 * @see https://developers.google.com/closure/compiler/docs/api-tutorial1
 */
class Javascript extends Cached
{
	const URL = 'https://closure-compiler.appspot.com/compile';
	const DIR = SRC.'_js'.DIRECTORY_SEPARATOR;
	private $config;


	public function __construct()
	{
		parent::__construct();
		$this->config = Config::js();
		
		// Add full path to bundle files
		array_walk_recursive($this->config->bundles, function(&$value)
		{
			if(is_string($value) && ! starts_with('http', $value))
				$value = self::DIR.$value;
		});

		// Add single files
		foreach(glob(self::DIR.'*.js') as $file)
			$singles[basename($file)] = [$file];
		$this->config->bundles += $singles;
	}

	public function before(array &$info)
	{
		$this->files = $this->config->bundles[$info['params'][1]] ?? null;

		if( ! $this->files)
			HTTP::plain_exit(404, $info['path']);

		parent::before($info);
	}



	protected function cache_valid($cached_time)
	{
		$files = array_filter($this->files, function($f)
			{
				return ! starts_with('http', $f);
			});
		$files = array_map('filemtime', $files);
		$newest = array_reduce($files, 'max');
		return parent::cache_valid($cached_time)
		   and $cached_time >= $newest;
	}
	


	public function get()
	{
		header('Content-Type: text/javascript; charset=utf-8');

		// Gather contents of all input files into one string
		$js = array_map('file_get_contents', $this->files);
		$js = implode(PHP_EOL.PHP_EOL, $js);


		// Try compile
		$params = [
			'language' => 'ECMASCRIPT6_STRICT',
			'language_out' => 'ECMASCRIPT5',
			'compilation_level' => ENV == 'dev'
				? 'WHITESPACE_ONLY'
				: 'SIMPLE_OPTIMIZATIONS',
			'output_format' => 'text',
			'output_info' => 'compiled_code',
			'js_code' => $js,
		];
		$compiled = HTTP::post(self::URL, $params);


		Log::group();
		Log::trace_raw("Files:", array_map('basename', $this->files));
		Log::trace_raw("Compilation took: {$compiled->info['total_time']}s");

		// Output if we got something
		if($compiled->header['Content-Length'] > 1)
		{
			$time = date('Y-m-d H:i:s');
			echo implode("\r\n", [
				"/**",
				" * Compiled: $time",
				" * By: ".__CLASS__,
				" * Using: ".self::URL,
				" * Took: {$compiled->info['total_time']}",
				" */",
				$compiled->content,
				]);
		}
		// Otherwise, get helpful error (hopefully)
		else
		{
			$error = HTTP::post(self::URL, ['output_info' => 'errors'] + $params);
			http_response_code(500);
			echo implode("\r\n", [
				"/**",
				" * Javascript error",
				" * Using: ".self::URL,
				" * Took: {$compiled->info['total_time']} + {$error->info['total_time']}",
				" **",
				"",
				trim($error->content),
				" */",
				]);
			Log::error_raw("Error: ", $error->content);
			Log::trace_raw("Took: {$error->info['total_time']}s");
		}

		Log::groupEnd();
	}
}
