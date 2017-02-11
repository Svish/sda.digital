<?php
namespace View;
use HTTP, View, Generator;

/**
 * Json data.
 */
class Json extends View
{
	protected $_accept = [
		'application/json',			// JSON
		'text/json',				// JSON
		'application/javascript',	// JSONP
		];


	private $_data;

	public function __construct($data)
	{
		if($data instanceof Generator)
			$data = iterator_to_array($data, false);
		
		$this->_data = $data;
	}

	public function render($mime)
	{
		// JSON not acceptable
		if( ! in_array($mime, $this->_accept))
			return parent::render($mime);

		// Set mime if jsonp
		if(isset($_GET['callback']))
			$mime = $_accept[2];

		// Set content-type
		if( ! headers_sent($file, $line))
			header("content-type: $mime; charset=utf-8");

		// If no data
		if($this->_data === null)
			HTTP::exit();

		// Output
		$json = json_encode($this->_data, 
			JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

		// TODO: Validate callback (see blog)
		echo isset($_GET['callback'])
			? "{$_GET['callback']}($json)"
			: $json;
	}
}
