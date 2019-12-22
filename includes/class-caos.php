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

class CAOS
{
    /**
     * CAOS constructor.
     */
    public function __construct()
    {
        $this->define_constants();
        $this->do_ajax();

        if(is_admin()) {
            $this->do_setup();
            $this->do_settings();
        }

        if(!is_admin()) {
            $this->do_frontend();
            $this->do_tracking_code();
        }
    }

    /**
     * Define constants
     */
    public function define_constants()
    {
        define('CAOS_SITE_URL', 'https://daan.dev');
        define('CAOS_BLOG_ID', get_current_blog_id());
        define('CAOS_OPT_TRACKING_ID', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_TRACKING_ID)));
        define('CAOS_OPT_ALLOW_TRACKING', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_ALLOW_TRACKING)));
        define('CAOS_OPT_COOKIE_NAME', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_COOKIE_NOTICE_NAME)));
        define('CAOS_OPT_COOKIE_VALUE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_COOKIE_VALUE)));
        define('CAOS_OPT_COMPATIBILITY_MODE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_COMPATIBILITY_MODE, null)));
        define('CAOS_OPT_STEALTH_MODE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_STEALTH_MODE)));
        define('CAOS_OPT_COOKIE_EXPIRY', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_GA_COOKIE_EXPIRY_DAYS)));
        define('CAOS_OPT_ADJUSTED_BOUNCE_RATE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_ADJUSTED_BOUNCE_RATE)));
        define('CAOS_OPT_ENQUEUE_ORDER', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_ENQUEUE_ORDER, 0)));
        define('CAOS_OPT_ANONYMIZE_IP', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_ANONYMIZE_IP)));
        define('CAOS_OPT_TRACK_ADMIN', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_TRACK_ADMIN)));
        define('CAOS_OPT_DISABLE_DISPLAY_FEAT', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_DISABLE_DISPLAY_FEATURES)));
        define('CAOS_OPT_SCRIPT_POSITION', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_SCRIPT_POSITION)));
        define('CAOS_OPT_SNIPPET_TYPE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_SNIPPET_TYPE, 'default')));
        define('CAOS_OPT_REMOTE_JS_FILE', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_JS_FILE, 'analytics.js')));
        define('CAOS_OPT_CACHE_DIR', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_CACHE_DIR, '/cache/caos-analytics/')));
        define('CAOS_OPT_CDN_URL', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_CDN_URL)));
        define('CAOS_OPT_CAPTURE_OUTBOUND_LINKS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_CAPTURE_OUTBOUND_LINKS)));
        define('CAOS_OPT_UNINSTALL_SETTINGS', esc_attr(get_option(CAOS_Admin_Settings::CAOS_SETTING_UNINSTALL_SETTINGS)));
        define('CAOS_COOKIE_EXPIRY_DAYS', CAOS_OPT_COOKIE_EXPIRY ? CAOS_OPT_COOKIE_EXPIRY * 86400 : 0);
        define('CAOS_CRON', 'caos_update_analytics_js');
        define('CAOS_GA_URL', 'https://www.google-analytics.com');
        define('CAOS_GTM_URL', 'https://www.googletagmanager.com');
        define('CAOS_REMOTE_URL', CAOS_OPT_REMOTE_JS_FILE == 'gtag.js' ? CAOS_GTM_URL : CAOS_GA_URL);
        define('CAOS_LOCAL_DIR', WP_CONTENT_DIR . CAOS_OPT_CACHE_DIR);
        define('CAOS_LOCAL_FILE_DIR', CAOS_LOCAL_DIR . CAOS_OPT_REMOTE_JS_FILE);
        define('CAOS_LOCAL_FILE_URL', $this->get_url());
        define('CAOS_PROXY_URI', '/wp-json/caos-analytics/v1/proxy');
    }

    /**
     * @return CAOS_AJAX
     */
    private function do_ajax()
    {
        return new CAOS_AJAX();
    }

    /**
     * @return CAOS_Setup
     */
    private function do_setup()
    {
        register_uninstall_hook(CAOS_PLUGIN_FILE, 'CAOS::do_uninstall');

        return new CAOS_Setup();
    }

    /**
     * @return CAOS_Admin_Settings
     */
    private function do_settings()
    {
        return new CAOS_Admin_Settings();
    }

    /**
     * @return CAOS_Frontend_Functions
     */
    private function do_frontend()
    {
        return new CAOS_Frontend_Functions();
    }

    /**
     * @return CAOS_Frontend_Tracking
     */
    private function do_tracking_code()
    {
        return new CAOS_Frontend_Tracking();
    }

    /**
     * @return string
     */
    public function get_url()
    {
        $url = content_url() . CAOS_OPT_CACHE_DIR . CAOS_OPT_REMOTE_JS_FILE;

        if (CAOS_OPT_CDN_URL) {
            $url = str_replace(get_site_url(CAOS_BLOG_ID), '//' . CAOS_OPT_CDN_URL, $url);
        }

        return $url;
    }

    /**
     * @return CAOS_Uninstall
     * @throws ReflectionException
     */
    public static function do_uninstall()
    {
        return new CAOS_Uninstall();
    }
}
