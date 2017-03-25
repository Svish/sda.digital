<?php

namespace Data;

/**
 * Location of content.
 *
 * @see http://stackoverflow.com/a/12161804/39321
 * @see http://edndoc.esri.com/arcsde/9.0/general_topics/wkb_representation.htm
 * @see https://dev.mysql.com/doc/refman/5.7/en/gis-data-formats.html#gis-wkb-format
 */
class Location extends UrlEntity
{
	const SERIALIZE = true;

	protected $rules = [
			'latitude' => [['within', -90, 90]],
			'longitude' => [['within', -180, 180]],
			'website' => ['http_ok'],
		];

	public function __construct()
	{
		parent::__construct();

		$this->computed( new Slug('name') );
	}
}
