=== Complete Analytics Optimization Suite (CAOS) - GDPR Compliant! ===
Contributors: DaanvandenBergh
Donate link: https://dev.daanvandenbergh.com/donate/
Tags: gdpr, google, analytics, wp_cron, update, host, save, local, locally, anonymize, place in footer, optimize, do not track administrator, leverage, browser, cache, minimize, external, requests
Requires at least: 4.5
Tested up to: 4.9
Stable tag: 1.66
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A GDPR compliant plugin that inserts the Analytics tracking code into the header or footer, saves the analytics.js file locally and keeps it updated using wp_cron().

== Description ==

A cool, GDPR compliant plugin created by [Daan van den Bergh](https://dev.daanvandenbergh.com "Click here to visit my Wordpress Development Blog") that enables you to complete optimize the usage of Google Analytics on your Wordpress Website. Host your Google Analytics javascript-file (analytics.js) locally and keep it updated using wp_cron(). Easily Anonymize the IP-address of your visitors. Set an Adjusted Bounce Rate. Decide whether to load the Analytics Tracking-code in the header or footer. And more!

Whenever you run an analysis of your website on *Google Pagespeed Insights* or *Pingdom*, it'll tell you to **leverage browser cache** when you're using Google Analytics. Because Google has set the cache expiry time to 2 hours. This plugin will get you a **higher score** on Pagespeed and Pingdom and make **your website load faster**, because the user's browser doesn't have to make a roundtrip to download the file from Google's external server.

Just install the plugin, enter your Tracking-ID and the plugin adds the necessary Tracking Code for Google Analytics to the head of your theme, downloads and saves the analytics.js-file to your website's server and keeps it updated (automagically) using a scheduled script in wp_cron().

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/host-analyticsjs-local` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings -> Optimize Analytics screen to configure the plugin

= Configuring Cookie Notice and GDPR Compliance in CAOS =

Please visit my blog for instructions on [how to set-up a properly functioning Cookie Notice using CAOS](https://dev.daanvandenbergh.com/wordpress/analytics-gdpr-caos).

== Frequently Asked Questions ==

= Is this plugin GDPR compliant? =

Yes, it is! It is compatible with all Wordpress Cookie Notice plugins which either set a cookie to allow tracking or set a certain value to a cookie to allow tracking. It's completely customizable!

= What is the current update interval of the Analytics-script? =

I have set it to daily, because Google updates the script very often. Also, the daily interval is the longest interval that wp_cron() allows, as far as I know.

= The ga-local.js-file remains empty! What should I do? =

Make sure you are running the latest version, as I added some compatibility fixes along the way. If this doesn't resolve your issue, then your wp-cron isn't working properly. This is a server related issue. You can add the 'includes/update_local_ga.php'-file to [your crontab](http://crontab-generator.org/ "Click here to create a crontab line using Crontab Generator"). As this problem has been resolved many times already, check the forum!

= I just updated to the latest version and the tracking stopped working! =

Probably your ga-local.js file got overwritten and emptied. Try activating and de-activating the plugin, otherwise this will automatically resolve itself after the cronjob has run.

= I disabled Demographic Reports in Google Analytics, but the script is still redirecting to doubleclick.net. How do I turn this off? =

Try enabling the option called "Disable all display features functionality?" With this you can enforce the disabling of the DisplayFeatures plugin.

= I've installed your plugin but analytics.js/ga.js is still showing up as an external request in Pingdom e.a.? =

CAOS adds a local file called ga-local.js, which enables you to use Analytics, while hosting the necessary files locally. This doesn't mean that it scans your entire plugins or themes directory for other manually/programatically added Analytics tracking-code. I.e. If analytics.js or ga.js is still showing up in the list of requests, this mean that something else (probably the theme you're using or another plugin) is adding this tracking code to your Wordpress Install. Find it. Remove it. And let CAOS take care of sending your needed data to Google Analytics.

= I use a CDN. Can I use CAOS in combination with my CDN? =

Yes, while I tried to add a CDN-function to CAOS, this seemed to create more problems than it'd solve. But @sixer came up with a great alternative, created by my friends @keycdn: CDN Enabler. Add the path to this plugin's directory to CDN Enabler and the local-ga.js script is pulled directly from your CDN.

= Can I buy you a beer? =

Yes, please! [Click here to buy me a beer](http://dev.daanvandenbergh.com/donate/ "Let's do shots!")!

== Screenshots ==

N/A

== Changelog ==

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
