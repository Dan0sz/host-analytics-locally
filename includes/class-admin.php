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

class CAOS_Admin
{
    const CAOS_ADMIN_JS_HANDLE  = 'caos-admin-js';
    const CAOS_ADMIN_CSS_HANDLE = 'caos-admin-css';

    /**
     * CAOS_Admin constructor.
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('admin_notices', [$this, 'add_notice']);

        // Settings
        $this->do_basic_settings();
        $this->do_advanced_settings();
        $this->do_connect_settings();

        // Notices
        add_action('update_option_sgal_tracking_id', [$this, 'add_tracking_code_notice'], 10, 2);
        add_action('update_option_sgal_script_position', [$this, 'add_script_position_notice'], 10, 2);
        add_action('update_option_caos_stealth_mode', [$this, 'add_stealth_mode_notice'], 10, 2);
        add_action('update_option_caos_analytics_js_file', [$this, 'add_js_file_notice'], 10, 2);
        add_action('update_option_caos_analytics_cache_dir', [$this, 'add_cache_dir_notice'], 10, 2);
    }

    /**
     * Enqueues the necessary JS and CSS and passes options as a JS object.
     *
     * @param $hook
     */
    public function enqueue_admin_scripts($hook)
    {
        if ($hook == 'settings_page_host_analyticsjs_local') {
            wp_enqueue_script(self::CAOS_ADMIN_JS_HANDLE, plugin_dir_url(CAOS_PLUGIN_FILE) . 'assets/js/caos-admin.js', array('jquery'), CAOS_STATIC_VERSION, true);
            wp_enqueue_style(self::CAOS_ADMIN_CSS_HANDLE, plugin_dir_url(CAOS_PLUGIN_FILE) . 'assets/css/caos-admin.css', array(), CAOS_STATIC_VERSION);
        }
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
    private function do_connect_settings()
    {
        return new CAOS_Admin_Settings_Extensions();
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
            CAOS_Admin_Notice::set_notice(__("CAOS has connected WordPress to Google Analytics using Tracking ID: $new_tracking_id.", 'host-analyticsjs-local'), false);
        }

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
                    CAOS_Admin_Notice::set_notice(__('Since you\'ve chosen to add it manually, don\'t forget to add the tracking code to your theme.', 'host-analyticsjs-local'), false, 'info');
                    break;
                default:
                    CAOS_Admin_Notice::set_notice(__("CAOS has added the Google Analytics tracking code to the $new_position of your theme.", 'host-analyticsjs-local'), false, 'success');
                    break;
            }
        }

        return $new_position;
    }

    /**
     * @param $old_value
     * @param $new_value
     *
     * @return bool
     */
    public function add_stealth_mode_notice($old_value, $new_value)
    {
        if ($new_value == 'on') {
            $message = apply_filters('caos_stealth_mode_setting_on_notice', sprintf(__('Stealth Mode enabled. CAOS will now attempt to bypass Ad Blockers! To bypass <u>all</u> Ad Blockers and <em>track Incognito Browser Sessions</em>, get the <a href="%s" target="_blank">Super Stealth Upgrade</a>.', 'host-analyticsjs-local'), 'https://woosh.dev/wordpress-plugins/caos-upgrades/super-stealth/'));

            CAOS_Admin_Notice::set_notice($message, false);
        } else {
            $message = apply_filters('caos_stealth_mode_setting_off_notice', __('Stealth Mode disabled.', 'host-analyticsjs-local'));
            CAOS_Admin_Notice::set_notice($message, false);
        }

        $this->add_update_file_reminder();

        return $new_value;
    }

    /**
     * @param $old_filename
     * @param $new_filename
     *
     * @return string
     */
    public function add_js_file_notice($old_filename, $new_filename)
    {
        if ($new_filename !== $old_filename && !empty($new_filename)) {
            CAOS_Admin_Notice::set_notice(sprintf(__('%s will now be used to track visitors on your website.', 'host-analyticsjs-local'), ucfirst($new_filename)), false);
        }

        $this->add_update_file_reminder();

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
            CAOS_Admin_Notice::set_notice(sprintf(__('<strong>%s</strong> will now be saved in <em>%s</em>.', 'host-analyticsjs-local'), ucfirst(CAOS_OPT_REMOTE_JS_FILE), $new_dir), false);
        }

        $this->add_update_file_reminder();

        return $new_dir;
    }

    /**
     * Set reminder to update the selected file.
     */
    private function add_update_file_reminder()
    {
        CAOS_Admin_Notice::set_notice('<a href="#" id="notice-manual-download">' . __('Click here', 'host-analyticsjs-local') . '</a> ' . sprintf(__('to download/update %s.', 'host-analyticsjs-local'), CAOS_OPT_REMOTE_JS_FILE), false, 'info');
    }
}