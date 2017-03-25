<?php 

/**
 * Parser for cURL responses.
 */
class CurlResponse
{
	const HEADER = '/^([!#$%&\'*+-.^`|~[:word:]]++) :  \s*(.++)  (?:\s^\s+.+)*/mx';

	public $info;
	public $header;
	public $headers;
	public $content;

	public function __construct($curl, string $response)
	{
		$this->info = curl_getinfo($curl);
		
		$headers = substr($response, 0, $this->info['header_size']);
		$headers = explode("\r\n\r\n", trim($headers));

		foreach($headers as $n => $s)
		{
			$s = explode("\r\n", $s, 2);
			
			$this->headers[$n][0] = self::status_line($s[0]);

			if(isset($s[1]))
				preg_match_all_callback(self::HEADER, $s[1], [$this, 'set_header'], $n);
		}
		
		$this->header = end($this->headers);
		$this->content = substr($response, $this->info['header_size']);
	}

	public function __tostring()
	{
		return $this->content;
	}


	public function set_header(array $regex_match, int $n)
	{
		$name = ucwords($regex_match[1], '-');

		if( ! isset($this->headers[$n][$name]))
		{
			$this->headers[$n][$name] = $regex_match[2];
		}
		else
		{
			if( ! is_array($this->headers[$n][$name]))
				$this->headers[$n][$name] = [$this->headers[$n][$name]];
			$this->headers[$n][$name][] = $regex_match[2];
		}
	}
	
	private static function status_line(string $line)
	{
		$line = explode(' ', $line, 3);
		return array_combine(['Version','Code','Text'], $line);
	}
}
