<?php
/*
	Question2Answer HTTPS image proxy plugin
	Copyright (C) 2018 Scott Vivian
	License: https://www.gnu.org/licenses/gpl.html
*/

class WAConfig
{
	public $secretKey;
	public $proxyUrl;
	public $cacheDir;
	public $cacheLength;
	public $missingImage;
	public $secureHosts;
	public $validMimes;
	public $hashLength = 16;

	public function __construct(array $config)
	{
		foreach ($config as $key => $value) {
			if (property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}
	}
}
