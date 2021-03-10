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

class CAOS_Frontend_Functions
{
    /**
     * CAOS_Frontend_Functions constructor.
     */
    public function __construct()
    {
        // Needs to be added after Google Analytics library is requested.
        add_action('wp_enqueue_scripts', [$this, 'enqueue_js_scripts'], CAOS_OPT_ENQUEUE_ORDER + 1);

        // If Stealth Mode is disabled, add DNS Prefetch for google-analytics.com
        if (!CAOS_OPT_EXT_STEALTH_MODE) {
            add_filter('wp_resource_hints', [$this, 'add_dns_prefetch'], 10, 2);
        }
    }

    /**
     * Enqueue JS scripts for frontend.
     */
    function enqueue_js_scripts()
    {
        if (current_user_can('manage_options') && !CAOS_OPT_TRACK_ADMIN) {
            return;
        }

        if (CAOS_OPT_EXT_CAPTURE_OUTBOUND_LINKS === 'on') {
            $tracking = new CAOS_Frontend_Tracking();
            wp_add_inline_script($tracking->handle, $this->get_frontend_template('outbound-link-tracking'));
        }
    }

    /**
     * @param $name
     *
     * @return false|string
     */
    public function get_frontend_template($name)
    {
        ob_start();

        include CAOS_PLUGIN_DIR . 'templates/frontend-' . $name . '.phtml';

        return str_replace(['<script>', '</script>'], '', ob_get_clean());
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
