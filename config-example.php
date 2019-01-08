<?php
/*
	Question2Answer HTTPS image proxy plugin
	Copyright (C) 2018 Scott Vivian
	License: https://www.gnu.org/licenses/gpl.html
*/

return [

	// long random string for security
	'secretKey' => '',

	// public URL of the proxy script
	'proxyUrl' => '/qa-plugin/https-img-proxy/image.php',

	// directory in which to cache images locally (full server path, no trailing slash)
	'cacheDir' => __DIR__ . '/cache',

	// time in seconds to cache images locally (86400 = 1 day)
	'cacheLength' => 86400,

	// image to use if original cannot be found or times out (full server path)
	'missingImage' => __DIR__ . '/missing.png',

	// domains that are known to work over HTTPS
	'secureHosts' => [
		'i.ytimg.com',
		'i.reddit.com',
		'i.imgur.com',
	],

	// allowed mime types
	'validMimes' => [
		'image/jpg' => 'jpg',
		'image/jpeg' => 'jpg',
		'image/png' => 'png',
		'image/gif' => 'gif',
	],

];
