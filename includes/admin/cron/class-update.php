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

class CAOS_Admin_Cron_Update
{
    /**
     * @var $file
     */
    private $file;

    /**
     * Downloads $remoteFile and writes it to $localFile
     *
     * We're using cUrl so allow_furl_open doesn't need to be set.
     *
     * @param $localFile
     * @param $remoteFile
     */
    protected function update_file_curl($localFile, $remoteFile)
    {
        $this->file = fopen($localFile, 'w+');
        $curl       = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL            => $remoteFile,
                CURLOPT_FILE           => $this->file,
                CURLOPT_HEADER         => false,
                CURLOPT_FOLLOWLOCATION => true
            )
        );

        curl_exec($curl);
        curl_close($curl);
        fclose($this->file);

        if (file_exists($localFile) && filesize($localFile) > 1) {
            return;
        }

        $this->update_file($localFile, $remoteFile);
    }

    /**
     * @param $localFile
     * @param $remoteFile
     */
    protected function update_file($localFile, $remoteFile)
    {
        file_put_contents($localFile, file_get_contents($remoteFile));
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
     * @param $gaUrl
     * @param $caosGaUrl
     */
    protected function update_gtag_js($file, $gaUrl, $caosGaUrl)
    {
        return file_put_contents($file, str_replace($gaUrl, $caosGaUrl, file_get_contents($file)));
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
        $find     = array(
            'http://',
            'https://'
        );
        $replace  = '';
        $siteUrl  = str_replace($find, $replace, get_site_url(CAOS_BLOG_ID));
        $proxyUrl = $siteUrl . CAOS_PROXY_URI;

        return file_put_contents($file, str_replace('www.google-analytics.com', $proxyUrl, file_get_contents($file)));
    }
}
