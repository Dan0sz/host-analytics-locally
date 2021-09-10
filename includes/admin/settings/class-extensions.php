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

defined('ABSPATH') || exit;

class CAOS_Admin_Settings_Extensions extends CAOS_Admin_Settings_Builder
{
    public function __construct()
    {
        $this->title = __('Extensions', $this->plugin_text_domain);

        add_filter('caos_extensions_settings_content', [$this, 'do_title'], 10);
        add_filter('caos_extensions_settings_content', [$this, 'do_description'], 15);

        add_filter('caos_extensions_settings_content', [$this, 'do_before'], 20);

        add_filter('caos_extensions_settings_content', [$this, 'do_plugin_handling'], 50);
        add_filter('caos_extensions_settings_content', [$this, 'do_stealth_mode'], 60);
        add_filter('caos_extensions_settings_content', [$this, 'do_track_ad_blockers'], 70);

        add_filter('caos_extensions_settings_content', [$this, 'do_compatibility_mode_notice'], 100);

        // Non-compatibility mode settings
        add_filter('caos_extensions_settings_content', [$this, 'do_tbody_extensions_settings_open'], 110);
        add_filter('caos_extensions_settings_content', [$this, 'do_cookieless_analytics_promo'], 120);
        add_filter('caos_extensions_settings_content', [$this, 'do_linkid'], 130);
        add_filter('caos_extensions_settings_content', [$this, 'do_optimize'], 140);
        add_filter('caos_extensions_settings_content', [$this, 'do_optimize_id'], 150);
        add_filter('caos_extensions_settings_content', [$this, 'do_tbody_close'], 160);

        add_filter('caos_extensions_settings_content', [$this, 'do_capture_outbound_links'], 80);

        add_filter('caos_extensions_settings_content', [$this, 'do_after'], 200);
    }

    /**
     * Description
     */
    public function do_description()
    {
        $file = CAOS_OPT_REMOTE_JS_FILE;
?>
        <p>
            <?= sprintf(__("Enhance the functionality of CAOS and %s to aid in measuring user interaction using plugins.", $this->plugin_text_domain), $file); ?>
        </p>
        <p>
            <?= sprintf(__("Plugins are typically specific to a set of features that may not be required by all CAOS and/or Google Analytics users, such as Super Stealth, ecommerce or cross-domain measurement, and are therefore not enabled/included in CAOS (and %s) by default.", $this->plugin_text_domain), $file); ?>
        </p>
        <p>
            <?= sprintf(__("For a list of available plugins, click <a href='%s'>here</a>.", $this->plugin_text_domain), 'https://ffw.press/wordpress-plugins/'); ?>
        </p>
<?php
    }

    /**
     * Plugin Handling
     */
    public function do_plugin_handling()
    {
        $this->do_select(
            __('Plugin Handling', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_PLUGIN_HANDLING,
            CAOS_Admin_Settings::CAOS_ADMIN_EXT_PLUGIN_HANDLING,
            CAOS_OPT_EXT_PLUGIN_HANDLING,
            __('Safe Mode works on all environments, because it creates two requests to get the plugin file: one 302 redirect and the plugin file (e.g. linkid.js). Experimental Mode returns the plugin file immediately. If Google Analytics doesn\'t collect any data when in Experimental Mode, switch back to Safe Mode.', $this->plugin_text_domain)
        );
    }

    /**
     * Enable Stealth Mode Lite
     */
    public function do_stealth_mode()
    {
        $this->do_checkbox(
            __('Enable Stealth Mode Lite (deprecated)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_STEALTH_MODE,
            CAOS_OPT_EXT_STEALTH_MODE,
            sprintf(__('Bypass some Ad Blockers and uncover ⅓ of data normally blocked by Ad Blockers. Upgrade to <a target="_blank" href="%s">Super Stealth</a> to <strong>bypass all Ad Blockers</strong> and for <strong>Enhanced Ecommerce</strong> (ec.js) support. <a target="_blank" href="%s">How does it work?</a> <strong>This option will be removed from CAOS in a next release.</strong>', $this->plugin_text_domain), CAOS_Admin_Settings::FFW_PRESS_WORDPRESS_PLUGINS_SUPER_STEALTH . $this->utm_tags, CAOS_SITE_URL . '/how-to/bypass-ad-blockers-caos/')
        );
    }

    /**
     * Add Cookieless Analytics option.
     * 
     * @return void 
     */
    public function do_cookieless_analytics_promo()
    {
        $description = __('When enabled Google Analytics will not create any cookies. This increases GDPR Compliance and effectively removes the necessity for cookie consent.', $this->plugin_text_domain) . ' ' . $this->promo;

        if (CAOS_OPT_REMOTE_JS_FILE != 'analytics.js') {
            $description = __('This option will only work when <strong>Download File</strong> is set to <code>analytics.js</code>.', $this->plugin_text_domain) . ' ' . $description;
        }

        $this->do_checkbox(
            __('Enable Cookieless Analytics', $this->plugin_text_domain),
            'super_stealth_cookieless_analytics',
            defined('SUPER_STEALTH_COOKIELESS_ANALYTICS') && SUPER_STEALTH_COOKIELESS_ANALYTICS,
            $description,
            true
        );
    }

    /**
     *
     */
    public function do_track_ad_blockers()
    {
        $this->do_checkbox(
            __('Track Ad Blockers', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_TRACK_AD_BLOCKERS,
            CAOS_OPT_EXT_TRACK_AD_BLOCKERS,
            sprintf(__("Enable this option to gain insight into the missing data in your Google Analytics dashboard. Adds two tiny (< 1 KiB / non-render blocking) bits of JavaScript right before Analytics' tracking code. Reports an event to Google Analytics containing a visitor's ad blocker usage. This is not the same as Stealth Mode! <a target='blank' href='%s'>Read more</a>", $this->plugin_text_domain), CAOS_SITE_URL . '/wordpress-plugins/caos#extensions-settings')
        );
    }

    /**
     *
     */
    public function do_tbody_extensions_settings_open()
    {
        $this->do_tbody_open('caos_extensions_settings');
    }

    /**
     * Enable Enhanced Link Attribution
     */
    public function do_linkid()
    {
        $this->do_checkbox(
            __('Enable Enhanced Link Attribution', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_LINKID,
            CAOS_OPT_EXT_LINKID,
            sprintf(__('Automatically differentiate between multiple links to the same URL on a single page. Does not work with Minimal Analytics. <a href="%s" target="_blank">Read more</a>.', $this->plugin_text_domain), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution')
        );
    }

    /**
     *
     */
    public function do_optimize()
    {
        $this->do_checkbox(
            __('Enable Google Optimize integration (deprecated)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_OPTIMIZE,
            CAOS_OPT_EXT_OPTIMIZE,
            sprintf(__('Use A/B testing to test different versions of your web pages to see how they perform against an objective you’ve specified. Not compatible with Stealth Mode and Minimal Analytics. <a href="%s" target="_blank">How does it work?</a> <strong>This feature will be moved to a separate (free) plugin in a next release.</strong>', $this->plugin_text_domain), 'https://support.google.com/optimize/answer/6262084/', CAOS_Admin_Settings::FFW_PRESS_WORDPRESS_PLUGINS_SUPER_STEALTH)
        );
    }

    /**
     *
     */
    public function do_optimize_id()
    {
        $this->do_text(
            __('Google Optimize ID', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_OPTIMIZE_ID,
            __('e.g. GTM-123ABCD', $this->plugin_text_domain),
            CAOS_OPT_EXT_OPTIMIZE_ID,
            __('Enter your Optimize container ID.', $this->plugin_text_domain),
            CAOS_OPT_EXT_OPTIMIZE == 'on'
        );
    }

    /**
     * Capture outbound links?
     */
    public function do_capture_outbound_links()
    {
        $this->do_checkbox(
            __('Capture Outbound Links', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_CAPTURE_OUTBOUND_LINKS,
            CAOS_OPT_EXT_CAPTURE_OUTBOUND_LINKS,
            sprintf(__('Sends an event to Google Analytics, containing the link information your users used to leave your site. Might not work properly with Stealth Mode enabled. %sRead more%s', $this->plugin_text_domain), '<a target="_blank" href="https://support.google.com/analytics/answer/1136920">', '</a>')
        );
    }
}
