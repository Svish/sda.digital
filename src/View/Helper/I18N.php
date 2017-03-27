<?php

namespace View\Helper;
use Text, Config;


/**
 * Helper: Translation in Mustache templates.
 */
class I18N
{
	private $from;
	private $to;

	public function __construct()
	{
		$strings = Config::translations()[LANG] ?? [];
		$this->from = array_keys($strings);
		$this->to = array_values($strings);
	}

	public function __invoke($text, $render = null)
	{
		if($render)
			$text = $render($text);

		return str_replace($this->from, $this->to, $text);
	}
}
