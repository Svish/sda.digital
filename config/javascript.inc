<?php

return (object) [
	'global' => [
		'//cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js',
		'//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js',
		'//cdnjs.cloudflare.com/ajax/libs/knockout/3.4.1/knockout-min.js',
		'//cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.min.js',
		'js/script.js',
		],
	'bundles' => [
		'script.js' => [
			'global.js',
			],
		],
];
