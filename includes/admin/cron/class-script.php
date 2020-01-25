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

class CAOS_Admin_Cron_Script extends CAOS_Admin_Cron_Update
{
    /** @var string $remoteFile */
    private $remoteFile;

    /** @var string $localFile */
    private $localFile;

    /**
     * CAOS_Admin_Cron_Script constructor.
     */
    public function __construct()
    {
        $this->remoteFile = CAOS_REMOTE_URL . '/' . CAOS_OPT_REMOTE_JS_FILE;;
        $this->localFile  = CAOS_LOCAL_FILE_DIR;

        // Check if directory exists, otherwise create it.
        $this->create_dir_recursive(CAOS_LOCAL_DIR);

        $this->is_gtag();

        $file = $this->download();

        // Only sent a success message if this is a AJAX request.
        if (wp_doing_ajax()) {
            wp_die($file . ' ' . __("successfully downloaded and saved.", 'host-analyticsjs-local'));
        }
    }

    /**
     * If gtag.js is selected. We need to download analytics.js as well.
     */
    private function is_gtag()
    {
        if (CAOS_OPT_REMOTE_JS_FILE == 'gtag.js') {
            $this->remoteFile = array(
                'analytics' => array(
                    'remote' => CAOS_GA_URL . '/analytics.js',
                    'local'  => CAOS_LOCAL_DIR . 'analytics.js'
                ),
                'gtag' => array(
                    'remote' => CAOS_GTM_URL . '/' . CAOS_OPT_REMOTE_JS_FILE,
                    'local'  => CAOS_LOCAL_FILE_DIR
                )
            );
        }
    }

    /**
     * Download file.
     */
    private function download()
    {
        if (is_array($this->remoteFile)) {
            foreach ($this->remoteFile as $file => $location) {
                $this->update_file_curl($location['local'], $location['remote']);

                if ($file == 'gtag') {
                    $caosGaUrl = str_replace('gtag.js', 'analytics.js', caos_init()->get_url());
                    $gaUrl     = CAOS_GA_URL . '/analytics.js';
                    $this->update_gtag_js($location['local'], $gaUrl, $caosGaUrl);
                }
            }

            return 'Gtag.js and analytics.js';
        }

        $file = 'Analytics.js';

        $this->update_file_curl($this->localFile, $this->remoteFile);

        if (CAOS_OPT_STEALTH_MODE && (CAOS_OPT_REMOTE_JS_FILE == 'analytics.js')) {
            $this->insert_proxy(CAOS_LOCAL_FILE_DIR);

            $pluginDir = CAOS_LOCAL_DIR . '/plugins/ua';
            $this->create_dir_recursive($pluginDir);

            $plugins = [
                '/plugins/ua/ec.js',
                '/plugins/ua/linkid.js'
            ];

            foreach ($plugins as $plugin) {
                $this->localFile = rtrim(CAOS_LOCAL_DIR, '/') . $plugin;
                $this->remoteFile = CAOS_GA_URL . $plugin;
                $this->update_file_curl($this->localFile, $this->remoteFile);
            }

            $file .= ', ec.js and linkid.js';
        }

        return $file;
    }
}
