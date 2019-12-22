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

class CAOS_Setup
{
    /**
     * CAOS_Setup constructor.
     */
    public function __construct()
    {
        // @formatter:off
        register_activation_hook(CAOS_PLUGIN_FILE, array($this, 'create_cache_dir'));
        register_activation_hook(CAOS_PLUGIN_FILE, array($this, 'activate_cron'));
        register_deactivation_hook(CAOS_PLUGIN_FILE, array($this, 'deactivate_cron'));
        add_action(CAOS_CRON, array($this, 'load_cron_script'));
        // @formatter:on
    }

    /**
     * Create Cache-dir upon reactivation.
     */
    public function create_cache_dir()
    {
        $uploadDir = CAOS_LOCAL_DIR;
        if (!is_dir($uploadDir)) {
            wp_mkdir_p($uploadDir);
        }
    }

    /**
     * Register hook to schedule script in wp_cron()
     */
    public function activate_cron()
    {
        if (!wp_next_scheduled(CAOS_CRON)) {
            wp_schedule_event(time(), 'daily', CAOS_CRON);
        }
    }

    /**
     *
     */
    public function deactivate_cron()
    {
        if (wp_next_scheduled(CAOS_CRON)) {
            wp_clear_scheduled_hook(CAOS_CRON);
        }
    }

    /**
     *
     */
    public function load_cron_script()
    {
        new CAOS_Admin_Cron_Script();
    }
}
