<?php
namespace View;
use Config,Model,View,Mustache,HttpException;

/**
 * Views using Mustache templates.
 */
class Template extends View
{
	protected $_accept = ['text/html'];

	private $_context = [];
	private $_template;



	public function __construct(array $context = [], $template = null)
	{
		$this->_context = $context;
		$this->_template = $template ?? PATH;
	}

	

	public function render($mime)
	{
		switch($mime)
		{
			case 'text/html':

				// Common context
				$this->_context += [
					'_user' => Model::users()->logged_in(),
					'_post' => $_POST,
					'_css' => (Config::less())->global,
					'_js' => (Config::javascript())->global,

					'url' => new Helper\Url,
					'messages' => new Helper\Messages,
					'isCurrent' => new Helper\IsCurrent,
					'clicky' => new Helper\Clicky,
					'pathClasses' => new Helper\PathClasses,
				];


				// Return rendered template
				try
				{
					if( ! headers_sent($file, $line))
						header('content-type: text/html; charset=utf-8');

					return Mustache::engine([], $this->_template)
						->render($this->_template, $this);
				}
				catch(\Mustache_Exception_UnknownTemplateException $e)
				{
					throw new \Error\PageNotFound(null, $e);
				}

			default:
				return parent::render($mime);
		}
	}

	

	public function __get($key)
	{
		return $this->_context[$key];
	}

	public function __isset($key)
	{
		// Already set?
		if(array_key_exists($key, $this->_context))
			return true;

		// Constant?
		if(defined($key))
			return $this->set($key, constant($key));

		// Function?
		if(Helper\PhpFunction::exists($key))
			return $this->set($key, new Helper\PhpFunction($key));

		// Class?
		foreach($this->alternatives($key) as $name)
		{
			if(class_exists($name))
				return $this->set($key, new $name($this));
		}

		return false;
	}



	protected function set($key, $value)
	{
		$this->_context[$key] = $value;
		return true;
	}



	private function alternatives($key)
	{
		$key = ucfirst($key);
		yield __NAMESPACE__."\\Helper\\$key";
	}
}
