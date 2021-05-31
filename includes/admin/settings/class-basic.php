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

        add_filter('caos_basic_settings_content', [$this, 'do_compatibility_mode_notice'], 40);

        // Non-compatibility mode settings
        add_filter('caos_basic_settings_content', [$this, 'do_tbody_basic_settings_open'], 50);
        add_filter('caos_basic_settings_content', [$this, 'do_track_admin'], 60);
        add_filter('caos_basic_settings_content', [$this, 'do_allow_tracking'], 70);
        add_filter('caos_basic_settings_content', [$this, 'do_cookie_name'], 80);
        add_filter('caos_basic_settings_content', [$this, 'do_cookie_value'], 90);
        add_filter('caos_basic_settings_content', [$this, 'do_snippet_type'], 100);
        add_filter('caos_basic_settings_content', [$this, 'do_anonymize_ip'], 110);
        add_filter('caos_basic_settings_content', [$this, 'do_script_position'], 120);
        add_filter('caos_basic_settings_content', [$this, 'do_add_manually'], 130);
        add_filter('caos_basic_settings_content', [$this, 'do_tbody_close'], 140);

        // Close
        add_filter('caos_basic_settings_content', [$this, 'do_after'], 150);
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
            __('Enter your Tracking ID, e.g. UA-1234567-89 (v3 API) or G-123ABC789 (v4 API)', $this->plugin_text_domain)
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
            __('Should we use the default or the asynchronous tracking snippet? Minimal Analytics is fastest, but supports only basic features i.e. pageviews and events.', $this->plugin_text_domain) . ' ' . '<a href="https://daan.dev/wordpress-plugins/caos/#basic-settings" target="_blank">' . __('Read more', $this->plugin_text_domain) . '</a>'
        );
    }

    /**
     * Anonymize IP
     */
    public function do_anonymize_ip()
    {
        $this->do_checkbox(
            __('Anonymize IP', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_ANONYMIZE_IP,
            CAOS_OPT_ANONYMIZE_IP,
            __('Increase GDPR compliance by enabling this setting. Required by law in some countries. Replaces the last digits of a visitor\'s IP-address with \'000\'.', $this->plugin_text_domain)
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
