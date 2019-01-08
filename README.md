
HTTPS image proxy (Question2Answer plugin)
=================================================

A plugin for [Question2Answer](http://www.question2answer.org) that converts embedded images to use HTTPS links rather than HTTP. This avoids the problem of "mixed content" warnings in the web browser when a secure page links to an insecure image.

Security is included to prevent anyone proxying unauthorized images through the script. Known HTTPS hosts can also be defined to link to those directly.


Pay What You Like
-------------------------------------------------

Most of my code is released under the open source GPLv3 license, and provided with a 'Pay What You Like' approach. Feel free to download and modify the code to suit your needs, and I hope you value it enough to make a small donation - any amount is welcome.

### [Donate here](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4R5SHBNM3UDLU&source=url)


Installation & Usage
-------------------------------------------------

1. Ensure HTTPS is set up and working correctly on your Q2A site.

2. Download and extract the files to your plugins folder (e.g. `qa-plugins/https-img-proxy`). Check the [releases page](https://github.com/svivian/q2a-tagging-tools/releases) for the latest official version.

3. Rename `config-example.php` to `config.php`. For performance reasons, configuration for this plugin is done via PHP and not Q2A options. The config options are:
	- `secretKey` --- should be a long string of random characters. This is used to calculate the 'hash' parameter in the URL, providing security so that no one can just link up any image through your site.
	- `proxyUrl` --- the public URL of the proxy script, as will be linked from the image src attribute. It's recommended to include the domain name to ensure HTTPS.
	- `cacheDir` --- directory to cache images to locally (avoids overhead). Must be a full server path.
	- `cacheLength` --- length of time, in seconds, to cache images locally. This is also used for the HTTP Cache-control header (for the user's browser cache).
	- `missingImage` --- an image to use if the original cannot be loaded or times out. It can be a server path, or a URL (provided the `file_get_contents` works with URL wrappers on your server).
	- `secureHosts` --- an array of domain names that are known to work over HTTPS. Any HTTP images on these domains will be linked on the HTTPS version of the original domain instead of the proxy (for example `http://i.imgur.com` will be replaced by `https://i.imgur.com` instead of `imgproxy.php?...`).
	- `validMimes` --- a list of valid MIME types. This should not require editing but the option is there.

4. Log in to your Q2A site as an Administrator, head to Admin > Plugins and enable the plugin. Check everything is working as expected - images that were originally HTTP should now have a URL of the form `/qa-plugin/https-img-proxy/imgproxy.php?img=http%3A%2F%2Fexample.com%2Fimage.jpg&hash=abc123def456`
