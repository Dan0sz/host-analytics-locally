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

defined('ABSPATH') || exit;

class CAOS_Admin_Settings_Extensions extends CAOS_Admin_Settings_Builder
{
    public function __construct()
    {
        $this->title = __('Extensions', $this->plugin_text_domain);

        add_filter('caos_extensions_settings_content', [$this, 'do_title'], 10);
        add_filter('caos_extensions_settings_content', [$this, 'do_description'], 15);

        add_filter('caos_extensions_settings_content', [$this, 'do_before'], 20);

        add_filter('caos_extensions_settings_content', [$this, 'do_stealth_mode'], 30);
        add_filter('caos_extensions_settings_content', [$this, 'do_linkid'], 50);
        add_filter('caos_extensions_settings_content', [$this, 'do_optimize'], 70);
        add_filter('caos_extensions_settings_content', [$this, 'do_optimize_id'], 90);

        add_filter('caos_extensions_settings_content', [$this, 'do_after'], 100);
    }

    /**
     * Description
     */
    public function do_description()
    {
        $file = CAOS_OPT_REMOTE_JS_FILE;
        ?>
        <p>
            <?= sprintf(__("Enhance the functionality of CAOS and %s to aid in measuring user interaction using plugins. Plugins are typically specific to a set of features that may not be required by all Google Analytics users, such as ecommerce or cross-domain measurement, and are therefore not included in %s by default.", $this->plugin_text_domain), $file, $file); ?>
        </p>
        <?php
    }

    /**
     * Enable Stealth Mode Lite
     */
    public function do_stealth_mode()
    {
        $this->do_checkbox(
            __('Enable Stealth Mode Lite', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_STEALTH_MODE,
            CAOS_OPT_EXT_STEALTH_MODE,
            sprintf(__('Bypass some Ad Blockers and uncover ⅓ of data normally blocked by Ad Blockers. Upgrade to <a target="_blank" href="%s">Super Stealth</a> to <strong>bypass all Ad Blockers</strong> and for <strong>Enhanced Ecommerce</strong> (ec.js) support. <a target="_blank" href="%s">How does it work?</a>', $this->plugin_text_domain), CAOS_Admin_Settings::WOOSH_DEV_WORDPRESS_PLUGINS_SUPER_STEALTH . $this->utm_tags, CAOS_SITE_URL . '/how-to/bypass-ad-blockers-caos/')
        );
    }

    /**
     * Enable Enhanced Link Attribution
     */
    public function do_linkid()
    {
        $this->do_checkbox(
            __('Enable Enhanced Link Attribution?', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_LINKID,
            CAOS_OPT_EXT_LINKID,
            sprintf(__('Automatically differentiate between multiple links to the same URL on a single page. <a href="%s" target="_blank">Read more</a>.', $this->plugin_text_domain), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution')
        );
    }

    /**
     *
     */
    public function do_optimize()
    {
        $this->do_checkbox(
            __('Enable Google Optimize integration', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_OPTIMIZE,
            CAOS_OPT_EXT_OPTIMIZE,
            sprintf(__('Use A/B testing to test different versions of your web pages to see how they perform against an objective you’ve specified. To use Google Optimize with Stealth Mode, get the <a href="%s" target="_blank">CAOS Super Stealth Upgrade</a>. <a href="%s" target="_blank">How does it work?</a>', $this->plugin_text_domain), 'https://support.google.com/optimize/answer/6262084/', CAOS_Admin_Settings::WOOSH_DEV_WORDPRESS_PLUGINS_SUPER_STEALTH)
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
            __('e.g. GTM-123456', $this->plugin_text_domain),
            CAOS_OPT_EXT_OPTIMIZE_ID,
            __('Replace GTM-XXXXXXX with your Optimize container ID.', $this->plugin_text_domain),
            false,
            CAOS_OPT_EXT_OPTIMIZE == 'on'
        );
    }
}
