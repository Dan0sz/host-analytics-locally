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

class CAOS_Admin_Cron_Script extends CAOS_Admin_Cron_Update
{
    /** @var string $remoteFile */
    private $remoteFile;

    /** @var string $localFile */
    private $localFile;

    /** @var string $tweet */
    private $tweet = "https://twitter.com/intent/tweet?text=I+am+now+hosting+%s+locally+for+Google+Analytics.+Thanks+to+CAOS+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/";

    /** @var string $review */
    private $review = 'https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post';

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

        $file_downloaded = $this->download();

        // Only sent a success message if this is a AJAX request.
        if (wp_doing_ajax()) {
            $review_link = apply_filters('caos_manual_download_review_link', $this->review);
            $tweet_link  = apply_filters('caos_manual_download_tweet_link', $this->tweet);

            CAOS_Admin_Notice::set_notice(__('Congratulations!', 'host-analyticsjs-local') . ' ' . $file_downloaded . ' ' . sprintf(__('Would you be willing to <a href="%s" target="_blank">leave a review</a> or <a href="%s" target="_blank">tweet</a> about it?', 'host-analyticsjs-local'), $review_link, $tweet_link));
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
        $added = __('added to your Google Analytics tracking code.', 'host-analyticsjs-local');

        if (is_array($this->remoteFile)) {
            foreach ($this->remoteFile as $file => $location) {
                $this->update_file($location['local'], $location['remote']);

                if ($file == 'gtag') {
                    $caosGaUrl = str_replace('gtag.js', 'analytics.js', caos_init()->get_url());
                    $gaUrl     = CAOS_GA_URL . '/analytics.js';
                    $this->update_gtag_js($location['local'], $gaUrl, $caosGaUrl);
                }
            }

            $this->tweet = sprintf($this->tweet, 'gtag.js+and+analytics.js');

            return __('Gtag.js and analytics.js are downloaded successfully and', 'host-analyticsjs-local') . ' ' . $added;
        }

        $file = ucfirst(CAOS_OPT_REMOTE_JS_FILE);

        $this->update_file($this->localFile, $this->remoteFile);

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
                $this->update_file($this->localFile, $this->remoteFile);
            }

            $this->tweet = sprintf($this->tweet, 'analytics.js,+ec.js+and+linkid.js');

            return $file . ', ' . __('ec.js and linkid.js are downloaded successfully and', 'host-analyticsjs-local') . ' ' . $added;
        }

        $this->tweet = sprintf($this->tweet, CAOS_OPT_REMOTE_JS_FILE);

        return $file . ' ' . __('is downloaded successfully and', 'host-analyticsjs-local') . ' ' . $added;
    }
}
