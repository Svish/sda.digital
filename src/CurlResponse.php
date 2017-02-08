<?php 

/**
 * Parser for cURL responses.
 */
class CurlResponse
{
	const HEADER = '/^([!#$%&\'*+-.^`|~[:word:]]++) :  \s*(.++)  (?:\s^\s+.+)*/mx';

	public function __construct($curl, $response)
	{
		$this->info = curl_getinfo($curl);
		
		$headers = substr($response, 0, $this->info['header_size']);
		preg_match_all_callback(self::HEADER, $headers, [$this, 'set_header']);
		
		$this->content = substr($response, $this->info['header_size']);
	}

	public function __tostring()
	{
		return $this->content;
	}

	public function set_header(array $regex_match)
	{
		$name = ucwords($regex_match[1], '-');
		$this->header[$name] = $regex_match[2];
	}
}
