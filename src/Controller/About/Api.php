<?php

namespace Controller\About;
use Controller\Less;

class Api extends \Controller\Api
{
	public function get_colors()
	{
		$colors = [];
		foreach(glob(Less::DIR.'*'.Less::EXT) as $file)
		{
			$regex = '/(@.+-color): (#[a-f0-9]+);/mi';
			$file = file_get_contents($file);
			preg_match_all($regex, $file, $list, PREG_SET_ORDER);

			foreach($list as $color)
				$colors[] = array_combine(['less', 'name', 'color'], $color);
		}

		array_sort_by('name', $colors);
		return $colors;
	}
}
