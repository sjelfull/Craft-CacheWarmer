## Cache Warmer plugin for Craft

Allows you to fire off one request to warm up your cache for selection sections.

### Usage

After clearing your cache, fire off a request to yoururl.com/actions/cacheWarmer/fire?key=yourkey.

The plugin uses Guzzle to fire off a batch of x requests to the urls of entries fetched from sections specified in the plugin settings.

## License

[MIT](http://opensource.org/licenses/mit-license.php)
