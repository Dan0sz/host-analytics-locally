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

class CAOS_Admin
{
    const CAOS_ADMIN_JS_HANDLE          = 'caos-admin-js';
    const CAOS_ADMIN_CSS_HANDLE         = 'caos-admin-css';
    const CAOS_ADMIN_UTM_PARAMS_NOTICES = '?utm_source=caos&utm_medium=plugin&utm_campaign=notices';

    /** @var string $plugin_text_domain */
    private $plugin_text_domain = 'host-analyticsjs-local';

    /**
     * CAOS_Admin constructor.
     */
    public function __construct()
    {
        add_action('admin_notices', [$this, 'add_notice']);

        // Settings
        $this->do_basic_settings();
        $this->do_advanced_settings();
        $this->do_extensions_settings();
        $this->do_help_section();

        // Notices
        add_action('update_option_sgal_tracking_id', [$this, 'add_tracking_code_notice'], 10, 2);
        add_action('update_option_sgal_script_position', [$this, 'add_script_position_notice'], 10, 2);
        add_action('pre_update_option_caos_analytics_js_file', [$this, 'add_js_file_notice'], 10, 2);
        add_action('update_option_caos_analytics_cache_dir', [$this, 'add_cache_dir_notice'], 10, 2);
        add_action('pre_update_option_caos_stealth_mode', [$this, 'add_stealth_mode_notice'], 10, 2);
    }

    /**
     * Add notice to admin screen.
     */
    public function add_notice()
    {
        CAOS_Admin_Notice::print_notice();
    }

    /**
     * @return CAOS_Admin_Settings_Basic
     */
    private function do_basic_settings()
    {
        return new CAOS_Admin_Settings_Basic();
    }

    /**
     * @return CAOS_Admin_Settings_Advanced
     */
    private function do_advanced_settings()
    {
        return new CAOS_Admin_Settings_Advanced();
    }

    /**
     * @return CAOS_Admin_Settings_Extensions
     */
    private function do_extensions_settings()
    {
        return new CAOS_Admin_Settings_Extensions();
    }

    /**
     * @return CAOS_Admin_Settings_Help 
     */
    private function do_help_section()
    {
        return new CAOS_Admin_Settings_Help();
    }

    /**
     * @param $new_tracking_id
     * @param $old_tracking_id
     *
     * @return mixed
     */
    public function add_tracking_code_notice($old_tracking_id, $new_tracking_id)
    {
        if ($new_tracking_id !== $old_tracking_id && !empty($new_tracking_id)) {
            CAOS_Admin_Notice::set_notice(sprintf(__("CAOS has connected WordPress to Google Analytics using Tracking ID: %s.", $this->plugin_text_domain), $new_tracking_id));
        }

        if (empty($new_tracking_id)) {
            return $new_tracking_id;
        }

        $title = 'Universal Analytics';
        $version = 'V3';
        $remote_file = 'analytics.js';

        if (substr($new_tracking_id, 0, 2) == 'G-') {
            $title = 'Google Analytics 4';
            $version = 'V4';
            $remote_file = 'gtag.js (V4 API)';
        }

        CAOS_Admin_Notice::set_notice(
            sprintf(__('You\'ve entered a %s ID which is only supported by Google Analytics\' %s API. Please change the <strong>file to download</strong> setting to <code>%s</code> under <em>Advanced Settings</em> if you haven\'t done so already.', $this->plugin_text_domain), $title, $version, $remote_file),
            'warning'
        );

        return $new_tracking_id;
    }

    /**
     * @param $new_position
     * @param $old_position
     *
     * @return mixed
     */
    public function add_script_position_notice($old_position, $new_position)
    {
        if ($new_position !== $old_position && !empty($new_position)) {
            switch ($new_position) {
                case 'manual':
                    CAOS_Admin_Notice::set_notice(__('Since you\'ve chosen to add it manually, don\'t forget to add the tracking code to your theme.', $this->plugin_text_domain), 'info');
                    break;
                default:
                    CAOS_Admin_Notice::set_notice(__("CAOS has added the Google Analytics tracking code to the $new_position of your theme.", $this->plugin_text_domain), 'success');
                    break;
            }
        }

        return $new_position;
    }

    /**
     * @param $old_filename
     * @param $new_filename
     *
     * @return string
     */
    public function add_js_file_notice($new_filename, $old_filename)
    {
        if ($new_filename !== $old_filename && !empty($new_filename)) {
            CAOS_Admin_Notice::set_notice(sprintf(__('%s will now be used to track visitors on your website.', $this->plugin_text_domain), ucfirst($new_filename)));
        }


        return $new_filename;
    }

    /**
     * @param $old_dir
     * @param $new_dir
     *
     * @return string
     */
    public function add_cache_dir_notice($old_dir, $new_dir)
    {
        if ($new_dir !== $old_dir && !empty($new_dir)) {
            CAOS_Admin_Notice::set_notice(sprintf(__('<strong>%s</strong> will now be saved in <em>%s</em>.', $this->plugin_text_domain), ucfirst(CAOS_OPT_REMOTE_JS_FILE), $new_dir));
        }


        return $new_dir;
    }

    /**
     * @param $old_value
     * @param $new_value
     *
     * @return bool
     */
    public function add_stealth_mode_notice($new_value, $old_value)
    {
        if ($new_value !== $old_value && $new_value == 'on') {
            $message = apply_filters('caos_stealth_mode_setting_on_notice', sprintf(__('Stealth Mode enabled. CAOS will now attempt to bypass Ad Blockers! To bypass <u>all</u> Ad Blockers and <em>track Incognito Browser Sessions</em>, get the <a href="%s" target="_blank">Super Stealth Upgrade</a>.', $this->plugin_text_domain), CAOS_Admin_Settings::FFW_PRESS_WORDPRESS_PLUGINS_SUPER_STEALTH . self::CAOS_ADMIN_UTM_PARAMS_NOTICES));

            CAOS_Admin_Notice::set_notice($message);
        } elseif (empty($new_value)) {
            $message = apply_filters('caos_stealth_mode_setting_off_notice', __('Stealth Mode disabled.', $this->plugin_text_domain));

            CAOS_Admin_Notice::set_notice($message);
        }

        return $new_value;
    }
}
