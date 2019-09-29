=== CAOS | Host Google Analytics Locally ===
Contributors: DaanvandenBergh
Donate link: https://daan.dev/donate/
Tags: analytics, host, locally, ga, gtag, analytics, woocommerce, gdpr, cookie notice, leverage browser cache, minimize external requests
Requires at least: 4.6
Tested up to: 5.2
Stable tag: 2.8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automagically download and update analytics.js/ga.js/gtag.js, bypass Ad Blockers with Stealth Mode, add the tracking code to your site's footer and tons of other features!

== Description ==

CAOS (Complete Analytics Optimization Suite) for Google Analytics allows you to **host analytics.js/gtag.js/ga.js** locally and keep it updated using WordPress' built-in Cron-schedule. Fully automatic!

Whenever you run an analysis of your website on *Google Pagespeed Insights*, *Pingdom* or *GTMetrix*, it'll tell you to **leverage browser cache** when you're using Google Analytics. Because Google has set the cache expiry time to 2 hours. This plugin will get you a **higher score** on Pagespeed and Pingdom and make **your website load faster**, because the user's browser doesn't have to make a roundtrip to download the file from Google's external server.

Just install the plugin, enter your Tracking-ID and the plugin adds the necessary Tracking Code for Google Analytics to the header (or footer) of your theme, downloads and saves the analytics.js/ga.js/gtag.js-file to your website's server and keeps it updated (automagically) using a scheduled script in wp_cron(). CAOS is a set and forget plugin.

Please keep in mind that, although I try to make the configuration of this plugin as easy as possible, the concept of locally hosting a file or optimizing Google Analytics for *Pagespeed Insights* or *GT Metrix* has proven to be confusing for some people. If you're not sure of what your doing, please consult a SEO expert or Webdeveloper to help you with the configuration and optimization of your WordPress blog. Or feel free to [contact me](https://daan.dev/contact/) for a quote.

For more information: [How to setup CAOS](https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/).

== Features ==
- Host analytics.js, ga.js or gtag.js locally ([What's the difference?](https://daan.dev/wordpress/difference-analyics-gtag-ga-js/)),
- When using gtag.js, the underlying request to analytics.js is also loaded from the local source!
- **Bypass Ad Blockers** in Stealth Mode: Sneak past Security and protect your Google Analytics data,
- Allow tracking always or only when a certain cookie exists or has a value -- [Read more about GDPR Compliance](https://daan.dev/wordpress/gdpr-compliance-google-analytics/),
- **Add tracking code** to header, **footer** or manually,
- Load the tracking snippet Asynchronous or Default (Synchronous)
- Fully compatible with [Google Analytics Dashboard Plugin for WP by MonsterInsights](https://daan.dev/wordpress/leverage-browser-caching-host-analytics-local-monster-insights/), WooCommerce Google Analytics Integration, Google Analytics Dashboard Plugin for WP by Analytify and Google Analytics Dashboard for WP by ExactMetrics,
- Save analytics.js/ga.js/gtag.js anywhere within the WordPress content (wp-content) directory to avoid detection by WordPress security plugins (such as WordFence) or removal by caching plugins (such as WP Super Cache),
- Serve analytics.js/ga.js/gtag.js from your CDN,
- Set Cookie Expiry Period,
- Set Adjusted Bounce Rate,
- Change enqueue order (prioritize order of loaded scripts),
- Force disabling display features functionalities,
- Anonymize IP addresses,
- Track logged in Administrators,
- Remove script from wp-cron, so you can add it manually to your Crontab,
- Manually update analytics.js/ga.js/gtag.js with the click of a button!

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/host-analyticsjs-local` directory, or install the plugin through the WordPress plugins repository directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings -> Optimize Analytics screen to configure the plugin

== Frequently Asked Questions ==

= I have another file I want to host locally. Could you make a plugin? =

Maintaining two plugins besides my daily 9-to-5 job is a handful, so no. If you're looking for a way to host Google Webfonts locally; please install [OMGF](https://wordpress.org/plugins/host-webfonts-local/). For anything else, please follow the steps in [this how-to](https://daan.dev/how-to/host-js-locally-crontab/).

= CAOS says analytics.js/gtag.js/ga.js isn't updated in more than two days. What's going on? =

This is due to server configuration. Probably a permissions issue. It might be that PHP/WordPress isn't allowed to create files programmatically. If you can upload media files just fine, then this probably isn't the issue. Is your cron running? Try clicking the 'update analytics.js/gtag.js/ga.js' option to update the file manually. Meanwhile, contact your hosting provider so they can help you figure out if your cron is running correctly.

= I have disabled the plugin, but the Google Analytics tracking code is still added to the page. What's going on? =

This question has been asked on the support forum lots of times and in ALL cases it was caused by a Full Page Caching plugin, such as WP Fastest Cache or WP Super Cache, or another caching mechanism, such as Varnish. Possibly the page containing the snippet was cached in its entirety, and that cached version is still loading. Try flushing the plugin's cache and empty your browser's cache. Then reload the page.

= After installing this plugin I'm getting a 'Load resources from a consistent URL'-error on Google Pagespeed or GT Metrix. How can I fix this? =

CAOS *adds* a modified version of the Google Analytics tracking code to your header or footer, depending on your settings. If you're getting this notification (or optimization suggestion) this means that besides the GA tracking code added by CAOS, you're also loading a second tracking code somewhere else in your blog. Possibly in your theme's options or by another Google Analytics plugin. Remove/disable this and you're good to go.

= Is this plugin GDPR compliant? =

Yes, it is! It is compatible with all Wordpress Cookie Notice plugins which either set a cookie to allow tracking or set a certain value to a cookie to allow tracking. It's completely customizable!

= Google Tag Assistant says analytics.js is missing. Is this normal? =

Yes, this is normal. This browser extensions looks for this exact string 'www.google-analytics.com/analytics.js'. Because with CAOS you're hosting it locally, Tag Assistant doesn't find this string and returns an error. However, you can still use Tag Assistant to verify tracking works.

= I use Google Analytics by Monster Insights. Is CAOS compatible with that plugin? =

Yes, it is! Since version 1.80 CAOS is completely compatible with Google Analytics for Wordpress by Monster Insights. Just enable the option 'Enable compatibility with Monster Insights?' within the 'Optimize Analytics'-screen and CAOS will automagically replace the default external source of analytics.js to your locally hosted file.

= Is CAOS compatible with WooCommerce? =

Yes, it is! CAOS is completely compatible with WooCommerce. If you're a WooCommerce-user, install WooCommerce Google Analytics Integration (compatible since v2.6.4) or Monster Insights' Google Analytics (compatible since v1.8.0) plugin and enable the corresponding compatibility mode in CAOS.

= I use WP Super Cache / W3 Total Cache / Autoptimize or another Caching and/or Minification plugin. Why is it removing analytics.js when I flush my cache? =

This happens because analytics.js is saved within the 'cache'-folder and for some reason the cache/minify plugin thinks it should be refreshed. Change the path where analytics.js is saved within CAOS to a path outside the /cache/ folder (e.g. /caos-cache/analytics/). This should resolve any issues you're having.

= WordFence (or another security plugin) is detecting file changes in analytics.js/gtag.js/ga.js. What's going on? =

This is perfectly normal, since this is the file that was updated by the built-in cronjob in older versions of CAOS. Update to the latest version and change the path where analytics.js is saved within CAOS to a path which is ignored by your security plugin (e.g. /uploads/ or /cache/). This should resolve any issues you're having.

= What is the current update interval of the Analytics-script? =

I have set it to daily, because Google updates the script very often. Also, the daily interval is the longest interval that wp_cron() allows, as far as I know. If you suspect you might've gotten behind (which I doubt) I've implement a manual update button within the CAOS' settings.

= The ga.js/analytics.js/gtag.js-file remains empty! What should I do? =

Make sure you are running the latest version, as I added some compatibility fixes along the way. If this doesn't resolve your issue, then your wp-cron isn't working properly. This is a server related issue. You can add the 'includes/update-analytics.php'-file to [your crontab](http://crontab-generator.org/ "Click here to create a crontab line using Crontab Generator"). As this problem has been resolved many times already, check the forum!

= I just updated to the latest version and the tracking stopped working! =

Probably your gtag.js/analytics.js/ga.js file got overwritten and emptied. Try activating and de-activating the plugin, otherwise this will automatically resolve itself after the cronjob has run.

= I disabled Demographic Reports in Google Analytics, but the script is still redirecting to doubleclick.net. How do I turn this off? =

Try enabling the option called "Disable all display features functionality?" With this you can enforce the disabling of the DisplayFeatures plugin.

= I've installed your plugin but analytics.js/ga.js/gtag.js is still showing up as an external request in Pingdom e.a.? =

CAOS adds a local file called gtag.js/analytics.js/gtag.js (depending on your choice), which enables you to use Analytics, while hosting the necessary files locally. This doesn't mean that it scans your entire plugins or themes directory for other manually/programatically added Analytics tracking-code. I.e. If analytics.js or ga.js is still showing up in the list of requests, this mean that something else (probably the theme you're using or another plugin) is adding this tracking code to your Wordpress Install. Find it. Remove it. And let CAOS take care of sending your needed data to Google Analytics.

= I use a CDN. Can I use CAOS in combination with my CDN? =

Yes, you can! Simply add the URL of your CDN within the advanced options and analytics.js/gtag.js/ga.js will be served from your CDN.

= Can I buy you a beer? =

Yes, please! [Click here to buy me a beer](http://daan.dev/donate/ "Let's do shots!")!

== Screenshots ==

N/A

== Changelog ==

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
Stealth Mode now also works for Google Analytics' Enhanced Commerce and Enhanced Link Attribution plugins.

= 2.7.6 =
Added fix for Stealth Mode, when using Google Analytics E-commerce features. Known issue: the download
of ec.js does not bypass all Ad Blockers.

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
Added link to explain differences between ga.js, analytics.js and gtag.js.

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
