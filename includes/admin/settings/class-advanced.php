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
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

class CAOS_Admin_Settings_Advanced extends CAOS_Admin_Settings_Builder
{
    /**
     * CAOS_Admin_Settings_Advanced constructor.
     */
    public function __construct()
    {
        parent::__construct();

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

        // Non Compatibility Mode settings.
        add_filter('caos_advanced_settings_content', [$this, 'do_tbody_advanced_settings_open'], 100);
        add_filter('caos_advanced_settings_content', [$this, 'do_cookie_expiry'], 110);
        add_filter('caos_advanced_settings_content', [$this, 'do_adjusted_bounce_rate'], 120);
        add_filter('caos_advanced_settings_content', [$this, 'do_site_speed_sample_rate'], 130);
        add_filter('caos_advanced_settings_content', [$this, 'do_change_enqueue_order'], 140);
        add_filter('caos_advanced_settings_content', [$this, 'do_disable_display_feat'], 150);
        add_filter('caos_advanced_settings_content', [$this, 'do_tbody_close'], 200);

        // Uninstall Setting
        add_filter('caos_advanced_settings_content', [$this, 'do_debug_mode'], 210);
        add_filter('caos_advanced_settings_content', [$this, 'do_uninstall_settings'], 220);

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
        </p>
<?php
    }

    /**
     * Enable Compatibility Mode
     */
    public function do_compatibility_mode()
    {
        $this->do_select(
            __('Compatibility Mode', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE,
            CAOS_Admin_Settings::CAOS_ADMIN_COMPATIBILITY_OPTIONS,
            CAOS_OPT_COMPATIBILITY_MODE,
            sprintf(__('Allow another Google Analytics plugin to use <code>%s</code> and manage Google Analytics entirely within the other plugin. After changing this setting, review the <strong>Which File to Download?</strong> setting, and make sure %s is compatible with the plugin of your choice.', $this->plugin_text_domain), CAOS_OPT_CACHE_DIR . CAOS_OPT_REMOTE_JS_FILE, CAOS_OPT_REMOTE_JS_FILE)
        );
    }

    /**
     * Which file to download?
     */
    public function do_remote_js_file()
    {
        $this->do_select(
            __('Download File', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_JS_FILE,
            CAOS_Admin_Settings::CAOS_ADMIN_JS_FILE_OPTIONS,
            CAOS_OPT_REMOTE_JS_FILE,
            sprintf(__('<code>analytics.js</code> is recommended in most situations. <code>gtag.js</code> is a wrapper for <code>analytics.js</code> and should only be used if you\'re using other Google services. Both are hosted locally when this option is selected. <code>gtag.js</code> and <code>analytics.js</code> can only be used with the v3 API. Are you using the Beta V4 API? Then choose <code>gtag.js</code> (V4 API). Need help choosing? %sRead this%s', $this->plugin_text_domain), '<a href="' . CAOS_SITE_URL . '/wordpress/difference-analyics-gtag-ga-js/' . $this->utm_tags . '" target="_blank">', '</a>')
        );
    }

    /**
     * Save .js file to...
     */
    public function do_cache_dir()
    {
        $this->do_text(
            sprintf(__('Cache directory for %s', $this->plugin_text_domain), CAOS_OPT_REMOTE_JS_FILE),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR,
            __('e.g. /uploads/caos/', $this->plugin_text_domain),
            CAOS_OPT_CACHE_DIR,
            __("Change the path where the Analytics-file is cached inside WordPress' content directory (usually <code>wp-content</code>). Defaults to <code>/uploads/caos/</code>.", $this->plugin_text_domain)
        );
    }

    /**
     * Serve from a CDN?
     */
    public function do_cdn_url()
    {
        $this->do_text(
            __('Serve from CDN', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_CDN_URL,
            __('e.g. cdn.mydomain.com', $this->plugin_text_domain),
            CAOS_OPT_CDN_URL,
            sprintf(__('If you\'re using a CDN, enter the URL here to serve <code>%s</code> from your CDN.', $this->plugin_text_domain), CAOS_OPT_REMOTE_JS_FILE)
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
            CAOS_OPT_COOKIE_EXPIRY_DAYS,
            __('The number of days when the cookie will automatically expire. Defaults to 30 days.', $this->plugin_text_domain)
        );
    }

    /**
     * Use adjusted bounce rate?
     */
    public function do_adjusted_bounce_rate()
    {
        $this->do_number(
            __('Adjusted Bounce Rate (seconds)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_ADJUSTED_BOUNCE_RATE,
            CAOS_OPT_ADJUSTED_BOUNCE_RATE,
            sprintf(__('Set up an event which is triggered after a user spends X seconds on the landing page. <a target="_blank" href="%s">Read more</a>.', $this->plugin_text_domain), CAOS_SITE_URL . '/how-to/adjusted-bounce-rate-caos/' . $this->utm_tags)
        );
    }


    /**
     * Site Speed Sample Rate (%)
     * 
     * @return void 
     */
    public function do_site_speed_sample_rate()
    {
        $this->do_number(
            __('Site Speed Sample Rate (%)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_SITE_SPEED_SAMPLE_RATE,
            CAOS_OPT_SITE_SPEED_SAMPLE_RATE,
            __('This setting determines how often site speed beacons will be sent. Defaults to 1%. For low-traffic sites it is advised to set this to 50 or higher.', $this->plugin_text_domain)
        );
    }

    /**
     * Change enqueue order
     */
    public function do_change_enqueue_order()
    {
        $this->do_number(
            __('Enqueue order', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_ENQUEUE_ORDER,
            CAOS_OPT_ENQUEUE_ORDER,
            __('Do not change this unless you know, what you\'re doing. Defaults to 10.', $this->plugin_text_domain)
        );
    }

    /**
     * Disable all display features functionality
     */
    public function do_disable_display_feat()
    {
        $this->do_checkbox(
            __('Disable Display Features', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_DISPLAY_FEATURES,
            CAOS_OPT_DISABLE_DISPLAY_FEAT,
            sprintf(__('Override and disable all advertising reporting and remarketing features established in Google Analytics. <a href="%s" target="_blank">What\'s this?</a>', $this->plugin_text_domain), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/display-features')
        );
    }

    /**
     * Debug Mode
     * 
     * @return void 
     */
    public function do_debug_mode()
    {
        $this->do_checkbox(
            __('Enable Debug Mode', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_DEBUG_MODE,
            CAOS_OPT_DEBUG_MODE,
            __('Only use this for debugging purposes! When enabled, debug information is logged to <code>wp-content/caos-debug.log</code>', $this->plugin_text_domain)
        );
    }

    /**
     * Remove settings at uninstall
     */
    public function do_uninstall_settings()
    {
        $this->do_checkbox(
            __('Remove settings at Uninstall', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_UNINSTALL_SETTINGS,
            CAOS_OPT_UNINSTALL_SETTINGS,
            '<strong>' . __('Warning!', 'host-analytics-local') . '</strong> ' . __('This will remove the settings from the database upon plugin deletion!', $this->plugin_text_domain)
        );
    }
}
