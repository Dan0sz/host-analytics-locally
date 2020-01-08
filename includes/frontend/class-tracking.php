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
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined('ABSPATH') || exit;

class CAOS_Frontend_Tracking
{
    /**
     * CAOS_Frontend_Tracking constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'insert_tracking_code'));
        add_action('caos_process_settings', array($this, 'disable_display_features'));
        add_action('caos_process_settings', array($this, 'anonymize_ip'));
    }

    /**
     * Render the tracking code in it's selected locations
     */
    public function insert_tracking_code()
    {
        if (CAOS_OPT_COMPATIBILITY_MODE == 'woocommerce') {
            add_filter('woocommerce_google_analytics_script_src', array($this, 'return_analytics_js_url'), PHP_INT_MAX);
        } elseif (CAOS_OPT_COMPATIBILITY_MODE == 'monster_insights') {
            add_filter('monsterinsights_frontend_output_analytics_src', array($this, 'return_analytics_js_url'), PHP_INT_MAX);
        } elseif (CAOS_OPT_COMPATIBILITY_MODE == 'analytify') {
            add_filter('analytify_output_ga_js_src', array($this, 'return_analytics_js_url'), PHP_INT_MAX);
        } elseif (CAOS_OPT_COMPATIBILITY_MODE == 'exact_metrics') {
            add_filter('gadwp_analytics_script_path', array($this, 'return_analytics_js_url'), PHP_INT_MAX);
        } elseif (current_user_can('manage_options') && !CAOS_OPT_TRACK_ADMIN) {
            switch (CAOS_OPT_SCRIPT_POSITION) {
                case "footer":
                    add_action('wp_footer', array($this, 'show_admin_message'), CAOS_OPT_ENQUEUE_ORDER);
                    break;
                case "manual":
                    break;
                default:
                    add_action('wp_head', array($this, 'show_admin_message'), CAOS_OPT_ENQUEUE_ORDER);
                    break;
            }
        } else {
            /**
             * Allows WP DEV's to modify the output of the tracking code.
             *
             * E.g. add_action('caos_process_settings', 'your_function_name');
             */
            do_action('caos_process_settings');

            switch (CAOS_OPT_SCRIPT_POSITION) {
                case "footer":
                    add_action('wp_footer', array($this, 'render_tracking_code'), CAOS_OPT_ENQUEUE_ORDER);
                    break;
                case "manual":
                    break;
                default:
                    add_action('wp_head', array($this, 'render_tracking_code'), CAOS_OPT_ENQUEUE_ORDER);
                    break;
            }
        }
    }

    /**
     * Process disable display features setting.
     */
    public function disable_display_features()
    {
        if (CAOS_OPT_DISABLE_DISPLAY_FEAT !== 'on') {
            return;
        }

        if ($this->is_gtag()) {
            add_filter('caos_gtag_config', function($config, $trackingId) {
                return $config + array('displayFeaturesTask' => null);
            }, 10, 2);
        }

        add_filter('caos_analytics_before_send', function($config) {
            $option = array(
                'display_features' => "ga('set', 'displayFeaturesTask', null);"
            );

            return $config + $option;
        });
    }

    /**
     * Process Anonymize IP setting.
     */
    public function anonymize_ip()
    {
        if (CAOS_OPT_ANONYMIZE_IP !== 'on') {
            return;
        }

        if ($this->is_gtag()) {
            add_filter('caos_gtag_config', function($config, $trackingId) {
                return $config + array('anonymize_ip' => true);
            }, 10, 2);
        }

        add_filter('caos_analytics_before_send', function($config) {
            $option = array(
                'anonymize' => "ga('set', 'anonymizeIp', true);"
            );

            return $config + $option;
        });
    }

    /**
     * @return bool
     */
    private function is_gtag()
    {
        return CAOS_OPT_REMOTE_JS_FILE == 'gtag.js';
    }

    /**
     * Render a HTML comment for logged in Administrators in the source code.
     */
    public function show_admin_message()
    {
        echo "<!-- " . __('This site is using CAOS. You\'re logged in as an administrator, so we\'re not loading the tracking code.', 'host-analyticsjs-local') . " -->\n";
    }

    /**
     * Render the URL of the cached local-ga.js file
     *
     * @return string
     */
    public function return_analytics_js_url()
    {
        return CAOS_LOCAL_FILE_URL;
    }

    /**
     * Generate tracking code and add to header/footer (default is header)
     */
    public function render_tracking_code()
    {
        if (!CAOS_OPT_TRACKING_ID) {
            return;
        }

        echo "<!-- " . __('This site is running CAOS for Wordpress', 'host-analyticsjs-local') . " -->\n";

        if (CAOS_OPT_REMOTE_JS_FILE == 'gtag.js' || (CAOS_OPT_SNIPPET_TYPE == 'async' && CAOS_OPT_REMOTE_JS_FILE != 'ga.js')) {
            $urlId            = CAOS_OPT_REMOTE_JS_FILE == 'gtag.js' ? "?id=" . CAOS_OPT_TRACKING_ID : '';
            $snippetType      = CAOS_OPT_SNIPPET_TYPE;
            $localFileUrl     = CAOS_LOCAL_FILE_URL . $urlId;
            $scriptAttributes = CAOS_OPT_REMOTE_JS_FILE == 'gtag.js'
                ? apply_filters('caos_gtag_script_element_attributes', '')
                : apply_filters('caos_analytics_script_element_attributes', '');

            echo "<script $snippetType src='$localFileUrl' $scriptAttributes></script>";
        }

        if (CAOS_OPT_ALLOW_TRACKING == 'cookie_has_value' && CAOS_OPT_COOKIE_NAME && CAOS_OPT_COOKIE_VALUE) {
            $this->get_tracking_code_template('cookie-value');
        }

        if (CAOS_OPT_REMOTE_JS_FILE == 'gtag.js') {
            $this->get_tracking_code_template('gtag');
        } else {
            $this->get_tracking_code_template('analytics');
        }
    }

    /**
     * @param $name
     */
    public function get_tracking_code_template($name)
    {
        return include CAOS_PLUGIN_DIR . 'templates/frontend-tracking-code-' . $name . '.phtml';
    }
}
