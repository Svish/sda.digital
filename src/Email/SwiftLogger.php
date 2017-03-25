<?php

namespace Email;
use Log;


/**
 * Logger for Swift email sending.
 */
class SwiftLogger implements \Swift_Plugins_Logger
{
	public static function plugin()
	{
		return new \Swift_Plugins_LoggerPlugin(new self);
	}

	public function add($entry)
	{
		$entry = trim($entry);
		return Log::trace_raw($entry);
	}

	public function clear() {}
	public function dump() {}
}
