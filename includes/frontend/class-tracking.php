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

class CAOS_Frontend_Tracking
{
    const CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS = 'caos-track-ad-blockers';

    /** @var string $handle */
    public $handle = '';

    /** @var bool $in_footer */
    private $in_footer = false;

    /**
     * CAOS_Frontend_Tracking constructor.
     */
    public function __construct()
    {
        $this->handle    = 'caos-' . (CAOS_OPT_SNIPPET_TYPE ? CAOS_OPT_SNIPPET_TYPE . '-' : '') . str_replace('.', '-', CAOS_OPT_REMOTE_JS_FILE);
        $this->in_footer = CAOS_OPT_SCRIPT_POSITION == 'footer';

        add_action('init', [$this, 'insert_tracking_code']);
        add_filter('script_loader_tag', [$this, 'add_async_attribute'], 10, 2);
        add_filter('caos_minimal_analytics_endpoint', [$this, 'set_minimal_analytics_endpoint'], 10, 1);
        add_action('caos_process_settings', [$this, 'disable_display_features']);
        add_action('caos_process_settings', [$this, 'anonymize_ip']);
        add_action('caos_process_settings', [$this, 'linkid']);
        add_action('caos_process_settings', [$this, 'google_optimize']);
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
            if (CAOS_OPT_EXT_TRACK_AD_BLOCKERS == 'on') {
                add_action('wp_enqueue_scripts', [$this, 'insert_ad_blocker_tracking'], CAOS_OPT_ENQUEUE_ORDER);
            }

            /**
             * Allows WP DEV's to modify the output of the tracking code.
             *
             * E.g. add_action('caos_process_settings', 'your_function_name');
             */
            do_action('caos_process_settings');

            switch (CAOS_OPT_SCRIPT_POSITION) {
                case "manual":
                    break;
                default:
                    add_action('wp_enqueue_scripts', [$this, 'render_tracking_code'], CAOS_OPT_ENQUEUE_ORDER);
                    break;
            }
        }
    }

    /**
     * Adds async attribute to analytics.js/gtag.js script.
     *
     * @param $tag
     * @param $handle
     *
     * @return string
     */
    public function add_async_attribute($tag, $handle)
    {
        if ((CAOS_OPT_SNIPPET_TYPE == 'async' && $handle == $this->handle)) {
            return str_replace('script src', 'script async src', $tag);
        }

        if ($handle == self::CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS) {
            return str_replace('script src', 'script defer src', $tag);
        }

        return $tag;
    }

    /**
     * Set correct endpoint for minimal analytics.
     * If Stealth Mode is enabled, override $endpoint.
     *
     * @param $endpoint
     *
     * @return string
     */
    public function set_minimal_analytics_endpoint($endpoint)
    {
        if (CAOS_OPT_EXT_STEALTH_MODE == 'on') {
            return apply_filters('caos_minimal_analytics_stealth_mode_endpoint', site_url('wp-json/caos/v1/proxy/collect'));
        }

        return $endpoint;
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
     * Enhanced Link Attribution
     *
     * TODO: Set samesite flag as soon as it's available. (https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution)
     */
    public function linkid()
    {
        if (CAOS_OPT_EXT_LINKID !== 'on') {
            return;
        }

        if ($this->is_gtag()) {
            add_filter('caos_gtag_config', function ($config, $tracking_id) {
                return $config + ['linkid', [
                        'cookie_name'  => 'caos_linkid',
                        'cookie_flags' => 'samesite=none;secure'
                    ]
                ];
            }, 10, 2);
        }

        add_filter('caos_analytics_before_send', function($config) {
            $option = [
                'linkid' => "ga('require', 'linkid', { 'cookieName':'caosLinkid', 'cookieFlags':'samesite=none;secure' });"
            ];

            return $config + $option;
        });
    }

    /**
     * Google Optimize
     */
    public function google_optimize()
    {
        if (CAOS_OPT_EXT_OPTIMIZE !== 'on') {
            return;
        }

        $optimize_id = CAOS_OPT_EXT_OPTIMIZE_ID;

        if (!$optimize_id) {
            return;
        }

        if ($this->is_gtag()) {
            add_filter('caos_gtag_config', function ($config, $tracking_id) use ($optimize_id) {
                return $config + [ 'optimize_id' => $optimize_id ];
            }, 10, 2);
        }

        add_filter('caos_analytics_before_send', function ($config) use ($optimize_id) {
            $option = [
                'optimize' => "ga('require', '$optimize_id');"
            ];

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
     * Render the URL of the cached local file
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

        $deps = CAOS_OPT_EXT_TRACK_AD_BLOCKERS ? [ 'jquery', self::CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS ] : [ 'jquery' ];

        if (CAOS_OPT_SNIPPET_TYPE != 'minimal') {
            $url_id         = CAOS_OPT_REMOTE_JS_FILE == 'gtag.js' ? "?id=" . CAOS_OPT_TRACKING_ID : '';
            $local_file_url = CAOS_LOCAL_FILE_URL . $url_id;
            wp_enqueue_script($this->handle, $local_file_url, $deps, null, $this->in_footer);
        }

        if (CAOS_OPT_ALLOW_TRACKING == 'cookie_has_value' && CAOS_OPT_COOKIE_NAME && CAOS_OPT_COOKIE_VALUE) {
            wp_add_inline_script($this->handle, $this->get_tracking_code_template('cookie-value'));
        }

        if (CAOS_OPT_SNIPPET_TYPE == 'minimal') {
            /**
             * Since no other libraries are loaded when Minimal Analytics is enabled, we need to add the
             * inline script to a default WordPress library. We're using jQuery, but this might not work in all
             * configurations. Open to suggestions.
             */
            $handle = CAOS_OPT_EXT_TRACK_AD_BLOCKERS ? self::CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS : 'jquery';
            wp_add_inline_script($handle, $this->get_tracking_code_template('minimal'));

            return;
        }

        switch (CAOS_OPT_REMOTE_JS_FILE) {
            case 'gtag.js':
                wp_add_inline_script($this->handle, $this->get_tracking_code_template('gtag'));
                break;
            default:
                wp_add_inline_script($this->handle, $this->get_tracking_code_template('analytics'));
                break;
        }
    }

    /**
     * @param $name
     *
     * @return false|string
     */
    public function get_tracking_code_template($name)
    {
        ob_start();

        include CAOS_PLUGIN_DIR . 'templates/frontend-tracking-code-' . $name . '.phtml';

        return str_replace([ '<script>', '</script>' ], '', ob_get_clean());
    }

    /**
     * Respects the tracking code's position (header/footer) because this script needs to be triggered after the
     * pageview is sent.
     */
    public function insert_ad_blocker_tracking()
    {
        wp_enqueue_script(self::CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS, plugins_url('assets/js/detect-ad-block.js', CAOS_PLUGIN_FILE), [ 'jquery' ], CAOS_STATIC_VERSION, $this->in_footer);
        wp_add_inline_script(self::CAOS_SCRIPT_HANDLE_TRACK_AD_BLOCKERS, $this->send_ad_blocker_result());
    }

    /**
     * @return false|string
     */
    private function send_ad_blocker_result()
    {
        $url = site_url('wp-json/caos/v1/block/detect');

        ob_start();
        ?>
        <script>jQuery(window).on('caos_track_ad_blockers', function () { jQuery(document).ready(function ($) { var caos_detect_ad_blocker = 1; if (document.getElementById('caos-detect-ad-block')) { caos_detect_ad_blocker = 0; } $.ajax({ method: 'GET', url: '<?= $url; ?>', data: { result: caos_detect_ad_blocker } }); }); });</script>
        <?php

        return str_replace([ '<script>', '</script>' ], '', ob_get_clean());
    }
}