# open-status-page
Open status page leveraging Uptime Robot and Twitter timeline.


## Installation
This site works fine on PHP 7.0; I'm sure it'll work on previous versions too, but why bother? Just move the file into your web directory and change the first 3 lines - $domain, $apiKey, and $twUser - to match your installation. Nothing else needs done since past that it uses CDN's.

You should probably also make sure that you have an Uptime Robot monitor created first.

## TODO
Add database-driven stuff so that you're not hitting the Uptime Robot API every time you load the page
