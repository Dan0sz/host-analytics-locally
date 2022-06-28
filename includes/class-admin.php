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
        add_action('update_option_' . CAOS_Admin_Settings::CAOS_BASIC_SETTING_TRACKING_ID, [$this, 'add_tracking_code_notice'], 10, 2);
        add_action('update_option_' . CAOS_Admin_Settings::CAOS_BASIC_SETTING_DUAL_TRACKING, [$this, 'maybe_remove_related_settings'], 10, 2);
        add_action('update_option_' . CAOS_Admin_Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID, [$this, 'update_remote_js_file'], 10, 2);
        add_action('update_option_' . CAOS_Admin_Settings::CAOS_BASIC_SETTING_SCRIPT_POSITION, [$this, 'add_script_position_notice'], 10, 2);
        add_action('update_option_' . CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR, [$this, 'set_cache_dir_notice'], 10, 2);
        add_action('pre_update_option_' . CAOS_Admin_Settings::CAOS_ADV_SETTING_CACHE_DIR, [$this, 'validate_cache_dir'], 10, 2);
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

        $is_ga4    = substr($new_tracking_id, 0, 2) == 'G-';
        $is_dt     = CAOS_OPT_DUAL_TRACKING && strpos(CAOS_OPT_GA4_MEASUREMENT_ID, 'G-') !== false;
        $dt_notice = '';

        if ($is_ga4 && $is_dt) {
            $title     = 'Universal Analytics';
            $filename  = 'gtag.js';
            $dt_notice = 'but enabled Dual Tracking,';
        } elseif ($is_ga4) {
            $title    = 'Google Analytics 4';
            $filename = 'gtag-v4.js';

            /**
             * When a GA V4 measurement ID is used, the defined ID in Dual Tracking should be emptied, just to make sure it isn't used anywhere.
             */
            delete_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_DUAL_TRACKING);
            delete_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID);
        } else {
            $title    = 'Universal Analytics';
            $filename = 'analytics.js';
        }

        /**
         * If Minimal Analytics is used, don't throw notices and execute any further logic to prevent confusion.
         * 
         * @since v4.4.0
         */
        if (CAOS::uses_minimal_analytics()) {
            return $new_tracking_id;
        }

        update_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_JS_FILE, $filename);

        CAOS_Admin_Notice::set_notice(
            sprintf(__('Since you\'ve entered a %s ID, %s the <em>file to download</em> was changed to %s.', $this->plugin_text_domain), $title, $dt_notice, $filename),
            'warning'
        );

        if ($filename == 'analytics.js') {
            CAOS_Admin_Notice::set_notice(
                __('You can change the <em>file to download</em> manually to gtag.js in <em>Advanced Settings</em> if you wish to do so.', $this->plugin_text_domain),
                'info'
            );
        }

        return $new_tracking_id;
    }

    /**
     * Check if Dual Tracking is disabled and if so, remove GA4 Measurement ID.
     * 
     * @param mixed $old_value 
     * @param mixed $new_value 
     * @return mixed 
     */
    public function maybe_remove_related_settings($old_value, $new_value)
    {
        if ($new_value == $old_value) {
            return $new_value;
        }

        // Dual tracking has been enabled. Let's delete related options.
        if ($new_value != 'on') {
            delete_option(CAOS_Admin_Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID);

            /** 
             * This prevents the option from being added after this action is done running.
             * 
             * @see /wp-admin/options.php:305-314
             */
            if (isset($_POST[CAOS_Admin_Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID])) {
                unset($_POST[CAOS_Admin_Settings::CAOS_BASIC_SETTING_GA4_MEASUREMENT_ID]);
            }
        }

        return $new_value;
    }

    /**
     * Throw appropriate notices for enabling Dual Tracking.
     * 
     * @param mixed $old_id 
     * @param mixed $new_id 
     * @return mixed 
     */
    public function update_remote_js_file($old_id, $new_id)
    {
        if (strpos($new_id, 'G-') !== 0) {
            CAOS_Admin_Notice::set_notice(
                __('The entered Measurement ID isn\'t correct. Fix it to avoid breaking your Analytics.', $this->plugin_text_domain),
                'error'
            );
        } elseif (CAOS_OPT_REMOTE_JS_FILE != 'gtag.js') {
            CAOS_Admin_Notice::set_notice(
                __('Dual Tracking is enabled and the <em>file to download</em> was changed to <em>gtag.js</em>.', $this->plugin_text_domain),
                'info'
            );

            update_option(CAOS_Admin_Settings::CAOS_ADV_SETTING_JS_FILE, 'gtag.js');
        } else {
            CAOS_Admin_Notice::set_notice(
                __('Dual Tracking is enabled.', $this->plugin_text_domain),
                'info'
            );
        }

        return $new_id;
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
     * Perform a few checks before saving the Cache Directory value to the database.
     * 
     * @param mixed $new_dir 
     * @param mixed $old_dir 
     * @return mixed 
     */
    public function validate_cache_dir($new_dir, $old_dir)
    {
        $allowed_path = WP_CONTENT_DIR . $new_dir;
        $mkdir        = true;

        if (!file_exists($allowed_path)) {
            /**
             * wp_mkdir_p() already does some simple checks for path traversal, but we check it again using realpath() later on anyway.
             */
            $mkdir = wp_mkdir_p($allowed_path);
        }

        if (!$mkdir) {
            CAOS_Admin_Notice::set_notice(sprintf(__('Something went wrong while trying to create CAOS\' Cache Directory: %s. Setting wasn\'t updated.', $this->plugin_text_domain), $new_dir), 'error');

            return $old_dir;
        }

        $real_path = realpath($allowed_path);

        if ($real_path != rtrim($allowed_path, '/')) {
            CAOS_Admin_Notice::set_notice(__('CAOS\' Cache Directory wasn\'t changed. Attempted path traversal.', $this->plugin_text_domain), 'error');

            return $old_dir;
        }

        return $new_dir;
    }

    /**
     * @param $old_dir
     * @param $new_dir
     *
     * @return string
     */
    public function set_cache_dir_notice($old_dir, $new_dir)
    {
        if ($new_dir !== $old_dir && !empty($new_dir)) {
            CAOS_Admin_Notice::set_notice(sprintf(__('<strong>%s</strong> will now be saved in <em>%s</em>.', $this->plugin_text_domain), ucfirst(CAOS_OPT_REMOTE_JS_FILE), $new_dir));
        }


        return $new_dir;
    }
}
