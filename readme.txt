=== CAOS | Host Google Analytics Locally ===
Contributors: DaanvandenBergh
Tags: analytics, host, locally, gtag, woocommerce, gdpr, cookie notice, leverage browser cache, minimize external requests
Requires at least: 4.6
Tested up to: 6.6
Stable tag: 4.8.2
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automagically download and update gtag.js, bypass Ad Blockers with Stealth Mode, add the tracking code to your site's footer and tons of other features!

== Description ==

**CAOS can be downloaded for free without any paid subscription from [the official WordPress repository](https://wordpress.org/plugins/host-analyticsjs-local/).**

CAOS (Complete Analytics Optimization Suite) for Google Analytics allows you to **host gtag.js** locally and keep it updated using WordPress' built-in Cron-schedule. Fully automatic!

Not a big Google Analytics user and just curious about your pageviews? CAOS fully supports [Minimal Analytics 4](https://github.com/jahilldev/minimal-analytics/tree/main/packages/ga4#readme). An extremely lightweight alternative to Google Analytics' default libraries (gtag.js). *Minimal Analytics also helps you get rid of that annoying **Unused JS** notice in Google PageSpeed Insights!*

Whenever you run an analysis of your website on *Google Pagespeed Insights*, *Pingdom* or *GTMetrix*, it'll tell you to **leverage browser cache** when you're using Google Analytics. Because Google has set the cache expiry time to 2 hours. This plugin will get you a **higher score** on Pagespeed and Pingdom and make **your website load faster**, because the user's browser doesn't have to make a roundtrip to download the file from Google's external server.

Just install the plugin, enter your Mesurement ID and the plugin adds the necessary Tracking Code for Google Analytics 4 to the header (or footer) of your theme, downloads and saves the gtag.js-file to your website's server and keeps it updated (automagically) using a scheduled script in wp_cron(). Or if you'd like to use the locally hosted file with another plugin, check **Compatibility Mode** under *Advanced Settings*, Either way, CAOS is a set and forget plugin.

For more information: [How to setup CAOS](For more information: [How to setup CAOS](https://daan.dev/docs/caos/?utm_source=wordpress&utm_medium=description&utm_campaign=caos).

== Features ==
- Host gtag.js for Google Analytics 4 locally,
- Downloaded files are renamed to random strings to avoid ad blockers,
- Minimal Analytics 4 support,
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
- Preconnect to google-analytics.com to reduce latency and speed up requests,
- **Add tracking code** to header, **footer** or manually,
- Save gtag.js anywhere within the WordPress content (wp-content) directory to avoid detection by WordPress security plugins (such as WordFence) or removal by caching plugins (such as WP Super Cache),
- Serve gtag.js from your CDN,
- Set Cookie Expiry Period,
- Force disabling display features functionalities,
- Track logged in Administrators,

== Features in CAOS Pro ==
Use Google Analytics in [compliance with GDPR](https://daan.dev/blog/wordpress/gdpr-compliance-google-analytics/?utm_source=wordpress&utm_medium=description&utm_campaign=caos) with:
- Randomize Client ID (which grants a fresh, untraceable UUID/ClientID to each visitor),
- Stealth Mode (a unique, customized API, designed for WordPress, which anonymizes your visitor's data before sending it to Google's servers).

Other features:
- Cloaked Affiliate Link Tracking,
- Cloudflare Compatibility Mode.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/host-analyticsjs-local` directory, or install the plugin through the WordPress plugins repository directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings -> Optimize Google Analytics screen to configure the plugin

== Frequently Asked Questions ==

For CAOS' FAQ and Troubleshooting, [visit the docs](https://daan.dev/docs/caos-troubleshooting/).

== Screenshots ==

N/A

== Changelog ==

= 4.8.2 =
* Added: WP Rocket compatibility for Minimal Analytics tracking code.

= 4.8.1 =
* Fixed: Advanced Settings tab is no longer locker when Tracking Code is set to Minimal Analytics 4.
* Improved: sometimes it was unclear why an option was disabled. Clarified the messages.

= 4.8.0 =
* Added: Minimal Analytics now works with Allow Tracking... option.
* Improved: When Allow Tracking... is set to Consent Mode, Minimal Analytics is automatically disabled.
* Improved: When Tracking Code is set to Minimal Analytics, Consent Mode is automatically disabled.
* Fixed: DB migrations will no run on plugins_loaded action to prevent errors on some systems.

= 4.7.17 =
* Fixed: Removed CaosGtag_ prefix from Google Analytics 4 cookies.
* Tested with WP 6.6

= 4.7.16 =
* Fixed: CAOS would hit Daan.dev updates server on each admin pageload when CAOS Pro was installed.
* Tested with WP 6.5.

= 4.7.15 =
* Fixed: This time a proper fix against CSRF attacks. It's been a long day. Sorry, guys!

= 4.7.14 =
* Fixed: undefined array key _wpnonce. (Big Oops! on my end, sorry about that.)

= 4.7.13 | December 12th, 2023 =
* Fixed: CSRF issue in custom Update Settings logic.

= 4.7.12 | November 8th, 2023 =
* Tested with WP 6.4

= 4.7.11 | October 19, 2023 =
* Added: LiteSpeed Cache compatibility.

= 4.7.10 | September 5th, 2023 =
* Fixed: users of the premium plugin would still get update notices, saying "Automatic update not available" right after updating to the latest version.

= 4.7.9 | August 30th, 2023 =
* Fixed: Allow tracking when cookie has a value (exact match) didn't work.

= 4.7.8 | August 24th, 2023 =
* Fixed: default value of Cache Dir setting should have a trailing slash in all cases.
* Fixed: JS in "Update Failed" message for CAOS Pro failed, due to early execution.
* Fixed: For multisites, COOKIE_DOMAIN constant is now used (if properly defined) to define cookie_domain Gtag config value.

= 4.7.7 =
* Tested with WP 6.3
* Improved: PHP 8.1 compatibility.

= 4.7.6 =
* Fixed: allow `page_view` parameter to be rewritten by external plugins (e.g. CAOS Pro).
* Fixed: deprecated notice - version_compare() passing null to param #1 is deprecated in PHP 8.1.

= 4.7.5 | July 31st, 2023 =
* Fixed: PHP warning file_get_contents() when no premium plugins are installed.

= 4.7.4 | July 30th, 2023 =
* Added: CAOS Pro users are now notified in the All Plugins screen when (automatic) updates are failing.
* Added filter: `caos_exclude_from_tracking` which allows WP devs to not add the tracking code on certain pages.
* Minor code optimizations.

= 4.7.3 | July 21st, 2023 =
* Fixed: remove trailing comma from list to support PHP 7.2 (Props, @artoliukkonen)
* Improved: Don't echo input from caos_gtag_additional_config filter by default. Devs using this filter need to check their code.
* Fixed: remove duplicate `script` tags from Consent Mode API script.
* Added filter: caos_local_dir which allows devs to define the absolute path to CAOS' cache directory.
* Added filter: caos_file_alias which allows devs to define the JS library's file alias.
* Removed: IP anonymization is [no longer required according to Google](https://support.google.com/analytics/answer/2763052?hl=en) and no longer supported. Therefore all code related to this feature has been removed.
* Minor all-round code optimizations.

= 4.7.2 | July 10th, 2023 =
* Added filter: caos_local_file_url, which can be used to change the alias of the downloaded (gtag.js) library.
* Fixed: the Consent Mode API (for the Allow Tracking When option) wasn't properly implemented, and was missing the `wait_for_update` attribute in its defaults.

= 4.7.1 =
* Fixed: Minimal Analytics 4 couldn't be enabled.

= 4.7.0 | June 30th, 2023 =
* This release marks the end of Universal Analytics (GA3), all options only supported by UA have been removed.
  - I've implemented a database migration script. If you were using Dual Tracking or GA4 before, tracking isn't interrupted.
    Either way, I suggest you check your settings.
    Read [this blogpost](https://daan.dev/blog/wordpress/rip-universal-analytics/) for a full rundown of the changes.
* Added: `caos_frontend_tracking_promo_message` filter.
* Improved: @jahilldev's Minimal Analytics 4 tracking code is now used, which adds support for event tracking.
* Fixed: Tracking ID, Cookie Name and Cookie Value would get lost in migration when upgrading to 4.6.0 (for those of you who still haven't updated, you can safely do so now :-))

= 4.6.1 | June 21st, 2023 =
* Fixed: Fatal Error (undefined method `CAOS::get()`) directly after updating.

= 4.6.0 | June 19th, 2023 =
* This release marks the deprecation of Universal Analytics (GA3), all options that'll disappear on July 1st are
  marked as deprecated.
* Tested with WP 6.2 - No issues.
* Improved: DB reads/writes reduced by ~90%!
* Renamed Extensions tab to Avoid Ad Blockers, because all extensions are deprecated (due to incompatibility with GA4 or being replaced with a GA4 native option)

= 4.5.0 | March 3rd, 2023 =
* Fixed: filemtime() stat failed for /wp-content/uploads/caos/analytic.js bug
* Removed: change enqueue order option, because it was rendered useless years ago
* Improved: all options are now always displayed and show a clear explanation if they're disabled due to configuration.
* Added: Increase GDPR Compliance (Pro) promotional option.
* Added: Google Analytics 4's Consent Mode compatibility to Allow Tracking When-option. 

= 4.4.5 | September 29th, 2022 =
* Fixed: Bug in Minimal Analytics GA4 when Stealth Mode was enabled.

= 4.4.4 | September 22nd, 2022 =
* Fixed: preconnect header shouldn't be added if Compatibility Mode is on.
* Fixed: rewrite new endpoints for GA4 when Dual Tracking is enabled.
* Fixed: Minify Minimal Analytics code (Damn auto-format!)

= 4.4.3 | August 3rd, 2022 =
* Fixed: explicitly check if CAOS::get_current_file_key() exists to prevent "undefined method" uncaught error.

= 4.4.2 | August 2nd, 2022 =
* Fixed: prevent warnings when updating downloaded tracking scripts directly after updating the plugin.
* Fixed: show proper file update notice when using Plausible Analytics.
* Fixed: updated signature in Help-tab to Daan.dev logo.

= 4.4.1 | June 28th, 2022 =
* Fixed: updated links from ffw.press to daan.dev after the migration.

= 4.4.0 | June 14th, 2022 =
* Added: Plausible Analytics support
* Added: Minimal Analytics for Google Analytics V4
* Fixed: A few warnings, bugs and re-worded some descriptions

= 4.3.5 | June 6th, 2022 =
* Fixed: Remote JS file should be allowed to be modified, when Compatibility Mode is active.

= 4.3.4 =
* Fixed: don't use WP_Filemanager to get and put file contents.
* Fixed: updated links to new documentation hub.

= 4.3.3 =
* Fixed: do not escape slashes when encoding the tracking code's config section.
* Fixed: Links to documentation updated.
* Fixed: double pageview hits when using GA4.
* UX: When dual tracking is enabled, switch to gtag.js automatically.

= 4.3.2 =
* Added: filter caos_gtag_custom_attributes and caos_analytics_custom_attributes to add custom attributes to script tag.
* Fixed: File to Download couldn't be changed when using Compatibility Mode.
* Fixed: Mute XML errors when loading news reel.

= 4.3.1 =
* Fixed (hopefully): CAOS Compatibility Mode would break page caching. Tested with:
  - Autoptimize
  - W3 Total Cache
  - WP Fastest Cache
  - WP Optimize
  - WP Rocket
  - WP Super Cache
* Fixed: Compatibility Mode would break previews of page builders. Tested with:
  - Beaver Builder
  - Divi
  - Elementor
  - Visual Composer
* Fixed: Compatibility Mode would break WordPress' Customizer preview.

= 4.3.0 =
* Added: CAOS is now compatible with **all** Google Analytics plugins! (I finally figured it out, yay! 🎉)
         That's why Compatibility Mode is now merely a checkbox.
* Fixed: Minor tweaks.

= 4.2.6 =
* Fixed: undefined constant.
* Fixed: Disable Advertising Features didn't work with analytics.js

= 4.2.5 =
* Tested with WP 5.9
  - Fixed: WP 5.9 welcome background was displayed in Help and Extensions section of Settings screen.

= 4.2.4 =
* Hotfix: subfolders (e.g. /en/) weren't properly stripped from the cookieDomain.

= 4.2.3 =
* Fix: strip protocol from Home URL to properly set the cookieDomain.
* Enhancement: Added a visual example to the IP Anonymize Mode feature to visualize the different anonymization modes.

= 4.2.2 =
* Dev: added filter 'caos_analytics_use_local_storage'.
* Fix: set cookie domain to Home URL instead of $_SERVER['SERVER_NAME'].
* Fix: IPs weren't properly anonymized when Ad Block Detect and IP Anonymize were enabled.
* Fix: Source mapping URL was invalid in minified stylesheets, which threw a notice in Chrome's DevTools.
* Fix: Ad Block Detect returned false positives. 

= 4.2.1 =
* Fix: warning undefined property CAOS_Cron::$plugin_text_domain.
* Fix: warning undefined variable $file.
* Feat: Ability to force clean up and regeneration of file aliases.

= 4.2.0 | January 14th, 2022 =
* Feat: Added Dual Tracking to track Google Analytics v3 and Google Analytics v4 simultaneously.
* Del: Removed Stealth Mode Lite since its code was too outdated. To continue using Stealth Mode, upgrade to CAOS Pro. [Read more](https://daan.dev/blog/wordpress/major-changes-caos-pro/)
* Del: Google Optimize was removed and moved to a separate free plugin, to be found [here](https://github.com/Dan0sz/caos-google-optimize).
* Fix: Removed all references to Super Stealth, since it's been renamed to CAOS Pro.
* Dev: Debug information is now logged when Track Ad Blockers is enabled.
* Fix: Disable Display Features didn't work for gtag.js.
* Several bug fixes and optimizations.
* Several design and UX tweaks and enhancements in the settings screen.

= 4.1.9 | November 27th, 2021 =
* Sec: prevent path traversal when cache dir is changed (thnx, @jsgm!)

= 4.1.8 | November 17th, 2021 =
* Fix: Properly encode XML to prevent parse error in simplexml_load_string().

= 4.1.7 | November 12th, 2021 =
* Fix: Updated RSS feed URL, because daan.dev moved to ffw.press/blog
* Fix: Updated documentation links.

= 4.1.6 =
* Fix: Adjusted Bounce Rate setting was broken after moving it to the Advanced Tab.

= 4.1.5 | September 10th, 2021 =
* Dev: 3rd party plugins can now easily modify CAOS' admin screen (e.g. adding/saving/modifying settings).
* Enhancement: Minimal Analytics code is now minified.
* Fix: Explicitly enabling Display Features (if not disabled within the settings) should fix missing Demographics reporting.
* Moved Adjusted Bounce Rate setting to Advanced Settings tab.

= 4.1.4 | August 18th, 2021 =
* Enhancement: The hit type parameter can now be filtered to allow more flexibility when ad blocker lists are updated. (hint: CAOS Pro)

= 4.1.3 | July 28th, 2021 =
* Enhancement: Added news real for CAOS relevant blog posts in footer of CAOS' settings screen.
* Fix: CAOS Pro promotion material is removed from options that're free.
* Tested with WP 5.8.

= 4.1.2 | June 16th, 2021 =
* Fix: When gtag.js was used, SSL was enabled and your site was behind a load balancer/reverse proxy, CAOS attempted to load analytics.js thru a non-SSL link, which would break Analytics.
* Fix: PNG logo in footer of settings screen would break on Apple machines.

= 4.1.1 | June 7th, 2021 =
* Dev: added hooks around tracking code to allows developers to easily alter the tracking code.
* Fix: When Google Analytics and Google Ads are connected, this is now properly handled gtag.js is used.
* Fix: Tracking code can now be properly translated by translating the string UA-123456789.
* Fix: Capture Outbound Links is only triggered on left click (instead of all mousedown events).
* Feature: Cookieless Analytics is now available under Advanced Settings when CAOS Pro is installed and active.
* UX: Re-worded some options and option descriptions.

= 4.1.0 | June 1st, 2021 =
- Tracking ID can now be translated with plugins like WPML, so you can set different tracking IDs per language.
- Added compatibility modes for SEOPress and RankMath SEO.

= 4.0.5 | May 5th, 2021 =
* Added extra debug points for Stealth Mode users using Analytics plugins (ec.js, linkid.js, etc.)

= 4.0.4 | April 27th, 2021 =
* Footer logo in settings screen is now loaded from local src,  instead of external src.
* Added extra debug points for Stealth Mode users using Cloudflare.

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

= 3.0.0 =
Major code overhaul. Major performance improvements.

= 2.0.0 =
Finally rid of the ugly versioning. Added settings link to plugins overview.

= 1.0 =
First release! No changes so far!
