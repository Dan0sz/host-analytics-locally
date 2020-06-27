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

class CAOS_Frontend_Functions
{
    /**
     * CAOS_Frontend_Functions constructor.
     */
    public function __construct()
    {
        // @formatter:off
        add_action('wp_enqueue_scripts', [$this, 'enqueue_js_scripts']);

        // If Stealth Mode is disabled, add DNS Prefetch for google-analytics.com
        if (!CAOS_OPT_EXT_STEALTH_MODE) {
            add_filter('wp_resource_hints', [$this, 'add_dns_prefetch'], 10, 2);
        }

        add_action('rest_api_init', [$this, 'register_routes']);
        // @formatter:on
    }

    /**
     * Enqueue JS scripts for frontend.
     */
    function enqueue_js_scripts()
    {
        if (current_user_can('manage_options') && !CAOS_OPT_TRACK_ADMIN) {
            return;
        }

        if (CAOS_OPT_CAPTURE_OUTBOUND_LINKS === 'on') {
            wp_enqueue_script('caos_frontend_script', plugins_url('assets/js/caos-frontend.js', CAOS_PLUGIN_FILE), ['jquery'], CAOS_STATIC_VERSION, true);
        }
    }

    /**
     * Register CAOS Proxy so endpoint can be used.
     * For using Stealth mode, SSL is required.
     */
    public function register_routes()
    {
        if (CAOS_OPT_EXT_STEALTH_MODE) {
            $proxy = new CAOS_API_Proxy();
            $proxy->register_routes();
        }

        if (CAOS_OPT_EXT_TRACK_AD_BLOCKERS) {
            $proxy = new CAOS_API_AdBlockDetect();
            $proxy->register_routes();
        }
    }

    /**
     * Add Preconnect to google-analytics.com and CDN URL (if set) in wp_head().
     */
    public function add_dns_prefetch($hints, $type)
    {
        if ($type == 'preconnect') {
            $hints[] = '//www.google-analytics.com';
        }

        return $hints;
    }
}
