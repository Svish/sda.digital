<?php


/**
 * Returns the first line of text.
 */
class Helper_PathClasses
{
	public function __invoke()
	{
		return str_replace('/', ' ', PATH);
	}
}
