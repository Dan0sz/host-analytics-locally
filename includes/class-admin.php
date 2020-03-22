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
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_notices', array($this, 'add_notice'));
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
}