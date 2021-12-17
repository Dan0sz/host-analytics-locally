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
        add_filter('caos_extensions_settings_content', [$this, 'do_description'], 11);

        /**
         * Super Stealth Promo "settings"
         */
        add_filter('caos_extensions_settings_content', [$this, 'open_extensions_panel'], 12);
        add_filter('caos_extensions_settings_content', [$this, 'do_before'], 13);

        add_filter('caos_extensions_settings_content', [$this, 'do_super_stealth_promo'], 14);
        add_filter('caos_extensions_settings_content', [$this, 'do_request_handling_promo'], 15);
        add_filter('caos_extensions_settings_content', [$this, 'do_cookieless_analytics_promo'], 16);
        add_filter('caos_extensions_settings_content', [$this, 'do_cloaked_affiliate_links_tracking'], 17);

        add_filter('caos_extensions_settings_content', [$this, 'do_after'], 18);
        add_filter('caos_extensions_settings_content', [$this, 'close_extensions_panel'], 19);

        add_filter('caos_extensions_settings_content', [$this, 'do_sub_title'], 20);
        add_filter('caos_extensions_settings_content', [$this, 'do_before'], 21);

        add_filter('caos_extensions_settings_content', [$this, 'do_capture_outbound_links'], 30);

        /**
         * Priorities 150 and up can't be used when compatibility mode is on. A proper notice will be shown when it's enabled.
         */
        add_filter('caos_extensions_settings_content', [$this, 'do_compatibility_mode_notice'], 40);
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
            <?= sprintf(__("Extensions are typically specific to a set of features that may not be required by all CAOS and/or Google Analytics users, such as Super Stealth, ecommerce or cross-domain measurement, and are therefore not enabled/included in CAOS (and %s) by default.", $this->plugin_text_domain), $file); ?>
        </p>
        <p>
            <?= sprintf(__("For a list of available extensions, click <a href='%s'>here</a>.", $this->plugin_text_domain), 'https://ffw.press/wordpress-plugins/'); ?>
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
        $file = CAOS_OPT_REMOTE_JS_FILE;
    ?>
        <div class="caos-extensions welcome-panel" style="padding: 0 15px 5px;">
            <h3><?= __('Super Stealth', $this->plugin_text_domain); ?></h3>
            <p>
                <?= __('Super Stealth offers several, unique ways to increase the value and quality of your Google Analytics data.', $this->plugin_text_domain) . ' ' . $this->promo; ?>
            </p>
        <?php
    }

    /**
     * 
     * @return void 
     */
    public function do_super_stealth_promo()
    {
        $this->do_checkbox(
            __('Enable Stealth Mode', $this->plugin_text_domain),
            'super_stealth_mode',
            defined('SUPER_STEALTH_MODE') ? SUPER_STEALTH_MODE : false,
            sprintf(__('Stealth Mode is a unique technology developed specifically for CAOS to recover valuable Google Analytics data otherwise lost by Ad Blockers. It enables WordPress to route all Google Analytics traffic (e.g. <code>google-analytics.com/g/collect</code> or <code>googletagmanager.com/gtag/js</code>) through a custom-built API, making it undetectable by Ad Blockers. <a target="_blank" href="%s">How does it work?</a>', $this->plugin_text_domain), CAOS_Admin_Settings::FFW_PRESS_WORDPRESS_PLUGINS_SUPER_STEALTH . $this->utm_tags, CAOS_SITE_URL . '/how-to/bypass-ad-blockers-caos/') . ' ' . $this->promo,
            true
        );
    }

    /**
     * Request Handling
     */
    public function do_request_handling_promo()
    {
        $this->do_radio(
            __('Request Handling', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADMIN_EXT_REQUEST_HANDLING,
            'super_stealth_request_handling',
            defined('SUPER_STEALTH_REQUEST_HANDLING') ? SUPER_STEALTH_REQUEST_HANDLING : false,
            __('In Stealth Mode, all Google Analytics related requests (e.g. <code>/g/collect</code>, <code>linkid.js</code> or <code>ec.js</code>) are routed through WordPress\' (<strong>often sluggish</strong>) API to avoid Ad Blockers. Using the (<em>10x faster</em>) Super Stealth API, requests are served almost instantly; closely mimicking Google Analytics\' own methods.', $this->plugin_text_domain) . ' ' . $this->promo,
            true
        );
    }

    /**
     * Add Cookieless Analytics option.
     * 
     * @return void 
     */
    public function do_cookieless_analytics_promo()
    {
        $description = __('When enabled Google Analytics will not create any cookies. This adds a layer of privacy for your visitors, increases GDPR Compliance and effectively removes the necessity for cookie consent.', $this->plugin_text_domain) . ' ' . $this->promo;

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

    public function do_cloaked_affiliate_links_tracking()
    {
        ?>
            <tr>
                <th><?= __('Track Cloaked Affiliate Links', $this->plugin_text_domain); ?></th>
                <td>
                    <table class="track-cloaked-affiliate-links">
                        <tr>
                            <th><?= __('Path', $this->plugin_text_domain); ?></th>
                            <th><?= __('Event Category', $this->plugin_text_domain); ?></th>
                            <th></th>
                        </tr>
                        <?php
                        $affiliate_links = defined('SUPER_STEALTH_AFFILIATE_LINKS') ? SUPER_STEALTH_AFFILIATE_LINKS : [0 => ['path' => '', 'label' => '']];
                        $disabled        = apply_filters('super_stealth_track_cloaked_affiliate_links_setting_disabled', true) ? 'disabled' : '';

                        foreach ($affiliate_links as $key => $properties) :
                        ?>
                            <tr id="affiliate-link-row-<?= $key; ?>">
                                <?php foreach ($properties as $prop_key => $prop_value) : ?>
                                    <td id="affiliate-link-<?= $prop_key; ?>-<?= $key; ?>">
                                        <input type="text" <?= $disabled; ?> class="affiliate-link-<?= $prop_key; ?>" name="super_stealth_cloaked_affiliate_links[<?= $key; ?>][<?= $prop_key; ?>]" value="<?= $prop_value; ?>" />
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <span class="dashicons dashicons-remove affiliate-link-remove" data-row="<?= $key; ?>" <?= $disabled ? 'style="opacity: 15%;"' : ''; ?>></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <p>
                        <input type="button" <?= $disabled; ?> class="button button-secondary" id="affiliate-link-add" value="<?= __('Add Link Path', $this->plugin_text_domain); ?>" />
                    </p>
                    <p class="description">
                        <?= __('Send an event to Google Analytics whenever a Cloaked Affiliate Link is clicked. An event with the configured <strong>Event Category</strong> is sent to Google Analytics whenever a link containing the <strong>Path</strong> value is clicked. The <strong>Event Label</strong> will be the URL of the link.', $this->plugin_text_domain) . ' ' . $this->promo; ?>
                    </p>
                </td>
            </tr>
        <?php
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
            sprintf(__("Enable this option to gain insight into the missing data in your Google Analytics dashboard. Adds two tiny (< 1 KiB / non-render blocking) bits of JavaScript right before Analytics' tracking code. Reports an event to Google Analytics containing a visitor's ad blocker usage. This is not the same as Stealth Mode! <a target='blank' href='%s'>Read more</a>", $this->plugin_text_domain), 'https://docs.ffw.press/article/32-extensions')
        );
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
        $this->do_tbody_open('caos_extensions_settings');
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
