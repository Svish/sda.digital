<?php
namespace View;
use View, Generator;

/**
 * Json data.
 */
class Json extends View
{
	protected $_accept = [
		'text/json',
		'application/json',
		'application/javascript',
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
		if( ! in_array($mime, $this->_accept))
			return parent::render($mime);

		if( ! headers_sent($file, $line))
			header("content-type: $mime; charset=utf-8");

		$json = json_encode($this->_data, 
			JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

		echo isset($_GET['callback'])
			? "{$_GET['callback']}($json)"
			: $json;
	}
}
