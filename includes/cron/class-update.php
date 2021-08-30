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

class CAOS_Cron_Update extends CAOS_Cron
{
    /** @var [] $files */
    private $files;

    /** @var string $tweet */
    private $tweet = 'https://twitter.com/intent/tweet?text=I+am+now+hosting+%s+locally+for+Google+Analytics.+Thanks+to+CAOS+for+@WordPress!+Try+it+for+yourself:&via=Dan0sz&hashtags=GoogleAnalytics,WordPress,Pagespeed,Insights&url=https://wordpress.org/plugins/host-analyticsjs-local/';

    /** @var string $review */
    private $review = 'https://wordpress.org/support/plugin/host-analyticsjs-local/reviews/?rate=5#new-post';

    /**
     * CAOS_Cron_Script constructor.
     */
    public function __construct()
    {
        if (CAOS_OPT_SNIPPET_TYPE == 'minimal') {
            return;
        }

        do_action('caos_cron_update');

        $this->files = $this->build_download_queue();

        CAOS::debug(sprintf(__('Built file queue: %s', $this->plugin_text_domain), print_r($this->files, true)));

        // Check if directory exists, otherwise create it.
        $create_dir = $this->create_dir_recursive(CAOS_LOCAL_DIR);

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

            $notice = $this->build_natural_sentence($downloaded_files);

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
     * Enqueues the files that need to be downloaded.
     * 
     * @return array
     */
    private function build_download_queue()
    {
        $key = str_replace('.js', '', CAOS_OPT_REMOTE_JS_FILE);

        if ($key == 'gtag') {
            return [
                'analytics' => [
                    'remote' => CAOS_GA_URL . '/analytics.js',
                    'local'  => CAOS::get_file_alias_path('analytics')
                ],
                $key => [
                    'remote' => CAOS_GTM_URL . '/' . 'gtag/js?id=' . CAOS_OPT_TRACKING_ID,
                    'local'  => CAOS::get_file_alias_path($key)
                ]
            ];
        }

        if ($key == 'gtag-v4') {
            return [
                $key => [
                    'remote' => CAOS_GTM_URL . '/' . 'gtag/js?id=' . CAOS_OPT_TRACKING_ID,
                    'local'  => CAOS::get_file_alias_path($key)
                ]
            ];
        }

        return [
            $key => [
                'remote' => CAOS_GA_URL . '/' . CAOS_OPT_REMOTE_JS_FILE,
                'local'  => CAOS::get_file_alias_path($key)
            ]
        ];
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
            $downloaded_file = $this->download_file($location['local'], $location['remote'], $file);

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
                $finds      = [$ext_ga_url, '/gtag/js?id=', '"//www.googletagmanager.com"', "\"pageview\""];
                $replaces   = [$local_ga_url, $file_alias . '?id=', "\"$home_url\"", $hit_type];

                $this->find_replace_in($downloaded_file, $finds, $replaces);

                $this->tweet = sprintf($this->tweet, 'gtag.js+and+analytics.js');
            }

            if ($file == 'gtag-v4' && CAOS_OPT_EXT_STEALTH_MODE) {
                /**
                 * Since V4 is still in beta, the endpoints are bound to change. This filters the used endpoints.
                 * 
                 * @since 3.9.0
                 */
                $v4_collect_endpoint   = apply_filters('caos_gtag_v4_collect_endpoint', 'https://www.google-analytics.com/g/collect');
                $stealth_mode_endpoint = apply_filters('caos_gtag_v4_stealth_mode_endpoint', home_url('wp-json/caos/v1/proxy/g/collect'));

                $this->find_replace_in($downloaded_file, $v4_collect_endpoint, $stealth_mode_endpoint);
            }

            if ($file == 'analytics' && CAOS_OPT_EXT_STEALTH_MODE) {
                do_action('before_caos_stealth_mode_enable');

                $this->insert_proxy($downloaded_file);

                $pluginDir = CAOS_LOCAL_DIR . '/plugins/ua';
                $this->create_dir_recursive($pluginDir);

                $plugins = apply_filters('caos_stealth_mode_plugin_endpoints', [
                    '/plugins/ua/linkid.js'
                ]);

                foreach ($plugins as $plugin) {
                    $plugin_file        = rtrim(CAOS_LOCAL_DIR, '/') . $plugin;
                    $plugin_remote_file = CAOS_GA_URL . $plugin;
                    $this->download_file($plugin_file, $plugin_remote_file, '', true);
                }

                do_action('after_caos_stealth_mode_enable');
            }

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
