<?php
/**
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 */

/**
 * Class CAOS_Update
 *
 * This class contains all needed functions for downloading and syncing remote files to a local
 * version.
 */
class CAOS_Update
{
    /**
     * Downloads $remoteFile and writes it to $localFile
     *
     * We're using cUrl so allow_furl_open doesn't need to be set.
     *
     * @param $localFile
     * @param $remoteFile
     */
    public function update_file($localFile, $remoteFile)
    {
        $localFile = fopen($localFile, 'w+');
        $curl      = curl_init($remoteFile);

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FILE           => $localFile,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HEADER         => false
            )
        );

        curl_exec($curl);
        curl_close($curl);

        return fclose($localFile);
    }

    /**
     * Create directories recursive
     *
     * @param $path
     */
    public function create_dir_recursive($path)
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
    public function update_gtag_js($file, $gaUrl, $caosGaUrl)
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
    public function insert_proxy($file)
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
