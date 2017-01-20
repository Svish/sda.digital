<?php

use Bitworking\Mimeparse;


abstract class View
{
	protected $_accept = [];


	public function render($mime = 'text/plain')
	{
		$accept = implode(', ', $this->_accept) ?: 'none';
		HTTP::exit_status(406, "Acceptable types: $accept");
	}


	public static function output()
	{
		$view = new static(...func_get_args());
		$mime = Mimeparse::bestMatch($view->_accept, $_SERVER['HTTP_ACCEPT'] ?? '*/*');
		echo $view->render($mime);
	}
}
