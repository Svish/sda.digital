<?php

namespace Error;

/**
 * Wrapping of curl errors.
 *
 * @see https://curl.haxx.se/libcurl/c/libcurl-errors.html
 */
class CurlError extends HttpException
{
	public function __construct($curl)
	{
		switch(curl_errno($curl))
		{
			// Connect timeout => Service Unavailable
			case 7:
				$status = 503; break;

			// Operation timeout => Gateway Timeout
			case 28:
				$status = 504; break;

			// Other errors
			default:
				$status = 500; break;
		}

		parent::__construct($status, curl_error($curl));
	}
}
