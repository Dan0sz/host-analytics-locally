# CAOS for Analytics | Host Google Analytics Locally

Automagically download analytics.js, keep it updated using WordPress' built-in Cron-schedule, generate the tracking code, add it to your site's header or footer and tons of other features!

## Description

CAOS for Google Analytics allows you to **host analytics.js** locally and keep it updated using WordPress' built-in Cron-schedule. Fully automatic!

Whenever you run an analysis of your website on *Google Pagespeed Insights*, *Pingdom* or *GTMetrix*, it'll tell you to **leverage browser cache** when you're using Google Analytics. Because Google has set the cache expiry time to 2 hours. This plugin will get you a **higher score** on Pagespeed and Pingdom and make **your website load faster**, because the user's browser doesn't have to make a roundtrip to download the file from Google's external server.

Just install the plugin, enter your Tracking-ID and the plugin adds the necessary Tracking Code for Google Analytics to the header (or footer) of your theme, downloads and saves the analytics.js-file to your website's server and keeps it updated (automagically) using a scheduled script in wp_cron(). CAOS for Analytics is a set and forget plugin.

For more information: [How to setup CAOS for Analytics](https://dev.daanvandenbergh.com/wordpress-plugins/optimize-analytics-wordpress/).

## Features
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

## Installation

### Using GIT

1. From your terminal, `cd` to your plugins directory (usually `wp-content/plugins`)
1. Run the following command: `git clone https://github.com/Dan0sz/host-analytics-locally.git host-analyticsjs-locally`

### From the WordPress Repository

1. From your WordPress administrator area, go to *Plugins > Add New*
1. Search for 'Daan van den Bergh'
1. Click the 'Install' button next to *CAOS | Host Google Analytics Locally*
1. Click 'Activate'

## Frequently Asked Questions

Visit the [FAQ at Wordpress.org](https://wordpress.org/plugins/host-analyticsjs-local/#faq)

## Support

For Support Queries, checkout the [Support Forum at Wordpress.org](https://wordpress.org/support/plugin/host-analyticsjs-local)

## Changelog

Visit the [Changelog at Wordpress.org](https://wordpress.org/plugins/host-analyticsjs-local/#developers)