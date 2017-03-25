<?php

use Geekality\ConsoleLog;



/**
 * Simple logger class.
 *
 * @uses ConsoleLog
 */
class Log
{
	use Instance;

	const LEVELS = ['group', 'groupEnd', 'trace', 'info', 'warn', 'error'];


	private $console;
	private function __construct()
	{
		$this->console = new class extends ConsoleLog
			{
				function formatBacktrace(array $bt): string
				{
					// HACK: Makes clicking work in FF Dev Console
					return "{$bt['file']}{$bt['line']}";
				}
			};
		$this->console->backtrace_level = 4;
	}


	/**
	 * Call via instance.
	 */
	public static function __callStatic($level, $args)
	{
		self::instance()->$level(...$args);
	}

	/**
	 * Logging calls.
	 */
	public function __call($level, $args)
	{
		$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[2];
		$raw = ends_with('_raw', $level);
		$level = $raw ? substr($level, 0, -4) : $level;

		if( ! in_array($level, self::LEVELS))
			throw new Exception("Unsupported log level: $level");

		self::instance()->_log($caller, $level, $raw, $args);
		self::instance()->_chromeLog($caller, $level, $raw, $args);
	}



	/**
	 * Helper: ConsoleLog wrapper with some extras.
	 */
	protected function _log(array $caller, string $level, bool $raw, array $args)
	{
		// TODO: Write errors to file/email? Tail on problems page?
		//var_dump(get_defined_vars());
	}


	
	/**
	 * Helper: ConsoleLog wrapper with some extras.
	 */
	protected function _chromeLog(array $caller, string $level, bool $raw, array $args)
	{
		if(ENV != 'dev')
			return;

		if($level == 'trace')
			$level = 'log';

		if( ! $raw)
		{
			$caller = $caller['class'];
			switch($level)
			{
				case 'group':
					// HACK: Group header isn't currently shown in FireFox Developer Console...
					$this->console->log("$caller:");

				case 'groupEnd':
				case 'log':
				case 'info':
				case 'warn':
				case 'error':
					// Add caller as header/first argument
					array_unshift($args, "$caller:");
					break;
			}
		}

		$this->console->$level(...$args);
	}
}
