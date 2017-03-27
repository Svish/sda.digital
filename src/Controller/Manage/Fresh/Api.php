<?php

namespace Controller\Manage\Fresh;
use Model;
use \Data\File;

/**
 * API for handling fresh content.
 */
class Api extends \Controller\Api
{
	protected $required_roles = ['editor'];
	

	public function get_fresh_dirs()
	{
		return Model::fresh()
			->directories();
	}

	public function get_lookup_series()
	{
		return Model::series()
			->search($_GET['term'] ?? null);
	}

	public function get_fresh_content()
	{
		$content = Model::fresh()->content($_GET['path'] ?? null);
		$content = iterator_to_array($content, false);
		$path = $_GET['path'] ?? null;

		$locations = Model::locations()->all_select();
		// TODO: Get roles from DB
		$roles = [
			['id' => 'speaker', 'label' => 'Taler'],
			['id' => 'translator', 'label' => 'Oversetter'],
			['id' => 'author', 'label' => 'Forfatter'],
		];

		return get_defined_vars();
	}

	public function get_tag_info()
	{
		$info = Model::fresh()
			->tag_info($_GET['path'] ?? null);
		return iterator_to_array($info);
	}

	public function get_content()
	{
		$cache = new \Cache(__CLASS__, null);
		return self::put_content($cache->get('data'));
	}

	public function put_content(array $data)
	{
		$cache = new \Cache(__CLASS__, null);
		$cache->set('data', $data);

		$x = Model::fresh()
			->save($data);
	}

	public function put_series($data)
	{
		var_dump($data);exit;
		return true;
	}
}
