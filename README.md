## Cache Warmer plugin for Craft

Allows you to fire off one request to warm up your cache for selected sections.

### Usage

After clearing your cache, fire off a request to yoururl.com/actions/cacheWarmer/fire?key=yourkey.

The plugin uses Guzzle to fire off a batch of x requests to the urls of entries fetched from sections specified in the plugin settings.

## Future plans

- Add locale support (currently hardcoded to *en*)
- Incorporate Cache Clear (need to check with Jason first)
- Support cache warming of single entries when they are updated
- Support cache warming of specific cache key (You need to supply which url to call after clearing, though)

## License

[MIT](http://opensource.org/licenses/mit-license.php)

