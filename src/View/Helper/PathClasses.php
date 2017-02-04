<?php

namespace View\Helper;

/**
 * Returns the first line of text.
 */
class PathClasses
{
	public function __invoke()
	{
		return str_replace('/', ' ', PATH);
	}
}
