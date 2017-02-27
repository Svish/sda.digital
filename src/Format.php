<?php

/**
 * Format helper.
 */
class Format
{
	public static function bytes($size, $precision = 2)
	{
		return self::si($size, 'B', $precision, 1024);
	}

	public static function si($number, $type = '', $precision = 2, $kilo = 1000)
	{
		for($i=0; ($number / $kilo) > 0.9; $i++, $number /= $kilo);
		return round($number, $precision)
			 . ' '
			 . ['','k','M','G','T','P','E','Z','Y'][$i]
			 . $type;
	}
}
