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
 * @url      : https://ffw.press/wordpress/caos/
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
        add_filter('caos_basic_settings_content', [$this, 'do_title'], 10);
        add_filter('caos_basic_settings_content', [$this, 'do_before'], 20);

        // Settings
        add_filter('caos_basic_settings_content', [$this, 'do_tracking_id'], 30);
        add_filter('caos_basic_settings_content', [$this, 'do_dual_tracking'], 40);
        add_filter('caos_basic_settings_content', [$this, 'do_ga4_measurement_id'], 50);
        add_filter('caos_basic_settings_content', [$this, 'do_compatibility_mode_notice'], 60);

        // Non-compatibility mode settings
        add_filter('caos_basic_settings_content', [$this, 'do_tbody_basic_settings_open'], 60);
        add_filter('caos_basic_settings_content', [$this, 'do_track_admin'], 70);
        add_filter('caos_basic_settings_content', [$this, 'do_allow_tracking'], 80);
        add_filter('caos_basic_settings_content', [$this, 'do_cookie_name'], 90);
        add_filter('caos_basic_settings_content', [$this, 'do_cookie_value'], 100);
        add_filter('caos_basic_settings_content', [$this, 'do_snippet_type'], 110);
        add_filter('caos_basic_settings_content', [$this, 'do_anonymize_ip_mode'], 120);
        add_filter('caos_basic_settings_content', [$this, 'do_script_position'], 130);
        add_filter('caos_basic_settings_content', [$this, 'do_add_manually'], 140);
        add_filter('caos_basic_settings_content', [$this, 'do_tbody_close'], 150);

        // Close
        add_filter('caos_basic_settings_content', [$this, 'do_after'], 200);

        parent::__construct();
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
            __('Enter your Tracking ID, e.g. UA-1234567-89 (v3 API) or G-123ABC789 (v4 API). Enter a V3 Tracking ID if you\'d like to enable Dual Tracking with GA V4.', $this->plugin_text_domain)
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
            '<strong>' . __('Warning!', $this->plugin_text_domain) . '</strong> ' . __('This will track all your traffic as a logged in user. (For testing/development purposes.)', $this->plugin_text_domain)
        );
    }

    /**
     *
     */
    public function do_tbody_basic_settings_open()
    {
        $this->do_tbody_open('caos_basic_settings');
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
            sprintf(__('Choose \'Always\' to use Google Analytics without a Cookie Notice. Follow %sthis tutorial%s to comply with GDPR Laws.', $this->plugin_text_domain), '<a href="' . CAOS_SITE_URL . "/wordpress/analytics-gdpr-anonymize-ip-cookie-notice/$this->utm_tags\" target='_blank'>", '</a>') . ' ' . __('Choose \'When cookie is set\' or \'When cookie has a value\' to make CAOS compatible with your Cookie Notice plugin.', $this->plugin_text_domain) . ' ' . sprintf(__('<a href="%s" target="_blank">Read more</a>.', $this->plugin_text_domain), CAOS_SITE_URL .  '/wordpress/gdpr-compliance-google-analytics/' . $this->utm_tags)
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
    public function do_snippet_type()
    {
        $this->do_select(
            __('Snippet Type', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_SNIPPET_TYPE,
            CAOS_Admin_Settings::CAOS_ADMIN_SNIPPET_TYPE_OPTIONS,
            CAOS_OPT_SNIPPET_TYPE,
            __('Should we use the default or the asynchronous tracking snippet? Minimal Analytics is fastest, but supports only basic features i.e. pageviews and events.', $this->plugin_text_domain) . ' ' . sprintf('<a href="%s" target="_blank">', 'https://docs.ffw.press/article/30-basic-settings' . $this->utm_tags) . __('Read more', $this->plugin_text_domain) . '</a>'
        );
    }

    /**
     * Render Anonymize IP Mode option and example.
     */
    public function do_anonymize_ip_mode()
    {
        $aip_mode     = CAOS_OPT_ANONYMIZE_IP_MODE;
        $third_octet  = $aip_mode == 'two' ? '0' : '178';
        $fourth_octet = $aip_mode != '' ? '0' : '123';

        $this->do_radio(
            __('Anonymize IP Mode', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADMIN_ANONYMIZE_IP_MODE_OPTIONS,
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_ANONYMIZE_IP_MODE,
            $aip_mode,
            sprintf(__('Enables the <code>aip</code> parameter, provided by Google. <strong>Important:</strong> Due to <a href="%s">recent rulings</a>, anonymizing the last octet of the IP address is no longer sufficient according to the GDPR. If you have IP anonymization set to \'off\' or \'one\', your website will not comply with GDPR as personal data might still be stored on Google\'s servers. Combining the option \'two\' with <a href="%s">Stealth Mode</a> will properly anonymize IP addresses before sending the data over to Google, however location data might be inaccurate.', $this->plugin_text_domain), CAOS_SITE_URL . '/gdpr/google-analytics-illegal-austria/' . $this->utm_tags, admin_url('options-general.php?page=host_analyticsjs_local&tab=caos-extensions-settings')) . sprintf(' <span class="caos-aip">Example: <span class="caos-aip-example">192.168.<span class="third-octet">%s</span>.<span class="fourth-octet">%s</span></span></span> ', $third_octet, $fourth_octet) . $this->promo,
            true
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
            __('Load the Analytics tracking-snippet in the header, footer or manually? If e.g. your theme doesn\'t load the <code>wp_head()</code> conventionally, choose \'Add manually\'.', $this->plugin_text_domain)
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

        if (CAOS_OPT_SNIPPET_TYPE != 'minimal') {
            $urlId        = CAOS_OPT_REMOTE_JS_FILE == 'gtag.js' ? "?id=" . CAOS_OPT_TRACKING_ID : '';
            $snippetType  = CAOS_OPT_SNIPPET_TYPE;
            $localFileUrl = CAOS::get_local_file_url() . $urlId;

            $tracking_code .= "<script $snippetType src='$localFileUrl'></script>\n";
        }

        if (CAOS_OPT_ALLOW_TRACKING == 'cookie_has_value' && CAOS_OPT_COOKIE_NAME && CAOS_OPT_COOKIE_VALUE) {
            $tracking_code .= $this->get_tracking_code_template('cookie-value');
        }

        if (CAOS_OPT_SNIPPET_TYPE == 'minimal') {
            return $tracking_code . $this->get_tracking_code_template('minimal');
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
