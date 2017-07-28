<?php

namespace View\Helper;
use Config, Mustache;

/**
 * Helper: Google API Helper.
 * 
 * @uses Config google
 */
class Google
{
	private $config;

	public function __construct()
	{
		$this->config = Config::google();
	}

	public function api_key()
	{
		return $this->config['api_key'];
	}

	public function __invoke()
	{
		return Mustache::engine()
			->render("google", $this->config);
	}
}
