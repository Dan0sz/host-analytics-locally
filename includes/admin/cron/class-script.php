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
    /** @var string $files */
    private $files;

    /** @var string $tweet */
    private $tweet = "https://twitter.com/intent/tweet?text=I+am+now+hosting+%s+locally+for+Google+Analytics.+Thanks+to+CAOS+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/";

    /** @var string $review */
    private $review = 'https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post';

    /**
     * CAOS_Admin_Cron_Script constructor.
     */
    public function __construct()
    {
        $this->files = $this->queue_files();

        // Check if directory exists, otherwise create it.
        $this->create_dir_recursive(CAOS_LOCAL_DIR);

        $file_downloaded = $this->download();

        // Only sent a success message if this is a AJAX request.
        if (wp_doing_ajax()) {
            $review_link = apply_filters('caos_manual_download_review_link', $this->review);
            $tweet_link  = apply_filters('caos_manual_download_tweet_link', $this->tweet);

            CAOS_Admin_Notice::set_notice(__('Congratulations!', 'host-analyticsjs-local') . ' ' . $file_downloaded . ' ' . sprintf(__('Would you be willing to <a href="%s" target="_blank">leave a review</a> or <a href="%s" target="_blank">tweet</a> about it?', 'host-analyticsjs-local'), $review_link, $tweet_link), true, 'success', 200, 'all', 'file_downloaded');
        }
    }

    private function queue_files()
    {
        $key = str_replace('.js', '', CAOS_OPT_REMOTE_JS_FILE);

        if (CAOS_OPT_REMOTE_JS_FILE == 'gtag.js') {
            return [
                'analytics' => [
                    'remote' => CAOS_GA_URL . '/analytics.js',
                    'local'  => CAOS_LOCAL_DIR . 'analytics.js'
                ],
                $key => [
                    'remote' => CAOS_GTM_URL . '/' . CAOS_OPT_REMOTE_JS_FILE,
                    'local'  => CAOS_LOCAL_FILE_DIR
                ]
            ];
        }

        return [
            $key => [
                'remote' => CAOS_GA_URL . '/' .  CAOS_OPT_REMOTE_JS_FILE,
                'local'  => CAOS_LOCAL_FILE_DIR
            ]
        ];
    }

    /**
     * Download file.
     */
    private function download()
    {
        $added = __('added to your Google Analytics tracking code.', 'host-analyticsjs-local');

        $this->tweet = sprintf($this->tweet, CAOS_OPT_REMOTE_JS_FILE);

        foreach ($this->files as $file => $location) {
            $this->update_file($location['local'], $location['remote']);

            if ($file == 'gtag') {
                $caosGaUrl = str_replace('gtag.js', 'analytics.js', CAOS::get_url());
                $gaUrl     = CAOS_GA_URL . '/analytics.js';
                $this->update_gtag_js($location['local'], $gaUrl, $caosGaUrl);

                $this->tweet = sprintf($this->tweet, 'gtag.js+and+analytics.js');
            }

            if ($file == 'analytics' && CAOS_OPT_EXT_STEALTH_MODE) {
                do_action('before_caos_stealth_mode_enable');

                $this->insert_proxy($location['local']);

                $pluginDir = CAOS_LOCAL_DIR . '/plugins/ua';
                $this->create_dir_recursive($pluginDir);

                $plugins = apply_filters('caos_stealth_mode_plugin_endpoints', [
                    '/plugins/ua/linkid.js'
                ]);

                foreach ($plugins as $plugin) {
                    $plugin_file        = rtrim(CAOS_LOCAL_DIR, '/') . $plugin;
                    $plugin_remote_file = CAOS_GA_URL . $plugin;
                    $this->update_file($plugin_file, $plugin_remote_file);
                }

                do_action('after_caos_stealth_mode_enable');
            }
        }

        return sprintf(__('%s downloaded successfully and', 'host-analyticsjs-local'), ucfirst(CAOS_OPT_REMOTE_JS_FILE)) . ' ' . $added;
    }
}
