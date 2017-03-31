<?php

namespace View\Helper;

/**
 * Helper: SVG importer for Mustache templates.
 *
 * If the name includes a ";" everything
 * following it will be added to the <svg> 
 * tag as attributes.
 */
class Svg
{
	const DIR = SRC.'_icons'.DIRECTORY_SEPARATOR;

	public function __invoke($name, $render = null)
	{
		if($render)
			$name = $render($name);

		return $this->get($name);
	}

	protected function get($file)
	{
		$file = self::DIR.$file.'.svg';

		if( ! is_file($file))
			return "[svg={$opt[0]}]";
		$svg = file_get_contents($file);

		if(isset($opt[1]))
			$svg = str_replace('<svg ', "<svg {$opt[1]} ", $svg);

		return $svg;
	}
}
