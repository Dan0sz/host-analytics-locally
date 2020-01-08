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

class CAOS_Frontend_Functions
{
    /**
     * CAOS_Frontend_Functions constructor.
     */
    public function __construct()
    {
        // @formatter:off
        add_action('wp_enqueue_scripts', array($this, 'enqueue_js_scripts'));
        add_action('rest_api_init', array($this, 'register_routes'));
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
            wp_enqueue_script('caos_frontend_script', plugins_url('js/caos-frontend.js', CAOS_PLUGIN_FILE), ['jquery'], CAOS_STATIC_VERSION, true);
        }
    }

    /**
     * Register CAOS Proxy so endpoint can be used.
     * For using Stealth mode, SSL is required.
     */
    public function register_routes()
    {
        if (!CAOS_OPT_STEALTH_MODE) {
            return;
        }

        $proxy = new CAOS_API_Proxy();
        $proxy->register_routes();
    }
}
