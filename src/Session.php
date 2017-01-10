<?php


/**
 * Session helper.
 *
 * @see Session fixation: http://stackoverflow.com/a/5081453/39321
 */
class Session
{
	const ID = 'session';

	public static function start()
	{
		session_name(self::ID);
		session_start();
	}

	public static function close()
	{
		if(session_name())
			session_write_close();
	}

	public static function destroy()
	{
		self::start();
		$_SESSION = array();
		
		if(ini_get("session.use_cookies"))
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}

		session_destroy();
	}
}
