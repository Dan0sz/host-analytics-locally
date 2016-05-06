=== Host Analytics.js Locally ===
Contributors: DaanvandenBergh
Donate link: http://dev.daanvandenbergh.com/donate/
Tags: google, analytics, wp_cron, update, host, save, local, locally
Requires at least: 4.5
Tested up to: 4.5.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin that inserts the Analytics tracking code into the header, saves the analytics.js file locally and keeps it updated using wp_cron().

== Description ==

A cool plugin created by [Daan van den Bergh](http://dev.daanvandenbergh.com "Click here to visit my Wordpress Development Blog") that enables you to host your Google Analytics javascript-file (analytics.js) locally and keep it updated using wp_cron().

Whenever you run an analysis of your website on *Google Pagespeed Insights* or *Pingdom*, it'll tell you to **leverage browser cache** when you're using Google Analytics. Because Google has set the cache expiry time to 2 hours. This plugin will get you a **higher score** on Pagespeed and Pingdom and make **your website load faster**, because the user's browser doesn't have to make a roundtrip to download the file from Google's external server. 

Just install the plugin, enter your Tracking-ID and the plugin adds the necessary Tracking Code for Google Analytics to the head of your theme, downloads and saves the analytics.js-file to your website's server and keeps it updated using a scheduled script in wp_cron(). 

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/save-ga-local` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings -> Save GA Locally screen to configure the plugin

== Frequently Asked Questions ==

= What do you have in store for us next? =

In the coming releases I'm going to give you the power to decide the update interval of the script. 

= What is the current update interval? =

I have set it to daily, because Google updates the script very often. Also, the daily interval is the longest interval that wp_cron() allows, as far as I know.

= Can I buy you a beer? =

Yes, please! [Click here to buy me a beer](http://dev.daanvandenbergh.com/donate/ "Let's do shots!")!

= Anything else? =

Yes! While you're buying. Get a beer for Matthew Horne as well. This plugin uses a slightly changed [update script](http://diywpblog.com/leverage-browser-cache-optimize-google-analytics/ "Click here to go to Matthew Horne's WP Blog") which he created.

== Screenshots ==

N/A

== Changelog ==

= 1.0 =
First release! No changes so far!