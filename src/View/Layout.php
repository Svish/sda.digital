<?php
namespace View;
use Config, Model, View;

/**
 * Mustache views with common context added.
 */
class Layout extends Mustache
{
	public function __construct(array $context = [], $template = null)
	{
		$context += [
			'_path' => PATH,
			'_user' => Model::users()->logged_in(),
			
			'_post' => $_POST,
			'_get' => $_GET,
			
			'_css' => (Config::css())->global,
			'_js' => (Config::js())->global,

			'_icp' => new Helper\IsCurrentPath,
			'_pc' => new Helper\PathClasses,

			'_url' => new Helper\Url,
			'__' => new Helper\I18N,
			'messages' => new Helper\Messages,
			'clicky' => new Helper\Clicky,
		];

		parent::__construct($context, $template);
	}
}
