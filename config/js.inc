<?php

return (object) [
	'global' => [
		'https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js',
		'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js',
		'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
		'https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js',
		'https://cdnjs.cloudflare.com/ajax/libs/knockout.mapping/2.4.1/knockout.mapping.min.js',
		'https://cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.min.js',
		'js/script.js',
		],
	'bundles' => [
		'dnd.js' => [
			'dnd-page-scroll.js',
			'dnd.js',
		],
		'script.js' => [
			'jquery.global-ajax.js',
			'jquery.my-stuff.js',
			'knockout.jqauto.js',
			'knockout.my-stuff.js',
			'polyfills.js',
			'global.js',
			'navigation.js',
			],
		],
];
