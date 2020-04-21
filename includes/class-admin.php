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
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('admin_notices', [$this, 'add_notice']);

        // Settings
        $this->do_basic_settings();
        $this->do_advanced_settings();
        $this->do_connect_settings();

        // Notices
        add_action('update_option_sgal_tracking_id', [$this, 'add_tracking_code_notice'], 10, 2);
        add_action('update_option_sgal_script_position', [$this, 'add_script_position_notice'], 10, 2);
        add_action('pre_update_option_caos_analytics_js_file', [$this, 'add_js_file_notice'], 10, 2);
        add_action('update_option_caos_analytics_js_file', [$this, 'add_update_js_file_notice']);
        add_action('update_option_caos_analytics_cache_dir', [$this, 'add_cache_dir_notice'], 10, 2);
        add_action('pre_update_option_caos_stealth_mode', [$this, 'add_stealth_mode_notice'], 10, 2);
        add_action('pre_update_option_caos_capture_outbound_links', [$this, 'add_outbound_links_notice'], 10, 2);
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
            CAOS_Admin_Notice::set_notice(__("CAOS has connected WordPress to Google Analytics using Tracking ID: $new_tracking_id.", $this->plugin_text_domain), false);
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
                    CAOS_Admin_Notice::set_notice(__('Since you\'ve chosen to add it manually, don\'t forget to add the tracking code to your theme.', $this->plugin_text_domain), false, 'info');
                    break;
                default:
                    CAOS_Admin_Notice::set_notice(__("CAOS has added the Google Analytics tracking code to the $new_position of your theme.", $this->plugin_text_domain), false, 'success');
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
            if (CAOS_OPT_EXT_STEALTH_MODE) {
                if ($new_filename == 'ga.js') {
                    CAOS_Admin_Notice::set_notice(__('Ga.js is not compatible with Stealth Mode. Disable Stealth Mode to start using ga.js.', $this->plugin_text_domain), false, 'warning');

                    return $old_filename;
                }

                if ($new_filename == 'gtag.js' && !$this->is_super_stealth_active()) {
                    CAOS_Admin_Notice::set_notice(sprintf(__('Gtag.js is not compatible with Stealth Mode Lite. Disable it or get the <a href="%s" target="_blank">Super Stealth Upgrade</a> to start using gtag.js.'), CAOS_Admin_Settings::WOOSH_DEV_WORDPRESS_PLUGINS_SUPER_STEALTH . self::CAOS_ADMIN_UTM_PARAMS_NOTICES), false, 'warning');

                    return $old_filename;
                }
            }

            CAOS_Admin_Notice::set_notice(sprintf(__('%s will now be used to track visitors on your website.', $this->plugin_text_domain), ucfirst($new_filename)), false);
        }


        return $new_filename;
    }

    /**
     * @return bool
     */
    public function is_super_stealth_active()
    {
        $super_stealth = array_filter($this->get_plugins(), function ($basename) {
            return strpos($basename, 'caos-super-stealth') !== false;
        });

        $is_plugin_active = false;

        if (!empty($super_stealth)) {
            $is_plugin_active = $this->is_plugin_active(reset($super_stealth));
        }

        return $is_plugin_active;
    }

    /**
     * @return array[]
     */
    private function get_plugins()
    {
        return (array) get_option('active_plugins', array());
    }

    /**
     * Polyfill for is_plugin_active()
     *
     * @param $plugin
     *
     * @return bool
     */
    private function is_plugin_active($plugin)
    {
        if (!function_exists('is_plugin_active')) {
            return in_array($plugin, $this->get_plugins());
        }

        return is_plugin_active($plugin);
    }

    /**
     * Throw an update file notice after Remote JS file option is changed.
     */
    public function add_update_js_file_notice()
    {
        $this->add_update_file_reminder();
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
            CAOS_Admin_Notice::set_notice(sprintf(__('<strong>%s</strong> will now be saved in <em>%s</em>.', $this->plugin_text_domain), ucfirst(CAOS_OPT_REMOTE_JS_FILE), $new_dir), false);

            $this->add_update_file_reminder();
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
            if (CAOS_OPT_CAPTURE_OUTBOUND_LINKS) {
                CAOS_Admin_Notice::set_notice(__('Stealth Mode couldn\'t be enabled, because <strong>Outbound Link Capturing</strong> is enabled. Disable it to use Stealth Mode.', $this->plugin_text_domain), false, 'warning');

                return $old_value;
            }

            if (CAOS_OPT_REMOTE_JS_FILE == 'gtag.js' && !$this->is_super_stealth_active()) {
                CAOS_Admin_Notice::set_notice(sprintf(__('Stealth Mode couldn\'t be enabled, because <strong>gtag.js</strong> is set as <em>file to download</em>. Set it to <em>analytics.js</em> or get the <a href="%s" target="_blank">Super Stealth Upgrade</a> to use Stealth Mode with <em>gtag.js</em>.'), CAOS_Admin_Settings::WOOSH_DEV_WORDPRESS_PLUGINS_SUPER_STEALTH), false, 'warning');

                return $old_value;
            }

            $message = apply_filters('caos_stealth_mode_setting_on_notice', sprintf(__('Stealth Mode enabled. CAOS will now attempt to bypass Ad Blockers! To bypass <u>all</u> Ad Blockers and <em>track Incognito Browser Sessions</em>, get the <a href="%s" target="_blank">Super Stealth Upgrade</a>.', $this->plugin_text_domain), CAOS_Admin_Settings::WOOSH_DEV_WORDPRESS_PLUGINS_SUPER_STEALTH . self::CAOS_ADMIN_UTM_PARAMS_NOTICES));

            CAOS_Admin_Notice::set_notice($message, false);

            $this->add_update_file_reminder();
        } elseif (empty($new_value)) {
            $message = apply_filters('caos_stealth_mode_setting_off_notice', __('Stealth Mode disabled.', $this->plugin_text_domain));

            CAOS_Admin_Notice::set_notice($message, false);

            $this->add_update_file_reminder();
        }

        return $new_value;
    }

    /**
     * @param $new_value
     * @param $old_value
     *
     * @return mixed
     */
    public function add_outbound_links_notice($new_value, $old_value)
    {
        if ($new_value !== $old_value && $new_value == 'on') {
            if (CAOS_OPT_EXT_STEALTH_MODE) {
                CAOS_Admin_Notice::set_notice(__('Outbound Link Capturing couldn\'t be enabled, because <strong>Stealth Mode</strong> is enabled. Disable it to use Outbound Link Capturing.', $this->plugin_text_domain), false, 'warning');

                return $old_value;
            }
        }

        return $new_value;
    }

    /**
     * Set reminder to update the selected file.
     */
    private function add_update_file_reminder()
    {
        CAOS_Admin_Notice::set_notice('<a href="#" id="notice-manual-download">' . __('Click here', $this->plugin_text_domain) . '</a> ' . sprintf(__('to download/update %s.', $this->plugin_text_domain), get_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_JS_FILE)), false, 'info', 200, 'all', 'update_file');
    }
}