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

class CAOS_AJAX
{
    /**
     * CAOS_AJAX constructor.
     */
    public function __construct()
    {
        // @formatter:off
        add_action('wp_ajax_caos_analytics_ajax_manual_download', [$this, 'manual_download']);
        // @formatter:on
    }

    /**
     * @return CAOS_Admin_Cron_Script
     */
    public function manual_download()
    {
        return new CAOS_Admin_Cron_Script();
    }
}
