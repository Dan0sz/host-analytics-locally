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

class CAOS_Admin_Settings_Basic extends CAOS_Admin_Settings_Builder
{
    /**
     * CAOS_Admin_Settings_Basic constructor.
     */
    public function __construct()
    {
        $this->title = __('Basic Settings', $this->plugin_text_domain);

        // Open
        add_filter('caos_basic_settings_content', [$this, 'do_title'], 1);
        add_filter('caos_basic_settings_content', [$this, 'do_before'], 2);

        // Settings
        add_filter('caos_basic_settings_content', [$this, 'do_service_provider'], 10);
        add_filter('caos_basic_settings_content', [$this, 'do_track_admin'], 12);
        add_filter('caos_basic_settings_content', [$this, 'do_domain_name'], 22);
        add_filter('caos_basic_settings_content', [$this, 'do_tracking_id'], 32);
        add_filter('caos_basic_settings_content', [$this, 'do_dual_tracking'], 34);
        add_filter('caos_basic_settings_content', [$this, 'do_ga4_measurement_id'], 36);
        add_filter('caos_basic_settings_content', [$this, 'do_gdpr_compliance_promo'], 51);
        add_filter('caos_basic_settings_content', [$this, 'do_allow_tracking'], 52);
        add_filter('caos_basic_settings_content', [$this, 'do_cookie_name'], 54);
        add_filter('caos_basic_settings_content', [$this, 'do_cookie_value'], 56);
        add_filter('caos_basic_settings_content', [$this, 'do_tracking_code'], 58);
        add_filter('caos_basic_settings_content', [$this, 'do_anonymize_ip_mode'], 60);
        add_filter('caos_basic_settings_content', [$this, 'do_script_position'], 61);
        add_filter('caos_basic_settings_content', [$this, 'do_add_manually'], 62);
        add_filter('caos_basic_settings_content', [$this, 'do_adjusted_bounce_rate'], 65);

        // Close
        add_filter('caos_basic_settings_content', [$this, 'do_after'], 100);

        parent::__construct();
    }

    /**
     * Service Provider
     * 
     * @return void 
     */
    public function do_service_provider()
    {
        $this->do_radio(
            __('Service Provider', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADMIN_SERVICE_PROVIDER_OPTION,
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_SERVICE_PROVIDER,
            CAOS_OPT_SERVICE_PROVIDER,
            sprintf(__('Looking for a simple, privacy and GDPR friendly alternative to Google Analytics? <a href="%s" target="_blank">Try Plausible Analytics free for 30 days</a>!', $this->plugin_text_domain), 'https://plausible.io/register')
        );
    }

    /**
     * Do Domain Name
     * 
     * @return void 
     */
    public function do_domain_name()
    {
        $this->do_text(
            __('Domain Name', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_DOMAIN_NAME,
            '',
            CAOS_OPT_DOMAIN_NAME,
            __('', $this->plugin_text_domain),
            true,
            CAOS_OPT_SERVICE_PROVIDER == 'google_analytics',
            __('Enable it by setting <strong>Service Provider</strong> to Plausible Analytics.', 'host-webfonts-local')
        );
    }

    /**
     * Google Analytics Tracking ID
     */
    public function do_tracking_id()
    {
        $this->do_text(
            __('Google Analytics Tracking ID', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_ID,
            __('e.g. UA-1234567-12', $this->plugin_text_domain),
            CAOS_OPT_TRACKING_ID,
            __('Enter your Tracking ID, e.g. UA-1234567-89 (v3 API) or G-123ABC789 (v4 API). Enter a V3 Tracking ID if you\'d like to enable Dual Tracking with GA V4.', $this->plugin_text_domain),
            true,
            CAOS_OPT_SERVICE_PROVIDER == 'plausible',
            __('Enable it by setting <strong>Service Provider</strong> to Google Analytics.', 'host-webfonts-local')
        );
    }

    /**
     * Enable Dual Tracking 
     * 
     * @return void 
     */
    public function do_dual_tracking()
    {
        $this->do_checkbox(
            __('Enable Dual Tracking', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_DUAL_TRACKING,
            CAOS_OPT_DUAL_TRACKING,
            'Enable dual tracking to send hits and events to both your UA and GA4 properties.',
            false,
            strpos(CAOS_OPT_TRACKING_ID, 'UA-') === 0
        );
    }

    /**
     * Google Analytics Dual Tracking ID
     * 
     * @return void 
     */
    public function do_ga4_measurement_id()
    {
        $this->do_text(
            __('GA4 Measurement ID', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID,
            __('e.g. G-123ABC456', $this->plugin_text_domain),
            CAOS_OPT_GA4_MEASUREMENT_ID,
            __('Enter a GA4 Measurement ID to enable dual tracking, e.g. G-123ABC789.', $this->plugin_text_domain),
            CAOS_OPT_DUAL_TRACKING == 'on' && strpos(CAOS_OPT_TRACKING_ID, 'UA-') === 0
        );
    }

    /**
     * Track logged in Administrators
     */
    public function do_track_admin()
    {
        $this->do_checkbox(
            __('Track logged in Administrators', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACK_ADMIN,
            CAOS_OPT_TRACK_ADMIN,
            '<strong>' . __('Warning!', $this->plugin_text_domain) . '</strong> ' . __('This will track all your traffic as a logged in user. (For testing/development purposes.)', $this->plugin_text_domain),
            false
        );
    }

    /**
     * GDPR Compliance
     */
    public function do_gdpr_compliance_promo()
    {
        $this->do_checkbox(
            __('Increase GDPR Compliance (Pro)', $this->plugin_text_domain),
            'caos_pro_gdpr',
            defined('CAOS_PRO_GDPR') ? CAOS_PRO_GDPR : false,
            sprintf(__('Remove any data that can be used to identify a person (i.e. personal data, e.g. IP address, User Agent, Location, etc.) to use Google Analytics in compliance with the GDPR. Be warned that enabling this setting <u>doesn\'t</u> guarantee GDPR compliance of your site, e.g. any parameters that enable (internal) routing (e.g. UTM tags) must be removed from any URLs on your site. <A href="%s" target="_blank">Read more</a>', $this->plugin_text_domain), 'https://www.cnil.fr/en/google-analytics-and-data-transfers-how-make-your-analytics-tool-compliant-gdpr') . ' ' . $this->promo,
            !defined('CAOS_PRO_GDPR') || CAOS_OPT_SERVICE_PROVIDER == 'plausible' || CAOS_OPT_COMPATIBILITY_MODE,
            true,
            true,
            __('Enable it by setting <strong>Service Provider</strong> to Google Analytics and/or disable <strong>Compatibility Mode</strong>.', 'host-webfonts-local')
        );
    }

    /**
     * Allow tracking...
     */
    public function do_allow_tracking()
    {
        $this->do_radio(
            __('Allow tracking...', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADMIN_ALLOW_TRACKING_OPTIONS,
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_ALLOW_TRACKING,
            CAOS_OPT_ALLOW_TRACKING,
            __('Configure CAOS to "listen" to your Cookie Notice plugin.', $this->plugin_text_domain) . ' ' . __('Choose \'Always\' to use Google Analytics without a Cookie Notice.', $this->plugin_text_domain) . ' ' . sprintf(__('<a href="%s" target="_blank">Consent Mode</a> is used when <strong>Consent mode</strong> is selected or a Google Analytics 4 (starting with G-) Measurement ID is configured in the <strong>Google Analytics Tracking ID</strong> field.', $this->plugin_text_domain), 'https://support.google.com/analytics/answer/9976101?hl=en'),
            CAOS_OPT_SERVICE_PROVIDER == 'plausible' || CAOS_OPT_COMPATIBILITY_MODE,
            false,
            __('Enable it by setting <strong>Service Provider</strong> to Google Analytics and/or disable <strong>Compatibility Mode</strong>.', 'host-webfonts-local')
        );
    }

    /**
     * Cookie name
     */
    public function do_cookie_name()
    {
        $this->do_text(
            __('Cookie Name', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME,
            __('e.g. cookie_accepted', $this->plugin_text_domain),
            CAOS_OPT_COOKIE_NAME,
            __('The cookie name set by your Cookie Notice plugin when user accepts.', $this->plugin_text_domain),
            CAOS_OPT_ALLOW_TRACKING
        );
    }

    /**
     * Cookie value
     */
    public function do_cookie_value()
    {
        $this->do_text(
            __('Cookie Value', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_VALUE,
            __('e.g. true', $this->plugin_text_domain),
            CAOS_OPT_COOKIE_VALUE,
            __('The value of the above specified cookie set by your Cookie Notice when user accepts.', $this->plugin_text_domain),
            CAOS_OPT_ALLOW_TRACKING == 'cookie_has_value'
        );
    }

    /**
     * Snippet type
     */
    public function do_tracking_code()
    {
        $this->do_select(
            __('Tracking Code', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_CODE,
            CAOS_Admin_Settings::CAOS_ADMIN_TRACKING_CODE_OPTIONS,
            CAOS_OPT_TRACKING_CODE,
            __('Should we use the default or the asynchronous tracking code? Minimal Analytics is fastest, but supports only basic features i.e. pageviews and events.', $this->plugin_text_domain) . ' ' . sprintf('<a href="%s" target="_blank">', 'https://daan.dev/docs/caos/basic-settings/' . $this->utm_tags) . __('Read more', $this->plugin_text_domain) . '</a>',
            CAOS_OPT_SERVICE_PROVIDER == 'plausible' || CAOS_OPT_COMPATIBILITY_MODE,
            __('Enable it by setting <strong>Service Provider</strong> to Google Analytics and/or disable <strong>Compatibility Mode</strong>.', 'host-webfonts-local')
        );
    }

    /**
     * Render Anonymize IP Mode option and example.
     */
    public function do_anonymize_ip_mode()
    {
        $aip_mode     = CAOS_OPT_ANONYMIZE_IP_MODE;
        $aip_template = '<span class="caos-aip-example"><span class="octet">%s</span>.<span class="octet">%s</span>.<span class="octet">%s</span>.<span class="octet">%s</span></span>';

        switch ($aip_mode) {
            case 'one':
                $aip_example = sprintf($aip_template, '192', '168', '178', '0');
                break;
            case 'two':
                $aip_example = sprintf($aip_template, '192', '168', '0', '0');
                break;
            case 'all':
                $aip_example = sprintf($aip_template, '1', '0', '0', '0');
                break;
            default:
                $aip_example = sprintf($aip_template, '192', '168', '178', '1');
        }

        $this->do_radio(
            __('Anonymize IP Mode', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADMIN_ANONYMIZE_IP_MODE_OPTIONS,
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_ANONYMIZE_IP_MODE,
            $aip_mode,
            sprintf(__('<strong>One octet</strong> enables the <code>aip</code> parameter, provided by Google. <strong>Important:</strong> Due to <a href="%s">recent rulings</a>, anonymizing the last octet of the IP address is no longer sufficient according to the GDPR. If you have IP anonymization set to \'off\' or \'one\', your website will not comply with GDPR as personal data is still be stored on Google\'s servers. Anonymize <strong>all octets</strong> and enable <a href="%s">Stealth Mode</a> to properly anonymize IP addresses before sending the data over to Google, however location data will be lost.', $this->plugin_text_domain), CAOS_SITE_URL . '/gdpr/google-analytics-illegal-austria/' . $this->utm_tags, admin_url('options-general.php?page=host_analyticsjs_local&tab=caos-extensions-settings')) . sprintf(' <span class="caos-aip">Example: %s', $aip_example) . ' ' . $this->promo,
            CAOS_OPT_SERVICE_PROVIDER == 'plausible' ? true : [false, false, !defined('CAOS_PRO_ANONYMIZE_IP'), !defined('CAOS_PRO_ANONYMIZE_IP')],
            false,
            __('Enable it by setting <strong>Service Provider</strong> to Google Analytics.', 'host-webfonts-local')
        );
    }

    /**
     * Position of tracking-code
     */
    public function do_script_position()
    {
        $this->do_radio(
            __('Tracking Code Position', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADMIN_SCRIPT_POSITION_OPTIONS,
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION,
            CAOS_OPT_SCRIPT_POSITION,
            __('Load the Analytics tracking-snippet in the header, footer or manually? If e.g. your theme doesn\'t load the <code>wp_head()</code> conventionally, choose \'Add manually\'.', $this->plugin_text_domain),
            CAOS_OPT_COMPATIBILITY_MODE || CAOS_OPT_SERVICE_PROVIDER == 'plausible',
            false,
            __('Enable it by setting <strong>Service Provider</strong> to Google Analytics and/or disable <strong>Compatibility Mode</strong>.', 'host-webfonts-local')
        );
    }

    /**
     * Use adjusted bounce rate?
     */
    public function do_adjusted_bounce_rate()
    {
        $this->do_number(
            __('Adjusted Bounce Rate (seconds)', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_ADJUSTED_BOUNCE_RATE,
            CAOS_OPT_ADJUSTED_BOUNCE_RATE,
            sprintf(__('Create a more realistic view of your website\'s Bounce Rate. This option creates an event which is triggered after a user spends X seconds on a page. <a target="_blank" href="%s">Read more</a>.', $this->plugin_text_domain), CAOS_SITE_URL . '/how-to/adjusted-bounce-rate-caos/' . $this->utm_tags),
            0,
            CAOS_OPT_COMPATIBILITY_MODE,
            __('Disable <strong>Compatibility Mode</strong> to use it.', 'host-webfonts-local')
        );
    }

    /**
     * Render Tracking-code when 'Add Manually' is selected.
     */
    public function do_add_manually()
    {
?>
        <tr class="caos_add_manually" valign="top" <?= CAOS_OPT_SCRIPT_POSITION == 'manual' ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?php _e('Tracking-code', $this->plugin_text_domain); ?></th>
            <td>
                <label>
                    <textarea style="display: block; width: 100%; height: 250px;"><?= $this->render_tracking_code(); ?></textarea>
                </label>
                <p class="description">
                    <?php _e('Copy this to the theme or plugin which should handle displaying the snippet.', $this->plugin_text_domain); ?>
                </p>
            </td>
        </tr>
<?php
    }

    /**
     * Render Tracking Code for Manual placement.
     *
     * @return string
     */
    private function render_tracking_code()
    {
        $tracking_code = "\n";

        if (!CAOS_OPT_TRACKING_ID) {
            return $tracking_code;
        }

        $tracking_code .= "<!-- " . __('This site is running CAOS for Wordpress.', 'host-analyticsjs-local') . " -->\n";

        if (CAOS_OPT_TRACKING_CODE == 'minimal') {
            return $tracking_code . $this->get_tracking_code_template('minimal');
        }

        if (CAOS_OPT_TRACKING_CODE == 'minimal_ga4') {
            return $tracking_code . $this->get_tracking_code_template('minimal-ga4');
        }

        $urlId        = CAOS_OPT_REMOTE_JS_FILE == 'gtag.js' ? "?id=" . CAOS_OPT_TRACKING_ID : '';
        $snippetType  = CAOS_OPT_TRACKING_CODE;
        $localFileUrl = CAOS::get_local_file_url() . $urlId;

        $tracking_code .= "<script $snippetType src='$localFileUrl'></script>\n";

        if (CAOS_OPT_ALLOW_TRACKING == 'cookie_has_value' && CAOS_OPT_COOKIE_NAME && CAOS_OPT_COOKIE_VALUE) {
            $tracking_code .= $this->get_tracking_code_template('cookie-value');
        }


        if (CAOS_OPT_REMOTE_JS_FILE == 'gtag.js') {
            return $tracking_code . $this->get_tracking_code_template('gtag');
        } else {
            return $tracking_code . $this->get_tracking_code_template('analytics');
        }
    }

    /**
     * @param $name
     *
     * @return false|string
     */
    private function get_tracking_code_template($name)
    {
        ob_start();

        include CAOS_PLUGIN_DIR . 'templates/frontend-tracking-code-' . $name . '.phtml';

        return ob_get_clean();
    }
}
