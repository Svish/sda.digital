<?php

/**
 * Handles normal pages.
 */
class Controller_Page extends SecureController
{
	private $ctx = [];
	protected $path;

	public function before(array &$info)
	{
		parent::before($info);
		$this->path = trim($info['path'], '/') ?: 'index';
	}


	public function get($url = null, $context = [])
	{
		if(is_null($url))
			$url = $this->path;

		if( ! is_array($context))
			$context = [];
		
		$url = ltrim($url, '/') ?: 'index';
		$this->ctx = $context + [
			'self' => $this->path,
			'class' => str_replace('/', ' ', $this->path),

			'user' => $this->user,
			'_post' => $_POST,
			
			'css' => Controller_Less::config()->global,
			'js' => Controller_Javascript::config()->global,
			
			'isProd' => ENV == 'prod',
		];

		try
		{
			header('content-type: text/html; charset=utf-8');
			echo Mustache::engine()->render($url, $this);
		}
		catch(Mustache_Exception_UnknownTemplateException $e)
		{
			throw new HttpException("Page '$url' not found", 404, $e);
		}
	}

	

	public function __get($key)
	{
		return $this->ctx[$key];
	}

	public function __isset($key)
	{
		// Already set?
		if(array_key_exists($key, $this->ctx))
			return true;

		// Constant?
		if(defined($key))
			return $this->set($key, constant($key));

		// Classes?
		foreach($this->class_alternatives($key) as $name)
			if(class_exists($name))
				return $this->set($key, new $name($this));

		// Function?
		if(Helper_Function::exists($key))
			return $this->set($key, new Helper_Function($key));

		return false;
	}



	private function set($key, $value)
	{
		$this->ctx[$key] = $value;
		return true;
	}



	private function class_alternatives($key)
	{
		$key = ucfirst($key);
		yield 'Helper_'.$key;
		yield 'Model_'.$key;
	}

	/**
	 * @usage    parent::get($url, $this->contextualize($e, 'email_fail'));
	 */
	protected function error($error_msg, HttpException $e)
	{
		HTTP::set_status($e->getHttpStatus());

		if($e instanceof ValidationException)
			return Msg::error($error_msg) + ['errors' => array_map('array_values', $e->getErrors())];

		return Msg::error('internal_error', $e->getMessage());
	}
}
