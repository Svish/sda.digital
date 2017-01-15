<?php

return (object) [
	'global' => [
		'//cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js',
		'//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js',
		'//cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.min.js',
		'//cdnjs.cloudflare.com/ajax/libs/react/15.4.2/react.min.js',
		'//cdnjs.cloudflare.com/ajax/libs/react/15.4.2/react-dom.min.js',
		'js/script.js',
		],
	'bundles' => [
		'script.js' => [
			'global.js',
			],
		],
];
