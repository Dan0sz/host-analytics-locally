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
 * @url      : https://daan.dev/wordpress/caos/
 * @copyright: (c) 2021 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */

defined('ABSPATH') || exit;

class CAOS_Cron
{
    /** @var [] $files */
    private $files;

    /** @var string $tweet */
    private $tweet = 'https://twitter.com/intent/tweet?text=I+am+now+hosting+%s+locally+for+Google+Analytics.+Thanks+to+CAOS+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/';

    /** @var string $review */
    private $review = 'https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post';

    /** @var string $plugin_text_domain */
    private $plugin_text_domain = 'host-analyticsjs-local';

    /**
     * CAOS_Cron_Script constructor.
     */
    public function __construct()
    {
        do_action('caos_cron_update');

        $this->files = $this->build_download_queue();

        CAOS::debug(sprintf(__('Built file queue: %s', $this->plugin_text_domain), print_r($this->files, true)));

        // Check if directory exists, otherwise create it.
        $create_dir = CAOS::create_dir_r(CAOS_LOCAL_DIR);

        if ($create_dir) {
            CAOS::debug(sprintf(__('%s created successfully.', $this->plugin_text_domain), CAOS_LOCAL_DIR));
        } else {
            CAOS::debug(sprintf(__('%s already exists.', $this->plugin_text_domain), CAOS_LOCAL_DIR));
        }

        $downloaded_files = $this->download();

        // Only sent a success message if this is a AJAX request.
        if (!wp_doing_cron()) {
            $review_link = apply_filters('caos_manual_download_review_link', $this->review);
            $tweet_link  = apply_filters('caos_manual_download_tweet_link', $this->tweet);
            $notice      = $this->build_natural_sentence($downloaded_files);

            CAOS_Admin_Notice::set_notice($notice . ' ' . sprintf(__('Would you be willing to <a href="%s" target="_blank">write a review</a> or <a href="%s" target="_blank">tweet</a> about it?', 'host-analyticsjs-local'), $review_link, $tweet_link), 'success', 'all', 'file_downloaded');
        }
    }

    /**
     * 
     * @param array $list 
     * @return string 
     */
    private function build_natural_sentence(array $list)
    {
        $i        = 0;
        $last     = count($list) - 1;
        $sentence = '';

        foreach ($list as $filename => $alias) {
            if (count($list) > 1 && $i == $last) {
                $sentence .= __('and ', $this->plugin_text_domain);
            }

            $sentence .= sprintf(__("%s"), $filename, $alias) . ' ';

            $i++;
        }

        $sentence .= _n(
            'is downloaded successfully and updated accordingly.',
            'are downloaded successfully and updated accordingly.',
            count($list),
            $this->plugin_text_domain
        );

        return $sentence;
    }

    /**
     * Enqueues the files that need to be downloaded, depending on the settings.
     * 
     * @since v4.2.0 Added Dual Tracking compatibility.
     * 
     * @return array
     */
    private function build_download_queue()
    {
        $queue = [];

        /**
         * This is a "fix" for the undefined method error @since v4.2.2.
         */
        if (!method_exists('CAOS', 'get_current_file_key')) {
            return $queue;
        }

        $key = CAOS::get_current_file_key();

        /**
         * Plausible Analytics
         */
        if ($key == 'plausible') {
            $remote_file = 'script.';

            if (CAOS_OPT_EXT_CAPTURE_OUTBOUND_LINKS == 'on') {
                $remote_file .= 'outbound-links.';
            }

            $remote_file .= 'js';

            $queue = array_merge($queue, [
                'plausible' => [
                    'remote' => "https://plausible.io/js/$remote_file",
                    'local'  => CAOS::get_file_alias_path('plausible')
                ]
            ]);

            // No need to continue here...
            return $queue;
        }

        /**
         * Gtag V3 is a wrapper for analytics.js, so add it to the queue.
         */
        if ($key == 'analytics' || $key == 'gtag') {
            $queue = array_merge($queue, [
                'analytics' => [
                    'remote' => CAOS_GA_URL . '/analytics.js',
                    'local'  => CAOS::get_file_alias_path('analytics')
                ]
            ]);
        }

        /**
         * Gtag V3
         */
        if ($key == 'gtag') {
            $queue = array_merge($queue, [
                $key => [
                    'remote' => CAOS_GTM_URL . '/' . 'gtag/js?id=' . CAOS_OPT_TRACKING_ID,
                    'local'  => CAOS::get_file_alias_path($key)
                ]
            ]);
        }

        /**
         * If Dual Tracking is enabled, then add Gtag V4 to the download queue.
         */
        if (CAOS_OPT_DUAL_TRACKING == 'on' || $key == 'gtag-v4') {
            $tracking_id = CAOS_OPT_DUAL_TRACKING == 'on' ? CAOS_OPT_GA4_MEASUREMENT_ID : CAOS_OPT_TRACKING_ID;

            $queue = array_merge($queue, [
                'gtag-v4' => [
                    'remote' => CAOS_GTM_URL . '/' . 'gtag/js?id=' . $tracking_id,
                    'local'  => CAOS::get_file_alias_path('gtag-v4')
                ]
            ]);
        }

        return $queue;
    }

    /**
     * Download files.
     */
    private function download()
    {
        $i                = 0;
        $downloaded_files = [];
        $this->tweet      = sprintf($this->tweet, CAOS_OPT_REMOTE_JS_FILE);

        foreach ($this->files as $file => $location) {
            $downloaded_file = CAOS::download_file($location['local'], $location['remote'], $file);

            if ($file == 'gtag') {
                $file_alias = CAOS::get_file_alias($file);
                /**
                 * Backwards compatibility with pre-file alias era.
                 * 
                 * @since 3.11.0
                 */
                if (!CAOS::get_file_aliases()) {
                    $local_ga_url = str_replace('gtag.js', 'analytics.js', CAOS::get_local_file_url());
                } else {
                    $local_ga_url = str_replace($file_alias, CAOS::get_file_alias('analytics'), CAOS::get_local_file_url());
                }

                $ext_ga_url = CAOS_GA_URL . '/analytics.js';
                $home_url   = str_replace(['https:', 'http:'], '', content_url(CAOS_OPT_CACHE_DIR));
                $hit_type   = apply_filters('caos_gtag_hit_type', '"pageview"');
                $file_alias = CAOS_OPT_DUAL_TRACKING == 'on' ? CAOS::get_file_alias('gtag-v4') : CAOS::get_file_alias($file);
                $finds      = [$ext_ga_url, '/gtag/js?id=', '"//www.googletagmanager.com"', "\"pageview\""];
                $replaces   = [$local_ga_url, $file_alias . '?id=', "\"$home_url\"", $hit_type];

                if (CAOS::dual_tracking_is_enabled()) {
                    array_push($finds, 'https://www.googletagmanager.com/gtag/js?id=', 'www.googletagmanager.com', '/gtag/js');
                    array_push($replaces, CAOS::get_local_file_url() . '?id=', trim($home_url, '/'), '/' . $file_alias);
                }

                CAOS::find_replace_in($downloaded_file, $finds, $replaces);

                $this->tweet = sprintf($this->tweet, 'gtag.js+and+analytics.js');
            }

            $downloaded_file = apply_filters("caos_cron_update_${file}", $downloaded_file);

            /**
             * Make first entry uppercase.
             */
            if ($i == 0) {
                $file = ucfirst($file);
            }

            $i++;

            $downloaded_files[$file . '.js'] = CAOS::get_file_alias($file);
        }

        /**
         * Writes all currently stored file aliases to the database.
         */
        CAOS::set_file_aliases(CAOS::get_file_aliases(), true);

        return $downloaded_files;
    }
}
