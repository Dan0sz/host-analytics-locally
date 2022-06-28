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
        add_filter('caos_advanced_settings_content', [$this, 'do_remote_js_file'], 40);
        add_filter('caos_advanced_settings_content', [$this, 'do_cache_dir'], 50);
        add_filter('caos_advanced_settings_content', [$this, 'do_cdn_url'], 60);

        // Non Compatibility Mode settings.
        add_filter('caos_advanced_settings_content', [$this, 'do_invisible_option_notice'], 70);
        add_filter('caos_advanced_settings_content', [$this, 'do_tbody_advanced_settings_open'], 100);
        add_filter('caos_advanced_settings_content', [$this, 'do_cookieless_analytics_promo'], 110);
        add_filter('caos_advanced_settings_content', [$this, 'do_cloaked_affiliate_links_tracking_promo'], 120);
        add_filter('caos_advanced_settings_content', [$this, 'do_session_expiry'], 130);
        add_filter('caos_advanced_settings_content', [$this, 'do_site_speed_sample_rate'], 140);
        add_filter('caos_advanced_settings_content', [$this, 'do_change_enqueue_order'], 150);
        add_filter('caos_advanced_settings_content', [$this, 'do_advertising_features'], 160);
        add_filter('caos_advanced_settings_content', [$this, 'do_tbody_close'], 200);

        // Uninstall Setting
        add_filter('caos_advanced_settings_content', [$this, 'do_uninstall_settings'], 220);

        // Close
        add_filter('caos_advanced_settings_content', [$this, 'do_after'], 250);

        parent::__construct();
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
     * 
     * @since v4.3.0 Compatibility mode is now a checkbox, because it parses the HTML.
     */
    public function do_compatibility_mode()
    {
        $this->do_checkbox(
            __('Compatibility Mode', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_COMPATIBILITY_MODE,
            CAOS_OPT_COMPATIBILITY_MODE != '' ? 'on' : '',
            __('Check this option to use CAOS with any other Google Analytics plugin. Any reference to <code>google-analytics.com/analytics.js</code> and <code>googletagmanager.com/gtag/js</code> on your site will be replaced with a local copy. <strong>Warning!</strong> Please make sure that CAOS\' <strong>Basic Settings</strong> and <strong>Download File</strong> settings match your Google Analytics plugin\'s configuration.', $this->plugin_text_domain)
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
            sprintf(__('<code>analytics.js</code> is recommended in most situations. <code>gtag.js</code> is a wrapper for <code>analytics.js</code> and should only be used if you\'re using other Google services or want to enable dual tracking with GA4. Both files are hosted locally when this option is selected! GA v4 (beta) users should choose <code>gtag.js</code> (V4 API). %sI don\'t know what to choose%s.', $this->plugin_text_domain), '<a href="' . CAOS_SITE_URL . '/wordpress/difference-analyics-gtag-ga-js/' . $this->utm_tags . '" target="_blank">', '</a>')
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
            __('Enable Cookieless Analytics (Pro)', $this->plugin_text_domain),
            'caos_pro_cookieless_analytics',
            defined('CAOS_PRO_COOKIELESS_ANALYTICS') && CAOS_PRO_COOKIELESS_ANALYTICS,
            $description,
            true
        );
    }

    /**
     * Add Cloacked Affiliate Links Tracking promo.
     * 
     * @return void 
     */
    public function do_cloaked_affiliate_links_tracking_promo()
    {
    ?>
        <tr>
            <th><?= __('Track Cloaked Affiliate Links (Pro)', $this->plugin_text_domain); ?></th>
            <td>
                <table class="track-cloaked-affiliate-links">
                    <tr>
                        <th><?= __('Path', $this->plugin_text_domain); ?></th>
                        <th><?= __('Event Category', $this->plugin_text_domain); ?></th>
                        <th></th>
                    </tr>
                    <?php
                    $affiliate_links = defined('CAOS_PRO_AFFILIATE_LINKS') && CAOS_PRO_AFFILIATE_LINKS ? CAOS_PRO_AFFILIATE_LINKS : [0 => ['path' => '', 'category' => '']];
                    $disabled        = apply_filters('caos_pro_track_cloaked_affiliate_links_setting_disabled', true) ? 'disabled' : '';

                    foreach ($affiliate_links as $key => $properties) :
                    ?>
                        <tr id="affiliate-link-row-<?= $key; ?>">
                            <?php foreach ($properties as $prop_key => $prop_value) : ?>
                                <td id="affiliate-link-<?= $prop_key; ?>-<?= $key; ?>">
                                    <input type="text" <?= $disabled; ?> class="affiliate-link-<?= $prop_key; ?>" name="caos_pro_cloaked_affiliate_links[<?= $key; ?>][<?= $prop_key; ?>]" value="<?= $prop_value; ?>" />
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
                    <?= defined('CAOS_PRO_STEALTH_MODE') && CAOS_PRO_STEALTH_MODE == 'on' ? __('If no events are registered in Google Analytics, your server might be too slow to send them in time. Please disable Stealth Mode if that\'s the case.', $this->plugin_text_domain) : ''; ?>
                    <?= __('Send an event to Google Analytics whenever a Cloaked Affiliate Link is clicked. An event with the configured <strong>Event Category</strong> is sent to Google Analytics whenever a link containing the <strong>Path</strong> value is clicked. The <strong>Event Label</strong> will be the URL of the link. Depending on your server\'s capacity, this might not work properly with Stealth Mode enabled.', $this->plugin_text_domain) . ' ' . $this->promo; ?>
                </p>
            </td>
        </tr>
<?php
    }


    /**
     * Tbody open
     */
    public function do_tbody_advanced_settings_open()
    {
        $this->do_tbody_open('caos_advanced_settings', CAOS_OPT_SERVICE_PROVIDER == 'google_analytics' && (empty(CAOS_OPT_COMPATIBILITY_MODE) || CAOS_OPT_SERVICE_PROVIDER == 'google_analytics'));
    }

    /**
     * Cookie expiry period (days)
     */
    public function do_session_expiry()
    {
        $this->do_number(
            __('Session expiry period (days)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_GA_SESSION_EXPIRY_DAYS,
            CAOS_OPT_SESSION_EXPIRY_DAYS,
            __('The number of days when the user session will automatically expire. When using <strong>Cookieless Analytics</strong> the ClientID will be refreshed after this amount of days. (Default: 30)', $this->plugin_text_domain)
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
     * Disable all advertising features functionality
     */
    public function do_advertising_features()
    {
        $this->do_checkbox(
            __('Disable Advertising Features', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADV_SETTING_DISABLE_ADS_FEATURES,
            CAOS_OPT_DISABLE_ADS_FEAT,
            sprintf(__('Override and disable all advertising reporting and remarketing features established in Google Analytics. <a href="%s" target="_blank">What\'s this?</a>', $this->plugin_text_domain), 'https://support.google.com/analytics/answer/9050852?hl=en')
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
