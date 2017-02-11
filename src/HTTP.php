<?php

/**
 * Utility class for HTTP stuff.
 */
class HTTP
{

	public static function get(string $url, array $opts = [])
	{
		$c = curl_init();
		curl_setopt_array($c, [
				CURLOPT_URL => $url,
				CURLOPT_HEADER => true,
			] + $opts);

		try
		{

			ob_start();
			curl_exec($c);
			$response = ob_get_clean();

			if(curl_errno($c))
				throw new \Error\CurlError($c);

			return new CurlResponse($c, $response);
		}
		finally
		{
			curl_close($c);	
		}
	}

	public static function post(string $url, array $post, array $opts = [])
	{
		return self::get($url, $opts + [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($post),
		]);
	}


	/**
	 * Check if URL is relative or to this site.
	 */
	public static function is_local(string $url): bool
	{
		extract(parse_url($url));

		// Truly relative
		if( ! isset($scheme))
			return true;

		// Absolute, but to this site
		return ($scheme??'') == SCHEME
			&& ($host??'') == HOST
			&& strpos(($path??''), WEBBASE) === 0;
	}

	/**
	 * Redirect to given target, and exit.
	 *
	 * @param code HTTP code to use
	 * @param target URL to redirect to
	 * @param prepend If target should be prepended with WEBROOT
	 */
	public static function redirect(string $target = null, int $code = 302, bool $prepend = true)
	{
		if($prepend)
			$target = WEBROOT.$target;

		Session::close();
		header('Location: '.$target, true, $code);
		exit;
	}

	/**
	 * Redirect to self, and exit.
	 *
	 * @param append Optionally appended to URL (e.g. ?foo=bar).
	 */
	public static function redirect_self(string $append = null)
	{
		self::redirect(PATH.$append, 303);
	}



	/**
	 * Get headers through a HEAD request.
	 */
public static function get_headers(string $url, array $opts = [])
{
	// Get headers
	$prev = stream_context_get_options(stream_context_get_default());
	stream_context_set_default(['http' => $opts + 
		[
			'method' => 'HEAD',
			'timeout' => 2,
		]]);
	$req = @get_headers($url, true);
	if( ! $req)
		return false;

	// Make more sane response
	foreach($req as $h => $v)
	{
		if(is_int($h))
			$headers[$h]['Status'] = $v;
		else
		{
			if(is_string($v))
				$headers[0][$h] = $v;
			else
				foreach($v as $x => $y)
					$headers[$x][$h] = $y;
		}

	}
	stream_context_set_default($prev);
	return $headers;
}



	/**
	 * Returns HTTP status text for given code.
	 *
	 * NOTE: Uses 500 if code not in self::$codes.
	 */
	public static function status(int $code)
	{
		return self::$codes[$code] ?? 'Unknown Error';
	}



	/**
	 * Set HTTP response status.
	 */
	public static function set_status($code)
	{
		if($code instanceof Error\HttpException)
			$code = $code->getHttpStatus();
		http_response_code($code);
	}



	/**
	 * Exit with no content.
	 */
	public static function exit(bool $remove_headers = true)
	{
		if($remove_headers)
			header_remove();
		http_response_code(204);
		exit;
	}



	/**
	 * Exit with HTTP status and an optional plain text message.
	 * 
	 * @param code HTTP status code
	 * @param message Optional plain text message to output.
	 */
	public static function plain_exit(int $code, string $message = null)
	{
		header_remove();

		self::set_status($code);
		header('Content-Type: text/plain; charset=utf-8');

		echo "$code " . self::$codes[$code];
		if($message)
			echo "\r\n\r\n$message";
		
		exit;
	}


	/**
	 * Array of HTTP codes and messages.
	 * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
	 */
	protected static $codes = [
		// 1xx: Informational - Request received, continuing process
		100 => "Continue",
		101 => "Switching Protocols",
		102 => "Processing",

		// 2xx: Success - The action was successfully received, understood, and accepted
		200 => "OK",
		201 => "Created",
		202 => "Accepted",
		203 => "Non-Authoritative Information",
		204 => "No Content",
		205 => "Reset Content",
		206 => "Partial Content",
		207 => "Multi-Status",
		208 => "Already Reported",

		226 => "IM Used",

		// 3xx: Redirection - Further action must be taken in order to complete the request
		300 => "Multiple Choices",
		301 => "Moved Permanently",
		302 => "Found",
		303 => "See Other",
		304 => "Not Modified",
		305 => "Use Proxy",
		306 => "(Unused)",
		307 => "Temporary Redirect",
		308 => "Permanent Redirect",

		// 4xx: Client Error - The request contains bad syntax or cannot be fulfilled
		400 => "Bad Request",
		401 => "Unauthorized",
		402 => "Payment Required",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Not Acceptable",
		407 => "Proxy Authentication Required",
		408 => "Request Timeout",
		409 => "Conflict",
		410 => "Gone",
		411 => "Length Required",
		412 => "Precondition Failed",
		413 => "Payload Too Large",
		414 => "URI Too Long",
		415 => "Unsupported Media Type",
		416 => "Range Not Satisfiable",
		417 => "Expectation Failed",

		421 => "Misdirected Request",
		422 => "Unprocessable Entity",
		423 => "Locked",
		424 => "Failed Dependency",

		426 => "Upgrade Required",

		428 => "Precondition Required",
		429 => "Too Many Requests",

		431 => "Request Header Fields Too Large",

		451 => "Unavailable for Legal Reasons",

		// 5xx: Server Error - The server failed to fulfill an apparently valid request
		500 => "Internal Server Error",
		501 => "Not Implemented",
		502 => "Bad Gateway",
		503 => "Service Unavailable",
		504 => "Gateway Timeout",
		505 => "HTTP Version Not Supported",
		506 => "Variant Also Negotiates",
		507 => "Insufficient Storage",
		508 => "Loop Detected",

		510 => "Not Extended",
		511 => "Network Authentication Required",


	];

}
