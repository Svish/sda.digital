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
		$callback = $_GET['callback'] ?? false;
		if($callback && self::validate_callback($callback))
			$mime = $_accept[2];

		// Set content-type
		if( ! headers_sent($file, $line))
			header("Content-Type: $mime; charset=utf-8");

		// If no data
		if($this->_data === null)
			HTTP::exit();

		// Output
		$json = json_encode($this->_data, 
			JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

		if($json === false)
			throw new \Exception('JSON encode failed: '.json_last_error_msg());

		return $callback
			? "$callback($json)"
			: "$json";
	}



	private static function validate_callback(string $callback)
	{
		if( ! self::is_valid_callback($callback))
			throw new \Error\HttpException(400, 'Invalid JSONP identifier.');
	}



	/**
	 * @see http://www.geekality.net/?p=1739
	 */
	private static function is_valid_callback($subject)
	{
		$identifier_syntax
		= '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

		$reserved_words = ['break', 'do', 'instanceof', 'typeof', 'case',
		'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 
		'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 
		'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 
		'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 
		'private', 'public', 'yield', 'interface', 'package', 'protected', 
		'static', 'null', 'true', 'false'];

		return preg_match($identifier_syntax, $subject)
			&& ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
	}
}
