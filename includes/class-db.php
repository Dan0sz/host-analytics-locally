<?php
defined('ABSPATH') || exit;

/* * * * * * * * * * * * * * * * * * * *
 *  ██████╗ █████╗  ██████╗ ███████╗
 * ██╔════╝██╔══██╗██╔═══██╗██╔════╝
 * ██║     ███████║██║   ██║███████╗
 * ██║     ██╔══██║██║   ██║╚════██║
 * ╚██████╗██║  ██║╚██████╔╝███████║
 *  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚══════╝
 *
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress/caos/
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

class CAOS_DB
{
    /** @var string */
    private $current_version = '';

    /**
     * DB Migration constructor.
     */
    public function __construct()
    {
        $this->current_version = get_option(CAOS_Admin_Settings::CAOS_DB_VERSION);

        if ($this->should_run_migration('4.2.2')) {
            new CAOS_DB_Migrate_V422();
        }

        if ($this->should_run_migration('4.3.0')) {
            new CAOS_DB_Migrate_V430();
        }
    }

    /**
     * Checks whether migration script has been run.
     * 
     * @param mixed $version 
     * @return bool 
     */
    private function should_run_migration($version)
    {
        return version_compare($this->current_version, $version) < 0;
    }
}
