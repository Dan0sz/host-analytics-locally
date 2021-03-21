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
    public function __construct()
    {
        $option_page             = $_POST['option_page'] ?? '';
        $this->optimization_mode = $_POST[OMGF_Admin_Settings::OMGF_OPTIMIZE_SETTING_OPTIMIZATION_MODE] ?? '';

        if (
            CAOS_Admin_Settings::CAOS_ADMIN_SECTION_BASIC_SETTINGS != $option_page
            && CAOS_Admin_Settings::CAOS_ADMIN_SECTION_ADV_SETTINGS != $option_page
            && CAOS_Admin_Settings::CAOS_ADMIN_SECTION_EXT_SETTINGS != $option_page
        ) {
            return;
        }

        parent::__construct();
    }
}
