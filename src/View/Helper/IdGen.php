<?php

namespace View\Helper;

/**
 * Makes sets of ids.
 */
class IdGen
{
	private $n = 0;
	public function __invoke($next = null)
	{
		if($next)
			$this->n++;
		return $this->n;
	}
}
