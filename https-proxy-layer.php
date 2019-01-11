<?php
/*
	Question2Answer HTTPS image proxy plugin
	Copyright (C) 2018 Scott Vivian
	License: https://www.gnu.org/licenses/gpl.html
*/

class qa_html_theme_layer extends qa_html_theme_base
{
	/** @var WAConfig */
	private $config;

	public function initialize()
	{
		parent::initialize();

		require_once QA_HTML_THEME_LAYER_DIRECTORY . '/WAConfig.php';
		$params = include QA_HTML_THEME_LAYER_DIRECTORY . '/config.php';
		$this->config = new WAConfig($params);
	}

	public function q_view_content($q_view)
	{
		if (isset($q_view['content'])) {
			$q_view['content'] = $this->rewriteImgTag($q_view['content']);
		}
		parent::q_view_content($q_view);
	}

	public function a_item_content($a_item)
	{
		if (isset($a_item['content'])) {
			$a_item['content'] = $this->rewriteImgTag($a_item['content']);
		}
		parent::a_item_content($a_item);
	}

	private function rewriteImgTag($content)
	{
		// require secret key
		if (empty($this->config->secretKey))
			return $content;

		// rewrite images to external HTTPS ones where possible
		foreach ($this->config->secureHosts as $host) {
			$selfSearch = '#<img src="http://'.$host.'/([^"]+)"#';
			$selfReplace = '<img src="https://'.$host.'/$1"';
			$content = preg_replace($selfSearch, $selfReplace, $content);
		}

		// rewrite remaining HTTP images to local proxy
		$extSearch = '#<img src="(http://[^"]+)"#';
		$content = preg_replace_callback($extSearch, function($matches) {
			$url = $matches[1];
			$hash = substr(md5($this->config->secretKey . $url), 0, $this->config->hashLength);
			return sprintf('<img src="%s?key=%s&url=%s"', $this->config->proxyUrl, $hash, urlencode($url));
		}, $content);

		return $content;
	}
}
