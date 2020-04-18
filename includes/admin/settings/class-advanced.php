<?php
/* * * * * * * * * * * * * * * * * * * *
 *  ██████╗ █████╗  ██████╗ ███████╗
 * ██╔════╝██╔══██╗██╔═══██╗██╔════╝
 * ██║     ███████║██║   ██║███████╗
 * ██║     ██╔══██║██║   ██║╚════██║
 * ╚██████╗██║  ██║╚██████╔╝███████║
 *  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚══════╝
 *
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/caos/
 * @copyright: (c) 2020 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

class CAOS_Admin_Settings_Advanced extends CAOS_Admin_Settings_Builder
{
    /**
     * CAOS_Admin_Settings_Advanced constructor.
     */
    public function __construct()
    {
        $this->title = __('Advanced Settings', $this->plugin_text_domain);

        // Open
        add_filter('caos_advanced_settings_content', [$this, 'do_title'], 10);
        add_filter('caos_advanced_settings_content', [$this, 'do_description'], 15);
        add_filter('caos_advanced_settings_content', [$this, 'do_before'], 20);

        // Content
        add_filter('caos_advanced_settings_content', [$this, 'do_compatibility_mode'], 30);
        add_filter('caos_advanced_settings_content', [$this, 'do_remote_js_file'], 50);
        add_filter('caos_advanced_settings_content', [$this, 'do_cache_dir'], 60);
        add_filter('caos_advanced_settings_content', [$this, 'do_cdn_url'], 70);
        add_filter('caos_advanced_settings_content', [$this, 'do_preconnect'], 80);
        add_filter('caos_advanced_settings_content', [$this, 'do_capture_outbound_links'], 90);

        // Non Compatibility Mode settings.
        add_filter('caos_advanced_settings_content', [$this, 'do_tbody_advanced_settings_open'], 100);
        add_filter('caos_advanced_settings_content', [$this, 'do_cookie_expiry'], 120);
        add_filter('caos_advanced_settings_content', [$this, 'do_adjusted_bounce_rate'], 140);
        add_filter('caos_advanced_settings_content', [$this, 'do_change_enqueue_order'], 160);
        add_filter('caos_advanced_settings_content', [$this, 'do_disable_display_feat'], 180);
        add_filter('caos_advanced_settings_content', [$this, 'do_anonymize_ip'], 200);
        add_filter('caos_advanced_settings_content', [$this, 'do_track_admin'], 220);
        add_filter('caos_advanced_settings_content', [$this, 'do_tbody_close'], 240);

        // Uninstall Setting
        add_filter('caos_advanced_settings_content', [$this, 'do_uninstall_settings'], 250);

        // Close
        add_filter('caos_advanced_settings_content', [$this, 'do_after'], 250);

    }

    /**
     * Description
     */
    public function do_description()
    {
        ?>
        <p>
            <strong>*</strong> <?php _e('Manual update required after saving changes.', $this->plugin_text_domain); ?>
        </p>
        <?php
    }

    /**
     * Enable Compatibility Mode
     */
    public function do_compatibility_mode()
    {
        $this->do_select(
            __('Enable Compatibility Mode', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE,
            CAOS_Admin_Settings::CAOS_ADMIN_COMPATIBILITY_OPTIONS,
            CAOS_OPT_COMPATIBILITY_MODE,
            sprintf(__('Allow another Google Analytics plugin to use <code>%s</code> and manage Google Analytics entirely within the other plugin.', $this->plugin_text_domain), CAOS_OPT_CACHE_DIR . CAOS_OPT_REMOTE_JS_FILE)
        );
    }

    /**
     * Which file to download?
     */
    public function do_remote_js_file()
    {
        $this->do_select(
            __('Which file to download?', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_JS_FILE,
            CAOS_Admin_Settings::CAOS_ADMIN_JS_FILE_OPTIONS,
            CAOS_OPT_REMOTE_JS_FILE,
            sprintf(__('<code>analytics.js</code> is recommended in most situations. When using <code>gtag.js</code>, <code>analytics.js</code> is also cached and updated! Need help choosing? %sRead this%s', $this->plugin_text_domain), '<a href="' . CAOS_SITE_URL . '/wordpress/difference-analyics-gtag-ga-js/' . $this->utm_tags . '" target="_blank">', '</a>'),
            true
        );
    }

    /**
     * Save .js file to...
     */
    public function do_cache_dir()
    {
        $this->do_text(
            sprintf(__('Save %s to...', $this->plugin_text_domain), CAOS_OPT_REMOTE_JS_FILE),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR,
            __('e.g. /cache/caos/', $this->plugin_text_domain),
            CAOS_OPT_CACHE_DIR,
            __("Change the path where the Analytics-file is cached inside WordPress' content directory (usually <code>wp-content</code>). Defaults to <code>/cache/caos/</code>.", $this->plugin_text_domain),
            true
        );
    }

    /**
     * Serve from a CDN?
     */
    public function do_cdn_url()
    {
        $this->do_text(
            __('Serve from a CDN?', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL,
            __('e.g. cdn.mydomain.com', $this->plugin_text_domain),
            CAOS_OPT_CDN_URL,
            sprintf(__('If you\'re using a CDN, enter the URL here to serve <code>%s</code> from your CDN.', $this->plugin_text_domain), CAOS_OPT_REMOTE_JS_FILE)
        );
    }

    /**
     * Enable Preconnect?
     */
    public function do_preconnect()
    {
        $this->do_checkbox(
            __('Enable Preconnect? (Recommended)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_PRECONNECT,
            CAOS_OPT_PRECONNECT,
            __('Preconnect to google-analytics.com and CDN URL (if set) to reduce latency and speed up requests to these servers.', $this->plugin_text_domain)
        );
    }

    /**
     * Capture outbound links?
     */
    public function do_capture_outbound_links()
    {
        $this->do_checkbox(
            __('Capture outbound links?', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_CAPTURE_OUTBOUND_LINKS,
            CAOS_OPT_CAPTURE_OUTBOUND_LINKS,
            sprintf(__('Find out when users click a link to leave your site. Only works with <code>analytics.js</code> and when Stealth Mode is disabled.  %sRead more%s', $this->plugin_text_domain), '<a target="_blank" href="https://support.google.com/analytics/answer/1136920">', '</a>')
        );
    }

    /**
     * Tbody open
     */
    public function do_tbody_advanced_settings_open()
    {
        $this->do_tbody_open('caos_advanced_settings');
    }

    /**
     * Cookie expiry period (days)
     */
    public function do_cookie_expiry()
    {
        $this->do_number(
            __('Cookie expiry period (days)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_GA_COOKIE_EXPIRY_DAYS,
            CAOS_OPT_COOKIE_EXPIRY,
            __('The number of days when the cookie will automatically expire.', $this->plugin_text_domain)
        );
    }

    /**
     * Use adjusted bounce rate?
     */
    public function do_adjusted_bounce_rate()
    {
        $this->do_number(
            __('Use adjusted bounce rate? (seconds)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_ADJUSTED_BOUNCE_RATE,
            CAOS_OPT_ADJUSTED_BOUNCE_RATE,
            sprintf(__('Set up an event which is triggered after a user spends X seconds on the landing page. <a target="_blank" href="%s">Read more</a>.', $this->plugin_text_domain), CAOS_SITE_URL . '/how-to/adjusted-bounce-rate-caos/' . $this->utm_tags)
        );
    }

    /**
     * Change enqueue order
     */
    public function do_change_enqueue_order()
    {
        $this->do_number(
            __('Change enqueue order? (Default = 0)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_ENQUEUE_ORDER,
            CAOS_OPT_ENQUEUE_ORDER,
            __('Do not change this unless you know, what you\'re doing.', $this->plugin_text_domain)
        );
    }

    /**
     * Disable all display features functionality
     */
    public function do_disable_display_feat()
    {
        $this->do_checkbox(
            __('Disable all display features functionality?', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_DISPLAY_FEATURES,
            CAOS_OPT_DISABLE_DISPLAY_FEAT,
            sprintf(__('Override and disable all advertising reporting and remarketing features established in Google Analytics. <a href="%s" target="_blank">What\'s this?</a>', $this->plugin_text_domain), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features')
        );
    }

    /**
     * Anonymize IP
     */
    public function do_anonymize_ip()
    {
        $this->do_checkbox(
            __('Anonymize IP?', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_ANONYMIZE_IP,
            CAOS_OPT_ANONYMIZE_IP,
            __('Required by law in some countries. Replaces the last digits of a visitor\'s IP-address with \'000\'.', $this->plugin_text_domain)
        );
    }

    /**
     * Track logged in Administrators
     */
    public function do_track_admin()
    {
        $this->do_checkbox(
            __('Track logged in Administrators?', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_TRACK_ADMIN,
            CAOS_OPT_TRACK_ADMIN,
            '<strong>' . __('Warning!', $this->plugin_text_domain) . '</strong> ' . __('This will track all your traffic as a logged in user.', $this->plugin_text_domain)
        );
    }

    /**
     * Remove settings at uninstall
     */
    public function do_uninstall_settings()
    {
        $this->do_checkbox(
            __('Remove settings at uninstall?', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS,
            CAOS_OPT_UNINSTALL_SETTINGS,
            '<strong>' . __('Warning!', 'host-analytics-local') . '</strong> ' . __('This will remove the settings from the database upon plugin deletion!', $this->plugin_text_domain)
        );
    }
}
