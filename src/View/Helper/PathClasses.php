<?php

namespace View\Helper;

/**
 * Helper: Returns the first line of text.
 */
class PathClasses
{
	public function __invoke()
	{
		if(PATH == 'index')
			return 'index front';
		return str_replace('/', ' ', PATH);
	}
}
