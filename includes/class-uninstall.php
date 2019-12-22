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

class CAOS_Uninstall
{
    /** @var array $options */
    private $options;

    /** @var string $cacheDir */
    private $cacheDir;

    /**
     * CAOS_Uninstall constructor.
     * @throws ReflectionException
     */
    public function __construct()
    {
        if (CAOS_OPT_UNINSTALL_SETTINGS !== 'on') {
            return;
        }

        $settings       = new CAOS_Admin_Settings();
        $this->options  = $settings->get_settings();
        $this->cacheDir = CAOS_OPT_CACHE_DIR;

        $this->remove_db_entries();
        $this->delete_files();
        $this->delete_dir();
    }

    /**
     * Remove all options from the database.
     */
    private function remove_db_entries()
    {
        foreach ($this->options as $constant => $option) {
            delete_option($option);
        }
    }

    /**
     * Delete all files in the cache directory.
     *
     * @return array
     */
    private function delete_files()
    {
        return array_map('unlink', glob($this->cacheDir . '/*.*'));
    }

    /**
     * Delete the cache directory.
     *
     * @return bool
     */
    private function delete_dir()
    {
        return rmdir($this->cacheDir);
    }
}
