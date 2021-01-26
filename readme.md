# CAOS | Host Google Analytics Locally

Automagically download and update analytics.js/gtag.js, bypass Ad Blockers with Stealth Mode, add the tracking code to your site's footer and tons of other features!

## Description
CAOS (Complete Analytics Optimization Suite) for Google Analytics allows you to **host analytics.js/gtag.js** locally and keep it updated using WordPress' built-in Cron-schedule. Fully automatic!

Not a big Google Analytics user and just curious about your pageviews? CAOS fully supports [Minimal Analytics](https://minimalanalytics.com), which is basically Google Analytics Lite. An extremely lightweight alternative Google Analytics' default libraries (analytics.js/gtag.js). *Minimal Analytics also helps you get rid of that annoying **Unused JS** notice in Google PageSpeed Insights!*

Whenever you run an analysis of your website on *Google Pagespeed Insights*, *Pingdom* or *GTMetrix*, it'll tell you to **leverage browser cache** when you're using Google Analytics. Because Google has set the cache expiry time to 2 hours. This plugin will get you a **higher score** on Pagespeed and Pingdom and make **your website load faster**, because the user's browser doesn't have to make a roundtrip to download the file from Google's external server.

Just install the plugin, enter your Tracking-ID and the plugin adds the necessary Tracking Code for Google Analytics to the header (or footer) of your theme, downloads and saves the analytics.js/gtag.js-file to your website's server and keeps it updated (automagically) using a scheduled script in wp_cron(). CAOS is a set and forget plugin.

Please keep in mind that, although I try to make the configuration of this plugin as easy as possible, the concept of locally hosting a file or optimizing Google Analytics for *Pagespeed Insights* or *GT Metrix* has proven to be confusing for some people. If you're not sure of what your doing, please consult a SEO expert or Webdeveloper to help you with the configuration of this plugin. Or [hire me to do it for you](https://ffw.press/wordpress-services/caos-expert-configuration/?utm_source=wordpress&utm_medium=description&utm_campaign=caos).

For more information: [How to setup CAOS](https://daan.dev/wordpress-plugins/caos/?utm_source=github&utm_medium=description&utm_campaign=caos).

## Features
- Host analytics.js or gtag.js locally ([What's the difference?](https://daan.dev/wordpress/difference-analyics-gtag-ga-js/?utm_source=github&utm_medium=description&utm_campaign=caos)),
- Endlessly extensible using the integrated filters and available mini plugins! E.g. [track Google Adwords conversions](https://github.com/Dan0sz/caos-google-adwords) and much, much more!
- When using gtag.js, the underlying request to analytics.js is also hosted locally!
- **[Bypass Ad Blockers](https://daan.dev/how-to/bypass-ad-blockers-caos/?utm_source=github&utm_medium=description&utm_campaign=caos)** in Stealth Mode: Sneak past Security and protect your Google Analytics data,
  - In Stealth Mode, requests to linkid.js and ec.js are also hosted locally,
- Preconnect to google-analytics.com and CDN URL (if set) to reduce latency and speed up requests,
- Capture outbound links,
- Integrate Google Optimize,
- Enhanced link attribution,
- Allow tracking always or only when a certain cookie exists or has a value -- [Read more about GDPR Compliance](https://daan.dev/wordpress/gdpr-compliance-google-analytics/?utm_source=github&utm_medium=description&utm_campaign=caos),
- **Add tracking code** to header, **footer** or manually,
- Load the tracking snippet Asynchronous or Default (Synchronous)
- Fully compatible with [Google Analytics Dashboard Plugin for WP by MonsterInsights](https://daan.dev/wordpress/leverage-browser-caching-host-analytics-local-monster-insights/?utm_source=github&utm_medium=description&utm_campaign=caos), WooCommerce Google Analytics Integration, Google Analytics Dashboard Plugin for WP by Analytify and Google Analytics Dashboard for WP by ExactMetrics,
- Save analytics.js/gtag.js anywhere within the WordPress content (wp-content) directory to avoid detection by WordPress security plugins (such as WordFence) or removal by caching plugins (such as WP Super Cache),
- Serve analytics.js/gtag.js from your CDN,
- Set Cookie Expiry Period,
- Set Adjusted Bounce Rate,
- Change enqueue order (prioritize order of loaded scripts),
- Force disabling display features functionalities,
- Anonymize IP addresses,
- Track logged in Administrators,
- Remove script from wp-cron, so you can add it manually to your Crontab,
- Manually update analytics.js/gtag.js with the click of a button!

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