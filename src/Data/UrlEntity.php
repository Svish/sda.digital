<?php

namespace Data;

class UrlEntity extends ExtendedSql
{
	// <type>/<pk>/<slug>
	const URL = "%s/%u/%s";

	public function __isset($key)
	{
		return in_array($key, ['url', 'slug'])
			|| parent::__isset($key);
	}


	public function __get($key)
	{
		switch($key)
		{
			case 'slug':
				$slug = $this->get_computed_handler(Slug::class);
				return $this->{$slug->column()};

			case 'url':
				return sprintf(self::URL,
				strtolower(get_class_name($this)),
				implode(',', $this->pk()),
				$this->slug
				);

			default:
				return parent::__get($key);
		}
	}

	public function jsonData(): array
	{
		$columns = [
			'slug' => $this->slug,
			'url' => $this->url,
			];
		
		return parent::jsonData()
			+ $columns;
	}
}
