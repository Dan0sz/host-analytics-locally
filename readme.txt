=== CAOS | Host Google Analytics Locally ===
Contributors: DaanvandenBergh
Tags: analytics, host, locally, ga, gtag, analytics, woocommerce, gdpr, cookie notice, leverage browser cache, minimize external requests
Requires at least: 4.6
Tested up to: 5.8
Stable tag: 4.1.6
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automagically download and update analytics.js/gtag.js, bypass Ad Blockers with Stealth Mode, add the tracking code to your site's footer and tons of other features!

== Description ==

CAOS (Complete Analytics Optimization Suite) for Google Analytics allows you to **host analytics.js/gtag.js** locally and keep it updated using WordPress' built-in Cron-schedule. Fully automatic!

Not a big Google Analytics user and just curious about your pageviews? CAOS fully supports [Minimal Analytics](https://minimalanalytics.com), which is basically Google Analytics Lite. An extremely lightweight alternative to Google Analytics' default libraries (analytics.js/gtag.js). *Minimal Analytics also helps you get rid of that annoying **Unused JS** notice in Google PageSpeed Insights!*

Whenever you run an analysis of your website on *Google Pagespeed Insights*, *Pingdom* or *GTMetrix*, it'll tell you to **leverage browser cache** when you're using Google Analytics. Because Google has set the cache expiry time to 2 hours. This plugin will get you a **higher score** on Pagespeed and Pingdom and make **your website load faster**, because the user's browser doesn't have to make a roundtrip to download the file from Google's external server.

Just install the plugin, enter your Tracking-ID and the plugin adds the necessary Tracking Code for Google Analytics to the header (or footer) of your theme, downloads and saves the analytics.js/gtag.js-file to your website's server and keeps it updated (automagically) using a scheduled script in wp_cron(). CAOS is a set and forget plugin.

For more information: [How to setup CAOS](https://daan.dev/wordpress-plugins/caos/?utm_source=wordpress&utm_medium=description&utm_campaign=caos).

== Features ==
- Host analytics.js or gtag.js locally ([What's the difference?](https://daan.dev/wordpress/difference-analyics-gtag-ga-js/?utm_source=wordpress&utm_medium=description&utm_campaign=caos)),
- Downloaded files are renamed to random strings to avoid ad blockers,
- Minimal Analytics support,
- Google Analytics V4 API support,
- Endlessly extensible for developers by using the integrated filters and available mini plugins! E.g. [track Google Adwords conversions](https://github.com/Dan0sz/caos-google-adwords) and much, much more!
- **[Bypass Ad Blockers](https://daan.dev/how-to/bypass-ad-blockers-caos/?utm_source=wordpress&utm_medium=description&utm_campaign=caos)** in Stealth Mode,
  - Plugins (e.g. linkid.js) are also served from a local source when in Stealth Mode!
  - *[CAOS Super Stealth Upgrade](https://ffw.press/wordpress-plugins/caos-super-stealth-upgrade/?utm_source=wordpress&utm_medium=description&utm_campaign=caos) adds compatibility with Enhanced Ecommerce (ec.js) and Analytics' Ecommerce Features (ecommerce.js).*
- Enable Cookieless Analytics (Super Stealth Upgrade required),
- Preconnect to google-analytics.com to reduce latency and speed up requests (if not using Stealth Mode),
- Send an event to your Google Analytics dashboard when a visitor is viewing your pages using an Ad Blocker,
- Capture outbound links,
- Integrate Google Optimize,
- Enhanced link attribution,
- Allow tracking always or only when a certain cookie exists or has a value -- [Read more about GDPR Compliance](https://daan.dev/wordpress/gdpr-compliance-google-analytics/?utm_source=wordpress&utm_medium=description&utm_campaign=caos),
- **Add tracking code** to header, **footer** or manually,
- Load the tracking snippet Asynchronous or Default (Synchronous)
- Fully compatible with [Google Analytics Dashboard Plugin for WP by MonsterInsights](https://daan.dev/wordpress/leverage-browser-caching-host-analytics-local-monster-insights/?utm_source=wordpress&utm_medium=description&utm_campaign=caos), WooCommerce Google Analytics Integration, Google Analytics Dashboard Plugin for WP by Analytify, RankMath SEO, SEOPress and Google Analytics Dashboard for WP by ExactMetrics,
- Save analytics.js/gtag.js anywhere within the WordPress content (wp-content) directory to avoid detection by WordPress security plugins (such as WordFence) or removal by caching plugins (such as WP Super Cache),
- Serve analytics.js/gtag.js from your CDN,
- Set Cookie Expiry Period,
- Set Site Speed Sample Rate,
- Set [Adjusted Bounce Rate](https://daan.dev/wordpress/adjusted-bounce-rate-caos/?utm_source=wordpress&utm_medium=description&utm_campaign=caos),
- Change enqueue order (prioritize order of loaded scripts),
- Enable Enhanced Link Attribution (linkid.js),
- Force disabling display features functionalities,
- Anonymize IP addresses,
- Track logged in Administrators,
- Manually update analytics.js/gtag.js with the click of a button!

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/host-analyticsjs-local` directory, or install the plugin through the WordPress plugins repository directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings -> Optimize Google Analytics screen to configure the plugin

== Frequently Asked Questions ==

= I have another file I want to host locally. Could you make a plugin? =

Maintaining multiple plugins besides my daily 9-to-5 job is a handful, so no. If you're looking for a way to host Google Webfonts locally; please install [OMGF](https://wordpress.org/plugins/host-webfonts-local/).

= Why am I getting an Unused JS notice on Google PageSpeed Insights for analytics.js/gtag.js? =

Google Analytics offers two code libraries: analytics.js and gtag.js. A library offers easy implementation of a certain application, but this wide compatibility range comes at a cost: it probably contains a bunch of code you don't even need.

If you are a light-weight Google Analytics user, you can enable Minimal Analytics in CAOS' Basic Settings: Settings > Optimize Google Analytics > Basic Settings > Snippet Type: Minimal Analytics.

Minimal Analytics is fully compatible with Stealth Mode.

= CAOS says analytics.js/gtag.js isn't updated in more than two days. What's going on? =

This is due to server configuration. Probably a permissions issue. It might be that PHP/WordPress isn't allowed to create files programmatically. If you can upload media files just fine, then this probably isn't the issue. Is your cron running? Try clicking the 'update analytics.js/gtag.js' option to update the file manually. Meanwhile, contact your hosting provider so they can help you figure out if your cron is running correctly.

= Can the gtm/js file used by Google Optimize be hosted locally? =

No, it can't. The gtm/js (or gtm.js) file is generated using a Client ID, which is unique for each visitor of your site. Saving this file locally would break your A/B tests.

= I have disabled the plugin, but the Google Analytics tracking code is still added to the page. What's going on? =

This question has been asked on the support forum lots of times and in ALL cases it was caused by a Full Page Caching plugin, such as WP Fastest Cache or WP Super Cache, or another caching mechanism, such as Varnish. Possibly the page containing the snippet was cached in its entirety, and that cached version is still loading. Try flushing the plugin's cache and empty your browser's cache. Then reload the page.

= After installing this plugin I'm getting a 'Load resources from a consistent URL'-error on Google Pagespeed or GT Metrix. How can I fix this? =

CAOS *adds* a modified version of the Google Analytics tracking code to your header or footer, depending on your settings. If you're getting this notification (or optimization suggestion) this means that besides the GA tracking code added by CAOS, you're also loading a second tracking code somewhere else in your blog. Possibly in your theme's options or by another Google Analytics plugin. Remove/disable this and you're good to go.

= Is this plugin GDPR compliant? =

Yes, it is! It is compatible with all Wordpress Cookie Notice plugins which either set a cookie to allow tracking or set a certain value to a cookie to allow tracking. It's completely customizable!

= Google Tag Assistant says analytics.js is missing. Is this normal? =

Yes, this is normal. This browser extensions looks for this exact string 'www.google-analytics.com/analytics.js'. Because with CAOS you're hosting it locally, Tag Assistant doesn't find this string and returns an error. However, you can still use Tag Assistant to verify tracking works.

= Is CAOS compatible with WooCommerce? =

Yes, it is! CAOS is compatible with several Google Analytics plugins for WooCommerce.

= I use WP Super Cache / W3 Total Cache or another Caching and/or Minification plugin. Why is it removing analytics.js when I flush my cache? =

This happens because analytics.js is saved within the 'cache'-folder and for some reason the cache/minify plugin thinks it should be refreshed. Change the path where analytics.js is saved within CAOS to a path outside the /uploads/ folder (e.g. /uploads/caos/). This should resolve any issues you're having.

= WordFence (or another security plugin) is detecting file changes in analytics.js/gtag.js. What's going on? =

This is perfectly normal, since this is the file that was updated by the built-in cronjob in older versions of CAOS. Update to the latest version and change the path where analytics.js is saved within CAOS to a path which is ignored by your security plugin (e.g. /uploads/ or /cache/). This should resolve any issues you're having.

= What is the current update interval of the Analytics-script? =

I have set it to twice daily, because Google updates the script very often. If you suspect you might've gotten behind (which I doubt) Run a manual update button within CAOS' settings.

= I disabled Demographic Reports in Google Analytics, but the script is still redirecting to doubleclick.net. How do I turn this off? =

Try enabling the option called "Disable all display features functionality?" With this you can enforce the disabling of the DisplayFeatures plugin.

= I've installed your plugin but analytics.js/gtag.js is still showing up as an external request in Pingdom e.a.? =

CAOS adds a local file called gtag.js/analytics.js (depending on your choice), which enables you to use Analytics, while hosting the necessary files locally. This doesn't mean that it scans your entire plugins or themes directory for other manually/programatically added Analytics tracking-code. I.e. If analytics.js is still showing up in the list of requests, this mean that something else (probably the theme you're using or another plugin) is adding this tracking code to your Wordpress Install. Find it. Remove it. And let CAOS take care of sending your needed data to Google Analytics.

= I use a CDN. Can I use CAOS in combination with my CDN? =

Yes, you can! Simply add the URL of your CDN within the advanced options and analytics.js/gtag.js will be served from your CDN.

== Screenshots ==

N/A

== Changelog ==

= 4.1.6 =
* Fix: Adjusted Bounce Rate setting was broken after moving it to the Advanced Tab.

= 4.1.5 | September 10th, 2021 =
* Dev: 3rd party plugins can now easily modify CAOS' admin screen (e.g. adding/saving/modifying settings).
* Enhancement: Minimal Analytics code is now minified.
* Fix: Explicitly enabling Display Features (if not disabled within the settings) should fix missing Demographics reporting.
* Moved Adjusted Bounce Rate setting to Advanced Settings tab.

= 4.1.4 | August 18th, 2021 =
* Enhancement: The hit type parameter can now be filtered to allow more flexibility when ad blocker lists are updated. (hint: Super Stealth)

= 4.1.3 | July 28th, 2021 =
* Enhancement: Added news real for CAOS relevant blog posts in footer of CAOS' settings screen.
* Fix: Super Stealth Upgrade promotion material is removed from options that're free.
* Tested with WP 5.8.

= 4.1.2 | June 16th, 2021 =
* Fix: When gtag.js was used, SSL was enabled and your site was behind a load balancer/reverse proxy, CAOS attempted to load analytics.js thru a non-SSL link, which would break Analytics.
* Fix: PNG logo in footer of settings screen would break on Apple machines.

= 4.1.1 | June 7th, 2021 =
* Dev: added hooks around tracking code to allows developers to easily alter the tracking code.
* Fix: When Google Analytics and Google Ads are connected, this is now properly handled gtag.js is used.
* Fix: Tracking code can now be properly translated by translating the string UA-123456789.
* Fix: Capture Outbound Links is only triggered on left click (instead of all mousedown events).
* Feature: Cookieless Analytics is now available under Advanced Settings when CAOS Super Stealth Upgrade is installed and active.
* UX: Re-worded some options and option descriptions.

= 4.1.0 | June 1st, 2021 =
- Tracking ID can now be translated with plugins like WPML, so you can set different tracking IDs per language.
- Added compatibility modes for SEOPress and RankMath SEO.

= 4.0.5 | May 5th, 2021 =
* Added extra debug points for Stealth Mode users using Analytics plugins (ec.js, linkid.js, etc.)

= 4.0.4 | April 27th, 2021 =
* Footer logo in settings screen is now loaded from local src,  instead of external src.
* Added extra debug points for Stealth Mode users using CloudFlare.

= 4.0.3 | April 12th, 2021 =
* Small performance increase by removing unused code and re-factoring code.
* Google Analytics plugins (e.g. linkid.js, ec.js, etc.) are no longer renamed, because this would cause 404-errors.

= 4.0.2 | April 6th, 2021 =
* File alias will now only be generated once, and will not change afterwards.
* Fixed the confirmation notice after saving changes.
* Don't show file last updated notice if Minimal Analytics is used.
* Fixed some PHP warnings and notices.

= 4.0.1 | April 2nd, 2021 =
* Don't show notice when Minimal Analytics is used.
* Use local path to load JS/CSS assets, instead of URL (what was I thinking?)

= 4.0.0 | March 29th, 2021 =
* Added 'Cookie value contains' option to Allow Tracking setting, to increase compatibility with plugins like Borlabs Cookie.
* All downloaded files (e.g. analytics.js or gtag.js) are now renamed to random strings, to avoid being blocked by Ad Blockers.
* Fixed some console errors, like when CookieValue is null and when document.body doesn't exist (yet)
* Update analytics.js/gtag.js is now ran directly after Saving Changes in settings screen.
* GA V4 endpoints are now filterable.
* GA V4 is marked as beta and Gtag V3 is no longer marked as deprecated.
* Added extra debug information to Update Script when Debug Mode is enabled.
* Moved sidebar to separate Help tab leaving more space for settings and descriptions.
* Admin CSS and JS assets are now loaded inline to stop ad blockers from messing with CAOS' admin functionality.
* Outbound Link Tracking is no longer locked when (Super) Stealth Mode is enabled, because on same (fast) servers it does work in Stealth Mode.
* Code clean-up and overall UX improvements.
* Allround performance improvements and tweaks.

= 3.11.0 | March 19th, 2021 =
* Added Debug Mode in Advanced Settings
* Fixed bug in CloudFlare IP Geolocation detection

= 3.10.1 | March 19th, 2021 =
* Re-factored CAOS_API_Proxy class to make it more readable and logical.
* Increased CloudFlare compatibility for Stealth Mode (Lite) and Super Stealth:
  - If CloudFlare's GeoIP Location option is enabled, this will be used to overwrite the location derived from the user's IP address.

= 3.10.0 | March 14th, 2021 =
* Added Advanced Setting: Site Speed Sample Rate.

= 3.9.4 | Martch 10th, 2021 =
* Minor code optimizations.
* Increased compatibility for WP Bedrock configurations.
* Removed notice stating that gtag.js isn't compatible with Stealth Mode (because it is since 3.9.0.)

= 3.9.3 | March 9th, 2021 =
* Tested with WP 5.7.
* Fixed bug in Outbound Links Tracking script.

= 3.9.2 | February 18th, 2021 =
* As if the previous release wasn't embarrassing enough. This release *really* fixes the bug.
* Cleaned-up sidebar and added links to documentation.

= 3.9.1 | February 17th, 2021 =
* Quick patch release, because I'm a bit of a dumdum. Using gtag.js with Stealth Mode would result in requests being sent through the wrong URL.

= 3.9.0 | February 4th, 2021 =
* Re-introduced Stealth Mode compatibility for gtag.js for Google Analytics V3 API.
* Reviewed plugins in Compatibility Mode:
  * Updated MonsterInsights compatibility to also include gtag.js.
  * Updated WooCommerce Google Analytics Integration to also include Google Analytics' V4 API (Global Site Tag) option.
* Added Stealth Mode Lite support for Google Analytics V4 API.

= 3.8.1 | January 27th, 2021 =
* Improved compatiblity with WordPress in Subdirectory installs.

= 3.8.0 | January 2nd, 2021 =
* Added support for Google Analytics V4 API (Stealth Mode not (yet) supported).
* Gtag.js (v3 API) is now deprecated in favor of gtag.js (v4 API).

= 3.7.8 | September 15h, 2020 =
* API endpoints are now registered in front- and backend.
* Performance improvements for class-loader.

= 3.7.7 =
* Last update broke Minimal Analytics. Sorry, guys. This fixes it.

= 3.7.6 | August 26th, 2020 =
* Slowly recovering from extreme sleep deprivation. ;)
* Updated custom script attributes filter to work with re-factored templating methods.
* Minor code optimizations in templates.
* Fixed bug where in some situations the Google Analytics disable script (When cookie is (not) set, etc.) wouldn't load.

= 3.7.5 =
* Set default value of Cookie Expiry to 30 days to prevent multiple sessions from the same IP.

= 3.7.4 =
* Tested with WP 5.5.
* Re-added custom attribute filters for Complianz.io compatibility. Sorry, Aert!
* Minor re-factors.

= 3.7.3 =
* Fixed bug where tracking code wouldn't render when Add Manually was selected under Basic Settings.
* Fixed bug where the Basic Settings tab would be tracked in Google Analytics.
* Minimal Analytics no longer depends on jQuery.

= 3.7.2 =
* jQuery is no longer used in the frontend (e.g. for Track Ad Blockers option).
* Fixed bug where Track Ad Blockers wouldn't work with gtag.js.
* Replaced jQuery in frontend with vanilla JavaScript to avoid conflicts with JS optimization plugins.

= 3.7.1 =
* Fixed bug where Minimal Analytics wouldn't run if gtag.js was previously set as remote JS file.

= 3.7.0 | August 9th, 2020 =
* My daughter, Emma, is exactly one month old now!
* *Track Ad Blockers* events are now sent *after* the Pageview is sent. Fixing the bug for it to create sessions instead of pageviews.
* Added Minimal Analytics support to allow (light-weight) users to get rid of that nasty **Unused JS** notice in Google PageSpeed Insights.
  * This option can be enabled in Basic Settings > Snippet Type.
* Fixed event category and label for Adjusted Bounce Rate for gtag.js.
* Moved Capture Outbound Links and Adjusted Bounce Rate options to Extensions tab.
* Moved Track Logged In Administrators? and Anonymize IP to Basic Settings tab.
* Added Outbound Link Tracking support for gtag.js.
* Outbound Link Tracking script is now loaded in line, to prevent blocking by Ad Blockers.
* Dropped Stealth Mode support for gtag.js, because it has become unusable after an update of Global Site Tag. Will research the possibility of re-adding it.
  * gtag.js will load analytics.js from google-analytics.com from now on.
* CAOS now uses the same autoloader as OMGF (or other FFWP plugins -- if installed) removing overhead and effectively increasing performance.
* Added release dates to the changelog. :)

= 3.6.0 | June 27th, 2020 =
* CAOS now throws a notice if an Ad Blocker is enabled on CAOS' admin screen, because Ad Blockers block any URL with 'analytics' in it, e.g. host-analyticsjs-local, which'll cause manual updates to not work properly.
* Added feature to send a custom event to Google Analytics when a page is viewed with an Ad Blocker enabled.
* DNS-prefetch resource hint to google-analytics.com is now added automatically when Stealth Mode is disabled.
* Changed CAOS' default cache-path to /uploads/caos/
* Minor refactors and code optimizations.

= 3.5.3 | May 29th, 2020 =
* UX improvements and better notices.
* Updated FAQ.

= 3.5.2 | May 15th, 2020 =
* Some options in extensions tab are now hidden when Compatibility Mode is enabled.
* Run cron twice daily to prevent schedule misses.

= 3.5.1 | May 12th, 2020 =
* Added clearer descriptions for some settings in the Extensions tab.
* Added more filter and action hooks.
* Added fix for SameSite cookie policy for analytics.js and gtag.js. It's not yet supported for linkid.js.

= 3.5.0 | April 26th, 2020 =
* Plugins (e.g. linkid.js or ec.js) are now handled twice as fast with the new *Extensions* > *Plugin Handling* option set to **Experimental** Mode.
  * Mind you that loading ec.js in Stealth Mode requires CAOS Super Stealth Upgrade to work.

= 3.4.4 | April 21st, 2020 =
* It's my 35th birthday!
* Added polyfill for is_plugin_active to fix Fatal Errors in certain WordPress configurations.

= 3.4.3 | April 20th, 2020 =
* [HOTFIX] Fix for Fatal Error: Call to undefined function get_plugins().

= 3.4.2 =
* Added notices for settings incompatible with (Super) Stealth Mode to increase UX.
* CAOS Super Stealth Upgrade now supports gtag.js.
* Minor code optimizations.

= 3.4.1 =
* Fixed syntax errors in sidebar.

= 3.4.0 | April 19th, 2020 =
* Code improvements for setting screen.
* Added support for Enhanced Link Attribution (also works with (Super) Stealth.)
* Added more filters and hooks for developers to extend CAOS.
* Added support for Google Optimize (not compatible with Stealth Mode, and plugin file is not hosted locally, because file differs per user).
* Fixed bug where notices would sometimes be displayed twice.
* Added several notice to improve UX.

= 3.3.7 | April 8th, 2020 =
* Added filter hooks for when Stealth mode is enabled/disabled, to improve UX.
* Added filter hooks for tweet and review link, to improve UX.

= 3.3.6 =
* Possible fix for Cron issues.

= 3.3.5 =
* HOTFIX: Stealth Mode filter to be used for Super Stealth Mode.

= 3.3.4 | April 4th, 2020=
* Stealth Mode now respects the Anonymize IP setting.
* Added some notices and reminders when some settings are changed, to improve UX.
* Notices are now grouped.
* When an update of the JS library is required after changing the settings, you will now be notified.

= 3.3.3 | March 27th, 2020 =
* Added tabs in Settings to comply with WordPress plugin conventions == major code clean-up.

= 3.3.2 | March 23rd, 2020 =
* Admin screen is now responsive.
* Sidebar scrolls on larger screens.

= 3.3.1 =
* Fixed bug of paths to JS files for Capture Outbound Links and Admin JS.

= 3.3.0 | March 22nd, 2020 =
* Added new notices interface. Notices are now dismissible and CAOS now throws a global notice to notify you of issues with your analytics/gtag file.
* Revamped settings screen with toggleable interface.

= 3.2.0 | January 31st, 2020 =
CAOS can now preconnect to google-analytics.com and CDN URL (if set).

= 3.1.3 | January 21st, 2020 =
Do not output success message when update-script is executed by CRON.
Added extra filter to allow further manipulation of the analytics.js tracking code.

= 3.1.2 | January 8th, 2020 =
Added filters on script elements.

= 3.1.1 | January 7th, 2020 =
CAOS now throws a notice if cURL is disabled on the server.

= 3.1.0 | January 5th, 2020 =
Added filter to add additional configuration using the gtag tracking snippet. Updated readme.txt.

= 3.0.1 | January 3rd, 2020 =
Fixed bug where using Adjusted Bounce Rate would trigger two pageviews.

= 3.0.0 =
Major code overhaul. Major performance improvements.

= 2.9.4 =
Further improvements for downloading of analytics.js.

= 2.9.3 =
Fixed minor 'ga is not defined'-bug when user is logged in.

= 2.9.2 =
Track outbound links doesn't work when Stealth Mode is enabled. Added additional verification in settings form.

= 2.9.1 =
Forgot to add caos-frontend.js to SVN before committing previous version.

= 2.9.0 =
Tested with WP 5.3. Added new feature to track outbound links.

= 2.8.2 =
Replaced file_get_contents() and with cUrl to make CAOS compatible with servers that have `allow_url_fopen` disabled.

= 2.8.1 =
Code optimizations: the tracking code snippets can now be modified using `add_filter()`. CAOS' own settings are also added using those filters.

= 2.8.0 =
Major overhaul of code to minimize plugin size and maximize performance.
[BUGFIX] the origin User-Agent is now passed to Google Analytics, instead of 'WordPress'.

= 2.7.11 =
Severely improved performance of update-analytics script.

= 2.7.10 =
Apparently some servers require to die() after setting a location header in PHP.

= 2.7.9 =
Forgot a slash.

= 2.7.8 =
Damn Subversion. Had to add new file to versioning.

= 2.7.7 =
Stealth Mode now also works for Google Analytics Enhanced Link Attribution plugin.

= 2.7.5 =
Optimized Javascript.

= 2.7.4 =
UX improvements in Settings-area. Code re-factors.

= 2.7.3 =
Bugfix for 'when cookie is set'-option. Added 'when cookie is NOT set'-option. Code optimization for 'when cookie has value'-option.

= 2.7.1 =
Bugfix for detecting user's location when Stealth Mode is used.

= 2.7.0 =
Added new feature: Stealth Mode allows you to Bypass Ad Blockers and protects your Google Analytics data.

= 2.6.5 =
Code optimizations. Preparations for really cool (still secret) new feature!

= 2.6.4 =
Added compatibility with WooCommerce Google Analytics Integration!

= 2.6.3 =
Fixed 'getCookieValue() is not a function.'-bug when using 'Allow tracking' options.

= 2.6.2 =
Fixed a bug where sometimes the default tracking code wasn't loaded correctly.

= 2.6.1 =
CAOS can now be properly translated.

= 2.6.0 =
Added support for asynchronous loading of the snippet.

= 2.5.0 =
When using the gtag.js wrapper, the request to analytics.js is now also loaded from a local source!
Added link to explain differences between analytics.js and gtag.js.

= 2.4.3/2.4.4 =
Oops! Forgot some strings!

= 2.4.2 =
Added translations and text domain according to WordPress' requirements.

= 2.4.1 =
Changed name back to CAOS, because OMGF has its own shorthand now.

= 2.4.0 =
Added compatibility mode for Google Analytics Dashboard for WP by ExactMetrics. If you're using any compatibility mode, the settings path has changed, so you need to set it again.

= 2.3.5 =
The cache-file and folder are now removed at plugin uninstall.

= 2.3.4 =
Added uninstall option and script.

= 2.3.3 =
Fixed minor console error

= 2.3.2 =
Fixed Adjusted Bounce Rate for gtag.js.

= 2.3.1 =
Fixed bug where sometimes wp-content directory wasn't detected correctly.

= 2.3.0 =
Support for gtag.js added. Minor code optimizations.

= 2.2.2 =
Added option to cache ga.js locally, instead of analytics.js.

= 2.2.1 =
Updated Welcome Panel

= 2.2.0 =
Added CDN support

= 2.1.7 =
Changed URLs to new home: daan.dev

= 2.1.6 =
Added compatibility with Analytify for WordPress.

= 2.1.5 =
Added extra checks for PHP intl-module, which apparently isn't enabled by default on all servers.

= 2.1.4 =
Bugfix for display of formatted Date/Time.

= 2.1.2 =
Fallback for servers who don't have certain PHP date-modules installed.

= 2.1.1 =
Status bar now displays a date/time formatted according to locale chosen in WordPress.

= 2.1.0 =
Added status bar display cron and file health.

= 2.0.4 =
CAOS is now compatible with WordPress Multi Site.

= 2.0.3 =
Tested with WP 5+

= 2.0.2 =
Fixed bug where settings couldn't be saved.

= 2.0.1 =
Refactored code for includes to increase compatibility. Code optimizations. Renamed tasks to reflect code and name changes. Changed menu slug to reflect name changes.

= 2.0.0 =
Finally rid of the ugly versioning. Added settings link to plugins overview.

= 1.97 =
Compatibility fix for PHP versions lower than 5.4.

= 1.95 =
Added option to change the directory where analytics.js is saved -- relative to WordPress' content directory (usually wp-content).

= 1.94 =
Fixed directory creation error. Removed notice from admin-screen, because it caused bugs on some systems.

= 1.93 =
Moved analytics.js to wp-content/cache to maximally optimize compatibility with WordPress security plugins.

= 1.91 / 1.92 =
Updated readme.txt. Refactored code and minor improvements.

= 1.90 =
Renamed local-ga.js to WordPress' upload-directory and renamed it to analytics.js to make the file more recognizable for less experienced users.

= 1.85 =
Code optimizations and added function to trigger the update script manually.

= 1.83 =
Fixed bug where manually add tracking code wouldn't show the tracking snippet.

= 1.82 =
Minor usability fixes.

= 1.81 =
Replace relative paths with absolute paths.

= 1.80 = MAJOR UPDATE & Name change
Changed the name from Complete Analytics Optimization Suite to CAOS for Analytics. Because it's cooler IMO.
CAOS is now compatible with Google Analytics by Monster Insights. This allows users of e-Commerce platforms such as WooCommerce to also locally host their analytics.js-file!

= 1.72 =
To improve compatibility with other plugins and themes, I added an option to add the snippet manually. So e.g. it can be added to a theme's 'custom head' field or blocked until a Cookie Notice is approved.

= 1.70 =
UX optimizations in Admin-screen. Major code optimizations.

= 1.67 =
Bugfix.

= 1.66 =
Code optimizations.

= 1.65 =
Fixed bug in admin-screen, where new options weren't always shown.
Code optimizations.

= 1.64 =
To maximize compatibility with other GDPR plugins I've added the option to choose whether to allow tracking when a certain cookie is set or when it has a certain value. Otherwise tracking will not be allowed.

= 1.61 =
Moved to Github.

= 1.60 - BUGFIXES =
Fixed important bug where cookie value wasn't read correctly, if user rejected cookies.
Code optimizations

= 1.56 - IMPROVEMENTS =
Minor usability improvements.

= 1.55 - IMPROVEMENTS =
Minor coding/performance/usability improvements.

= 1.53 - BUGFIX =
Fixed important bug which would render the entire plugin useless if GDPR Compliance was disabled.

= 1.51 - Quickfix =
Added an option which explicitly enables/disables all GDPR functions.

= 1.50 - New Features =
Added options to make CAOS GDPR compliant. Thanks, Peter from [Applejack](https://www.applejack.co.uk/)!
Minor optimizations and bugfixes.
Updated readme.txt to reflect GDPR compliance.

= 1.45 =
Updated FAQ.
Tested with latest WP versions.

= 1.43 =
Added feature to specify URL to CDN.
Updated readme.txt.

= 1.42 =
Added feature to disable [DisplayFeatures](https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features) plug-in, because sometimes disabling it from the Google Analytics options-panel isn't enough.

= 1.41 =
Version change, to push it through the auto-updater in Wordpress.

= 1.40 - New Features =
Added the option for tracking administrators. It completely disables the code for Wordpress administrators i.e. your other logged in users will still be tracked. Made the plugin fully translatable. Did some code optimizations.

PS. Just figured out I should've never used 1.36, as SVN now thinks 1.4 is an older version. So here we go, version 1.40 (Bluegh! I'll do it right once we hit 2.0)

= 1.36 - Bugfixes =
With new features comes great responsibility. My German neighbor Denis Abendroth was so kind to point out that the anonymize IP option was added in the wrong order in the tracking code. So I fixed it immediately!

= 1.35 - New Features =
This update features the much requested Anonymize IP (@arcticblue, thanks for your help!), which is now added to the options panel. Besides that I optimized the code a bit, removing about 5 lines of code.

= 1.32 - New Features =
Added option to change the enqueue order of the tracking code. That way the user can decide the priority of the tracking code.

= 1.31 - Bugfixes =
Fixed the Adjusted Bounce Rate issue: when enabled it breaks the tracking. I have made some code-changes that should fix this issue, according to the script provided by @BrianLeeJackson (Thnx for your help!)

= 1.3 - New Features =
Added option for adjusted bounce rate
Added option for loading Analytics Tracking ID in footer

= 1.2 =
Replaced short PHP-tags with regular '<?php' to increase compatibility. (Thanks for the tip @burhandodhy!)

= 1.1 =
Updated readme.txt and some minimal code changes to increase UX.

= 1.0 =
First release! No changes so far!
