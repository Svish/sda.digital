<?php

/**
 * Helper class for Clicky integration.
 */
class Clicky
{
	private static $api = 'https://in.getclicky.com/in.php';
	private static $parameters = ['type', 'href','title', 'ref', 'ua', 'ip_address', 'session_id', 'goal', 'custom'];
	private static $types = ['click', 'pageview', 'download', 'outbound', 'custom'];
	private $config;



	public function __construct()
	{
		$this->config = Config::clicky();
	}



	public function __isset($key)
	{
		return array_key_exists(ENV, $this->config)
			&& array_key_exists($key, $this->config[ENV]);
	}

	public function __get($key)
	{
		return $this->config[ENV][$key];
	}



	public function log(array $data = null)
	{
		if( ! isset($this->site_id) || ! isset($this->admin_key))
			return;

		// Filter and append default values
		$data = Util::array_whitelist($data ?: [], self::$parameters)
			+ [
				'type' => self::$types[0],
				'ip_address' =>  @$_SERVER['REMOTE_ADDR'],
				'ref' => @$_SERVER['HTTP_REFERER'],
				'ua' => @$_SERVER['HTTP_USER_AGENT'],
				'href' => @$_SERVER['REQUEST_URI'],
				'site_id' => $this->site_id,
				'sitekey_admin' => $this->admin_key,
			];

		if(@$_SERVER['HTTP_DNT'])
			return;

		if( ! in_array($data['type'], self::$types))
			trigger_error("Invalid clicky log type: {$data['type']}", E_USER_ERROR);

		$c = curl_init();
		curl_setopt_array($c, array
		(
			CURLOPT_URL => self::$api.'?'.http_build_query($data),
			CURLOPT_RETURNTRANSFER => TRUE,
		));

		curl_exec($c);
		curl_close($c);
	}
}
