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
        add_filter('caos_basic_settings_content', [$this, 'do_allow_tracking'], 60);
        add_filter('caos_basic_settings_content', [$this, 'do_cookie_name'], 70);
        add_filter('caos_basic_settings_content', [$this, 'do_cookie_value'], 80);
        add_filter('caos_basic_settings_content', [$this, 'do_snippet_type'], 90);
        add_filter('caos_basic_settings_content', [$this, 'do_script_position'], 100);
        add_filter('caos_basic_settings_content', [$this, 'do_add_manually'], 110);
        add_filter('caos_basic_settings_content', [$this, 'do_tbody_close'], 120);

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
            CAOS_OPT_TRACKING_ID
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
            sprintf(__('Choose \'Always\' to use Google Analytics without a Cookie Notice. Follow %sthis tutorial%s to comply with GDPR Laws.', $this->plugin_text_domain), '<a href="' . CAOS_SITE_URL . "/wordpress/analytics-gdpr-anonymize-ip-cookie-notice/$this->utm_tags\" target='_blank'>", '</a>') . __('Choose \'When cookie is set\' or \'When cookie has a value\' to make CAOS compatible with your Cookie Notice plugin.', $this->plugin_text_domain) . sprintf(__('<a href="%s" target="_blank">Read more</a>.', $this->plugin_text_domain), CAOS_SITE_URL .  '/wordpress/gdpr-compliance-google-analytics/' . $this->utm_tags)
        );
    }

    /**
     * Cookie name
     */
    public function do_cookie_name()
    {
        $this->do_text(
            __('Cookie name', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_NOTICE_NAME,
            __('e.g. cookie_accepted', $this->plugin_text_domain),
            CAOS_OPT_COOKIE_NAME,
            __('The cookie name set by your Cookie Notice plugin when user accepts.', $this->plugin_text_domain),
            false,
            CAOS_OPT_ALLOW_TRACKING
        );
    }

    /**
     * Cookie value
     */
    public function do_cookie_value()
    {
        $this->do_text(
            __('Cookie value', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_COOKIE_VALUE,
            __('e.g. true', $this->plugin_text_domain),
            CAOS_OPT_COOKIE_VALUE,
            __('The value of the above specified cookie set by your Cookie Notice when user accepts.', $this->plugin_text_domain),
            false,
            CAOS_OPT_ALLOW_TRACKING == 'cookie_has_value'
        );
    }

    /**
     * Snippet type
     */
    public function do_snippet_type()
    {
        $this->do_select(
            __('Snippet type', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_SNIPPET_TYPE,
            CAOS_Admin_Settings::CAOS_ADMIN_SNIPPET_TYPE_OPTIONS,
            CAOS_OPT_SNIPPET_TYPE,
            __('Should we use the default or the asynchronous tracking snippet? (Only supported for <code>gtag.js</code> and <code>analytics.js</code>)', $this->plugin_text_domain) . '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/" target="_blank">' . __('Read more', $this->plugin_text_domain) . '</a>'
        );
    }

    /**
     * Position of tracking-code
     */
    public function do_script_position()
    {
        $this->do_radio(
            __('Position of tracking-code', $this->plugin_text_domain),
            CAOS_Admin_Settings::CAOS_ADMIN_SCRIPT_POSITION_OPTIONS,
            CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION,
            CAOS_OPT_SCRIPT_POSITION,
            __('Load the Analytics tracking-snippet in the header, footer or manually? If e.g. your theme doesn\'t load the <code>wp_head()</code> conventionally, choose \'Add manually\'.', $this->plugin_text_domain)
        );
    }

    /**
     * Tracking-code
     */
    public function do_add_manually()
    {
        $frontend = new CAOS_Frontend_Tracking();

        ?>
        <tr class="caos_add_manually" valign="top" <?= CAOS_OPT_SCRIPT_POSITION == 'manual' ? '' : 'style="display: none;"'; ?>>
            <th scope="row"><?php _e('Tracking-code', $this->plugin_text_domain); ?></th>
            <td>
                <label>
                    <textarea style="display: block; width: 100%; height: 250px;"><?php $frontend->render_tracking_code(); ?></textarea>
                </label>
                <p class="description">
                    <?php _e('Copy this to the theme or plugin which should handle displaying the snippet.', $this->plugin_text_domain); ?>
                </p>
            </td>
        </tr>
        <?php
    }
}
