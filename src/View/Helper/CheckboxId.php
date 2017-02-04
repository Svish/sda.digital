<?php

namespace View\Helper;

/**
 * Makes sets of ids for checkboxes and labels
 */
class CheckboxId
{
	private $new = true;
	private $n = 0;
	public function __invoke()
	{
		if($this->new)
			$this->n++;

		$this->new = !$this->new;
		
		return $this->n;
	}
}
