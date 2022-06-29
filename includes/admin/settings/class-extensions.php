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
 * @url      : https://daan.dev/wordpress/caos/
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
        add_filter('caos_extensions_settings_content', [$this, 'do_description'], 11);

        /**
         * Stealth Mode Promo "settings"
         */
        add_filter('caos_extensions_settings_content', [$this, 'open_extensions_panel'], 12);
        add_filter('caos_extensions_settings_content', [$this, 'do_before'], 13);

        add_filter('caos_extensions_settings_content', [$this, 'do_stealth_mode_promo'], 14);
        add_filter('caos_extensions_settings_content', [$this, 'do_request_handling_promo'], 15);
        add_filter('caos_extensions_settings_content', [$this, 'do_cloudflare_compatibility'], 16);

        add_filter('caos_extensions_settings_content', [$this, 'do_after'], 18);
        add_filter('caos_extensions_settings_content', [$this, 'close_extensions_panel'], 19);

        add_filter('caos_extensions_settings_content', [$this, 'do_sub_title'], 20);
        add_filter('caos_extensions_settings_content', [$this, 'do_before'], 21);

        add_filter('caos_extensions_settings_content', [$this, 'do_capture_outbound_links'], 30);

        /**
         * Priorities 150 and up can't be used when compatibility mode is on. A proper notice will be shown when it's enabled.
         */
        add_filter('caos_extensions_settings_content', [$this, 'do_invisible_option_notice'], 40);
        add_filter('caos_extensions_settings_content', [$this, 'do_tbody_extensions_settings_open'], 50);

        add_filter('caos_extensions_settings_content', [$this, 'do_track_ad_blockers'], 60);
        add_filter('caos_extensions_settings_content', [$this, 'do_linkid'], 70);

        add_filter('caos_extensions_settings_content', [$this, 'do_tbody_close'], 99);
        add_filter('caos_extensions_settings_content', [$this, 'do_after'], 100);

        parent::__construct();
    }

    public function do_description()
    {
?>
        <p>
            <?= sprintf(__("Extensions are typically specific to a set of features that may not be required by all CAOS and/or Google Analytics users, such as Stealth Mode, ecommerce or cross-domain measurement, and are therefore not enabled/included in CAOS by default.", $this->plugin_text_domain)); ?>
        </p>
        <p>
            <?= sprintf(__("For a list of available extensions, click <a href='%s'>here</a>.", $this->plugin_text_domain), 'https://daan.dev/wordpress-plugins/'); ?>
        </p>
    <?php
    }

    /**
     * Opens the Automatic Optimization Mode status screen container.
     * 
     * @return void 
     */
    public function open_extensions_panel()
    {
    ?>
        <div class="caos-extensions postbox" style="padding: 0 15px 5px;">
            <h3><?= __('Stealth Mode (Pro)', $this->plugin_text_domain); ?></h3>
            <p>
                <?= __('Stealth Mode is a unique technology developed specifically for CAOS to recover valuable Google Analytics data otherwise lost by Ad Blockers.', $this->plugin_text_domain) . ' ' . $this->promo; ?>
            </p>
        <?php
    }

    /**
     * @return void 
     */
    public function do_stealth_mode_promo()
    {
        $this->do_checkbox(
            __('Stealth Mode (Pro)', $this->plugin_text_domain),
            'caos_pro_stealth_mode',
            defined('CAOS_PRO_STEALTH_MODE') ? CAOS_PRO_STEALTH_MODE : false,
            sprintf(__('Stealth Mode enables WordPress to route all Plausible and Google Analytics traffic (e.g. <code>plausible.io/api/event</code> or <code>google-analytics.com/g/collect</code>) through a custom-built API, making it undetectable by Ad Blockers. <a href="%s" target="_blank">Read More</a>', $this->plugin_text_domain), CAOS_SITE_URL . '/how-to/bypass-ad-blockers-caos/' . $this->utm_tags) . ' ' . $this->promo,
            true
        );
    }

    /**
     * Request Handling
     */
    public function do_request_handling_promo()
    {
        $this->do_radio(
            __('Request Handling (Pro)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADMIN_EXT_REQUEST_HANDLING,
            'caos_pro_request_handling',
            defined('CAOS_PRO_REQUEST_HANDLING') ? CAOS_PRO_REQUEST_HANDLING : false,
            __('In Stealth Mode, all Plausible and Google Analytics related requests (e.g. <code>/api/event</code>, <code>/g/collect</code>, <code>linkid.js</code> or <code>ec.js</code>) are routed through WordPress\' (<strong>often sluggish</strong>) API to avoid Ad Blockers. Using the (<em>10x faster</em>) Super Stealth API, requests are sent almost instantly.', $this->plugin_text_domain) . ' ' . $this->promo,
            [true, true]
        );
    }

    /**
     * @return void 
     */
    public function do_cloudflare_compatibility()
    {
        $this->do_checkbox(
            __('Cloudflare Compatibility (Pro)', $this->plugin_text_domain),
            'caos_pro_cf_compatibility',
            defined('CAOS_PRO_CF_COMPATIBILITY') ? CAOS_PRO_CF_COMPATIBILITY : false,
            __('When your site is proxied through Cloudflare and your Google Analytics data is incomplete (e.g. location data is missing) enable this option.', $this->plugin_text_domain),
            true,
            CAOS_OPT_SERVICE_PROVIDER == 'google_analytics'
        );
    }

    /**
     * Close the container.
     * 
     * @return void 
     */
    public function close_extensions_panel()
    {
        ?>
        </div>
    <?php
    }

    public function do_sub_title()
    {
    ?>
        <h3><?= __('Installed Extensions', $this->plugin_text_domain); ?></h3>
<?php
    }

    /**
     *
     */
    public function do_tbody_extensions_settings_open()
    {
        $this->do_tbody_open('caos_extensions_settings', !empty(CAOS_OPT_COMPATIBILITY_MODE) || CAOS_OPT_SERVICE_PROVIDER == 'google_analytics');
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
            sprintf(__('Sends an event, containing the link information your users used to leave your site. Might not work properly while using Google Analytics with Stealth Mode enabled. %sRead more%s', $this->plugin_text_domain), '<a target="_blank" href="https://support.google.com/analytics/answer/1136920">', '</a>')
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
            sprintf(__("Enable this option to gain insight into the missing data in your Google Analytics dashboard. Adds two tiny (< 1 KiB / non-render blocking) bits of JavaScript right before Analytics' tracking code. Reports an event to Google Analytics containing a visitor's ad blocker usage. This is not the same as Stealth Mode! <a target='blank' href='%s'>Read more</a>", $this->plugin_text_domain), 'https://daan.dev/docs/caos/extensions/')
        );
    }


    /**
     * Enable Enhanced Link Attribution
     */
    public function do_linkid()
    {
        $this->do_checkbox(
            __('Enhanced Link Attribution', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_EXT_SETTING_LINKID,
            CAOS_OPT_EXT_LINKID,
            sprintf(__('Automatically differentiate between multiple links to the same URL on a single page. Does not work with Minimal Analytics. <a href="%s" target="_blank">Read more</a>.', $this->plugin_text_domain), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution')
        );
    }
}
