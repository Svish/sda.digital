<?php
use Bitworking\Mimeparse;

abstract class View
{
	protected $_accept = [];


	public static function __callStatic($name, $args)
	{
		$name = __CLASS__.'\\'.ucfirst($name);
		return new $name(...$args);
	}


	public function render($mime)
	{
		$accept = implode(', ', $this->_accept) ?: 'none';
		HTTP::plain_exit(406, "Acceptable types: $accept");
	}


	public function output()
	{
		$mime = Mimeparse::bestMatch($this->_accept, $_SERVER['HTTP_ACCEPT'] ?? '*/*');
		echo $this->render($mime);
	}
}
