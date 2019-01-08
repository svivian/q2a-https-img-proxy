<?php
/*
	Question2Answer HTTPS image proxy plugin
	Copyright (C) 2018 Scott Vivian
	License: https://www.gnu.org/licenses/gpl.html
*/

// prevent errors messing with image data
@ini_set('display_errors', 0);

class WAProxyHelper
{
	private $config;

	public function __construct(WAConfig $config)
	{
		$this->config = $config;
	}

	public function isValidRequest($url, $hash)
	{
		if (empty($this->config->secretKey)) {
			return false;
		}

		$expectedHash = substr(md5($this->config->secretKey . $url), 0, $this->config->hashLength);
		return $hash === $expectedHash;
	}

	public function isValidImage($imageData, $contentType)
	{
		return !empty($imageData) && $this->config->isValidMimeType($contentType);
	}

	public function getImageFilename($hash)
	{
		return $this->config->cacheDir . '/' . $hash[0] . '/' . $hash[1] . $hash[2] . '/' . $hash;
	}

	/**
	 * Fetches the image data from the cache, if available.
	 * @param string $hash
	 * @return array
	 */
	public function getCache($hash)
	{
		$cacheData = $contentType = null;
		$cacheFile = $this->getImageFilename($hash);

		if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $this->config->cacheLength)) {
			$contentType = mime_content_type($cacheFile);
			$cacheData = file_get_contents($cacheFile);
		}

		return [$cacheData, $contentType];
	}

	/**
	 * Save image data to cache.
	 * @param string $imageData
	 * @param string $hash
	 * @return bool
	 */
	public function saveCache($imageData, $contentType, $hash)
	{
		if (!$this->isValidImage($imageData, $contentType)) {
			return false;
		}

		$cacheFile = $this->getImageFilename($hash);
		$cacheSubDir = dirname($cacheFile);
		if (is_dir($cacheSubDir) || (is_writable($this->config->cacheDir) && mkdir($cacheSubDir, 0755, true))) {
			return file_put_contents($cacheFile, $imageData) !== false;
		}

		return false;
	}

	/**
	 * Download image from external (non-secure) host.
	 * @param string $url
	 * @return array
	 */
	public function getExternalImage($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$imageData = curl_exec($ch);

		// check image is a valid type
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		return [$imageData, $contentType];
	}

	/**
	 * Outputs image to browser.
	 * @param  string $imageData
	 * @param  string $contentType
	 * @return bool
	 */
	public function serveImageIfValid($imageData, $contentType)
	{
		if ($this->isValidImage($imageData, $contentType)) {
			header("Content-Type: $contentType");
			header("Cache-control: private,max-age={$this->config->cacheLength}");
			echo $imageData;
			exit;
		}

		return false;
	}
}


if (!isset($_GET['url']) || !isset($_GET['key'])) {
	exit;
}

// load config
require_once __DIR__ . '/WAConfig.php';
$params = require __DIR__ . '/config.php';
$config = new WAConfig($params);
$proxy = new WAProxyHelper($config);

$url = $_GET['url'];
$hash = $_GET['key'];

// check request is secure
if (!$proxy->isValidRequest($url, $hash)) {
	header('HTTP/1.0 404 Not Found');
	echo 'Invalid image';
	exit;
}


// get image from cache
list($cacheData, $contentType) = $proxy->getCache($hash);
$proxy->serveImageIfValid($cacheData, $contentType);


// otherwise, fetch image and cache
list($imageData, $contentType) = $proxy->getExternalImage($url);
$proxy->saveCache($imageData, $contentType, $hash);
$proxy->serveImageIfValid($imageData, $contentType);


// if we get to here, image could not be found or timed out; serve placeholder image instead
if (strlen($config->missingImage) > 0) {
	$contentType = mime_content_type($config->missingImage);
	$missingData = file_get_contents($config->missingImage);
	// although we could just serve the placeholder, we cache it to avoid repeated requests to the original
	$proxy->saveCache($missingData, $contentType, $hash);
	$proxy->serveImageIfValid($missingData, $contentType);
}
