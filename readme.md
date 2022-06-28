# CAOS | Host Google Analytics Locally

Automagically download and update analytics.js/gtag.js, bypass Ad Blockers with Stealth Mode, add the tracking code to your site's footer and tons of other features!

## Description

CAOS (Complete Analytics Optimization Suite) for Google Analytics allows you to **host analytics.js/gtag.js** locally and keep it updated using WordPress' built-in Cron-schedule. Fully automatic!

Not a big Google Analytics user and just curious about your pageviews? CAOS fully supports [Minimal Analytics](https://minimalanalytics.com), which is basically Google Analytics Lite. An extremely lightweight alternative to Google Analytics' default libraries (analytics.js/gtag.js). *Minimal Analytics also helps you get rid of that annoying **Unused JS** notice in Google PageSpeed Insights!*

Whenever you run an analysis of your website on *Google Pagespeed Insights*, *Pingdom* or *GTMetrix*, it'll tell you to **leverage browser cache** when you're using Google Analytics. Because Google has set the cache expiry time to 2 hours. This plugin will get you a **higher score** on Pagespeed and Pingdom and make **your website load faster**, because the user's browser doesn't have to make a roundtrip to download the file from Google's external server.

Just install the plugin, enter your Tracking-ID and the plugin adds the necessary Tracking Code for Google Analytics to the header (or footer) of your theme, downloads and saves the analytics.js/gtag.js-file to your website's server and keeps it updated (automagically) using a scheduled script in wp_cron(). Or if you'd like to use the locally hosted file with another plugin, check **Compatibility Mode** under *Advanced Settings*, Either way, CAOS is a set and forget plugin.

For more information: [How to setup CAOS](For more information: [How to setup CAOS](https://daan.dev/docs/caos/?utm_source=wordpress&utm_medium=description&utm_campaign=caos).

## Features
- Host analytics.js or gtag.js locally ([What's the difference?](https://daan.dev/blog/wordpress/difference-analyics-gtag-ga-js/?utm_source=wordpress&utm_medium=description&utm_campaign=caos)),
- Downloaded files are renamed to random strings to avoid ad blockers,
- Compatibility Mode allows you to use the locally hosted file with all Google Analytics plugins, e.g.
  - MonsterInsights (Pro),
  - ExactMetrics
  - Site Kit by Google,
  - WooCommerce Google Analytics Integration,
  - WooCommerce Google Analytics Pro,
  - Analytify,
  - And many more!
- Compatible with all Cookie Notice plugins, e.g.
  - Complianz,
  - CookieYes,
  - WP Cookie Notice,
  - Cookie Notice & Compliance,
  - Cookie Notice & Consent Banner,
  - And many more!
- Minimal Analytics support,
- Google Analytics V4 support (incl. Dual Tracking),
- Preconnect to google-analytics.com to reduce latency and speed up requests,
- Send an event to your Google Analytics dashboard when a visitor is viewing your pages using an Ad Blocker,
- Capture outbound links,
- Enhanced link attribution,
- **Add tracking code** to header, **footer** or manually,
- Load the tracking snippet Asynchronous or Default (Synchronous)
- Save analytics.js/gtag.js anywhere within the WordPress content (wp-content) directory to avoid detection by WordPress security plugins (such as WordFence) or removal by caching plugins (such as WP Super Cache),
- Serve analytics.js/gtag.js from your CDN,
- Set Cookie Expiry Period,
- Set Site Speed Sample Rate,
- Set [Adjusted Bounce Rate](https://daan.dev/blog/wordpress/adjusted-bounce-rate-caos/?utm_source=wordpress&utm_medium=description&utm_campaign=caos),
- Change enqueue order (prioritize order of loaded scripts),
- Enable Enhanced Link Attribution (linkid.js),
- Force disabling display features functionalities,
- Anonymize IP addresses (last octet),
- Track logged in Administrators,
- Endlessly extensible for developers by using the integrated filters and available mini plugins.

##  Features in CAOS Pro
Use Google Analytics in [compliance with GDPR](https://daan.dev/blog/wordpress/gdpr-compliance-google-analytics/?utm_source=wordpress&utm_medium=description&utm_campaign=caos) with:
- Cookieless Analytics (which grants a fresh, untraceable UUID/ClientID to each visitor),
- True IP anonymization (which anonymizes the last 2 octets of your user's IP address, e.g. 192.168.0.0 *before* sending it to Google Analytics),
- Stealth Mode (a unique, customized API, designed for WordPress, which anonymizes your visitor's data before sending it overseas, i.e. the US).

# Other features
- Cloaked Affiliate Link Tracking,
- Support for Enhanced Ecommerce when in Stealth Mode,
- Cloudflare Compatibility Mode.

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