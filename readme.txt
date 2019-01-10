=== CAOS | Host Google Analytics Locally ===
Contributors: DaanvandenBergh
Donate link: https://dev.daanvandenbergh.com/donate/
Tags: leverage browser cache, host analytics locally, google analytics, monster insights, gdpr, cookie notice, minimize external requests
Requires at least: 4.5
Tested up to: 5.0
Stable tag: 2.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automagically download analytics.js, keep it updated using WordPress' built-in Cron-schedule, generate the tracking code, add it to your site's header or footer and tons of other features!

== Description ==

CAOS for Google Analytics allows you to **host analytics.js** locally and keep it updated using WordPress' built-in Cron-schedule. Fully automatic!

Whenever you run an analysis of your website on *Google Pagespeed Insights*, *Pingdom* or *GTMetrix*, it'll tell you to **leverage browser cache** when you're using Google Analytics. Because Google has set the cache expiry time to 2 hours. This plugin will get you a **higher score** on Pagespeed and Pingdom and make **your website load faster**, because the user's browser doesn't have to make a roundtrip to download the file from Google's external server.

Just install the plugin, enter your Tracking-ID and the plugin adds the necessary Tracking Code for Google Analytics to the header (or footer) of your theme, downloads and saves the analytics.js-file to your website's server and keeps it updated (automagically) using a scheduled script in wp_cron(). CAOS for Analytics is a set and forget plugin.

For more information: [How to setup CAOS for Analytics](https://dev.daanvandenbergh.com/wordpress-plugins/optimize-analytics-wordpress/).

== Features ==
- Host analytics.js/ga.js locally,
- Allow tracking always or only when a certain cookie exists or has a value -- [Read more about GDPR Compliance](https://dev.daanvandenbergh.com/wordpress/gdpr-compliance-google-analytics/),
- Add tracking code to header, footer or manually,
- Enable [compatibility with *Monster Insights' Google Analytics for Wordpress (and WooCommerce)*](https://dev.daanvandenbergh.com/wordpress/leverage-browser-caching-host-analytics-local-monster-insights/),
- Save analytics.js anywhere within the WordPress content (wp-content) directory to avoid detection by WordPress security plugins (such as WordFence) or removal by caching plugins (such as WP Super Cache),
- Set Cookie Expiry Period,
- Set Adjusted Bounce Rate,
- Change enqueue order (prioritize order of loaded scripts),
- Force disabling display features functionalities,
- Anonymize IP addresses,
- Track logged in Administrators,
- Remove script from wp-cron, so you can add it manually to your Crontab,
- Manually update analytics.js by the click of a button!

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/host-analyticsjs-local` directory, or install the plugin through the WordPress plugins repository directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings -> Optimize Analytics screen to configure the plugin

== Frequently Asked Questions ==

= Is this plugin GDPR compliant? =

Yes, it is! It is compatible with all Wordpress Cookie Notice plugins which either set a cookie to allow tracking or set a certain value to a cookie to allow tracking. It's completely customizable!

= Google Tag Assistant says analytics.js is missing. Is this normal? =

Yes, this is normal. This browser extensions looks for this exact string 'www.google-analytics.com/analytics.js'. Because with CAOS for Analytics you're hosting it locally, Tag Assistant doesn't find this string and returns an error. However, you can still use Tag Assistant to verify tracking works.

= I use Google Analytics by Monster Insights. Is CAOS compatible with that plugin? =

Yes, it is! Since version 1.80 CAOS is completely compatible with Google Analytics for Wordpress by Monster Insights. Just enable the option 'Enable compatibility with Monster Insights?' within the 'Optimize Analytics'-screen and CAOS will automagically replace the default external source of analytics.js to your locally hosted file.

= Is CAOS compatible with WooCommerce? =

Yes, it is! Since version 1.80 CAOS is completely compatible with WooCommerce. If you're a WooCommerce-user, I suggest integrating Google Analytics for Wordpress by Monster Insights with WooCommerce to take advantage of the advanced e-Commerce tracking capabilities of Google Analytics. Just enable the option 'Enable compatibility with Monster Insights?' within the 'Optimize Analytics'-screen and CAOS will automagically replace the default external source of analytics.js to your locally hosted file.

= I use WP Super Cache / W3 Total Cache / Autoptimize or another Caching and/or Minification plugin. Why is it removing analytics.js when I flush my cache? =

This happens because analytics.js is saved within the 'cache'-folder and for some reason the cache/minify plugin thinks it should be refreshed. Change the path where analytics.js is saved within CAOS to a path outside the /cache/ folder (e.g. /caos-cache/analytics/). This should resolve any issues you're having.

= WordFence (or another security plugin) is detecting file changes in local-ga.js. What's going on? =

This is perfectly normal, since this is the file that was updated by the built-in cronjob in older versions of CAOS for Analytics. Update to the latest version and change the path where analytics.js is saved within CAOS to a path which is ignored by your security plugin (e.g. /uploads/ or /cache/). This should resolve any issues you're having.

= Is CAOS compatible with WooCommerce Google Analytics Integration? =

No, sadly it isn't, because that plugin doesn't offer CAOS a efficient way to change the source of the analytics.js-file. However if you want to use CAOS with WooCommerce, I suggest using [Google Analytics by Monster Insights](https://nl.wordpress.org/plugins/google-analytics-for-wordpress/).

= What is the current update interval of the Analytics-script? =

I have set it to daily, because Google updates the script very often. Also, the daily interval is the longest interval that wp_cron() allows, as far as I know. If you suspect you might've gotten behind (which I doubt) I've implement a manual update button within the CAOS for Analytics' settings.

= The ga-local.js-file remains empty! What should I do? =

Make sure you are running the latest version, as I added some compatibility fixes along the way. If this doesn't resolve your issue, then your wp-cron isn't working properly. This is a server related issue. You can add the 'includes/update-analytics.php'-file to [your crontab](http://crontab-generator.org/ "Click here to create a crontab line using Crontab Generator"). As this problem has been resolved many times already, check the forum!

= I just updated to the latest version and the tracking stopped working! =

Probably your ga-local.js file got overwritten and emptied. Try activating and de-activating the plugin, otherwise this will automatically resolve itself after the cronjob has run.

= I disabled Demographic Reports in Google Analytics, but the script is still redirecting to doubleclick.net. How do I turn this off? =

Try enabling the option called "Disable all display features functionality?" With this you can enforce the disabling of the DisplayFeatures plugin.

= I've installed your plugin but analytics.js/ga.js is still showing up as an external request in Pingdom e.a.? =

CAOS adds a local file called ga-local.js, which enables you to use Analytics, while hosting the necessary files locally. This doesn't mean that it scans your entire plugins or themes directory for other manually/programatically added Analytics tracking-code. I.e. If analytics.js or ga.js is still showing up in the list of requests, this mean that something else (probably the theme you're using or another plugin) is adding this tracking code to your Wordpress Install. Find it. Remove it. And let CAOS take care of sending your needed data to Google Analytics.

= I use a CDN. Can I use CAOS in combination with my CDN? =

Yes, while I tried to add a CDN-function to CAOS, this seemed to create more problems than it'd solve. But @sixer came up with a great alternative, created by my friends @keycdn: CDN Enabler. Add the path to this plugin's directory to CDN Enabler and the analytics.js script is pulled directly from your CDN.

= Can I buy you a beer? =

Yes, please! [Click here to buy me a beer](http://dev.daanvandenbergh.com/donate/ "Let's do shots!")!

== Screenshots ==

N/A

== Changelog ==

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
