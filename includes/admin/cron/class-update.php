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

class CAOS_Admin_Cron_Update
{
    /**
     * @var $file
     */
    private $file;

    /**
     * Downloads $remoteFile and writes it to $localFile
     *
     * @param $localFile
     * @param $remoteFile
     *
     * @return void|string
     */
    protected function download_file($localFile, $remoteFile)
    {
        do_action('caos_admin_update_before');

        $this->file = wp_remote_get($remoteFile, $localFile);

        if (is_wp_error($this->file)) {
            return $this->file->get_error_code() . ': ' . $this->file->get_error_message();
        }

        CAOS::filesystem()->put_contents($localFile, $this->file['body']);

        do_action('caos_admin_update_after');
    }

    /**
     * Create directories recursive
     *
     * @param $path
     */
    protected function create_dir_recursive($path)
    {
        if (!file_exists($path)) {
            wp_mkdir_p($path);
        }
    }

    /**
     * Update Gtag.js
     *
     * @param $file
     * @param $find
     * @param $replace
     */
    protected function update_gtag_js($file, $find, $replace)
    {
        return file_put_contents($file, str_replace($find, $replace, file_get_contents($file)));
    }

    /**
     * Opens file and replaces every instance of google-analytics.com with CAOS' proxy endpoint
     * inside $file.
     * Used only when Stealth Mode is enabled.
     *
     * @param $file
     */
    protected function insert_proxy($file)
    {
        $find             = array(
            'http://',
            'https://'
        );
        $replace          = '';
        $siteUrl          = str_replace($find, $replace, get_site_url(CAOS_BLOG_ID));
        $proxyUrl         = apply_filters('caos_stealth_mode_proxy_uri', $siteUrl . CAOS_PROXY_URI);
        $google_endpoints = apply_filters('caos_stealth_mode_google_endpoints', []);
        $caos_endpoint    = apply_filters('caos_stealth_mode_endpoint', '');
        $new_file         = file_put_contents($file, str_replace($google_endpoints, $caos_endpoint, file_get_contents($file)));
        $new_file         = file_put_contents($file, str_replace('www.google-analytics.com', $proxyUrl, file_get_contents($file)));

        return $new_file;
    }
}
