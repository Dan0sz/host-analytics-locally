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

class CAOS_Admin_UpdateFiles extends CAOS_Cron_Script
{
    /**
     * This class is triggered after settings are saved on one of CAOS' settings screens.
     */
    public function __construct()
    {
        $settings_page    = $_GET['page'] ?? '';
        $settings_updated = $_GET['settings-updated'] ?? '';

        if (
            CAOS_Admin_Settings::CAOS_ADMIN_PAGE != $settings_page
            || !$settings_updated
        ) {
            return;
        }

        parent::__construct();
    }
}
